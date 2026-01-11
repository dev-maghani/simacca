<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Add Foto to Jurnal KBM
 * 
 * Adds foto_dokumentasi field to jurnal_kbm table.
 * Allows teachers to upload photo documentation of teaching activities.
 * 
 * Dependencies: jurnal_kbm
 * Added Field: foto_dokumentasi (VARCHAR 255, NULLABLE)
 * 
 * @package App\Database\Migrations
 * @author SIMACCA Team
 * @version 1.0.0
 * @date 2026-01-11
 */
class AddFotoToJurnalKbm extends Migration
{
    public function up()
    {
        // Add foto_dokumentasi column to jurnal_kbm table
        $fields = [
            'foto_dokumentasi' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'catatan_khusus'
            ]
        ];
        
        $this->forge->addColumn('jurnal_kbm', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('jurnal_kbm', 'foto_dokumentasi');
    }
}
