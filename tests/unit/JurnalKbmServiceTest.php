<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\JurnalKbmService;

/**
 * JurnalKbmService Test
 * 
 * Basic tests to verify JurnalKbmService functionality
 * Tests cover: CRUD operations, validation, filtering, statistics
 */
class JurnalKbmServiceTest extends CIUnitTestCase
{
    protected $jurnalKbmService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->jurnalKbmService = new JurnalKbmService();
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
        $this->assertInstanceOf(JurnalKbmService::class, $this->jurnalKbmService);
    }

    /**
     * Test getAllJurnal returns proper structure
     */
    public function testGetAllJurnalReturnsProperStructure()
    {
        $result = $this->jurnalKbmService->getAllJurnal();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        
        if ($result['success']) {
            $this->assertArrayHasKey('data', $result);
            $this->assertArrayHasKey('jurnal', $result['data']);
            $this->assertArrayHasKey('pager', $result['data']);
        }
    }

    /**
     * Test getJurnalById with invalid ID returns error
     */
    public function testGetJurnalByIdWithInvalidIdReturnsError()
    {
        $result = $this->jurnalKbmService->getJurnalById(999999);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }

    /**
     * Test createJurnal validation fails with empty data
     */
    public function testCreateJurnalValidationFailsWithEmptyData()
    {
        $result = $this->jurnalKbmService->createJurnal([]);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('errors', $result);
    }

    /**
     * Test createJurnal validation fails with incomplete data
     */
    public function testCreateJurnalValidationFailsWithIncompleteData()
    {
        $data = [
            'absensi_id' => 1,
            // Missing required field: kegiatan_pembelajaran
        ];
        
        $result = $this->jurnalKbmService->createJurnal($data);
        
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('message', $result);
    }

    /**
     * Test getJurnalByAbsensi returns proper structure
     */
    public function testGetJurnalByAbsensiReturnsProperStructure()
    {
        $result = $this->jurnalKbmService->getJurnalByAbsensi(1);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
    }

    /**
     * Test getJurnalByGuru returns proper structure
     */
    public function testGetJurnalByGuruReturnsProperStructure()
    {
        $result = $this->jurnalKbmService->getJurnalByGuru(1);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('data', $result);
    }

    /**
     * Test getJurnalByGuruWithDateRange returns proper structure
     */
    public function testGetJurnalByGuruWithDateRangeReturnsProperStructure()
    {
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-d');
        
        $result = $this->jurnalKbmService->getJurnalByGuru(1, $startDate, $endDate);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('data', $result);
    }

    /**
     * Test getJurnalByGuruAndKelas returns proper structure
     */
    public function testGetJurnalByGuruAndKelasReturnsProperStructure()
    {
        $result = $this->jurnalKbmService->getJurnalByGuruAndKelas(1, 1);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('data', $result);
    }

    /**
     * Test getJurnalByKelas returns proper structure
     */
    public function testGetJurnalByKelasReturnsProperStructure()
    {
        $result = $this->jurnalKbmService->getJurnalByKelas(1);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('data', $result);
    }

    /**
     * Test getJurnalByKelasWithDateRange returns proper structure
     */
    public function testGetJurnalByKelasWithDateRangeReturnsProperStructure()
    {
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-d');
        
        $result = $this->jurnalKbmService->getJurnalByKelas(1, $startDate, $endDate);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('data', $result);
    }

    /**
     * Test updateJurnal with invalid ID returns error
     */
    public function testUpdateJurnalWithInvalidIdReturnsError()
    {
        $data = [
            'kegiatan_pembelajaran' => 'Updated activity',
            'tujuan_pembelajaran' => 'Updated goal'
        ];
        
        $result = $this->jurnalKbmService->updateJurnal(999999, $data);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }

    /**
     * Test updateJurnal validation fails with empty data
     */
    public function testUpdateJurnalValidationFailsWithEmptyData()
    {
        $result = $this->jurnalKbmService->updateJurnal(1, []);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }

    /**
     * Test deleteJurnal with invalid ID returns error
     */
    public function testDeleteJurnalWithInvalidIdReturnsError()
    {
        $result = $this->jurnalKbmService->deleteJurnal(999999);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }

    /**
     * Test isJurnalExist returns boolean
     */
    public function testIsJurnalExistReturnsBool()
    {
        $result = $this->jurnalKbmService->isJurnalExist(1);
        
        $this->assertIsBool($result);
    }

    /**
     * Test getJurnalStatistics returns proper structure
     */
    public function testGetJurnalStatisticsReturnsProperStructure()
    {
        $result = $this->jurnalKbmService->getJurnalStatistics();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        
        if ($result['success']) {
            $this->assertArrayHasKey('data', $result);
            $this->assertIsArray($result['data']);
        }
    }

    /**
     * Test uploadFotoDokumentasi with invalid ID returns error
     */
    public function testUploadFotoDokumentasiWithInvalidIdReturnsError()
    {
        // Create a mock file object
        $mockFile = $this->createMock(\CodeIgniter\HTTP\Files\UploadedFile::class);
        
        $result = $this->jurnalKbmService->uploadFotoDokumentasi(999999, $mockFile);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }

    /**
     * Test deleteFotoDokumentasi with invalid ID returns error
     */
    public function testDeleteFotoDokumentasiWithInvalidIdReturnsError()
    {
        $result = $this->jurnalKbmService->deleteFotoDokumentasi(999999);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }

    /**
     * Test service has required methods
     */
    public function testServiceHasRequiredMethods()
    {
        $this->assertTrue(method_exists($this->jurnalKbmService, 'getAllJurnal'));
        $this->assertTrue(method_exists($this->jurnalKbmService, 'getJurnalById'));
        $this->assertTrue(method_exists($this->jurnalKbmService, 'createJurnal'));
        $this->assertTrue(method_exists($this->jurnalKbmService, 'updateJurnal'));
        $this->assertTrue(method_exists($this->jurnalKbmService, 'deleteJurnal'));
        $this->assertTrue(method_exists($this->jurnalKbmService, 'uploadFotoDokumentasi'));
    }

    /**
     * Test createJurnal with invalid absensi_id returns error
     */
    public function testCreateJurnalWithInvalidAbsensiIdReturnsError()
    {
        $data = [
            'absensi_id' => 999999,
            'kegiatan_pembelajaran' => 'Test activity',
            'tujuan_pembelajaran' => 'Test goal'
        ];
        
        $result = $this->jurnalKbmService->createJurnal($data);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }
}
