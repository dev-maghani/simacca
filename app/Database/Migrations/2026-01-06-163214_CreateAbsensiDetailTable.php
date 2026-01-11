<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Create Absensi Detail Table
 * 
 * Creates attendance detail table (per student per session).
 * Records individual student attendance status.
 * 
 * Dependencies: absensi, siswa
 * Foreign Keys:
 *   - absensi_id -> absensi(id) ON DELETE CASCADE
 *   - siswa_id -> siswa(id) ON DELETE CASCADE
 * 
 * Status Enum: hadir, sakit, izin, alpa
 * 
 * @package App\Database\Migrations
 * @author SIMACCA Team
 * @version 1.0.0
 */
class CreateAbsensiDetailTable extends Migration
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
            'absensi_id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
            ],
            'siswa_id' => [
                'type'              => 'INT', 
                'constraint'        => 11,
                'unsigned'          => true,
            ],
            'status' => [
                'type'              => 'ENUM',
                'constraint'        => ['hadir', 'izin', 'sakit', 'alpa'],
                'default'           => 'alpa',
            ],
            'keterangan' => [
                'type'              => 'TEXT',
                'null'              => true,
            ],
            'waktu_absen' => [
                'type'              => 'DATETIME',
                'null'              => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('absensi_id', 'absensi', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['absensi_id', 'siswa_id']);
        $this->forge->createTable('absensi_detail');
    }

    public function down()
    {
        $this->forge->dropTable('absensi_detail');
    }
}
