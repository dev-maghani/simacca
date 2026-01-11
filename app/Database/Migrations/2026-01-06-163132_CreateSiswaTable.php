<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Create Siswa Table
 * 
 * Creates table for students with profile and class assignment.
 * Links to users table for authentication and kelas for class membership.
 * 
 * Dependencies: users, kelas
 * Foreign Keys: 
 *   - user_id -> users(id)
 *   - kelas_id -> kelas(id)
 * 
 * @package App\Database\Migrations
 * @author SIMACCA Team
 * @version 1.0.0
 */
class CreateSiswaTable extends Migration
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
            'user_id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
            ],
            'nis' => [
                'type'              => 'VARCHAR',
                'constraint'        => '20',
                'unique'            => true,
            ],
            'nama_lengkap' => [
                'type'              => 'VARCHAR',
                'constraint'        => '100',
            ],
            'jenis_kelamin' => [
                'type'              => 'ENUM',
                'constraint'        => ['L', 'P'],
            ],
            'kelas_id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
                'null'              => true,
            ],
            'tahun_ajaran' => [
                'type'              => 'VARCHAR',
                'constraint'        => '9',
            ],
            'created_at' => [
                'type'              => 'DATETIME',
                'null'              => true,
            ],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('siswa');
    }

    public function down()
    {
        $this->forge->dropTable('siswa');
    }
}
