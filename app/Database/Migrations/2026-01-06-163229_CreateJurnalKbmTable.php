<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

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
