# 🚀 Service Layer Quick Reference - SIMACCA

A quick cheat sheet for working with the Service Layer pattern.

---

## 📋 Quick Start

### 1. Create a New Service

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

    public function methodName(array $data): array
    {
        // Validate
        if (!$this->validate($data, $rules)) {
            return $this->errorResponse('Validation failed');
        }

        // Execute in transaction
        return $this->executeInTransaction(function () use ($data) {
            // Business logic here
            $result = $this->yourModel->insert($data);
            $this->log('info', 'Operation completed');
            return ['id' => $result];
        });
    }
}
```

### 2. Use in Controller

```php
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
        $result = $this->yourService->methodName($data);

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

---

## 🔧 BaseService Methods

### Transaction Management
```php
$this->beginTransaction();              // Start transaction
$this->commitTransaction();             // Commit transaction
$this->rollbackTransaction();           // Rollback transaction

// Recommended: Use wrapper
$this->executeInTransaction(function () {
    // Your code here
    return $data;
});
```

### Validation
```php
$rules = ['field' => 'required|min_length[3]'];
if (!$this->validate($data, $rules)) {
    return $this->errorResponse('Validation failed');
}
```

### Response Formatting
```php
// Success
return $this->successResponse($data, 'Operation successful');

// Error
return $this->errorResponse('Operation failed', $errors);
```

### Error Handling
```php
$this->addError('field', 'Error message');
$errors = $this->getErrors();
$hasErrors = $this->hasErrors();
$this->clearErrors();
```

### Logging
```php
$this->log('info', 'Message');
$this->log('warning', 'Warning message');
$this->log('error', 'Error message', ['context' => 'data']);
```

---

## 📤 Response Format

### Success Response
```php
[
    'success' => true,
    'message' => 'Operation successful',
    'data' => [
        'id' => 123,
        'name' => 'John Doe'
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
        'field2' => 'Field2 must be email'
    ]
]
```

---

## 🎯 Common Patterns

### Simple CRUD

```php
// Create
public function create(array $data): array
{
    if (!$this->validate($data, $this->rules())) {
        return $this->errorResponse('Validation failed');
    }
    
    return $this->executeInTransaction(function () use ($data) {
        $id = $this->model->insert($data);
        return ['id' => $id];
    });
}

// Read
public function getById(int $id): array
{
    $data = $this->model->find($id);
    
    if (!$data) {
        return $this->errorResponse('Not found');
    }
    
    return $this->successResponse($data);
}

// Update
public function update(int $id, array $data): array
{
    if (!$this->model->find($id)) {
        return $this->errorResponse('Not found');
    }
    
    if (!$this->validate($data, $this->rules($id))) {
        return $this->errorResponse('Validation failed');
    }
    
    return $this->executeInTransaction(function () use ($id, $data) {
        $this->model->update($id, $data);
        return ['id' => $id];
    });
}

// Delete
public function delete(int $id): array
{
    if (!$this->model->find($id)) {
        return $this->errorResponse('Not found');
    }
    
    return $this->executeInTransaction(function () use ($id) {
        $this->model->delete($id);
        return ['id' => $id];
    });
}
```

### Complex Operation with Multiple Models

```php
public function complexOperation(array $data): array
{
    if (!$this->validate($data, $this->rules())) {
        return $this->errorResponse('Validation failed');
    }
    
    return $this->executeInTransaction(function () use ($data) {
        // Step 1: Create main record
        $mainId = $this->createMainRecord($data);
        
        // Step 2: Create related records
        $this->createRelatedRecords($mainId, $data['items']);
        
        // Step 3: Update statistics
        $this->updateStatistics($mainId);
        
        // Step 4: Send notification
        $this->sendNotification($data);
        
        $this->log('info', "Operation completed: ID {$mainId}");
        
        return ['id' => $mainId];
    });
}
```

### Checking Availability

```php
public function checkAvailability(string $value, ?int $excludeId = null): array
{
    try {
        $query = $this->model->where('field', $value);
        
        if ($excludeId) {
            $query->where('id !=', $excludeId);
        }
        
        $exists = $query->countAllResults() > 0;
        
        return $this->successResponse([
            'available' => !$exists,
            'message' => $exists ? 'Already taken' : 'Available'
        ]);
    } catch (\Exception $e) {
        $this->log('error', $e->getMessage());
        return $this->errorResponse('Check failed');
    }
}
```

---

## 🎨 Controller Patterns

### List Page
```php
public function index()
{
    $result = $this->service->getAll();
    
    $data = [
        'items' => $result['data'] ?? [],
        'user' => $this->getUserData()
    ];
    
    return view('page/index', $data);
}
```

### Create Form
```php
public function create()
{
    $listsResult = $this->service->getFormLists();
    
    $data = [
        'lists' => $listsResult['data'] ?? [],
        'user' => $this->getUserData()
    ];
    
    return view('page/create', $data);
}
```

