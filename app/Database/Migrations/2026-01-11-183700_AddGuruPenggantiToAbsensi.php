<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGuruPenggantiToAbsensi extends Migration
{
    public function up()
    {
        $this->forge->addColumn('absensi', [
            'guru_pengganti_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'created_by',
            ],
        ]);

        // Add foreign key
        $this->forge->processIndexes('absensi');
        $this->db->query('ALTER TABLE absensi ADD CONSTRAINT fk_absensi_guru_pengganti FOREIGN KEY (guru_pengganti_id) REFERENCES guru(id) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        // Drop foreign key first
        $this->db->query('ALTER TABLE absensi DROP FOREIGN KEY fk_absensi_guru_pengganti');
        
        $this->forge->dropColumn('absensi', 'guru_pengganti_id');
    }
}
