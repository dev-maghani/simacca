<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Create Guru Table
 * 
 * Creates table for teachers with full profile information.
 * Links to users table for authentication.
 * 
 * Dependencies: users
 * Foreign Keys: user_id -> users(id)
 * 
 * @package App\Database\Migrations
 * @author SIMACCA Team
 * @version 1.0.0
 */
class CreateGuruTable extends Migration
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
            'nip' => [
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
                'constraint'        => ['L', 'P']
            ],
            'mata_pelajaran_id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
                'null'              => true,
            ],  
            'is_wali_kelas' => [
                'type'              => 'BOOLEAN',
                'default'           => false,
            ],
            'kelas_id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
                'null'              => true,
            ],
            'created_at' => [
                'type'              => 'DATETIME',
                'null'              => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('mata_pelajaran_id','mata_pelajaran', 'id', 'SET NULL', 'SET NULL');
        $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('guru');
    }

    public function down()
    {
        $this->forge->dropTable('guru');
    }
}
