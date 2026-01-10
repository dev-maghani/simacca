<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateKelasForeignKey extends Migration
{
    public function up()
    {
        $this->forge->addForeignKey('wali_kelas_id', 'guru', 'id', 'SET NULL', 'SET NULL');
    }

    public function down()
    {
        $this->forge->dropForeignKey('kelas', 'wali_kelas_id_foreign');
    }
}
