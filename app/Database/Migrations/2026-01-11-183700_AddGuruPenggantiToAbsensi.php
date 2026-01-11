<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Add Guru Pengganti to Absensi
 * 
 * Adds guru_pengganti_id field to absensi table for substitute teacher feature.
 * Records which teacher is substituting when the regular teacher is absent.
 * 
 * Dependencies: absensi, guru
 * Added Field: guru_pengganti_id (INT, NULLABLE)
 * Foreign Keys: guru_pengganti_id -> guru(id) ON DELETE SET NULL
 * 
 * Features:
 * - Allows recording substitute teachers for any class session
 * - Dual ownership access control (creator OR schedule owner)
 * - Integrated with attendance and teaching journal
 * 
 * @package App\Database\Migrations
 * @author SIMACCA Team
 * @version 1.1.0
 * @date 2026-01-11
 */
class AddGuruPenggantiToAbsensi extends Migration
{
    public function up()
    {
        $this->forge->addColumn('absensi', [
            'guru_pengganti_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'created_by',
            ],
        ]);

        // Add foreign key
        $this->forge->processIndexes('absensi');
        $this->db->query('ALTER TABLE absensi ADD CONSTRAINT fk_absensi_guru_pengganti FOREIGN KEY (guru_pengganti_id) REFERENCES guru(id) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        // Drop foreign key first
        $this->db->query('ALTER TABLE absensi DROP FOREIGN KEY fk_absensi_guru_pengganti');
        
        $this->forge->dropColumn('absensi', 'guru_pengganti_id');
    }
}
