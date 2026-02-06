<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\GuruService;

/**
 * GuruService Test
 * 
 * Basic tests to verify GuruService functionality
 */
class GuruServiceTest extends CIUnitTestCase
{
    protected $guruService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->guruService = new GuruService();
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
        $this->assertInstanceOf(GuruService::class, $this->guruService);
    }

    /**
     * Test getAllGuru returns proper structure
     */
    public function testGetAllGuruReturnsProperStructure()
    {
        $result = $this->guruService->getAllGuru();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('data', $result);
    }

    /**
     * Test getStatistics returns proper structure
     */
    public function testGetStatisticsReturnsProperStructure()
    {
        $result = $this->guruService->getStatistics();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        
        if ($result['success']) {
            $this->assertArrayHasKey('data', $result);
            $this->assertArrayHasKey('totalGuru', $result['data']);
            $this->assertArrayHasKey('waliKelas', $result['data']);
            $this->assertArrayHasKey('guruNonWali', $result['data']);
        }
    }

    /**
     * Test createGuru validation fails with empty data
     */
    public function testCreateGuruValidationFailsWithEmptyData()
    {
        $result = $this->guruService->createGuru([]);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('errors', $result);
    }

    /**
     * Test createGuru validation fails with incomplete data
     */
    public function testCreateGuruValidationFailsWithIncompleteData()
    {
        $data = [
            'nip' => '12345',
            // Missing required fields
        ];
        
        $result = $this->guruService->createGuru($data);
        
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
    }

    /**
     * Test checkNipAvailability returns proper structure
     */
    public function testCheckNipAvailabilityReturnsProperStructure()
    {
        $result = $this->guruService->checkNipAvailability('999999999');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('data', $result);
        
        if ($result['success']) {
            $this->assertArrayHasKey('available', $result['data']);
            $this->assertArrayHasKey('message', $result['data']);
        }
    }

    /**
     * Test checkUsernameAvailability returns proper structure
     */
    public function testCheckUsernameAvailabilityReturnsProperStructure()
    {
        $result = $this->guruService->checkUsernameAvailability('test_username_999');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('data', $result);
        
        if ($result['success']) {
            $this->assertArrayHasKey('available', $result['data']);
            $this->assertArrayHasKey('message', $result['data']);
        }
    }

    /**
     * Test getGuruById with invalid ID returns error
     */
    public function testGetGuruByIdWithInvalidIdReturnsError()
    {
        $result = $this->guruService->getGuruById(999999);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }

    /**
     * Test deleteGuru with invalid ID returns error
     */
    public function testDeleteGuruWithInvalidIdReturnsError()
    {
        $result = $this->guruService->deleteGuru(999999);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }

    /**
     * Test getFormLists returns proper structure
     */
    public function testGetFormListsReturnsProperStructure()
    {
        $result = $this->guruService->getFormLists();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        
        if ($result['success']) {
            $this->assertArrayHasKey('data', $result);
            $this->assertArrayHasKey('mapelList', $result['data']);
            $this->assertArrayHasKey('kelasList', $result['data']);
        }
    }
}
