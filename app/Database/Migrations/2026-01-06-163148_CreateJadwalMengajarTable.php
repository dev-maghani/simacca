<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Create Jadwal Mengajar Table
 * 
 * Creates teaching schedule table with conflict detection.
 * Links teacher, subject, and class with time schedule.
 * 
 * Dependencies: guru, mata_pelajaran, kelas
 * Foreign Keys:
 *   - guru_id -> guru(id)
 *   - mata_pelajaran_id -> mata_pelajaran(id)
 *   - kelas_id -> kelas(id)
 * 
 * @package App\Database\Migrations
 * @author SIMACCA Team
 * @version 1.0.0
 */
class CreateJadwalMengajarTable extends Migration
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
            'guru_id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
            ],
            'mata_pelajaran_id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
            ],
            'kelas_id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
            ],
            'hari' => [
                'type'              => 'ENUM',
                'constraint'        => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
            ],
            'jam_mulai' => [
                'type'              => 'TIME',
            ],
            'jam_selesai' => [
                'type'              => 'TIME',
            ],
            'semester' => [
                'type'              => 'ENUM',
                'constraint'        => ['Ganjil', 'Genap'],
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
        $this->forge->addForeignKey('guru_id', 'guru', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('mata_pelajaran_id', 'mata_pelajaran', 'id','CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('jadwal_mengajar');
    }

    public function down()
    {
        $this->forge->dropTable('jadwal_mengajar');
    }
}
