<?php

namespace Tests\Unit;

use App\Services\MataPelajaranService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class MataPelajaranServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $service;
    protected $migrate = true;
    protected $refresh = true;
    protected $namespace = 'Tests\Support';

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MataPelajaranService();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testGetAllMapel()
    {
        $result = $this->service->getAllMapel(50);
        
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('mapel', $result['data']);
        $this->assertArrayHasKey('pager', $result['data']);
    }

    public function testCreateMapelSuccess()
    {
        $data = [
            'kode_mapel' => 'MTK',
            'nama_mapel' => 'Matematika',
            'kategori' => 'umum'
        ];

        $result = $this->service->createMapel($data);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('id', $result['data']);
        $this->assertGreaterThan(0, $result['data']['id']);
    }

    public function testCreateMapelDuplicateKode()
    {
        // Create first mapel
        $data = [
            'kode_mapel' => 'BIO',
            'nama_mapel' => 'Biologi',
            'kategori' => 'umum'
        ];

        $this->service->createMapel($data);

        // Try to create duplicate
        $result = $this->service->createMapel($data);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('sudah digunakan', $result['message']);
    }

    public function testGetMapelById()
    {
        // Create a mapel first
        $data = [
            'kode_mapel' => 'FIS',
            'nama_mapel' => 'Fisika',
            'kategori' => 'umum'
        ];

        $createResult = $this->service->createMapel($data);
        $mapelId = $createResult['data']['id'];

        // Get the mapel
        $result = $this->service->getMapelById($mapelId);

        $this->assertTrue($result['success']);
        $this->assertEquals('FIS', $result['data']['kode_mapel']);
        $this->assertEquals('Fisika', $result['data']['nama_mapel']);
    }

    public function testUpdateMapel()
    {
        // Create a mapel first
        $data = [
            'kode_mapel' => 'KIM',
            'nama_mapel' => 'Kimia',
            'kategori' => 'umum'
        ];

        $createResult = $this->service->createMapel($data);
        $mapelId = $createResult['data']['id'];

        // Update the mapel
        $updateData = [
            'nama_mapel' => 'Kimia Dasar',
            'kategori' => 'umum'
        ];

        $result = $this->service->updateMapel($mapelId, $updateData);

        $this->assertTrue($result['success']);

        // Verify the update
        $getResult = $this->service->getMapelById($mapelId);
        $this->assertEquals('Kimia Dasar', $getResult['data']['nama_mapel']);
    }

    public function testDeleteMapel()
    {
        // Create a mapel
        $data = [
            'kode_mapel' => 'SEJ',
            'nama_mapel' => 'Sejarah',
            'kategori' => 'umum'
        ];

        $createResult = $this->service->createMapel($data);
        $mapelId = $createResult['data']['id'];

        // Delete the mapel
        $result = $this->service->deleteMapel($mapelId);

        $this->assertTrue($result['success']);

        // Verify deletion
        $getResult = $this->service->getMapelById($mapelId);
        $this->assertFalse($getResult['success']);
    }

    public function testGetMapelByKategori()
    {
        // Create test mapel in different categories
        $testData = [
            ['kode_mapel' => 'BIND', 'nama_mapel' => 'Bahasa Indonesia', 'kategori' => 'umum'],
            ['kode_mapel' => 'BING', 'nama_mapel' => 'Bahasa Inggris', 'kategori' => 'umum'],
            ['kode_mapel' => 'PROG', 'nama_mapel' => 'Pemrograman', 'kategori' => 'kejuruan']
        ];

        foreach ($testData as $data) {
            $this->service->createMapel($data);
        }

        // Get mapel by kategori 'umum'
        $result = $this->service->getMapelByKategori('umum');

        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
        $this->assertGreaterThanOrEqual(2, count($result['data']));
    }

    public function testGetMapelStatistics()
    {
        // Create test mapel
        $testData = [
            ['kode_mapel' => 'GEO', 'nama_mapel' => 'Geografi', 'kategori' => 'umum'],
            ['kode_mapel' => 'EKO', 'nama_mapel' => 'Ekonomi', 'kategori' => 'umum'],
            ['kode_mapel' => 'AKUN', 'nama_mapel' => 'Akuntansi', 'kategori' => 'kejuruan']
        ];

        foreach ($testData as $data) {
            $this->service->createMapel($data);
        }

        // Get statistics
        $result = $this->service->getMapelStatistics();

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('total_mapel', $result['data']);
        $this->assertArrayHasKey('mapel_per_kategori', $result['data']);
        $this->assertGreaterThanOrEqual(3, $result['data']['total_mapel']);
    }

    public function testGetMapelForDropdown()
    {
        // Create test mapel
        $data = [
            'kode_mapel' => 'PKN',
            'nama_mapel' => 'Pendidikan Kewarganegaraan',
            'kategori' => 'umum'
        ];

        $this->service->createMapel($data);

        // Get dropdown
        $result = $this->service->getMapelForDropdown();

        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
    }

    public function testBulkImportMapel()
    {
        $mapelData = [
            ['kode_mapel' => 'SEN', 'nama_mapel' => 'Seni Budaya', 'kategori' => 'umum'],
            ['kode_mapel' => 'OR', 'nama_mapel' => 'Pendidikan Jasmani', 'kategori' => 'umum'],
            ['kode_mapel' => 'TIK', 'nama_mapel' => 'Teknologi Informasi', 'kategori' => 'kejuruan']
        ];

        $result = $this->service->bulkImportMapel($mapelData);

        $this->assertTrue($result['success']);
        $this->assertEquals(3, $result['data']['success_count']);
        $this->assertEquals(0, $result['data']['failed_count']);
    }

    public function testKategoriNormalization()
    {
        // Test that kategori is normalized to lowercase
        $data = [
            'kode_mapel' => 'AGM',
            'nama_mapel' => 'Pendidikan Agama',
            'kategori' => 'UMUM' // Uppercase
        ];

        $createResult = $this->service->createMapel($data);
        $this->assertTrue($createResult['success']);

        $getResult = $this->service->getMapelById($createResult['data']['id']);
        $this->assertEquals('umum', $getResult['data']['kategori']); // Should be lowercase
    }
}
