<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Create Users Table
 * 
 * Creates the main users table for multi-role authentication system.
 * Roles: admin, guru_mapel, wali_kelas, siswa
 * 
 * Dependencies: None (First table)
 * 
 * @package App\Database\Migrations
 * @author SIMACCA Team
 * @version 1.0.0
 */
class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
                'auto_increment'    => true,
            ],
            'username' => [
                'type'              => 'VARCHAR',
                'constraint'        => '50',
                'unique'            => true,
            ],
            'password' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255',
            ],
            'role' => [
                'type'              => 'ENUM',
                'constraint'        => ['admin', 'guru_mapel', 'wali_kelas', 'siswa'],
                'default'           => 'siswa',
            ],
            'email' => [
                'type'              => 'VARCHAR',
                'constraint'        => '100',
                'null'              => true,
            ],
            'is_active' => [
                'type'              => 'BOOLEAN',
                'default'           => true,
            ],
            'created_at' => [
                'type'              => 'DATETIME',
                'null'              => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
