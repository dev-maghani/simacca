# 🏗️ Service Layer Pattern Guide - SIMACCA

## Overview

This guide documents the Service Layer pattern implemented in SIMACCA to separate business logic from controllers, making the codebase more maintainable, testable, and reusable.

---

## 📋 Table of Contents

1. [What is a Service Layer?](#what-is-a-service-layer)
2. [Why Use Service Layer?](#why-use-service-layer)
3. [Architecture](#architecture)
4. [BaseService Class](#baseservice-class)
5. [Creating a New Service](#creating-a-new-service)
6. [Using Services in Controllers](#using-services-in-controllers)
7. [Response Format](#response-format)
8. [Best Practices](#best-practices)
9. [Examples](#examples)
10. [Testing Services](#testing-services)

---

## What is a Service Layer?

A **Service Layer** is an architectural pattern that encapsulates business logic in dedicated service classes, separate from controllers. Controllers handle HTTP requests/responses, while services handle the actual business operations.

### The Problem (Before)

```php
// Controller with 800+ lines doing everything
class GuruController extends BaseController
{
    public function store()
    {
        // Validation
        $rules = [...];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput();
        }

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create user
            $userData = [...];
            $userId = $this->userModel->insert($userData);

            // Create guru
            $guruData = [...];
            $this->guruModel->insert($guruData);

            // Update wali kelas
            if ($isWaliKelas) {
                $this->kelasModel->update(...);
            }

            $db->transComplete();
            
            // Send email
            helper('email');
            send_notification(...);

            return redirect()->to('/admin/guru');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back();
        }
    }
}
```

**Problems:**
- ❌ Business logic mixed with HTTP handling
- ❌ Hard to test
- ❌ Code duplication across controllers
- ❌ Manual transaction management
- ❌ Inconsistent error handling

### The Solution (After)

```php
// Clean controller (HTTP handling only)
class GuruController extends BaseController
{
    protected $guruService;

    public function __construct()
    {
        $this->guruService = new GuruService();
    }

    public function store()
    {
        $data = $this->request->getPost();
        $result = $this->guruService->createGuru($data);

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors']);
        }

        session()->setFlashdata('success', 'Guru berhasil ditambahkan');
        return redirect()->to('/admin/guru');
    }
}

// Reusable service (business logic)
class GuruService extends BaseService
{
    public function createGuru(array $data): array
    {
        // Validation
        if (!$this->validate($data, $rules)) {
            return $this->errorResponse('Validasi gagal');
        }

        // Execute in transaction
        return $this->executeInTransaction(function () use ($data) {
            // Create user
            // Create guru
            // Update wali kelas
            // Send email
            return ['guru_id' => $guruId];
        });
    }
}
```

**Benefits:**
- ✅ Clean separation of concerns
- ✅ Reusable business logic
- ✅ Easy to test
- ✅ Automatic transaction management
- ✅ Consistent error handling

---

## Why Use Service Layer?

### 1. **Separation of Concerns**
- Controllers: Handle HTTP (requests/responses, redirects, sessions)
- Services: Handle business logic (validation, database operations, calculations)
- Models: Handle data access (queries, relationships)

### 2. **Reusability**
```php
// Same service can be used in multiple places
class AdminGuruController {
    public function store() {
        return $this->guruService->createGuru($data);
    }
}

class ApiGuruController {
    public function store() {
        return $this->guruService->createGuru($data);
    }
}

class ImportController {
    public function processImport() {
        foreach ($rows as $row) {
            $this->guruService->createGuru($row);
        }
    }
}
```

### 3. **Testability**
```php
// Easy to test without HTTP layer
public function testCreateGuru()
{
    $service = new GuruService();
    $result = $service->createGuru([
        'nip' => '123456',
        'nama_lengkap' => 'Test Guru',
        // ...
    ]);
    
    $this->assertTrue($result['success']);
}
```

### 4. **Maintainability**
- Business logic in one place
- Changes affect all consumers
- Easier to debug and understand

### 5. **Consistency**
- All services follow same pattern
- Predictable response format
- Standard error handling

---

## Architecture

```
┌─────────────────────────────────────────────┐
│                 HTTP Layer                  │
│  (Controllers handle requests/responses)    │
└────────────┬────────────────────────────────┘
             │
             │ calls
             ▼
┌─────────────────────────────────────────────┐
│              Service Layer                  │
│  (Services handle business logic)           │
│  - GuruService                              │
│  - AbsensiService                           │
│  - SiswaService                             │
│  - etc.                                     │
└────────────┬────────────────────────────────┘
             │
             │ uses
             ▼
┌─────────────────────────────────────────────┐
│               Data Layer                    │
│  (Models handle database operations)        │
│  - GuruModel                                │
│  - UserModel                                │
│  - etc.                                     │
└─────────────────────────────────────────────┘
```

---

## BaseService Class

All services extend `BaseService`, which provides common functionality.

### Location
`app/Services/BaseService.php`

### Features

#### 1. **Transaction Management**
```php
protected function beginTransaction(): void;
protected function commitTransaction(): bool;
protected function rollbackTransaction(): void;
```

#### 2. **Error Handling**
```php
protected function addError(string $field, string $message): void;
public function getErrors(): array;
public function hasErrors(): bool;
protected function clearErrors(): void;
```

#### 3. **Response Formatting**
```php
protected function successResponse($data = null, string $message = 'Success'): array;
protected function errorResponse(string $message, array $errors = []): array;
```

#### 4. **Validation**
```php
protected function validate(array $data, array $rules): bool;
```

#### 5. **Logging**
```php
protected function log(string $level, string $message, array $context = []): void;
```

#### 6. **Transaction Wrapper**
```php
protected function executeInTransaction(callable $callback): array;
```

### Example Usage

```php
class MyService extends BaseService
{
    public function doSomething($data): array
    {
        // Validate
        $rules = ['name' => 'required'];
        if (!$this->validate($data, $rules)) {
            return $this->errorResponse('Validation failed');
        }

        // Execute in transaction
        return $this->executeInTransaction(function () use ($data) {
            // Do database operations
            $this->model->insert($data);
            
            // Log success
            $this->log('info', 'Operation completed');
            
            return ['id' => $this->model->getInsertID()];
        });
    }
}
```

---

## Creating a New Service

### Step 1: Create the Service File

Create a new file in `app/Services/`:

```php
<?php

namespace App\Services;

use App\Models\YourModel;

class YourService extends BaseService
{
    protected $yourModel;

    public function __construct()
    {
        parent::__construct();
        $this->yourModel = new YourModel();
    }
}
```

### Step 2: Implement Service Methods

Follow this pattern for each method:

```php
/**
 * Method description
 * 
 * @param array $data
 * @return array ['success' => bool, 'message' => string, 'data' => mixed, 'errors' => array]
 */
public function methodName(array $data): array
{
    // 1. Validate input
    $rules = [
        'field' => 'required|...',
    ];
    
    if (!$this->validate($data, $rules)) {
        return $this->errorResponse('Validation failed');
    }

    // 2. Execute business logic in transaction
    return $this->executeInTransaction(function () use ($data) {
        // Database operations
        $result = $this->yourModel->insert($data);
        
        // Additional logic
        $this->doSomethingElse($result);
        
        // Log
        $this->log('info', 'Operation completed');
        
        // Return data
        return ['id' => $result];
    });
}
```

### Step 3: Add Helper Methods (Private/Protected)

```php
/**
 * Helper method (not called from controller)
 */
protected function helperMethod($param): void
{
    // Internal logic
}
```

---

## Using Services in Controllers

### Step 1: Inject Service

```php
class YourController extends BaseController
{
    protected $yourService;

    public function __construct()
    {
        $this->yourService = new YourService();
    }
}
```

### Step 2: Call Service Methods

```php
public function store()
{
    // Prepare data
    $data = [
        'field1' => $this->request->getPost('field1'),
        'field2' => $this->request->getPost('field2'),
    ];

    // Call service
    $result = $this->yourService->createSomething($data);

    // Handle response
    if (!$result['success']) {
        session()->setFlashdata('error', $result['message']);
        return redirect()->back()->withInput()->with('errors', $result['errors']);
    }

    session()->setFlashdata('success', 'Operation successful');
    return redirect()->to('/some/path');
}
```

### Step 3: Handle Different Response Types

#### Success Response
```php
$result = $this->yourService->getData();
if ($result['success']) {
    $data = $result['data']; // Use the data
}
```

#### Error Response
```php
$result = $this->yourService->doSomething($data);
if (!$result['success']) {
    // Show error message
    session()->setFlashdata('error', $result['message']);
    
    // Show validation errors
    return redirect()->back()->with('errors', $result['errors']);
}
```

#### API Response
```php
$result = $this->yourService->getData();
return $this->response->setJSON($result);
```

---

## Response Format

All service methods return a consistent array format:

### Success Response

```php
[
    'success' => true,
    'message' => 'Operation successful',
    'data' => [
        'id' => 123,
        'name' => 'Some Data',
        // ... other data
    ]
]
```

### Error Response

```php
[
    'success' => false,
    'message' => 'Operation failed',
    'errors' => [
        'field1' => 'Field1 is required',
        'field2' => 'Field2 must be valid email',
    ]
]
```

### Why This Format?

1. **Consistent**: All services use the same format
2. **Predictable**: Easy to handle in controllers
3. **Flexible**: Can contain any data structure
4. **Informative**: Includes success status, message, and details

---

## Best Practices

### 1. **Single Responsibility**

Each service should handle one domain:

```php
// ✅ Good: Focused service
class GuruService {
    public function createGuru() {}
    public function updateGuru() {}
    public function deleteGuru() {}
}

// ❌ Bad: Mixed concerns
class GuruService {
    public function createGuru() {}
    public function generateAbsensiReport() {} // Wrong domain
}
```

### 2. **Method Naming**

Use clear, action-based names:

```php
// ✅ Good names
public function createGuru(array $data): array
public function updateGuru(int $id, array $data): array
public function deleteGuru(int $id): array
public function getGuruById(int $id): array
public function getAllGuru(): array

// ❌ Bad names
public function guru(array $data): array
public function process(int $id): array
```

### 3. **Return Type Declarations**

Always specify return types:

```php
// ✅ Good
public function createGuru(array $data): array

// ❌ Bad
public function createGuru($data)
```

### 4. **Input Validation**

Validate early in the method:

```php
public function createGuru(array $data): array
{
    // Validate first
    if (!$this->validate($data, $rules)) {
        return $this->errorResponse('Validation failed');
    }
    
    // Then proceed with logic
    return $this->executeInTransaction(function () use ($data) {
        // ...
    });
}
```

### 5. **Use Transactions**

Wrap multiple database operations in transactions:

```php
// ✅ Good: Uses transaction wrapper
return $this->executeInTransaction(function () use ($data) {
    $this->model1->insert($data);
    $this->model2->update($id, $data);
    return ['id' => $id];
});

// ❌ Bad: Manual transaction without error handling
$this->db->transStart();
$this->model1->insert($data);
$this->model2->update($id, $data);
$this->db->transComplete();
```

### 6. **Log Important Operations**

Log key events for debugging:

```php
$this->log('info', "Guru created: {$data['nama_lengkap']} (ID: {$id})");
$this->log('warning', "Failed to send email to: {$email}");
$this->log('error', "Transaction failed: {$e->getMessage()}");
```

### 7. **Extract Complex Logic**

Break down complex methods into smaller ones:

```php
// ✅ Good: Broken down
public function createGuru(array $data): array
{
    if (!$this->validate($data, $this->getValidationRules())) {
        return $this->errorResponse('Validation failed');
    }
    
    return $this->executeInTransaction(function () use ($data) {
        $userId = $this->createUserAccount($data);
        $guruId = $this->createGuruRecord($userId, $data);
        $this->handleWaliKelasAssignment($guruId, $data);
        $this->sendWelcomeEmail($data);
        
        return ['guru_id' => $guruId, 'user_id' => $userId];
    });
}

protected function createUserAccount(array $data): int { /* ... */ }
protected function createGuruRecord(int $userId, array $data): int { /* ... */ }
protected function handleWaliKelasAssignment(int $guruId, array $data): void { /* ... */ }
protected function sendWelcomeEmail(array $data): void { /* ... */ }
```

### 8. **Don't Return Models Directly**

Return arrays or DTOs, not model instances:

```php
// ✅ Good: Returns array
public function getGuruById(int $id): array
{
    $guru = $this->guruModel->find($id);
    return $this->successResponse($guru);
}

// ❌ Bad: Returns model instance
public function getGuruById(int $id): GuruModel
{
    return $this->guruModel->find($id);
}
```

### 9. **Handle Errors Gracefully**

Always return proper error responses:

```php
try {
    // Operation
    return $this->successResponse($data);
} catch (\Exception $e) {
    $this->log('error', $e->getMessage());
    return $this->errorResponse('Operation failed');
}
```

### 10. **Document Public Methods**

Add PHPDoc comments:

```php
/**
 * Create a new guru (teacher)
 * 
 * Creates user account, guru record, and handles wali kelas assignment
 * if applicable. Sends welcome email to the new guru.
 * 
 * @param array $data Guru data including user credentials
 * @return array ['success' => bool, 'message' => string, 'data' => array, 'errors' => array]
 */
public function createGuru(array $data): array
{
    // ...
}
```

---

## Examples

### Example 1: Simple CRUD Service

```php
<?php

namespace App\Services;

use App\Models\MataPelajaranModel;

class MataPelajaranService extends BaseService
{
    protected $mapelModel;

    public function __construct()
    {
        parent::__construct();
        $this->mapelModel = new MataPelajaranModel();
    }

    /**
     * Get all mata pelajaran
     */
    public function getAllMapel(): array
    {
        try {
            $data = $this->mapelModel->findAll();
            return $this->successResponse($data);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get mapel: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data');
        }
    }

    /**
     * Create new mata pelajaran
     */
    public function createMapel(array $data): array
    {
        $rules = [
            'kode_mapel' => 'required|is_unique[mata_pelajaran.kode_mapel]',
            'nama_mapel' => 'required',
        ];

        if (!$this->validate($data, $rules)) {
            return $this->errorResponse('Validasi gagal');
        }

        return $this->executeInTransaction(function () use ($data) {
            $id = $this->mapelModel->insert($data);
            $this->log('info', "Mapel created: {$data['nama_mapel']} (ID: {$id})");
            return ['id' => $id];
        });
    }

    /**
     * Update mata pelajaran
     */
    public function updateMapel(int $id, array $data): array
    {
        $existing = $this->mapelModel->find($id);
        if (!$existing) {
            return $this->errorResponse('Data tidak ditemukan');
        }

        $rules = [
            'kode_mapel' => "required|is_unique[mata_pelajaran.kode_mapel,id,{$id}]",
            'nama_mapel' => 'required',
        ];

        if (!$this->validate($data, $rules)) {
            return $this->errorResponse('Validasi gagal');
        }

        return $this->executeInTransaction(function () use ($id, $data) {
            $this->mapelModel->update($id, $data);
            $this->log('info', "Mapel updated: ID {$id}");
            return ['id' => $id];
        });
    }

    /**
     * Delete mata pelajaran
     */
    public function deleteMapel(int $id): array
    {
        $existing = $this->mapelModel->find($id);
        if (!$existing) {
            return $this->errorResponse('Data tidak ditemukan');
        }

        return $this->executeInTransaction(function () use ($id) {
            $this->mapelModel->delete($id);
            $this->log('info', "Mapel deleted: ID {$id}");
            return ['id' => $id];
        });
    }
}
```

### Example 2: Complex Service with Multiple Models

```php
<?php

namespace App\Services;

use App\Models\AbsensiModel;
use App\Models\AbsensiDetailModel;
use App\Models\JadwalMengajarModel;
use App\Models\SiswaModel;

class AbsensiService extends BaseService
{
    protected $absensiModel;
    protected $absensiDetailModel;
    protected $jadwalModel;
    protected $siswaModel;

    public function __construct()
    {
        parent::__construct();
        $this->absensiModel = new AbsensiModel();
        $this->absensiDetailModel = new AbsensiDetailModel();
        $this->jadwalModel = new JadwalMengajarModel();
        $this->siswaModel = new SiswaModel();
    }

    /**
     * Create absensi with details
     */
    public function createAbsensi(array $data): array
    {
        // Validate main absensi data
        if (!$this->validateAbsensiData($data)) {
            return $this->errorResponse('Validasi gagal');
        }

        // Validate student details
        if (!$this->validateAbsensiDetails($data['siswa_list'])) {
            return $this->errorResponse('Data siswa tidak valid');
        }

        return $this->executeInTransaction(function () use ($data) {
            // Create main absensi record
            $absensiId = $this->createAbsensiRecord($data);

            // Create detail records for each student
            $this->createAbsensiDetails($absensiId, $data['siswa_list']);

            // Update statistics
            $this->updateAbsensiStatistics($absensiId);

            $this->log('info', "Absensi created: ID {$absensiId}");

            return [
                'absensi_id' => $absensiId,
                'total_siswa' => count($data['siswa_list'])
            ];
        });
    }

    /**
     * Validate absensi data
     */
    protected function validateAbsensiData(array $data): bool
    {
        $rules = [
            'jadwal_id' => 'required|integer',
            'tanggal' => 'required|valid_date',
            'materi' => 'required',
        ];

        return $this->validate($data, $rules);
    }

    /**
     * Validate absensi details
     */
    protected function validateAbsensiDetails(array $siswaList): bool
    {
        if (empty($siswaList)) {
            $this->addError('siswa_list', 'Minimal ada 1 siswa');
            return false;
        }

        foreach ($siswaList as $siswa) {
            if (empty($siswa['siswa_id']) || empty($siswa['status'])) {
                $this->addError('siswa_list', 'Data siswa tidak lengkap');
                return false;
            }
        }

        return true;
    }

    /**
     * Create main absensi record
     */
    protected function createAbsensiRecord(array $data): int
    {
        $absensiData = [
            'jadwal_id' => $data['jadwal_id'],
            'tanggal' => $data['tanggal'],
            'materi' => $data['materi'],
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->absensiModel->insert($absensiData);
    }

    /**
     * Create absensi detail records
     */
    protected function createAbsensiDetails(int $absensiId, array $siswaList): void
    {
        foreach ($siswaList as $siswa) {
            $detailData = [
                'absensi_id' => $absensiId,
                'siswa_id' => $siswa['siswa_id'],
                'status' => $siswa['status'],
                'keterangan' => $siswa['keterangan'] ?? null
            ];

            $this->absensiDetailModel->insert($detailData);
        }
    }

    /**
     * Update absensi statistics
     */
    protected function updateAbsensiStatistics(int $absensiId): void
    {
        $stats = $this->absensiDetailModel
            ->select('status, COUNT(*) as jumlah')
            ->where('absensi_id', $absensiId)
            ->groupBy('status')
            ->findAll();

        $statistics = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpha' => 0
        ];

        foreach ($stats as $stat) {
            $statistics[$stat['status']] = $stat['jumlah'];
        }

        $this->absensiModel->update($absensiId, $statistics);
    }
}
```

---

## Testing Services

### Unit Test Example

```php
<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\GuruService;

class GuruServiceTest extends CIUnitTestCase
{
    protected $guruService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->guruService = new GuruService();
    }

    public function testGetAllGuruReturnsProperStructure()
    {
        $result = $this->guruService->getAllGuru();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('data', $result);
    }

    public function testCreateGuruValidationFailsWithEmptyData()
    {
        $result = $this->guruService->createGuru([]);
        
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('errors', $result);
        $this->assertNotEmpty($result['errors']);
    }

    public function testCreateGuruSuccessWithValidData()
    {
        $data = [
            'nip' => '999999999',
            'nama_lengkap' => 'Test Guru',
            'jenis_kelamin' => 'L',
            'username' => 'test_guru_999',
            'password' => 'password123',
            'email' => 'test@example.com',
            'role' => 'guru_mapel'
        ];

        $result = $this->guruService->createGuru($data);
        
        if ($result['success']) {
            $this->assertArrayHasKey('data', $result);
            $this->assertArrayHasKey('guru_id', $result['data']);
            
            // Cleanup
            $this->guruService->deleteGuru($result['data']['guru_id']);
        }
    }
}
```

---

## Migration Checklist

When migrating a controller to use services:

- [ ] Create service class extending `BaseService`
- [ ] Extract business logic from controller methods
- [ ] Implement validation in service
- [ ] Use `executeInTransaction()` for database operations
- [ ] Add logging for important operations
- [ ] Return consistent response format
- [ ] Update controller to use service
- [ ] Test CRUD operations manually
- [ ] Create unit tests
- [ ] Update documentation

---

## Conclusion

The Service Layer pattern provides:

✅ **Clean Code**: Separation of concerns  
✅ **Reusability**: Use services anywhere  
✅ **Testability**: Easy to unit test  
✅ **Maintainability**: Single source of truth  
✅ **Consistency**: Standard patterns  
✅ **Safety**: Automatic transaction management  

By following this guide, you can create well-structured, maintainable services that will make the SIMACCA codebase easier to work with and extend.

---

## Next Steps

1. **Review existing services**: `GuruService` is the reference implementation
2. **Create new services**: Follow the patterns documented here
3. **Migrate controllers**: Gradually move business logic to services
4. **Write tests**: Ensure services work correctly
5. **Document**: Keep this guide updated as patterns evolve

Happy coding! 🚀
