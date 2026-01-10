<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\GuruModel;
use CodeIgniter\HTTP\ResponseInterface;

class Tester extends BaseController
{
    public function index()
    {
        // Test UserModel
        $userModel = new UserModel();
        echo "<h1>Testing Models</h1>";

        // Test get all users
        $users = $userModel->findAll();
        echo "<h2>All User:</h2>";
        echo "<pre>";
        print_r($users);
        echo "</pre>";

        // Test GuruModel
        $guruModel = new GuruModel();
        $guru = $guruModel->getAllGuru();
        echo "<h2>All Guru:</h2>";
        echo "<pre>";
        print_r($guru);
        echo "</pre>";

        // Test Login
        $testLogin = $userModel->checkLogin('gani828', 'wali123');
        echo "<h2>Test Login:</h2>";
        echo "<pre>";
        print_r($testLogin);
        echo "</pre>";
    }
}
