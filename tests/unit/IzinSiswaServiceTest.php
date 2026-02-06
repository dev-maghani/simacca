<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\IzinSiswaService;

/**
 * IzinSiswaService Test
 * 
 * Basic tests to verify IzinSiswaService functionality
 * Tests cover: CRUD operations, approval/rejection workflow, validation, statistics
 */
class IzinSiswaServiceTest extends CIUnitTestCase
{
    protected $izinSiswaService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->izinSiswaService = new IzinSiswaService();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test service instantiation
     */
    public function testServiceCanBeInstantiated()
    {
        $this->assertInstanceOf(IzinSiswaService::class, $this->izinSiswaService);
    }

    /**
     * Test getAllIzin returns proper structure
     */
    public function testGetAllIzinReturnsProperStructure()
    {
        $result = $this->izinSiswaService->getAllIzin();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        
        if ($result['success']) {
            $this->assertArrayHasKey('data', $result);
            $this->assertArrayHasKey('izin', $result['data']);
            $this->assertArrayHasKey('pager', $result['data']);
        }
    }

    /**
     * Test getIzinById with invalid ID returns error
     */
    public function testGetIzinByIdWithInvalidIdReturnsError()
    {
        $result = $this->izinSiswaService->getIzinById(999999);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }

    /**
     * Test createIzin validation fails with empty data
     */
    public function testCreateIzinValidationFailsWithEmptyData()
    {
        $result = $this->izinSiswaService->createIzin([]);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('errors', $result);
    }

    /**
     * Test createIzin validation fails with incomplete data
     */
    public function testCreateIzinValidationFailsWithIncompleteData()
    {
        $data = [
            'siswa_id' => 1,
            // Missing required fields: tanggal, jenis_izin, alasan
        ];
        
        $result = $this->izinSiswaService->createIzin($data);
        
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('message', $result);
    }

    /**
     * Test createIzin validation fails with invalid jenis_izin
     */
    public function testCreateIzinValidationFailsWithInvalidJenisIzin()
    {
        $data = [
            'siswa_id' => 1,
            'tanggal' => date('Y-m-d'),
            'jenis_izin' => 'invalid_type',
            'alasan' => 'Test reason'
        ];
        
        $result = $this->izinSiswaService->createIzin($data);
        
        $this->assertFalse($result['success']);
    }

    /**
     * Test getIzinBySiswa returns proper structure
     */
    public function testGetIzinBySiswaReturnsProperStructure()
    {
        $result = $this->izinSiswaService->getIzinBySiswa(1);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('data', $result);
    }

    /**
     * Test getIzinByKelas returns proper structure
     */
    public function testGetIzinByKelasReturnsProperStructure()
    {
        $result = $this->izinSiswaService->getIzinByKelas(1);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('data', $result);
    }

    /**
     * Test getPendingApproval returns proper structure
     */
    public function testGetPendingApprovalReturnsProperStructure()
    {
        $result = $this->izinSiswaService->getPendingApproval(1);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('data', $result);
    }

    /**
     * Test getIzinStatistics returns proper structure
     */
    public function testGetIzinStatisticsReturnsProperStructure()
    {
        $result = $this->izinSiswaService->getIzinStatistics();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
    }

    /**
     * Test approveIzin with invalid ID returns error
     */
    public function testApproveIzinWithInvalidIdReturnsError()
    {
        $result = $this->izinSiswaService->approveIzin(999999, 1, 'Test approval');
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }

    /**
     * Test rejectIzin with invalid ID returns error
     */
    public function testRejectIzinWithInvalidIdReturnsError()
    {
        $result = $this->izinSiswaService->rejectIzin(999999, 1, 'Test rejection');
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }

    /**
     * Test deleteIzin with invalid ID returns error
     */
    public function testDeleteIzinWithInvalidIdReturnsError()
    {
        $result = $this->izinSiswaService->deleteIzin(999999);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }

    /**
     * Test uploadBerkas with invalid ID returns error
     */
    public function testUploadBerkasWithInvalidIdReturnsError()
    {
        // Create a mock file object
        $mockFile = $this->createMock(\CodeIgniter\HTTP\Files\UploadedFile::class);
        
        $result = $this->izinSiswaService->uploadBerkas(999999, $mockFile);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }

    /**
     * Test isJurnalExist method (inherited check)
     */
    public function testServiceHasRequiredMethods()
    {
        $this->assertTrue(method_exists($this->izinSiswaService, 'getAllIzin'));
        $this->assertTrue(method_exists($this->izinSiswaService, 'getIzinById'));
        $this->assertTrue(method_exists($this->izinSiswaService, 'createIzin'));
        $this->assertTrue(method_exists($this->izinSiswaService, 'updateIzin'));
        $this->assertTrue(method_exists($this->izinSiswaService, 'deleteIzin'));
        $this->assertTrue(method_exists($this->izinSiswaService, 'approveIzin'));
        $this->assertTrue(method_exists($this->izinSiswaService, 'rejectIzin'));
    }

    /**
     * Test getApprovedIzinByDate returns proper structure
     */
    public function testGetApprovedIzinByDateReturnsProperStructure()
    {
        $result = $this->izinSiswaService->getApprovedIzinByDate(date('Y-m-d'), 1);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('data', $result);
    }

    /**
     * Test updateIzin validation fails with empty data
     */
    public function testUpdateIzinValidationFailsWithEmptyData()
    {
        $result = $this->izinSiswaService->updateIzin(1, []);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }

    /**
     * Test updateIzin with invalid ID returns error
     */
    public function testUpdateIzinWithInvalidIdReturnsError()
    {
        $data = [
            'tanggal' => date('Y-m-d'),
            'jenis_izin' => 'sakit',
            'alasan' => 'Updated reason'
        ];
        
        $result = $this->izinSiswaService->updateIzin(999999, $data);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }
}
