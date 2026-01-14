# 📧 Email Change Notification Feature

**Date:** 2026-01-15  
**Feature:** Email notification ketika user mengubah email  
**Status:** ✅ **IMPLEMENTED**

---

## 🎯 Overview

Fitur keamanan yang mengirim notifikasi email otomatis ketika user mengubah alamat email mereka. Email dikirim ke **dua alamat**:
1. **Email lama** - Notifikasi keamanan
2. **Email baru** - Konfirmasi perubahan

---

## ✨ Features

### 🔐 Security Notifications

**Email ke Alamat Lama:**
- ⚠️ Peringatan bahwa email telah diubah
- 📅 Waktu perubahan
- 📧 Email lama dan baru
- 🌐 IP Address yang melakukan perubahan
- 🚨 Instruksi jika user tidak melakukan perubahan

**Email ke Alamat Baru:**
- ✅ Konfirmasi perubahan berhasil
- 📅 Waktu perubahan
- 📧 Email lama dan baru
- 🌐 IP Address yang melakukan perubahan
- 💡 Tips keamanan akun

---

## 🔄 How It Works

### Flow Diagram

```
User mengubah email di profile
        ↓
Controller validates
        ↓
Database update (skipValidation)
        ↓
Check if email actually changed
        ↓
    YES → Send notifications
        ↓
    ├─→ Send to OLD email (security alert)
    │   Subject: "Email Akun Anda Telah Diubah"
    │   Content: Warning + change details
    │
    └─→ Send to NEW email (confirmation)
        Subject: "Konfirmasi Perubahan Email"
        Content: Confirmation + security tips
```

### Logic Flow

```php
if (email in updateData AND email different from current) {
    // Email actually changed
    
    1. Update session
    2. Get old and new email
    3. Send to old email (if exists)
    4. Send to new email
    5. Log results
    
} else {
    // Email not changed or same value
    // Just update session, no notification
}
```

---

## 📋 Implementation Details

### Files Created/Modified

#### 1. Email Template
**File:** `app/Views/emails/email_changed.php`

**Features:**
- Extends base email layout
- Shows change details (time, old/new email, IP)
- Different messages for old vs new email
- Security warnings for old email
- Security tips for new email
- Contact info for suspicious activity

**Variables:**
- `$username` - User's username
- `$oldEmail` - Previous email address
- `$newEmail` - New email address
- `$changeTime` - When the change occurred
- `$ipAddress` - IP that made the change
- `$isOldEmail` - Boolean flag (old or new email)

#### 2. Helper Function
**File:** `app/Helpers/email_helper.php`

**Function:** `send_email_change_notification()`

**Parameters:**
```php
send_email_change_notification(
    string $email,      // Recipient (old or new)
    string $username,   // User's username
    string $oldEmail,   // Old email address
    string $newEmail,   // New email address
    bool $isOldEmail    // true = old email, false = new email
)
```

**Returns:** `bool` - Success status

**Features:**
- Automatically gets IP address
- Formats change time
- Different subject for old vs new email
- Uses email template view
- Returns success/failure

#### 3. Controller Logic
**File:** `app/Controllers/ProfileController.php`

**Added in:** `update()` method after database update

**Logic:**
```php
// Check if email actually changed
if (isset($updateData['email']) && $updateData['email'] !== $userData['email']) {
    
    // Update session
    session()->set('email', $updateData['email']);
    
    // Send notifications
    helper('email');
    
    // Send to old email (security)
    if (!empty($oldEmail)) {
        send_email_change_notification($oldEmail, $username, $oldEmail, $newEmail, true);
    }
    
    // Send to new email (confirmation)
    send_email_change_notification($newEmail, $username, $oldEmail, $newEmail, false);
}
```

**Logging:**
- Success/failure for old email notification
- Success/failure for new email notification
- Email change detection

---

## 📧 Email Content

### Email to OLD Address

**Subject:** "SIMACCA - Email Akun Anda Telah Diubah"

