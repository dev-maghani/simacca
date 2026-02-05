# Services Directory

This directory contains the **Service Layer** for SIMACCA - business logic separated from controllers.

## 📁 Current Services

### ✅ BaseService
**File**: `BaseService.php`  
**Purpose**: Abstract base class providing common functionality for all services

**Features**:
- Transaction management (begin, commit, rollback)
- Error handling and validation
- Response formatting (success/error)
- Logging helpers
- Database connection management

**Usage**: All services extend this class

---

### ✅ GuruService
**File**: `GuruService.php`  
**Purpose**: Handles all business logic for teacher (Guru) management

**Methods**:
- `getAllGuru()` - Get all teachers with statistics
- `getGuruById($id)` - Get teacher details by ID
- `getStatistics()` - Get summary statistics
- `createGuru($data)` - Create new teacher
- `updateGuru($id, $data)` - Update teacher data
- `deleteGuru($id)` - Delete teacher
- `changeStatus($id)` - Toggle active/inactive
- `checkNipAvailability($nip, $excludeId)` - Check NIP uniqueness
- `checkUsernameAvailability($username, $excludeUserId)` - Check username uniqueness
- `getFormLists()` - Get dropdown data for forms

**Models Used**:
- `UserModel`
- `GuruModel`
- `MataPelajaranModel`
- `KelasModel`

**Controller**: `app/Controllers/Admin/GuruController.php`

---

## 🚀 Planned Services

### 🔜 AbsensiService (Next Priority)
**Purpose**: Attendance management business logic  
**Status**: Planned  
**Complexity**: High (1100+ lines in current controller)

### 🔜 SiswaService
**Purpose**: Student management business logic  
**Status**: Planned

### 🔜 JadwalService
**Purpose**: Schedule management business logic  
**Status**: Planned

### 🔜 KelasService
**Purpose**: Class management business logic  
**Status**: Planned

### 🔜 ImportExportService
**Purpose**: Shared import/export functionality  
**Status**: Planned

### 🔜 LaporanService
**Purpose**: Report generation business logic  
**Status**: Planned

### 🔜 JurnalService
**Purpose**: Journal (Jurnal KBM) management  
**Status**: Planned

### 🔜 IzinService
**Purpose**: Permission/leave management  
**Status**: Planned

---

## 📖 Documentation

### Quick Start
See: [Service Layer Quick Reference](../../docs/guides/SERVICE_LAYER_QUICK_REFERENCE.md)

### Complete Guide
See: [Service Layer Pattern Guide](../../docs/guides/SERVICE_LAYER_PATTERN_GUIDE.md)

### Implementation Details
See: [Service Layer Implementation Guide](../../docs/guides/SERVICE_LAYER_IMPLEMENTATION_GUIDE.md)

---

## 🎯 Creating a New Service

### 1. Create the Service File

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

    /**
     * Your method description
     * 
     * @param array $data
     * @return array
     */
    public function yourMethod(array $data): array
    {
        // Validate
        $rules = ['field' => 'required'];
        if (!$this->validate($data, $rules)) {
            return $this->errorResponse('Validation failed');
        }

        // Execute in transaction
        return $this->executeInTransaction(function () use ($data) {
            $result = $this->yourModel->insert($data);
            $this->log('info', 'Operation completed');
            return ['id' => $result];
        });
    }
}
```

### 2. Use in Controller

```php
<?php

namespace App\Controllers;

use App\Services\YourService;

class YourController extends BaseController
{
    protected $yourService;

    public function __construct()
    {
        $this->yourService = new YourService();
    }

    public function store()
    {
        $data = $this->request->getPost();
        $result = $this->yourService->yourMethod($data);

        if (!$result['success']) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $result['errors']);
        }

        session()->setFlashdata('success', 'Success!');
        return redirect()->to('/path');
    }
}
```

### 3. Create Tests

```php
<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\YourService;

class YourServiceTest extends CIUnitTestCase
{
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new YourService();
    }

    public function testServiceCanBeInstantiated()
    {
        $this->assertInstanceOf(YourService::class, $this->service);
    }

    public function testMethodReturnsProperStructure()
    {
        $result = $this->service->yourMethod($data);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
    }
}
```

---

## 📊 Response Format

All service methods return a consistent array format:

### Success Response
```php
[
    'success' => true,
    'message' => 'Operation successful',
    'data' => [
        // Your data here
    ]
]
```

### Error Response
```php
[
    'success' => false,
    'message' => 'Operation failed',
    'errors' => [
        'field1' => 'Error message 1',
        'field2' => 'Error message 2'
    ]
]
```

---

## ✅ Best Practices

1. **Always extend BaseService**
   ```php
   class YourService extends BaseService
   ```

2. **Call parent constructor**
   ```php
   public function __construct()
   {
       parent::__construct();
       // Your initialization
   }
   ```

3. **Use type declarations**
   ```php
   public function method(array $data): array
   ```

4. **Validate early**
   ```php
   if (!$this->validate($data, $rules)) {
       return $this->errorResponse('Validation failed');
   }
   ```

5. **Use transaction wrapper**
   ```php
   return $this->executeInTransaction(function () use ($data) {
       // Database operations
       return $result;
   });
   ```

6. **Log important operations**
   ```php
   $this->log('info', 'Operation completed');
   ```

7. **Return consistent format**
   ```php
   return $this->successResponse($data);
   return $this->errorResponse('Error message');
   ```

8. **Document public methods**
   ```php
   /**
    * Method description
    * 
    * @param array $data Input data
    * @return array Response array
    */
   ```

---

## 🔄 Migration Status

| Service | Status | Controller Lines | Reduction | Notes |
|---------|--------|------------------|-----------|-------|
| GuruService | ✅ Complete | 801 → 531 | 33.7% | Reference implementation |
| AbsensiService | 🔜 Next | ~1100 | TBD | High priority |
| SiswaService | 📋 Planned | TBD | TBD | |
| JadwalService | 📋 Planned | TBD | TBD | |
| KelasService | 📋 Planned | TBD | TBD | |
| LaporanService | 📋 Planned | TBD | TBD | |
| JurnalService | 📋 Planned | TBD | TBD | |
| IzinService | 📋 Planned | TBD | TBD | |

---

## 🧪 Testing

Run service tests:

```bash
# All tests
./vendor/bin/phpunit

# Specific service
./vendor/bin/phpunit --filter GuruServiceTest

# With coverage
./vendor/bin/phpunit --coverage-html coverage
```

---

## 📞 Need Help?

- Check [Quick Reference](../../docs/guides/SERVICE_LAYER_QUICK_REFERENCE.md) for common patterns
- Read [Pattern Guide](../../docs/guides/SERVICE_LAYER_PATTERN_GUIDE.md) for detailed explanations
- Review `GuruService.php` for a complete example
- Review `GuruController.php` to see how it's used

---

**Last Updated**: 2026-02-05  
**Version**: 1.0.0  
**Status**: Phase 1 Complete
