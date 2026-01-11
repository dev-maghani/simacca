<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Create Jurnal KBM Table
 * 
 * Creates teaching journal table for documenting learning activities.
 * One journal per absensi session with notes and photo documentation.
 * 
 * Dependencies: absensi
 * Foreign Keys:
 *   - absensi_id -> absensi(id) ON DELETE CASCADE
 * 
 * Note: foto_dokumentasi field added via AddFotoToJurnalKbm migration
 * 
 * @package App\Database\Migrations
 * @author SIMACCA Team
 * @version 1.0.0
 */
class CreateJurnalKbmTable extends Migration
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
                'unique'            => true,
            ],
            'tujuan_pembelajaran' => [
                'type'              => 'TEXT',
            ],
            'kegiatan_pembelajaran' => [
                'type'              =>'TEXT',
            ],
            'media_alat' => [
                'type'              => 'TEXT',
                'null'              => true,
            ],
            'penilaian' => [
                'type'              => 'TEXT',
                'null'              => true,
            ],
            'catatan_khusus' => [
                'type'              => 'TEXT',
                'null'              => true,
            ],
            'created_at' => [
                'type'              => 'DATETIME',
                'null'              => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('absensi_id', 'absensi', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('jurnal_kbm');
    }

    public function down()
    {
        $this->forge->dropTable('jurnal_kbm');
    }
}