**Content:**
```
Email Anda Telah Diubah 📧

Halo, [username]!

Kami ingin memberitahukan bahwa email akun SIMACCA Anda telah berhasil diubah.

┌─────────────────────────────────────────┐
│ Waktu Perubahan: 15 January 2026 12:00 │
│ Email Lama: old@example.com             │
│ Email Baru: new@example.com             │
│ IP Address: 192.168.1.1                 │
└─────────────────────────────────────────┘

⚠️ PENTING:
Email ini dikirim ke alamat email lama Anda sebagai 
pemberitahuan keamanan. Jika Anda tidak melakukan 
perubahan ini, segera hubungi administrator untuk 
mengamankan akun Anda.

Tips Keamanan Akun:
• 🔐 Gunakan password yang kuat dan unik
• 🔄 Perbarui password secara berkala
• 🚫 Jangan bagikan informasi login Anda
• 👁️ Periksa aktivitas akun secara rutin
• 📧 Pastikan email Anda aman

Jika Anda tidak melakukan perubahan ini atau 
mencurigai aktivitas yang tidak sah, segera hubungi 
administrator sistem di admin@smkn8bone.sch.id
```

### Email to NEW Address

**Subject:** "SIMACCA - Konfirmasi Perubahan Email"

**Content:**
```
Email Anda Telah Diubah 📧

Halo, [username]!

Kami ingin memberitahukan bahwa email akun SIMACCA Anda telah berhasil diubah.

┌─────────────────────────────────────────┐
│ Waktu Perubahan: 15 January 2026 12:00 │
│ Email Lama: old@example.com             │
│ Email Baru: new@example.com             │
│ IP Address: 192.168.1.1                 │
└─────────────────────────────────────────┘

Email ini dikirim ke alamat email baru Anda sebagai 
konfirmasi bahwa perubahan telah berhasil.

Tips Keamanan Akun:
• 🔐 Gunakan password yang kuat dan unik
• 🔄 Perbarui password secara berkala
• 🚫 Jangan bagikan informasi login Anda
• 👁️ Periksa aktivitas akun secara rutin
• 📧 Pastikan email Anda aman

Jika Anda tidak melakukan perubahan ini atau 
mencurigai aktivitas yang tidak sah, segera hubungi 
administrator sistem di admin@smkn8bone.sch.id
```

---

## 🔒 Security Features

### Why Two Emails?

**1. Old Email (Security Alert)**
- ✅ User still has access to old email
- ✅ Can detect unauthorized changes
- ✅ Can contact admin if suspicious
- ✅ Provides audit trail

**2. New Email (Confirmation)**
- ✅ Confirms user has access to new email
- ✅ Verifies change completed successfully
- ✅ Provides record of change
- ✅ Security tips in case of compromise

### Information Included

**Change Details:**
- ⏰ **Timestamp** - When the change occurred
- 📧 **Old Email** - What it was changed from
- 📧 **New Email** - What it was changed to
- 🌐 **IP Address** - Where the change came from

**Why IP Address?**
- Helps detect unauthorized access
- Provides location context
- Useful for security audits
- Helps users identify if it was them

---

## 🧪 Testing

### Test Scenario 1: Email Change

**Steps:**
1. Login as guru1
2. Go to profile
3. Change email: `old@example.com` → `new@example.com`
4. Click "Simpan Perubahan"
5. Check both inboxes

**Expected:**
- ✅ Profile updated successfully
- ✅ Email to `old@example.com` received (security alert)
- ✅ Email to `new@example.com` received (confirmation)
- ✅ Both emails contain correct details
- ✅ Logs show both emails sent

### Test Scenario 2: Email Not Changed

**Steps:**
1. Login as guru1
2. Go to profile
3. Keep email same: `test@example.com`
4. Click "Simpan Perubahan"

