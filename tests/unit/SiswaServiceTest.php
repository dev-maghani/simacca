<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\SiswaService;

/**
 * SiswaServiceTest
 * 
 * Unit tests for SiswaService
 * Tests cover: CRUD operations, validation, import logic, availability checks
 */
class SiswaServiceTest extends CIUnitTestCase
{
    protected $siswaService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->siswaService = new SiswaService();
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
        $this->assertInstanceOf(SiswaService::class, $this->siswaService);
    }

    /**
     * Test getAllSiswa returns proper structure
     */
    public function testGetAllSiswaReturnsPropperStructure()
    {
        $result = $this->siswaService->getAllSiswa();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('data', $result);
        
        if ($result['success']) {
            $this->assertArrayHasKey('siswa', $result['data']);
            $this->assertArrayHasKey('total', $result['data']);
            $this->assertArrayHasKey('kelasSummary', $result['data']);
            $this->assertIsArray($result['data']['siswa']);
            $this->assertIsInt($result['data']['total']);
        }
    }

    /**
     * Test getAllSiswa with search filter
     */
    public function testGetAllSiswaWithSearchFilter()
    {
        $result = $this->siswaService->getAllSiswa(['search' => 'test']);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
    }

    /**
     * Test getStatistics returns proper structure
     */
    public function testGetStatisticsReturnsPropperStructure()
    {
        $result = $this->siswaService->getStatistics();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        
        if ($result['success']) {
            $this->assertArrayHasKey('total', $result['data']);
            $this->assertArrayHasKey('active', $result['data']);
            $this->assertArrayHasKey('inactive', $result['data']);
            $this->assertArrayHasKey('byKelas', $result['data']);
        }
    }

    /**
     * Test createSiswa validation with empty data
     */
    public function testCreateSiswaValidationWithEmptyData()
    {
        $result = $this->siswaService->createSiswa([]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('message', $result);
    }

    /**
     * Test createSiswa validation with incomplete data
     */
    public function testCreateSiswaValidationWithIncompleteData()
    {
        $data = [
            'nis' => '12345',
            'nama_lengkap' => 'Test Siswa'
            // Missing required fields
        ];

        $result = $this->siswaService->createSiswa($data);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals(422, $result['code']);
    }

    /**
     * Test getSiswaById with invalid ID returns error
     */
    public function testGetSiswaByIdWithInvalidIdReturnsError()
    {
        $result = $this->siswaService->getSiswaById(999999);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertEquals(404, $result['code']);
    }

    /**
     * Test updateSiswa with invalid ID returns error
     */
    public function testUpdateSiswaWithInvalidIdReturnsError()
    {
        $result = $this->siswaService->updateSiswa(999999, [
            'nis' => '12345',
            'nama_lengkap' => 'Test',
            'jenis_kelamin' => 'L',
            'kelas_id' => 1,
            'tahun_ajaran' => '2023/2024',
            'username' => 'test',
            'email' => 'test@test.com'
        ]);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals(404, $result['code']);
    }

    /**
     * Test deleteSiswa with invalid ID returns error
     */
    public function testDeleteSiswaWithInvalidIdReturnsError()
    {
        $result = $this->siswaService->deleteSiswa(999999);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals(404, $result['code']);
    }

    /**
     * Test changeStatus with invalid ID returns error
     */
    public function testChangeStatusWithInvalidIdReturnsError()
    {
        $result = $this->siswaService->changeStatus(999999);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals(404, $result['code']);
    }

    /**
     * Test checkNisAvailability returns proper structure
     */
    public function testCheckNisAvailabilityReturnsPropperStructure()
    {
        $result = $this->siswaService->checkNisAvailability('999999');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('available', $result['data']);
        $this->assertArrayHasKey('message', $result['data']);
        $this->assertIsBool($result['data']['available']);
    }

    /**
     * Test checkUsernameAvailability returns proper structure
     */
    public function testCheckUsernameAvailabilityReturnsPropperStructure()
    {
        $result = $this->siswaService->checkUsernameAvailability('nonexistent_user_test');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('available', $result['data']);
        $this->assertArrayHasKey('message', $result['data']);
        $this->assertIsBool($result['data']['available']);
    }

    /**
     * Test getFormLists returns proper structure
     */
    public function testGetFormListsReturnsPropperStructure()
    {
        $result = $this->siswaService->getFormLists();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('kelasList', $result['data']);
        $this->assertArrayHasKey('tahunAjaranList', $result['data']);
        $this->assertIsArray($result['data']['kelasList']);
        $this->assertIsArray($result['data']['tahunAjaranList']);
    }

    /**
     * Test bulkAction with empty array returns error
     */
    public function testBulkActionWithEmptyArrayReturnsError()
    {
        $result = $this->siswaService->bulkAction('activate', []);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('tidak ada', strtolower($result['message']));
    }

    /**
     * Test bulkAction with invalid action
     */
    public function testBulkActionWithInvalidAction()
    {
        $result = $this->siswaService->bulkAction('invalid_action', [1, 2]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        // Should handle gracefully even with invalid action
    }

    /**
     * Test exportToExcel returns proper structure
     */
    public function testExportToExcelReturnsPropperStructure()
    {
        $result = $this->siswaService->exportToExcel();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        
        if ($result['success']) {
            $this->assertArrayHasKey('spreadsheet', $result['data']);
            $this->assertArrayHasKey('filename', $result['data']);
            $this->assertInstanceOf(\PhpOffice\PhpSpreadsheet\Spreadsheet::class, $result['data']['spreadsheet']);
        }
    }

    /**
     * Test generateImportTemplate returns proper structure
     */
    public function testGenerateImportTemplateReturnsPropperStructure()
    {
        $result = $this->siswaService->generateImportTemplate();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('spreadsheet', $result['data']);
        $this->assertArrayHasKey('filename', $result['data']);
        $this->assertInstanceOf(\PhpOffice\PhpSpreadsheet\Spreadsheet::class, $result['data']['spreadsheet']);
        $this->assertEquals('template-import-siswa.xlsx', $result['data']['filename']);
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

        $result = $this->siswaService->processExcelImport($mockFile);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        // Should return error for invalid file
    }

    /**
     * Test validation with invalid jenis_kelamin
     */
    public function testValidationWithInvalidJenisKelamin()
    {
        $data = [
            'nis' => '12345',
            'nama_lengkap' => 'Test Siswa',
            'jenis_kelamin' => 'X', // Invalid
            'kelas_id' => 1,
            'tahun_ajaran' => '2023/2024',
            'username' => 'test_user',
            'password' => 'password123',
            'email' => 'test@example.com'
        ];

        $result = $this->siswaService->createSiswa($data);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals(422, $result['code']);
    }

    /**
     * Test validation with short password
     */
    public function testValidationWithShortPassword()
    {
        $data = [
            'nis' => '12345',
            'nama_lengkap' => 'Test Siswa',
            'jenis_kelamin' => 'L',
            'kelas_id' => 1,
            'tahun_ajaran' => '2023/2024',
            'username' => 'test_user',
            'password' => '123', // Too short
            'email' => 'test@example.com'
        ];

        $result = $this->siswaService->createSiswa($data);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals(422, $result['code']);
    }
}
