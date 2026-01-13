<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Generate Encryption Key
 * 
 * Generate a secure encryption key for production use
 * Usage: php spark key:generate
 */
class KeyGenerate extends BaseCommand
{
    protected $group       = 'Setup';
    protected $name        = 'key:generate';
    protected $description = 'Generate a new encryption key for the application';

    public function run(array $params)
    {
        CLI::write('═══════════════════════════════════════', 'cyan');
        CLI::write('  ENCRYPTION KEY GENERATOR', 'yellow');
        CLI::write('═══════════════════════════════════════', 'cyan');
        CLI::newLine();

        try {
            // Generate a secure random key
            $key = bin2hex(random_bytes(32)); // 64 character hex string
            
            CLI::write('New encryption key generated:', 'green');
            CLI::newLine();
            CLI::write($key, 'white');
            CLI::newLine(2);
            
            CLI::write('Add this to your .env file:', 'yellow');
            CLI::write('encryption.key = ' . $key, 'cyan');
            CLI::newLine();
            
            CLI::write('Or using hex2bin format:', 'yellow');
            CLI::write('encryption.key = hex2bin(\'' . $key . '\')', 'cyan');
            CLI::newLine(2);
            
            // Option to automatically update .env file
            if (CLI::prompt('Do you want to update .env file automatically?', ['y', 'n']) === 'y') {
                $envPath = ROOTPATH . '.env';
                
                if (!file_exists($envPath)) {
                    CLI::error('.env file not found. Please create it first.');
                    return;
                }
                
                $envContent = file_get_contents($envPath);
                
                // Check if encryption.key already exists
                if (preg_match('/^encryption\.key\s*=\s*.*/m', $envContent)) {
                    // Replace existing key
                    $envContent = preg_replace(
                        '/^encryption\.key\s*=\s*.*/m',
                        'encryption.key = ' . $key,
                        $envContent
                    );
                    CLI::write('✓ Updated existing encryption key in .env', 'green');
                } else {
                    // Add new key
                    $envContent .= "\n# Encryption Key\nencryption.key = " . $key . "\n";
                    CLI::write('✓ Added encryption key to .env', 'green');
                }
                
                file_put_contents($envPath, $envContent);
                CLI::newLine();
                CLI::write('✓ .env file updated successfully!', 'green');
            } else {
                CLI::write('Please manually add the key to your .env file.', 'yellow');
            }
            
            CLI::newLine();
            CLI::write('═══════════════════════════════════════', 'cyan');
            CLI::write('⚠️  IMPORTANT SECURITY NOTES:', 'red');
            CLI::write('═══════════════════════════════════════', 'cyan');
            CLI::write('1. Keep this key SECRET and SECURE', 'yellow');
            CLI::write('2. Do NOT commit .env to version control', 'yellow');
            CLI::write('3. Use different keys for dev and production', 'yellow');
            CLI::write('4. Backup this key securely', 'yellow');
            CLI::write('═══════════════════════════════════════', 'cyan');
            CLI::newLine();
            
        } catch (\Exception $e) {
            CLI::error('Failed to generate encryption key: ' . $e->getMessage());
        }
    }
}
