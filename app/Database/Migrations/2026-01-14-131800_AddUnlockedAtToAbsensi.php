<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUnlockedAtToAbsensi extends Migration
{
    public function up()
    {
        $this->forge->addColumn('absensi', [
            'unlocked_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'created_at',
                'comment' => 'Timestamp when admin unlocked this absensi for editing'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('absensi', 'unlocked_at');
    }
}
