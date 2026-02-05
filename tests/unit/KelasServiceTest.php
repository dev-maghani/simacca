<?php

namespace Tests\Unit;

use App\Services\KelasService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class KelasServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $service;
    protected $migrate = true;
    protected $refresh = true;
    protected $namespace = 'Tests\Support';

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new KelasService();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testGetAllKelas()
    {
        // Test getting all kelas
        $result = $this->service->getAllKelas(20);
        
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('kelas', $result['data']);
        $this->assertArrayHasKey('pager', $result['data']);
    }

    public function testCreateKelasSuccess()
    {
        $data = [
            'nama_kelas' => 'XII-MPLB-1',
            'tingkat' => '12',
            'jurusan' => 'Manajemen Perkantoran dan Layanan Bisnis',
            'wali_kelas_id' => null
        ];

        $result = $this->service->createKelas($data);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('id', $result['data']);
        $this->assertGreaterThan(0, $result['data']['id']);
    }

    public function testCreateKelasDuplicateName()
    {
        // Create first kelas
        $data = [
            'nama_kelas' => 'XII-DKV-1',
            'tingkat' => '12',
            'jurusan' => 'Desain Komunikasi Visual',
            'wali_kelas_id' => null
        ];

        $this->service->createKelas($data);

        // Try to create duplicate
        $result = $this->service->createKelas($data);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('sudah digunakan', $result['message']);
    }

    public function testGetKelasById()
    {
        // Create a kelas first
        $data = [
            'nama_kelas' => 'XI-AT-1',
            'tingkat' => '11',
            'jurusan' => 'Agribisnis Tanaman',
            'wali_kelas_id' => null
        ];

        $createResult = $this->service->createKelas($data);
        $kelasId = $createResult['data']['id'];

        // Get the kelas
        $result = $this->service->getKelasById($kelasId);

        $this->assertTrue($result['success']);
        $this->assertEquals('XI-AT-1', $result['data']['nama_kelas']);
        $this->assertEquals('11', $result['data']['tingkat']);
    }

    public function testGetKelasNotFound()
    {
        $result = $this->service->getKelasById(99999);

        $this->assertFalse($result['success']);
        $this->assertEquals(404, $result['code']);
    }

    public function testUpdateKelas()
    {
        // Create a kelas first
        $data = [
            'nama_kelas' => 'X-MPLB-1',
            'tingkat' => '10',
            'jurusan' => 'Manajemen Perkantoran dan Layanan Bisnis',
            'wali_kelas_id' => null
        ];

        $createResult = $this->service->createKelas($data);
        $kelasId = $createResult['data']['id'];

        // Update the kelas
        $updateData = [
            'nama_kelas' => 'X-MPLB-2',
            'tingkat' => '10',
            'jurusan' => 'Manajemen Perkantoran dan Layanan Bisnis'
        ];

        $result = $this->service->updateKelas($kelasId, $updateData);

        $this->assertTrue($result['success']);

        // Verify the update
        $getResult = $this->service->getKelasById($kelasId);
        $this->assertEquals('X-MPLB-2', $getResult['data']['nama_kelas']);
    }

    public function testDeleteKelasWithoutSiswa()
    {
        // Create a kelas
        $data = [
            'nama_kelas' => 'XII-AT-1',
            'tingkat' => '12',
            'jurusan' => 'Agribisnis Tanaman',
            'wali_kelas_id' => null
        ];

        $createResult = $this->service->createKelas($data);
        $kelasId = $createResult['data']['id'];

        // Delete the kelas
        $result = $this->service->deleteKelas($kelasId);

        $this->assertTrue($result['success']);

        // Verify deletion
        $getResult = $this->service->getKelasById($kelasId);
        $this->assertFalse($getResult['success']);
    }

    public function testGetKelasStatistics()
    {
        // Create some test kelas
        $testData = [
            ['nama_kelas' => 'X-DKV-1', 'tingkat' => '10', 'jurusan' => 'Desain Komunikasi Visual'],
            ['nama_kelas' => 'XI-DKV-1', 'tingkat' => '11', 'jurusan' => 'Desain Komunikasi Visual'],
            ['nama_kelas' => 'XII-DKV-1', 'tingkat' => '12', 'jurusan' => 'Desain Komunikasi Visual']
        ];

        foreach ($testData as $data) {
            $data['wali_kelas_id'] = null;
            $this->service->createKelas($data);
        }

        // Get statistics
        $result = $this->service->getKelasStatistics();

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('total_kelas', $result['data']);
        $this->assertArrayHasKey('kelas_per_tingkat', $result['data']);
        $this->assertGreaterThanOrEqual(3, $result['data']['total_kelas']);
    }

    public function testGetKelasForDropdown()
    {
        // Create test kelas
        $data = [
            'nama_kelas' => 'X-AT-2',
            'tingkat' => '10',
            'jurusan' => 'Agribisnis Tanaman',
            'wali_kelas_id' => null
        ];

        $this->service->createKelas($data);

        // Get dropdown
        $result = $this->service->getKelasForDropdown();

        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
    }

    public function testGetKelasForDropdownFilteredByTingkat()
    {
        // Create test kelas in different tingkat
        $testData = [
            ['nama_kelas' => 'X-Test-1', 'tingkat' => '10', 'jurusan' => 'Test'],
            ['nama_kelas' => 'XI-Test-1', 'tingkat' => '11', 'jurusan' => 'Test']
        ];

        foreach ($testData as $data) {
            $data['wali_kelas_id'] = null;
            $this->service->createKelas($data);
        }

        // Get dropdown for tingkat 10 only
        $result = $this->service->getKelasForDropdown(10);

        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
    }
}
