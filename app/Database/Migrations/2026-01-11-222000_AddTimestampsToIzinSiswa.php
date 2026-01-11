<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Add Timestamps to Izin Siswa
 * 
 * Adds created_at and updated_at timestamp fields to izin_siswa table.
 * Enables tracking of when permission requests are submitted and modified.
 * 
 * Dependencies: izin_siswa
 * Added Fields: created_at, updated_at (DATETIME, NULLABLE)
 * 
 * @package App\Database\Migrations
 * @author SIMACCA Team
 * @version 1.0.0
 * @date 2026-01-11
 */
class AddTimestampsToIzinSiswa extends Migration
{
    public function up()
    {
        $fields = [
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ];
        
        $this->forge->addColumn('izin_siswa', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('izin_siswa', ['created_at', 'updated_at']);
    }
}
