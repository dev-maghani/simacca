<?php

namespace App\Services;

use CodeIgniter\Database\ConnectionInterface;

/**
 * Base Service Class
 * 
 * Provides common functionality for all services:
 * - Database transaction management
 * - Validation handling
 * - Error handling and logging
 * - Response formatting
 */
abstract class BaseService
{
    /**
     * Database connection
     * 
     * @var ConnectionInterface
     */
    protected $db;

    /**
     * Validation errors
     * 
     * @var array
     */
    protected $errors = [];

    /**
     * Logger instance
     * 
     * @var \CodeIgniter\Log\Logger
     */
    protected $logger;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->logger = service('logger');
    }

    /**
     * Begin database transaction
     * 
     * @return void
     */
    protected function beginTransaction(): void
    {
        $this->db->transStart();
    }

    /**
     * Commit database transaction
     * 
     * @return bool
     */
    protected function commitTransaction(): bool
    {
        $this->db->transComplete();
        return $this->db->transStatus();
    }

    /**
     * Rollback database transaction
     * 
     * @return void
     */
    protected function rollbackTransaction(): void
    {
        $this->db->transRollback();
    }

    /**
     * Add validation error
     * 
     * @param string $field
     * @param string $message
     * @return void
     */
    protected function addError(string $field, string $message): void
    {
        $this->errors[$field] = $message;
    }

    /**
     * Get all validation errors
     * 
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Check if service has errors
     * 
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Clear all errors
     * 
     * @return void
     */
    protected function clearErrors(): void
    {
        $this->errors = [];
    }

    /**
     * Log message
     * 
     * @param string $level
     * @param string $message
     * @param array $context
     * @return void
     */
    protected function log(string $level, string $message, array $context = []): void
    {
        $this->logger->log($level, $message, $context);
    }

    /**
     * Create success response
     * 
     * @param mixed $data
     * @param string $message
     * @return array
     */
    protected function successResponse($data = null, string $message = 'Success'): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
    }

    /**
     * Create error response
     * 
     * @param string $message
     * @param array $errors
     * @return array
     */
    protected function errorResponse(string $message, array $errors = []): array
    {
        return [
            'success' => false,
            'message' => $message,
            'errors' => !empty($errors) ? $errors : $this->errors
        ];
    }

    /**
     * Validate data using CodeIgniter validation
     * 
     * @param array $data
     * @param array $rules
     * @return bool
     */
    protected function validate(array $data, array $rules): bool
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules($rules);
        
        if (!$validation->run($data)) {
            $this->errors = $validation->getErrors();
            return false;
        }
        
        return true;
    }

    /**
     * Execute callback within transaction
     * 
     * @param callable $callback
     * @return array
     */
    protected function executeInTransaction(callable $callback): array
    {
        $this->clearErrors();
        $this->beginTransaction();

        try {
            $result = $callback();
            
            if (!$this->commitTransaction()) {
                throw new \Exception('Transaction failed to complete');
            }

            return $this->successResponse($result);
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            $this->log('error', $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse($e->getMessage());
        }
    }
}