### Store
```php
public function store()
{
    $data = $this->request->getPost();
    $result = $this->service->create($data);
    
    if (!$result['success']) {
        return redirect()->back()
            ->withInput()
            ->with('errors', $result['errors']);
    }
    
    session()->setFlashdata('success', 'Created successfully');
    return redirect()->to('/page');
}
```

### Edit Form
```php
public function edit($id)
{
    $itemResult = $this->service->getById($id);
    
    if (!$itemResult['success']) {
        session()->setFlashdata('error', 'Not found');
        return redirect()->to('/page');
    }
    
    $listsResult = $this->service->getFormLists();
    
    $data = [
        'item' => $itemResult['data'],
        'lists' => $listsResult['data'] ?? [],
        'user' => $this->getUserData()
    ];
    
    return view('page/edit', $data);
}
```

### Update
```php
public function update($id)
{
    $data = $this->request->getPost();
    $result = $this->service->update($id, $data);
    
    if (!$result['success']) {
        session()->setFlashdata('error', $result['message']);
        return redirect()->back()
            ->withInput()
            ->with('errors', $result['errors']);
    }
    
    session()->setFlashdata('success', 'Updated successfully');
    return redirect()->to('/page');
}
```

### Delete
```php
public function delete($id)
{
    $result = $this->service->delete($id);
    
    if (!$result['success']) {
        session()->setFlashdata('error', $result['message']);
    } else {
        session()->setFlashdata('success', 'Deleted successfully');
    }
    
    return redirect()->to('/page');
}
```

### AJAX Request
```php
public function checkField()
{
    if (!$this->request->isAJAX()) {
        return redirect()->to('/page');
    }
    
    $value = $this->request->getPost('value');
    $result = $this->service->checkAvailability($value);
    
    return $this->response->setJSON($result['data']);
}
```

---

## ✅ Best Practices Checklist

### Service Class
- [ ] Extends `BaseService`
- [ ] Constructor calls `parent::__construct()`
- [ ] Uses dependency injection for models
- [ ] All public methods return `array`
- [ ] All public methods have type declarations
- [ ] Uses `executeInTransaction()` for DB operations
- [ ] Validates input before processing
- [ ] Logs important operations
- [ ] Returns consistent response format
- [ ] Has PHPDoc comments

### Controller Class
- [ ] Injects service in constructor
- [ ] Only handles HTTP layer
- [ ] Checks service response `success` flag
- [ ] Handles errors with flashdata
- [ ] Redirects appropriately
- [ ] Passes validation errors to view
- [ ] Uses service for all business logic
- [ ] No direct model access
- [ ] No business logic in controller

---

## 🔍 Validation Rules Examples

```php
// Common validation rules
$rules = [
    'required_field'    => 'required',
    'email_field'       => 'required|valid_email',
    'numeric_field'     => 'required|numeric',
    'min_length_field'  => 'required|min_length[6]',
    'max_length_field'  => 'required|max_length[100]',
    'alpha_field'       => 'required|alpha',
    'alpha_numeric'     => 'required|alpha_numeric',
    'in_list_field'     => 'required|in_list[option1,option2,option3]',
    'unique_field'      => 'required|is_unique[table.field]',
    'unique_except'     => "required|is_unique[table.field,id,{$id}]",
    'integer_field'     => 'required|integer',
    'decimal_field'     => 'required|decimal',
    'date_field'        => 'required|valid_date',
    'url_field'         => 'required|valid_url',
    'optional_field'    => 'permit_empty|valid_email',
];
```

---

## 🧪 Testing Template

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
        $result = $this->service->methodName($data);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('message', $result);
    }

    public function testValidationFailsWithEmptyData()
    {
        $result = $this->service->methodName([]);
        
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
    }
}
```

---

## 📚 Reference Implementation

See `GuruService` for a complete reference implementation:
- Location: `app/Services/GuruService.php`
- Controller: `app/Controllers/Admin/GuruController.php`
- Tests: `tests/unit/GuruServiceTest.php`

---

## 🆘 Common Issues & Solutions

### Issue: "Call to undefined method"
**Solution**: Make sure you extended `BaseService` and called `parent::__construct()`

### Issue: "Transaction not working"
**Solution**: Use `executeInTransaction()` wrapper instead of manual transaction

### Issue: "Validation not working"
**Solution**: Check if you're using `$this->validate()` and returning error response

### Issue: "Errors not showing in view"
**Solution**: Make sure controller passes `->with('errors', $result['errors'])`

### Issue: "Service returns null"
**Solution**: Ensure all code paths return an array from service methods

---

## 📖 More Information

For detailed explanations and advanced patterns, see:
- [Service Layer Pattern Guide](SERVICE_LAYER_PATTERN_GUIDE.md)
- [Service Layer Implementation Guide](SERVICE_LAYER_IMPLEMENTATION_GUIDE.md)
- [Service Layer Refactoring Plan](../plans/SERVICE_LAYER_REFACTORING_PLAN.md)

---

**Last Updated**: 2026-02-05  
**Status**: Phase 1 Complete (GuruService implemented)
