<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class EmailDiagnostics extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Email';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'email:diagnostics';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Run email configuration diagnostics';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'email:diagnostics';

    /**
     * Run the command
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write('========================================', 'yellow');
        CLI::write('   EMAIL CONFIGURATION DIAGNOSTICS', 'yellow');
        CLI::write('========================================', 'yellow');
        CLI::newLine();

        // Load Email config
        $emailConfig = config('Email');

        // Check From Email
        CLI::write('1. Checking From Email Configuration...', 'cyan');
        if (empty($emailConfig->fromEmail)) {
            CLI::error('   ✗ From Email NOT SET');
            CLI::write('   Fix: Add email.fromEmail to .env file', 'yellow');
        } else {
            CLI::write('   ✓ From Email: ' . $emailConfig->fromEmail, 'green');
        }
        CLI::newLine();

        // Check From Name
        CLI::write('2. Checking From Name Configuration...', 'cyan');
        if (empty($emailConfig->fromName)) {
            CLI::error('   ✗ From Name NOT SET');
            CLI::write('   Fix: Add email.fromName to .env file', 'yellow');
        } else {
            CLI::write('   ✓ From Name: ' . $emailConfig->fromName, 'green');
        }
        CLI::newLine();

        // Check Protocol
        CLI::write('3. Checking Email Protocol...', 'cyan');
        CLI::write('   Protocol: ' . $emailConfig->protocol, 'white');
        if ($emailConfig->protocol === 'smtp') {
            CLI::write('   ✓ Using SMTP (Recommended)', 'green');
        } else {
            CLI::write('   ⚠ Using ' . $emailConfig->protocol . ' (SMTP recommended for production)', 'yellow');
        }
        CLI::newLine();

        // Check SMTP Configuration
        if ($emailConfig->protocol === 'smtp') {
            CLI::write('4. Checking SMTP Configuration...', 'cyan');
            
            // SMTP Host
            if (empty($emailConfig->SMTPHost)) {
                CLI::error('   ✗ SMTP Host NOT SET');
                CLI::write('   Fix: Add email.SMTPHost to .env file', 'yellow');
            } else {
                CLI::write('   ✓ SMTP Host: ' . $emailConfig->SMTPHost, 'green');
            }
            
            // SMTP User
            if (empty($emailConfig->SMTPUser)) {
                CLI::error('   ✗ SMTP User NOT SET');
                CLI::write('   Fix: Add email.SMTPUser to .env file', 'yellow');
            } else {
                CLI::write('   ✓ SMTP User: ' . $emailConfig->SMTPUser, 'green');
            }
            
            // SMTP Password
            if (empty($emailConfig->SMTPPass)) {
                CLI::error('   ✗ SMTP Password NOT SET');
                CLI::write('   Fix: Add email.SMTPPass to .env file', 'yellow');
                CLI::newLine();
                CLI::write('   For Gmail: Use App Password (not regular password)', 'yellow');
                CLI::write('   Guide: https://myaccount.google.com/apppasswords', 'yellow');
            } else {
                $passLength = strlen($emailConfig->SMTPPass);
                CLI::write('   ✓ SMTP Password: SET (' . $passLength . ' characters)', 'green');
                
                // Check if it might be a Gmail App Password
                if ($passLength === 16) {
                    CLI::write('   ✓ Password length matches Gmail App Password format', 'green');
                } elseif (strpos($emailConfig->SMTPHost, 'gmail.com') !== false) {
                    CLI::write('   ⚠ Gmail detected but password length is ' . $passLength . ' (App Password is 16 chars)', 'yellow');
                    CLI::write('   You may need to use Gmail App Password', 'yellow');
                }
            }
            
            // SMTP Port
            CLI::write('   Port: ' . $emailConfig->SMTPPort, 'white');
            if ($emailConfig->SMTPPort == 587) {
                CLI::write('   ✓ Using port 587 (TLS - Recommended)', 'green');
            } elseif ($emailConfig->SMTPPort == 465) {
                CLI::write('   ✓ Using port 465 (SSL)', 'green');
            } else {
                CLI::write('   ⚠ Using port ' . $emailConfig->SMTPPort . ' (Common: 587 or 465)', 'yellow');
            }
            
            // SMTP Crypto
            CLI::write('   Encryption: ' . $emailConfig->SMTPCrypto, 'white');
            if (in_array($emailConfig->SMTPCrypto, ['tls', 'ssl'])) {
                CLI::write('   ✓ Using encryption (Good)', 'green');
            } else {
                CLI::write('   ⚠ No encryption (Not recommended for production)', 'yellow');
            }
            
            CLI::newLine();
        }

        // Check Mail Type
        CLI::write('5. Checking Mail Type...', 'cyan');
        CLI::write('   Mail Type: ' . $emailConfig->mailType, 'white');
        if ($emailConfig->mailType === 'html') {
            CLI::write('   ✓ Using HTML (Recommended for templates)', 'green');
        } else {
            CLI::write('   Using plain text', 'white');
        }
        CLI::newLine();

        // Summary
        CLI::write('========================================', 'yellow');
        CLI::write('   DIAGNOSTIC SUMMARY', 'yellow');
        CLI::write('========================================', 'yellow');
        CLI::newLine();

        $issues = [];
        $warnings = [];

        if (empty($emailConfig->fromEmail)) {
            $issues[] = 'From Email not configured';
        }
        if (empty($emailConfig->fromName)) {
            $issues[] = 'From Name not configured';
        }
        if ($emailConfig->protocol === 'smtp') {
            if (empty($emailConfig->SMTPHost)) {
                $issues[] = 'SMTP Host not configured';
            }
            if (empty($emailConfig->SMTPUser)) {
                $issues[] = 'SMTP User not configured';
            }
            if (empty($emailConfig->SMTPPass)) {
                $issues[] = 'SMTP Password not configured';
            } else {
                $passLength = strlen($emailConfig->SMTPPass);
                if (strpos($emailConfig->SMTPHost, 'gmail.com') !== false && $passLength !== 16) {
                    $warnings[] = 'Gmail detected but password may not be an App Password';
                }
            }
        }

        if (empty($issues)) {
            CLI::write('✓ All required configurations are set!', 'green');
        } else {
            CLI::error('✗ Found ' . count($issues) . ' issue(s):');
            foreach ($issues as $issue) {
                CLI::write('  • ' . $issue, 'red');
            }
        }
        
        CLI::newLine();

        if (!empty($warnings)) {
            CLI::write('⚠ Warnings:', 'yellow');
            foreach ($warnings as $warning) {
                CLI::write('  • ' . $warning, 'yellow');
            }
            CLI::newLine();
        }

        // Recommendations
        CLI::write('========================================', 'yellow');
        CLI::write('   RECOMMENDATIONS', 'yellow');
        CLI::write('========================================', 'yellow');
        CLI::newLine();

        if (!empty($issues)) {
            CLI::write('1. Fix configuration issues in .env file', 'cyan');
            CLI::write('2. Run diagnostics again: php spark email:diagnostics', 'cyan');
            CLI::write('3. Test email: php spark email:test your-email@example.com', 'cyan');
        } else {
            CLI::write('Configuration looks good! Next steps:', 'green');
            CLI::newLine();
            CLI::write('1. Test email sending:', 'cyan');
            CLI::write('   php spark email:test your-email@example.com', 'white');
            CLI::newLine();
            CLI::write('2. If using Gmail and getting authentication errors:', 'cyan');
            CLI::write('   • Enable 2-Step Verification', 'white');
            CLI::write('   • Generate App Password: https://myaccount.google.com/apppasswords', 'white');
            CLI::write('   • Use App Password (not regular password)', 'white');
            CLI::write('   • See: GMAIL_APP_PASSWORD_SETUP.md', 'white');
            CLI::newLine();
            CLI::write('3. Check logs if issues persist:', 'cyan');
            CLI::write('   tail -f writable/logs/log-*.log', 'white');
        }

        CLI::newLine();
        CLI::write('========================================', 'yellow');
        CLI::write('Diagnostics complete!', 'green');
        CLI::write('========================================', 'yellow');
    }
}
