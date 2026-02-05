<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\AbsensiService;

/**
 * AbsensiService Test
 * 
 * Basic tests to verify AbsensiService functionality
 */
class AbsensiServiceTest extends CIUnitTestCase
{
    protected $absensiService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->absensiService = new AbsensiService();
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
        $this->assertInstanceOf(AbsensiService::class, $this->absensiService);
    }

    /**
     * Test getByGuru returns proper structure
     */
    public function testGetByGuruReturnsProperStructure()
    {
        $result = $this->absensiService->getByGuru(1);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('data', $result);
    }

    /**
     * Test getAbsensiStats returns proper structure
     */
    public function testGetAbsensiStatsReturnsProperStructure()
    {
        $result = $this->absensiService->getAbsensiStats(1);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        
        if ($result['success']) {
            $this->assertArrayHasKey('data', $result);
            $this->assertArrayHasKey('total', $result['data']);
            $this->assertArrayHasKey('hadir', $result['data']);
            $this->assertArrayHasKey('izin', $result['data']);
            $this->assertArrayHasKey('sakit', $result['data']);
            $this->assertArrayHasKey('alpa', $result['data']);
        }
    }

    /**
     * Test createAbsensi validation fails with empty data
     */
    public function testCreateAbsensiValidationFailsWithEmptyData()
    {
        $result = $this->absensiService->createAbsensi([]);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('errors', $result);
    }

    /**
     * Test createAbsensi validation fails with incomplete data
     */
    public function testCreateAbsensiValidationFailsWithIncompleteData()
    {
        $data = [
            'tanggal' => date('Y-m-d'),
            // Missing required fields
        ];
        
        $result = $this->absensiService->createAbsensi($data);
        
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
    }

    /**
     * Test getAbsensiDetail with invalid ID returns error
     */
    public function testGetAbsensiDetailWithInvalidIdReturnsError()
    {
        $result = $this->absensiService->getAbsensiDetail(999999);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }

    /**
     * Test deleteAbsensi with invalid ID returns error
     */
    public function testDeleteAbsensiWithInvalidIdReturnsError()
    {
        $result = $this->absensiService->deleteAbsensi(999999);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }

    /**
     * Test updateAbsensi with invalid ID returns error
     */
    public function testUpdateAbsensiWithInvalidIdReturnsError()
    {
        $data = [
            'tanggal' => date('Y-m-d'),
            'pertemuan_ke' => 1,
            'siswa' => []
        ];
        
        $result = $this->absensiService->updateAbsensi(999999, $data);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }

    /**
     * Test getNextPertemuan returns proper structure
     */
    public function testGetNextPertemuanReturnsProperStructure()
    {
        $result = $this->absensiService->getNextPertemuan(1, null, 1);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('data', $result);
        
        if ($result['success']) {
            $this->assertArrayHasKey('pertemuan_ke', $result['data']);
            $this->assertIsInt($result['data']['pertemuan_ke']);
        }
    }

    /**
     * Test checkAbsensiExists returns proper structure
     */
    public function testCheckAbsensiExistsReturnsProperStructure()
    {
        $result = $this->absensiService->checkAbsensiExists(1, date('Y-m-d'));
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('data', $result);
        
        if ($result['success']) {
            $this->assertArrayHasKey('exists', $result['data']);
            $this->assertIsBool($result['data']['exists']);
        }
    }

    /**
     * Test unlockAbsensi with invalid ID returns error
     */
    public function testUnlockAbsensiWithInvalidIdReturnsError()
    {
        $result = $this->absensiService->unlockAbsensi(999999);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }

    /**
     * Test bulkUnlockAbsensi with empty array returns error
     */
    public function testBulkUnlockAbsensiWithEmptyArrayReturnsError()
    {
        $result = $this->absensiService->bulkUnlockAbsensi([]);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }

    /**
     * Test getSiswaByKelas returns proper structure
     */
    public function testGetSiswaByKelasReturnsProperStructure()
    {
        $result = $this->absensiService->getSiswaByKelas(1);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        
        if ($result['success']) {
            $this->assertArrayHasKey('data', $result);
            $this->assertArrayHasKey('siswa', $result['data']);
            $this->assertArrayHasKey('approvedIzin', $result['data']);
        }
    }

    /**
     * Test isAbsensiEditable with fresh absensi
     */
    public function testIsAbsensiEditableWithFreshAbsensi()
    {
        $absensi = [
            'created_at' => date('Y-m-d H:i:s'),
            'unlocked_at' => null
        ];
        
        $result = $this->absensiService->isAbsensiEditable($absensi);
        
        $this->assertTrue($result);
    }

    /**
     * Test isAbsensiEditable with old absensi
     */
    public function testIsAbsensiEditableWithOldAbsensi()
    {
        $absensi = [
            'created_at' => date('Y-m-d H:i:s', strtotime('-25 hours')),
            'unlocked_at' => null
        ];
        
        $result = $this->absensiService->isAbsensiEditable($absensi);
        
        $this->assertFalse($result);
    }

    /**
     * Test getKelasSummary returns proper structure
     */
    public function testGetKelasSummaryReturnsProperStructure()
    {
        $absensi = [
            [
                'kelas_id' => 1,
                'nama_kelas' => 'X IPA 1',
                'nama_mapel' => 'Matematika',
                'tanggal' => date('Y-m-d'),
                'hadir' => 30,
                'total_siswa' => 32,
                'jam_mulai' => '07:00',
                'jam_selesai' => '08:00',
                'hari' => 'Senin'
            ]
        ];
        
        $result = $this->absensiService->getKelasSummary($absensi);
        
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        
        foreach ($result as $summary) {
            $this->assertArrayHasKey('kelas_id', $summary);
            $this->assertArrayHasKey('kelas_nama', $summary);
            $this->assertArrayHasKey('mata_pelajaran', $summary);
            $this->assertArrayHasKey('total_pertemuan', $summary);
            $this->assertArrayHasKey('total_hadir', $summary);
            $this->assertArrayHasKey('avg_kehadiran', $summary);
        }
    }
}