**Expected:**
- ✅ Profile updated successfully
- ❌ No email sent (email didn't change)
- ✅ Logs show "no change detected"

### Test Scenario 3: No Old Email

**Steps:**
1. User has no old email (NULL)
2. Set new email: `new@example.com`
3. Click "Simpan Perubahan"

**Expected:**
- ✅ Profile updated successfully
- ❌ No email to old address (doesn't exist)
- ✅ Email to `new@example.com` received
- ✅ Logs show only new email sent

---

## 📊 Log Messages

### Success Logs
```
INFO - ProfileController update - Email change notification sent to old email: old@example.com
INFO - ProfileController update - Email change notification sent to new email: new@example.com
```

### Error Logs
```
ERROR - ProfileController update - Failed to send notification to old email: old@example.com
ERROR - ProfileController update - Failed to send notification to new email: new@example.com
```

### No Change Logs
```
INFO - ProfileController update - Session email updated (no change detected)
```

---

## 🎯 Use Cases

### Use Case 1: Normal Email Update
**User Action:** User legitimately changes their email  
**System Response:**
- ✅ Updates database
- ✅ Sends notification to old email
- ✅ Sends confirmation to new email
- ✅ User receives both emails
- ✅ User knows change was successful

### Use Case 2: Account Compromise
**Scenario:** Attacker gains access and changes email  
**Protection:**
- ✅ Legitimate user receives alert at old email
- ✅ Alert includes IP address of attacker
- ✅ User can contact admin immediately
- ✅ Admin can investigate and secure account
- ✅ Attacker can't prevent notification to old email

### Use Case 3: Typo in New Email
**Scenario:** User types wrong new email  
**Protection:**
- ✅ Confirmation sent to wrong email
- ✅ User doesn't receive it → realizes mistake
- ✅ User can change again to correct email
- ✅ Each change notifies old email for security

---

## 🔧 Configuration

### Email Settings Required

Ensure email is configured in `.env`:
```env
email.fromEmail = noreply@smkn8bone.sch.id
email.fromName = 'SIMACCA - SMK Negeri 8 Bone'
email.protocol = smtp
email.SMTPHost = smtp.gmail.com
email.SMTPUser = your-email@gmail.com
email.SMTPPass = your-app-password
email.SMTPPort = 587
email.SMTPCrypto = tls
email.mailType = html
```

### Test Email Function

```bash
# Test that email works
php spark email:test your-email@example.com
```

---

## 💡 Best Practices

### For Users

**When Changing Email:**
1. ✅ Use a valid email you can access
2. ✅ Check both old and new inboxes
3. ✅ Verify the change details match
4. ✅ Report any suspicious activity

**Security Tips:**
1. 🔐 Use unique passwords
2. 📧 Keep email secure
3. 🔍 Monitor account activity
4. 🚨 Report unauthorized changes

### For Administrators

**Monitoring:**
1. 📊 Check logs for email failures
2. 🔍 Monitor for suspicious IP addresses
3. 📧 Verify email configuration works
4. 🚨 Investigate user reports quickly

**Email Configuration:**
1. ✅ Use reliable SMTP provider
2. ✅ Configure SPF/DKIM for deliverability
3. ✅ Test email regularly
4. ✅ Monitor email logs

---

## 🐛 Troubleshooting

### Email Not Received

**Check:**
1. Spam/junk folder
2. Email configuration in `.env`
3. Logs for send failures
4. SMTP credentials

**Commands:**
```bash
# Check logs
Get-Content writable/logs/log-$(Get-Date -Format 'yyyy-MM-dd').log -Tail 50 | Select-String 'email'

# Test email config
php spark email:test your-email@example.com
```

### Both Emails Not Sent

**Possible Causes:**
- SMTP configuration error
- Invalid email addresses
- Email helper not loaded
- Network issues

**Solution:**
1. Check email configuration
2. Verify email helper auto-loaded
3. Check network connectivity
4. Review error logs

---

## 📚 API Reference

### send_email_change_notification()

**Function Signature:**
```php
function send_email_change_notification(
    string $email,
    string $username,
    string $oldEmail,
    string $newEmail,
    bool $isOldEmail = false
): bool
```

**Parameters:**
- `$email` (string) - Recipient email address (old or new)
- `$username` (string) - User's username for personalization
- `$oldEmail` (string) - Previous email address
- `$newEmail` (string) - New email address
- `$isOldEmail` (bool) - true if sending to old email (security alert), false if new email (confirmation)

**Returns:**
- `bool` - true if email sent successfully, false otherwise

**Example Usage:**
```php
// Send to old email (security notification)
helper('email');
$result = send_email_change_notification(
    'old@example.com',      // Send to old email
    'john_doe',             // Username
    'old@example.com',      // Old email
    'new@example.com',      // New email
    true                    // This is old email (security alert)
);

// Send to new email (confirmation)
$result = send_email_change_notification(
    'new@example.com',      // Send to new email
    'john_doe',             // Username
    'old@example.com',      // Old email
    'new@example.com',      // New email
    false                   // This is new email (confirmation)
);
```

---

## ✅ Summary

**Feature:** Email change notification system

**Purpose:** Security and confirmation when users change their email

**Benefits:**
- ✅ Enhanced account security
- ✅ User confirmation of changes
- ✅ Detection of unauthorized changes
- ✅ Audit trail for email changes
- ✅ Professional user experience

**Files:**
- `app/Views/emails/email_changed.php` (template)
- `app/Helpers/email_helper.php` (function)
- `app/Controllers/ProfileController.php` (logic)

**Testing:** Ready for production ✅

---

**Feature Version:** 1.0  
**Last Updated:** 2026-01-15  
**Status:** ✅ IMPLEMENTED & DOCUMENTED
