<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Create Mata Pelajaran Table
 * 
 * Creates table for subjects/courses with KKM (Kriteria Ketuntasan Minimal).
 * 
 * Dependencies: None
 * 
 * @package App\Database\Migrations
 * @author SIMACCA Team
 * @version 1.0.0
 */
class CreateMataPelajaranTable extends Migration
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
            'kode_mapel' => [
                'type'              => 'VARCHAR',
                'constraint'        => '10',
                'unique'            => true,
            ],
            'nama_mapel' => [
                'type'              => 'VARCHAR',
                'constraint'        => '100',
            ],
            'kategori' => [
                'type'              => 'ENUM',
                'constraint'        => ['umum', 'kejuruan'],
                'default'           => 'umum',
            ],
            'created_at' => [
                'type'              => 'DATETIME',
                'null'              => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('mata_pelajaran');
    }

    public function down()
    {
        $this->forge->dropTable('mata_pelajaran');
    }
}
