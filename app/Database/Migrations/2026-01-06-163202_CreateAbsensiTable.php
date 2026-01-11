<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Create Absensi Table
 * 
 * Creates attendance header table (per session/meeting).
 * Contains meeting number, date, and learning materials.
 * 
 * Dependencies: jadwal_mengajar, users
 * Foreign Keys:
 *   - jadwal_mengajar_id -> jadwal_mengajar(id)
 *   - created_by -> users(id)
 * 
 * Note: guru_pengganti_id added via AddGuruPenggantiToAbsensi migration
 * 
 * @package App\Database\Migrations
 * @author SIMACCA Team
 * @version 1.0.0
 */
class CreateAbsensiTable extends Migration
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
            'jadwal_mengajar_id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
            ],
            'tanggal' => [
                'type'              => 'DATE',

            ],
            'pertemuan_ke' => [
                'type'              => 'INT',
                'constraint'        => 11,
            ],
            'materi_pembelajaran' => [
                'type'              => 'TEXT',
                'null'              => true,
            ],
            'created_by' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
            ],
            'created_at' => [
                'type'              => 'DATETIME',
                'null'              => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('jadwal_mengajar_id', 'jadwal_mengajar', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('absensi');
    }

    public function down()
    {
        $this->forge->dropTable('absensi');
    }
}
