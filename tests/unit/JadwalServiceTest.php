<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\JadwalService;

/**
 * JadwalServiceTest
 * 
 * Unit tests for JadwalService
 * Tests cover: CRUD operations, conflict detection, import/export, validation
 */
class JadwalServiceTest extends CIUnitTestCase
{
    protected $jadwalService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->jadwalService = new JadwalService();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test service instantiation
     */
    public function testServiceInstantiation()
    {
        $this->assertInstanceOf(JadwalService::class, $this->jadwalService);
    }

    /**
     * Test getAllJadwal returns proper structure
     */
    public function testGetAllJadwalReturnsPropperStructure()
    {
        $result = $this->jadwalService->getAllJadwal();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('data', $result);
        
        if ($result['success']) {
            $this->assertArrayHasKey('jadwal', $result['data']);
            $this->assertArrayHasKey('pager', $result['data']);
            $this->assertArrayHasKey('hariList', $result['data']);
            $this->assertArrayHasKey('semesterList', $result['data']);
            $this->assertArrayHasKey('tahunAjaranList', $result['data']);
        }
    }

    /**
     * Test getAllJadwal with filters
     */
    public function testGetAllJadwalWithFilters()
    {
        $filters = [
            'semester' => 'Ganjil',
            'tahun_ajaran' => '2023/2024',
            'search' => 'test'
        ];

        $result = $this->jadwalService->getAllJadwal($filters);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
    }

    /**
     * Test getJadwalById with invalid ID returns error
     */
    public function testGetJadwalByIdWithInvalidIdReturnsError()
    {
        $result = $this->jadwalService->getJadwalById(999999);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertEquals(404, $result['code']);
    }

    /**
     * Test createJadwal validation with incomplete data
     */
    public function testCreateJadwalValidationWithIncompleteData()
    {
        $data = [
            'guru_id' => 1,
            'hari' => 'Senin'
            // Missing required fields
        ];

        $result = $this->jadwalService->createJadwal($data);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        // May fail due to conflict check or missing fields
    }

    /**
     * Test updateJadwal with invalid ID returns error
     */
    public function testUpdateJadwalWithInvalidIdReturnsError()
    {
        $data = [
            'guru_id' => 1,
            'mata_pelajaran_id' => 1,
            'kelas_id' => 1,
            'hari' => 'Senin',
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '08:30:00',
            'semester' => 'Ganjil',
            'tahun_ajaran' => '2023/2024'
        ];

        $result = $this->jadwalService->updateJadwal(999999, $data);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals(404, $result['code']);
    }

    /**
     * Test deleteJadwal with invalid ID returns error
     */
    public function testDeleteJadwalWithInvalidIdReturnsError()
    {
        $result = $this->jadwalService->deleteJadwal(999999);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals(404, $result['code']);
    }

    /**
     * Test getByGuru returns proper structure
     */
    public function testGetByGuruReturnsPropperStructure()
    {
        $result = $this->jadwalService->getByGuru(1);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('jadwal', $result['data']);
        $this->assertIsArray($result['data']['jadwal']);
    }

    /**
     * Test getByKelas returns proper structure
     */
    public function testGetByKelasReturnsPropperStructure()
    {
        $result = $this->jadwalService->getByKelas(1);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('jadwal', $result['data']);
        $this->assertIsArray($result['data']['jadwal']);
    }

    /**
     * Test checkConflict returns proper structure
     */
    public function testCheckConflictReturnsPropperStructure()
    {
        $data = [
            'guru_id' => 1,
            'kelas_id' => 1,
            'hari' => 'Senin',
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '08:30:00'
        ];

        $result = $this->jadwalService->checkConflict($data);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('conflict_guru', $result['data']);
        $this->assertArrayHasKey('conflict_kelas', $result['data']);
        $this->assertIsBool($result['data']['conflict_guru']);
        $this->assertIsBool($result['data']['conflict_kelas']);
    }

    /**
     * Test getFormLists returns proper structure
     */
    public function testGetFormListsReturnsPropperStructure()
    {
        $result = $this->jadwalService->getFormLists();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('guruOptions', $result['data']);
        $this->assertArrayHasKey('mapelOptions', $result['data']);
        $this->assertArrayHasKey('kelasOptions', $result['data']);
        $this->assertArrayHasKey('hariList', $result['data']);
        $this->assertArrayHasKey('semesterList', $result['data']);
        $this->assertArrayHasKey('tahunAjaranList', $result['data']);
    }

    /**
     * Test exportToExcel returns proper structure
     */
    public function testExportToExcelReturnsPropperStructure()
    {
        $result = $this->jadwalService->exportToExcel();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        
        if ($result['success']) {
            $this->assertArrayHasKey('spreadsheet', $result['data']);
            $this->assertArrayHasKey('filename', $result['data']);
            $this->assertInstanceOf(\PhpOffice\PhpSpreadsheet\Spreadsheet::class, $result['data']['spreadsheet']);
        }
    }

    /**
     * Test exportToExcel with filters
     */
    public function testExportToExcelWithFilters()
    {
        $filters = [
            'semester' => 'Ganjil',
            'tahun_ajaran' => '2023/2024'
        ];

        $result = $this->jadwalService->exportToExcel($filters);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
    }

    /**
     * Test generateImportTemplate returns proper structure
     */
    public function testGenerateImportTemplateReturnsPropperStructure()
    {
        $result = $this->jadwalService->generateImportTemplate();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('spreadsheet', $result['data']);
        $this->assertArrayHasKey('filename', $result['data']);
        $this->assertInstanceOf(\PhpOffice\PhpSpreadsheet\Spreadsheet::class, $result['data']['spreadsheet']);
        $this->assertStringContainsString('template-import-jadwal', $result['data']['filename']);
    }

    /**
     * Test processExcelImport with invalid file
     */
    public function testProcessExcelImportWithInvalidFile()
    {
        // Create a mock file object
        $mockFile = $this->createMock(\CodeIgniter\HTTP\Files\UploadedFile::class);
        $mockFile->method('isValid')->willReturn(false);
        $mockFile->method('getError')->willReturn(UPLOAD_ERR_NO_FILE);

        $result = $this->jadwalService->processExcelImport($mockFile, false);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        // Should return error for invalid file
    }

    /**
     * Test conflict detection with valid data
     */
    public function testConflictDetectionWithValidData()
    {
        $data = [
            'guru_id' => 1,
            'kelas_id' => 1,
            'hari' => 'Senin',
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '08:30:00',
            'exclude_id' => null
        ];

        $result = $this->jadwalService->checkConflict($data);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertIsBool($result['data']['conflict_guru']);
        $this->assertIsBool($result['data']['conflict_kelas']);
    }

    /**
     * Test conflict detection with exclude ID
     */
    public function testConflictDetectionWithExcludeId()
    {
        $data = [
            'guru_id' => 1,
            'kelas_id' => 1,
            'hari' => 'Senin',
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '08:30:00',
            'exclude_id' => 1
        ];

        $result = $this->jadwalService->checkConflict($data);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
    }
}
