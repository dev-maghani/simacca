<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UserModel;

/**
 * Command: Set Profile Completion
 * 
 * CLI command to set profile completion tracking fields for existing users
 * 
 * Usage:
 *   php spark profile:complete all                    # Mark all users as complete
 *   php spark profile:complete user <user_id>         # Mark specific user as complete
 *   php spark profile:complete role <role>            # Mark all users of a role as complete
 *   php spark profile:complete smart                  # Smart update based on existing data
 * 
 * @package App\Commands
 * @author SIMACCA Team
 * @version 1.0.0
 */
class SetProfileCompletion extends BaseCommand
{
    protected $group       = 'Profile';
    protected $name        = 'profile:complete';
    protected $description = 'Set profile completion tracking for existing users';
    protected $usage       = 'profile:complete [all|user|role|smart] [value]';
    protected $arguments   = [
        'mode' => 'Mode: all, user, role, or smart',
        'value' => '[Optional] User ID for "user" mode, or role name for "role" mode'
    ];

    public function run(array $params)
    {
        $mode = $params[0] ?? null;
        $value = $params[1] ?? null;
        
        if (!$mode) {
            CLI::error('Mode required: all, user, role, or smart');
            CLI::write('Usage: php spark profile:complete [all|user|role|smart] [value]');
            return;
        }

        $userModel = new UserModel();
        $now = date('Y-m-d H:i:s');

        switch ($mode) {
            case 'all':
                $this->markAllComplete($userModel, $now);
                break;
                
            case 'user':
                if (!$value) {
                    CLI::error('User ID required for "user" mode');
                    return;
                }
                $this->markUserComplete($userModel, $value, $now);
                break;
                
            case 'role':
                if (!$value) {
                    CLI::error('Role name required for "role" mode');
                    return;
                }
                $this->markRoleComplete($userModel, $value, $now);
                break;
                
            case 'smart':
                $this->smartUpdate($userModel, $now);
                break;
                
            default:
                CLI::error('Invalid mode: ' . $mode);
                CLI::write('Valid modes: all, user, role, smart');
                return;
        }
    }

    /**
     * Mark all users as having completed profile
     */
    private function markAllComplete($userModel, $now)
    {
        CLI::write('Marking ALL users as profile complete...', 'yellow');
        
        $db = \Config\Database::connect();
        $result = $db->query("
            UPDATE users 
            SET password_changed_at = ?,
                email_changed_at = ?,
                profile_photo_uploaded_at = ?
            WHERE password_changed_at IS NULL
               OR email_changed_at IS NULL
               OR profile_photo_uploaded_at IS NULL
        ", [$now, $now, $now]);
        
        $affected = $db->affectedRows();
        CLI::write('✅ Updated ' . $affected . ' users', 'green');
    }

    /**
     * Mark specific user as having completed profile
     */
    private function markUserComplete($userModel, $userId, $now)
    {
        $user = $userModel->find($userId);
        
        if (!$user) {
            CLI::error('User ID ' . $userId . ' not found');
            return;
        }
        
        CLI::write('Marking user #' . $userId . ' (' . $user['username'] . ') as profile complete...', 'yellow');
        
        $updateData = [];
        if (empty($user['password_changed_at'])) {
            $updateData['password_changed_at'] = $now;
        }
        if (empty($user['email_changed_at'])) {
            $updateData['email_changed_at'] = $now;
        }
        if (empty($user['profile_photo_uploaded_at'])) {
            $updateData['profile_photo_uploaded_at'] = $now;
        }
        
        if (empty($updateData)) {
            CLI::write('User already has complete profile tracking', 'yellow');
            return;
        }
        
        $userModel->update($userId, $updateData);
        CLI::write('✅ User profile marked as complete', 'green');
        CLI::write('   Updated fields: ' . implode(', ', array_keys($updateData)));
    }

    /**
     * Mark all users of a specific role as having completed profile
     */
    private function markRoleComplete($userModel, $role, $now)
    {
        $validRoles = ['admin', 'guru_mapel', 'wali_kelas', 'siswa'];
        
        if (!in_array($role, $validRoles)) {
            CLI::error('Invalid role: ' . $role);
            CLI::write('Valid roles: ' . implode(', ', $validRoles));
            return;
        }
        
        CLI::write('Marking all users with role "' . $role . '" as profile complete...', 'yellow');
        
        $db = \Config\Database::connect();
        $result = $db->query("
            UPDATE users 
            SET password_changed_at = ?,
                email_changed_at = ?,
                profile_photo_uploaded_at = ?
            WHERE role = ?
              AND (password_changed_at IS NULL
                   OR email_changed_at IS NULL
                   OR profile_photo_uploaded_at IS NULL)
        ", [$now, $now, $now, $role]);
        
        $affected = $db->affectedRows();
        CLI::write('✅ Updated ' . $affected . ' users with role "' . $role . '"', 'green');
    }

    /**
     * Smart update - only set fields that have actual data
     */
    private function smartUpdate($userModel, $now)
    {
        CLI::write('Running SMART update (based on existing data)...', 'yellow');
        CLI::newLine();
        
        $db = \Config\Database::connect();
        
        // 1. Set password_changed_at for all users (they all have passwords)
        CLI::write('1. Setting password_changed_at for all users...', 'cyan');
        $result = $db->query("
            UPDATE users 
            SET password_changed_at = ?
            WHERE password_changed_at IS NULL
        ", [$now]);
        CLI::write('   ✅ Updated ' . $db->affectedRows() . ' users', 'green');
        
        // 2. Set email_changed_at only for users who have email
        CLI::write('2. Setting email_changed_at for users with email...', 'cyan');
        $result = $db->query("
            UPDATE users 
            SET email_changed_at = ?
            WHERE email_changed_at IS NULL
              AND email IS NOT NULL
              AND email != ''
        ", [$now]);
        CLI::write('   ✅ Updated ' . $db->affectedRows() . ' users', 'green');
        
        // 3. Set profile_photo_uploaded_at only for users who have profile photo
        CLI::write('3. Setting profile_photo_uploaded_at for users with photo...', 'cyan');
        $result = $db->query("
            UPDATE users 
            SET profile_photo_uploaded_at = ?
            WHERE profile_photo_uploaded_at IS NULL
              AND profile_photo IS NOT NULL
              AND profile_photo != ''
        ", [$now]);
        CLI::write('   ✅ Updated ' . $db->affectedRows() . ' users', 'green');
        
        CLI::newLine();
        CLI::write('✅ Smart update complete!', 'green');
        CLI::write('Note: Users without email or profile photo will still need to complete their profile.', 'yellow');
    }
}
