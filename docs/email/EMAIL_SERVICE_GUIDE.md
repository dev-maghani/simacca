# 📧 Email Service - Complete Guide

**SIMACCA Email Service Documentation**  
**Version:** 1.5.0  
**Last Updated:** 2026-01-15

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [Quick Start](#quick-start)
3. [Configuration](#configuration)
4. [Gmail Setup](#gmail-setup)
5. [Testing & Verification](#testing--verification)
6. [Email Features](#email-features)
7. [Troubleshooting](#troubleshooting)
8. [Advanced Configuration](#advanced-configuration)

---

## Overview

SIMACCA menggunakan email service untuk:
- 🔐 **Password Reset** - Reset password via email token
- 📧 **Email Change Notifications** - Notifikasi saat email diubah
- 🔑 **Password Change Notifications** - Notifikasi saat password diubah (by admin atau self)
- 👤 **Account Management** - Komunikasi terkait akun user

### Supported Email Providers

| Provider | Protocol | Port | Encryption | Recommended For |
|----------|----------|------|------------|-----------------|
| **Gmail** | SMTP | 587 | TLS | ✅ Production (requires App Password) |
| **Custom SMTP** | SMTP | 587/465 | TLS/SSL | ✅ Production (cPanel/Plesk) |
| **Mailtrap** | SMTP | 2525 | TLS | ✅ Development/Testing |
| **SendGrid/Mailgun** | API | - | - | Enterprise (requires API setup) |

---

## Quick Start

### ⚡ 5-Minute Setup (Gmail)

**Prerequisites:**
- Gmail account with 2-Step Verification enabled
- Access to server `.env` file

**Steps:**

```bash
# 1. Generate Gmail App Password
# Visit: https://myaccount.google.com/apppasswords
# Select: Mail → Other (Custom name) → Generate
# Copy the 16-character password

# 2. Edit .env file
nano .env

# 3. Add/Update email configuration
email.fromEmail = 'noreply@yourschool.sch.id'
email.fromName = 'SIMACCA - Your School Name'
email.SMTPHost = 'smtp.gmail.com'
email.SMTPUser = 'your-email@gmail.com'
email.SMTPPass = 'your-16-char-app-password'
email.SMTPPort = 587
email.SMTPCrypto = 'tls'

# 4. Test email
php spark email:test your-email@gmail.com

# 5. Expected output
# ✓ Email test berhasil dikirim
# Email configuration is working correctly!
```

✅ **Done!** Email service ready in 5 minutes.

📖 **Detailed Gmail setup:** See [Gmail Setup](#gmail-setup) section below.

---

## Configuration

### Environment Variables (.env)

All email configuration stored in `.env` file:

```env
#--------------------------------------------------------------------
# EMAIL Configuration
#--------------------------------------------------------------------

# Sender Information
email.fromEmail = 'noreply@yourschool.sch.id'
email.fromName = 'SIMACCA - Your School Name'

# SMTP Server Settings
email.SMTPHost = 'smtp.gmail.com'
email.SMTPUser = 'your-email@gmail.com'
email.SMTPPass = 'your-app-password'
email.SMTPPort = 587
email.SMTPCrypto = 'tls'
email.SMTPTimeout = 10

# Protocol (usually 'smtp')
email.protocol = 'smtp'

# Character Set
email.charset = 'UTF-8'

# Email Format (html or text)
email.mailType = 'html'

# Word Wrap (0 = no wrap)
email.wordWrap = false
```

### Configuration by Provider

#### Gmail
```env
email.SMTPHost = 'smtp.gmail.com'
email.SMTPUser = 'your-gmail@gmail.com'
email.SMTPPass = 'your-16-char-app-password'  # NOT regular password!
email.SMTPPort = 587
email.SMTPCrypto = 'tls'
```

#### cPanel/Plesk (Custom SMTP)
```env
email.SMTPHost = 'mail.yourdomain.com'
email.SMTPUser = 'noreply@yourdomain.com'
email.SMTPPass = 'your-regular-password'
email.SMTPPort = 587
email.SMTPCrypto = 'tls'
```

#### Mailtrap (Development)
```env
email.SMTPHost = 'smtp.mailtrap.io'
email.SMTPUser = 'your-mailtrap-username'
email.SMTPPass = 'your-mailtrap-password'
email.SMTPPort = 2525
email.SMTPCrypto = 'tls'
```

---

## Gmail Setup

### Why App Password?

Gmail stopped allowing regular passwords for SMTP in 2022. You must use **App Passwords**.

### Step-by-Step Guide

#### Step 1: Enable 2-Step Verification (2 minutes)

1. Visit: https://myaccount.google.com/security
2. Click **"2-Step Verification"**
3. Click **"Get Started"**
4. Follow instructions (verify via SMS/phone)
5. Complete setup

#### Step 2: Generate App Password (1 minute)

1. Visit: https://myaccount.google.com/apppasswords
   - Or: Google Account → Security → 2-Step Verification → App passwords

2. Sign in if prompted

3. Under "Select app and device":
   - **Select app:** Mail
   - **Select device:** Other (Custom name)
   - **Type:** "SIMACCA Email"

4. Click **"Generate"**

5. **Copy the 16-character password**
   - Format: `abcd efgh ijkl mnop`
   - Spaces don't matter
   - You won't see it again!

#### Step 3: Update .env (1 minute)

```env
# BEFORE (regular password - WON'T WORK)
email.SMTPPass = 'your-regular-password'

# AFTER (App Password - WORKS!)
email.SMTPPass = 'abcd efgh ijkl mnop'
```

Save the file.

#### Step 4: Test (30 seconds)

```bash
php spark email:test your-email@gmail.com
```

**Expected Success:**
```
✓ Email test berhasil dikirim ke your-email@gmail.com
Email configuration is working correctly!
```

**If Failed:**
- Check you copied the full 16-character App Password
- Verify no extra spaces in .env
- Make sure 2-Step Verification is enabled
- Try generating a NEW App Password

📖 **Detailed Gmail guide:** [docs/guides/GMAIL_APP_PASSWORD_SETUP.md](../guides/GMAIL_APP_PASSWORD_SETUP.md)

---

## Testing & Verification

### Command Line Tests

#### 1. Email Diagnostics
```bash
php spark email:diagnostics
```

**Output shows:**
- ✓ Configuration status (loaded from .env)
- ✓ SMTP settings (host, port, encryption)
- ✓ Sender information
- ✓ Password status (length check)

#### 2. Send Test Email
```bash
php spark email:test recipient@example.com
```

**Success output:**
```
✓ Email test berhasil dikirim ke recipient@example.com
Email configuration is working correctly!
```

**Failure output:**
```
✗ Gagal mengirim email
Error: [error details]
```

#### 3. Check Logs
```bash
# Linux/Mac - Real-time monitoring
tail -f writable/logs/log-$(date +%Y-%m-%d).log | grep -i email

# Windows PowerShell
Get-Content writable/logs/log-*.log -Tail 50 | Select-String "email"
```

### Manual Testing

1. **Test Password Reset:**
   - Go to `/forgot-password`
   - Enter your email
   - Check inbox for reset link
   - Click link, should redirect to reset page

2. **Test Password Change Notification:**
   - Login as user
   - Change password in profile
   - Check inbox for notification email

3. **Test Email Change Notification:**
   - Login as user
   - Change email in profile
   - Check OLD email for notification
   - Check NEW email for confirmation

---

## Email Features

### 1. Password Reset

**Trigger:** User clicks "Forgot Password"

**Flow:**
1. User enters email at `/forgot-password`
2. System generates secure token (60 chars, unique)
3. Token saved in `password_reset_tokens` table with 1-hour expiry
4. Email sent with reset link
5. User clicks link → redirected to `/reset-password?token=...`
6. User enters new password
7. Token validated and consumed (deleted)

**Email Content:**
- Subject: "Reset Password - SIMACCA"
- Contains: Reset link with token
- Expires: 1 hour
- Template: `app/Views/emails/password_reset.php`

**Security:**
- Token: 60 characters, cryptographically secure
- Expiry: 1 hour (configurable)
- One-time use: Token deleted after use
- No password in email

### 2. Password Change Notifications

#### a) Self Password Change

**Trigger:** User changes own password in profile

**Email sent to:** User's email

**Content:**
- Subject: "Password Anda Telah Diubah"
- Info: Date, time, IP address
- Action: Contact admin if not you
- Template: `app/Views/emails/password_changed_by_self.php`

📖 **Details:** [SELF_PASSWORD_CHANGE_NOTIFICATION.md](SELF_PASSWORD_CHANGE_NOTIFICATION.md)

#### b) Admin Password Change

**Trigger:** Admin changes user password

**Email sent to:** User's email

**Content:**
- Subject: "Password Anda Telah Diubah oleh Administrator"
- Info: Changed by admin, date, time
- Warning: Login with new password
- Template: `app/Views/emails/password_changed_by_admin.php`

📖 **Details:** [ADMIN_PASSWORD_CHANGE_EMAIL_NOTIFICATION.md](ADMIN_PASSWORD_CHANGE_EMAIL_NOTIFICATION.md)

### 3. Email Change Notification

**Trigger:** User changes email address in profile

**Emails sent:**
- **Old email:** Notification that email was changed
- **New email:** Confirmation (future feature)

**Content:**
- Subject: "Email Akun Anda Telah Diubah"
- Info: Old email → New email, date, time
- Action: Contact admin if not you
- Template: `app/Views/emails/email_changed.php`

📖 **Details:** [EMAIL_CHANGE_NOTIFICATION_FEATURE.md](EMAIL_CHANGE_NOTIFICATION_FEATURE.md)

### 4. Email Personalization

All emails include:
- 👤 **User's full name** (not just username)
- 🏫 **School name** in signature
- 🎨 **Professional email template** with layout
- 📱 **Responsive design** (looks good on mobile)
- 🔗 **Action buttons** (styled CTAs)

**Template Structure:**
```
app/Views/emails/
├── email_layout.php      # Base layout (header, footer, styling)
├── password_reset.php    # Password reset content
├── password_changed_by_self.php
├── password_changed_by_admin.php
├── email_changed.php
└── test.php             # Test email template
```

---

## Troubleshooting

### Common Issues

#### 1. "Username and Password not accepted" (Gmail)

**Error:**
```
535-5.7.8 Username and Password not accepted
```

**Cause:** Using regular Gmail password instead of App Password

**Solution:**
1. Enable 2-Step Verification on Gmail account
2. Generate App Password at https://myaccount.google.com/apppasswords
3. Use App Password in `.env` file (NOT regular password)
4. Test: `php spark email:test your@email.com`

📖 **Detailed fix:** See [Gmail Setup](#gmail-setup) section

#### 2. "SMTP connect() failed"

**Error:**
```
SMTP connect() failed
```

**Possible Causes:**
- Wrong SMTP host
- Blocked port (firewall)
- Wrong port number
- SSL/TLS misconfiguration

**Solutions:**

**Check port is open:**
```bash
# Linux/Mac
telnet smtp.gmail.com 587

# Windows PowerShell
Test-NetConnection -ComputerName smtp.gmail.com -Port 587
```

**Try different port/encryption combinations:**
```env
# TLS on port 587 (Recommended)
email.SMTPPort = 587
email.SMTPCrypto = 'tls'

# SSL on port 465 (Alternative)
email.SMTPPort = 465
email.SMTPCrypto = 'ssl'
```

**Check firewall:**
- Allow outbound traffic on port 587/465
- Contact hosting provider if on shared hosting

#### 3. "Could not instantiate mail function"

**Error:**
```
Could not instantiate mail function
```

**Cause:** PHP `mail()` function not available (usually on Windows)

**Solution:** Use SMTP instead of mail protocol

```env
# Change from 'mail' to 'smtp'
email.protocol = 'smtp'  # NOT 'mail'
```

#### 4. Email sent but not received

**Possible Causes:**
- Email in spam folder
- Wrong recipient email
- Email server rejecting sender
- SPF/DKIM not configured

**Solutions:**

1. **Check spam folder** (most common)

2. **Verify sender email:**
```env
# Use real email, not fake
email.fromEmail = 'noreply@yourschool.sch.id'  # Real domain
```

3. **Check logs:**
```bash
tail -f writable/logs/log-*.log | grep -i email
```

4. **Test with different recipient:**
```bash
php spark email:test another-email@example.com
```

5. **Use Mailtrap for testing:**
```env
# Development testing (all emails caught by Mailtrap)
email.SMTPHost = 'smtp.mailtrap.io'
email.SMTPUser = 'your-username'
email.SMTPPass = 'your-password'
email.SMTPPort = 2525
```

#### 5. Slow email sending

**Symptoms:** Email takes 10+ seconds to send

**Causes:**
- SMTP timeout too long
- DNS resolution slow
- Network latency

**Solutions:**

1. **Reduce timeout:**
```env
email.SMTPTimeout = 5  # Default: 10
```

2. **Use IP instead of hostname:**
```env
# Instead of smtp.gmail.com, use IP
email.SMTPHost = '142.250.185.108'  # Gmail IP (example)
```

3. **Check DNS:**
```bash
nslookup smtp.gmail.com
```

#### 6. Token Expired

**Error:** "Token expired or invalid"

**Cause:** Reset link used after 1 hour

**Solution:** Request new password reset

**Change expiry time:**
```php
// app/Models/PasswordResetTokenModel.php
public function createToken($email): string
{
    // Change 3600 to longer time (e.g., 7200 = 2 hours)
    $expiresAt = date('Y-m-d H:i:s', time() + 3600);
}
```

### Debug Mode

Enable debug mode for detailed error messages:

```env
# .env
CI_ENVIRONMENT = development
```

**WARNING:** Never enable in production! Contains sensitive info.

---

## Advanced Configuration

### Custom Email Templates

**Location:** `app/Views/emails/`

**Create custom template:**
```php
<?= $this->extend('emails/email_layout') ?>

<?= $this->section('content') ?>
<h2>Custom Email</h2>
<p>Your custom content here</p>
<?= $this->endsection() ?>
```

**Send custom email:**
```php
$email = \Config\Services::email();
$email->setTo('user@example.com');
$email->setSubject('Custom Email');
$email->setMessage(view('emails/custom_email', $data));
$email->send();
```

### Email Queue (Future)

**Current:** Emails sent synchronously (blocking)

**Future:** Queue emails for background processing
- Faster response time
- Retry failed emails
- Rate limiting
- Priority queuing

**Implementation plan:** TODO.md

### SPF/DKIM Configuration

For better deliverability, configure SPF and DKIM records:

**SPF Record (DNS TXT):**
```
v=spf1 include:_spf.google.com ~all
```

**DKIM:** Configure via Gmail/cPanel

**DMARC Record:**
```
v=DMARC1; p=none; rua=mailto:dmarc@yourdomain.com
```

📖 **Detailed setup:** Contact hosting provider for DNS management

---

## Support & Resources

### Commands Reference

| Command | Description |
|---------|-------------|
| `php spark email:diagnostics` | Check email configuration |
| `php spark email:test <email>` | Send test email |
| `php spark token:cleanup` | Clean expired reset tokens |
| `php spark cache:clear` | Clear application cache |

### Log Files

```bash
# View email logs
writable/logs/log-YYYY-MM-DD.log

# Search for email errors
grep -i "email" writable/logs/*.log
```

### Related Documentation

- [Gmail App Password Setup](../guides/GMAIL_APP_PASSWORD_SETUP.md) - Detailed Gmail setup
- [Email Service Quickstart](../guides/EMAIL_SERVICE_QUICKSTART.md) - Quick setup guide
- [Password Change Notifications](ADMIN_PASSWORD_CHANGE_EMAIL_NOTIFICATION.md) - Admin password change
- [Self Password Change](SELF_PASSWORD_CHANGE_NOTIFICATION.md) - User password change
- [Email Change Notification](EMAIL_CHANGE_NOTIFICATION_FEATURE.md) - Email change notification

### Getting Help

**Issue?**
1. Run diagnostics: `php spark email:diagnostics`
2. Check logs: `writable/logs/`
3. Test email: `php spark email:test`
4. Check troubleshooting section above

**Still stuck?**
- Open GitHub issue with diagnostics output
- Check CodeIgniter 4 Email Library docs
- Contact system administrator

---

## Changelog

### v1.5.0 (2026-01-15)
- ✅ Consolidated email documentation
- ✅ Added comprehensive troubleshooting
- ✅ Improved testing procedures

### v1.4.0 (2026-01-15)
- ✅ Email personalization (full name)
- ✅ Professional email templates
- ✅ Responsive design

### v1.3.0 (2026-01-14)
- ✅ Email change notification
- ✅ Password change notifications (self & admin)

### v1.2.0 (2026-01-13)
- ✅ Password reset via email
- ✅ Token-based security (1-hour expiry)

### v1.0.0 (2026-01-10)
- ✅ Initial email service setup
- ✅ SMTP configuration
- ✅ Basic testing commands

---

**Last Updated:** 2026-01-15  
**Maintained by:** SIMACCA Development Team  
**Version:** 1.5.0
