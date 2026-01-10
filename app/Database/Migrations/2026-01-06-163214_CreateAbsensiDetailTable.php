<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

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
