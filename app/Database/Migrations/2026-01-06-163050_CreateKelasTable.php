<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKelasTable extends Migration
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
            'nama_kelas' => [
                'type'              => 'VARCHAR',
                'constraint'        => '10',
            ],
            'tingkat' => [
                'type'              => 'ENUM',
                'constraint'        => ['10', '11', '12'],
            ],
            'jurusan' => [
                'type'              => 'VARCHAR',
                'constraint'        => '50',
            ],
            "wali_kelas_id" => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
                'null'              => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('nama_kelas');
        $this->forge->createTable('kelas');
    }

    public function down()
    {
        $this->forge->dropTable('kelas');
    }
}
