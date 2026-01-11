<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Update Kelas Foreign Key
 * 
 * Adds wali_kelas_id foreign key to kelas table.
 * This allows assignment of a homeroom teacher to each class.
 * 
 * Dependencies: kelas, guru
 * Foreign Keys: wali_kelas_id -> guru(id)
 * 
 * @package App\Database\Migrations
 * @author SIMACCA Team
 * @version 1.0.0
 */
class UpdateKelasForeignKey extends Migration
{
    public function up()
    {
        $this->forge->addForeignKey('wali_kelas_id', 'guru', 'id', 'SET NULL', 'SET NULL');
    }

    public function down()
    {
        $this->forge->dropForeignKey('kelas', 'wali_kelas_id_foreign');
    }
}
