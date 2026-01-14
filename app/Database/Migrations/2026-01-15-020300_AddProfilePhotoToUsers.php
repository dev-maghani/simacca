<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Add Profile Photo to Users Table
 * 
 * Adds profile_photo field to users table for profile picture storage.
 * Stores filename only, actual files stored in writable/uploads/profile/
 * 
 * Dependencies: CreateUsersTable migration
 * 
 * @package App\Database\Migrations
 * @author SIMACCA Team
 * @version 1.1.0
 */
class AddProfilePhotoToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'profile_photo' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'email',
            ],
        ];

        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'profile_photo');
    }
}
