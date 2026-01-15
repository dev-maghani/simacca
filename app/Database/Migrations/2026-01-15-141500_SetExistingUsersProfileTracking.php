<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Set Profile Tracking for Existing Users
 * 
 * This migration sets the tracking fields for existing users who were created
 * before the profile completion feature was implemented.
 * 
 * Options:
 * 1. Set all fields to current timestamp (users won't be forced to update profile)
 * 2. Leave fields as NULL (users will be forced to complete profile)
 * 
 * Default: Set to current timestamp for existing users to avoid disruption
 * 
 * @package App\Database\Migrations
 * @author SIMACCA Team
 * @version 1.0.0
 */
class SetExistingUsersProfileTracking extends Migration
{
    public function up()
    {
        // Get current timestamp
        $now = date('Y-m-d H:i:s');
        
        // Option 1: Set tracking fields for ALL existing users
        // This will mark all existing users as having "completed" their profile
        // Uncomment the lines below to apply this approach:
        
        /*
        $this->db->query("
            UPDATE users 
            SET password_changed_at = ?,
                email_changed_at = ?,
                profile_photo_uploaded_at = ?
            WHERE password_changed_at IS NULL
               OR email_changed_at IS NULL
               OR profile_photo_uploaded_at IS NULL
        ", [$now, $now, $now]);
        */
        
        // Option 2: Set tracking fields only for users who have email and profile_photo
        // Users without email or profile photo will still need to complete profile
        // Uncomment the lines below to apply this approach:
        
        /*
        $this->db->query("
            UPDATE users 
            SET password_changed_at = ?
            WHERE password_changed_at IS NULL
        ", [$now]);
        
        $this->db->query("
            UPDATE users 
            SET email_changed_at = ?
            WHERE email_changed_at IS NULL
              AND email IS NOT NULL
              AND email != ''
        ", [$now]);
        
        $this->db->query("
            UPDATE users 
            SET profile_photo_uploaded_at = ?
            WHERE profile_photo_uploaded_at IS NULL
              AND profile_photo IS NOT NULL
              AND profile_photo != ''
        ", [$now]);
        */
        
        // Option 3: Do nothing - let admin decide manually
        // This is the default - no automatic updates
        log_message('info', 'SetExistingUsersProfileTracking: No automatic updates applied. Admin must manually update users or uncomment migration code.');
    }

    public function down()
    {
        // Cannot reliably rollback - we don't know which users had NULL before
        log_message('info', 'SetExistingUsersProfileTracking: Rollback not implemented (cannot determine original NULL values)');
    }
}
