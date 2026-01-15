# Profile First Edit Email Notification Fix

## Problem Identified
User yang pertama kali edit profile masih menerima **2 email berbeda**:
1. ❌ **Email Konfirmasi Perubahan Email** - "SIMACCA - Konfirmasi Perubahan Email"
2. ❌ **Password Anda Berhasil Diubah** - "SIMACCA - Password Anda Berhasil Diubah"

Padahal seharusnya hanya menerima **1 email welcome** yang berisi username, email, dan password baru.

## Root Cause Analysis

### Previous Logic Issue
```php
// Check if profile is complete (requires photo upload too)
$isFirstProfileCompletion = empty($userData['password_changed_at']) 
    && empty($userData['email_changed_at']) 
    && empty($userData['profile_photo_uploaded_at']); // ❌ PROBLEM HERE

$profileNowComplete = !empty($updatedUser['password_changed_at']) 
    && !empty($updatedUser['email_changed_at']) 
    && !empty($updatedUser['profile_photo_uploaded_at']); // ❌ PROBLEM HERE

// Send welcome email
if ($isFirstProfileCompletion && $profileNowComplete) {
    send_welcome_email(...);
}
// Otherwise send regular notifications
else {
    send_email_change_notification(...); // ⚠️ Executed even on first edit!
    send_password_changed_notification(...); // ⚠️ Executed even on first edit!
}
```

**Problem:**
- Logic checked for `profile_photo_uploaded_at` (foto profil)
- Saat user edit profile pertama kali (isi email + password) tapi belum upload foto
- `$profileNowComplete` = `false` karena foto belum diupload
- Masuk ke `else` block → mengirim email change + password change notification
- Hasil: User dapat 2 email yang salah, bukan welcome email

## Solution

### Updated Logic
```php
// Only check password and email (ignore photo upload status)
$isFirstProfileEdit = empty($userData['password_changed_at']) 
    && empty($userData['email_changed_at']); // ✅ Only check these 2

// Send welcome email if first time editing (with password change)
if ($isFirstProfileEdit && $isPasswordChanged && !empty($updatedUser['email'])) {
    send_welcome_email(..., $plainPassword); // ✅ Welcome email only
}
// Otherwise send regular notifications
else {
    // Only send if email actually changed
    if (isset($updateData['email']) && $updateData['email'] !== $userData['email']) {
        send_email_change_notification(...);
    }
    
    // Only send if password changed
    if ($isPasswordChanged && $plainPassword) {
        send_password_changed_notification(...);
    }
}
```

**Key Changes:**
1. ✅ **Simplified Check**: Hanya cek `password_changed_at` dan `email_changed_at` (tidak cek foto)
2. ✅ **Added Password Requirement**: Harus ada perubahan password untuk trigger welcome email
3. ✅ **Clear Separation**: Welcome email ATAU regular notifications, tidak keduanya
4. ✅ **Better Logging**: Tambah log untuk debugging

## Changes Made

### File: `app/Controllers/ProfileController.php`

#### 1. Simplified First Edit Detection
**Before:**
```php
$isFirstProfileCompletion = empty($userData['password_changed_at']) 
    && empty($userData['email_changed_at']) 
    && empty($userData['profile_photo_uploaded_at']);
```

**After:**
```php
$isFirstProfileEdit = empty($userData['password_changed_at']) 
    && empty($userData['email_changed_at']);
```

#### 2. Updated Condition for Welcome Email
**Before:**
```php
if ($isFirstProfileCompletion && $profileNowComplete && !empty($updatedUser['email'])) {
    send_welcome_email(...);
}
```

**After:**
```php
if ($isFirstProfileEdit && $isPasswordChanged && !empty($updatedUser['email'])) {
    send_welcome_email(..., $plainPassword);
}
```

#### 3. Added Comprehensive Logging
```php
log_message('info', 'ProfileController update - Is first profile edit: ' . ($isFirstProfileEdit ? 'YES' : 'NO'));
log_message('info', 'ProfileController update - Old password_changed_at: ' . ($userData['password_changed_at'] ?? 'NULL'));
log_message('info', 'ProfileController update - Old email_changed_at: ' . ($userData['email_changed_at'] ?? 'NULL'));
log_message('info', 'ProfileController update - Sending welcome email (first profile edit)');
log_message('info', 'ProfileController update - Sending regular update notifications');
```

#### 4. Updated Success Message
**Before:**
```php
if ($isFirstProfileCompletion && $profileNowComplete) {
    session()->setFlashdata('success', 'Selamat datang di SIMACCA! 🎉 Profil Anda telah lengkap...');
}
```

**After:**
```php
if ($isFirstProfileEdit && $isPasswordChanged) {
    session()->setFlashdata('success', 'Selamat datang di SIMACCA! 🎉 Profil Anda telah diperbarui. Cek email Anda untuk informasi akun lengkap.');
}
```

## Email Flow Comparison

### Before Fix (WRONG):

#### First Time Edit Profile (email + password, NO photo yet)
1. ❌ Email change notification sent
2. ❌ Password change notification sent
3. ❌ Total: 2 wrong emails received

**Why it happened:**
- `$profileNowComplete` was `false` (no photo uploaded)
- Went to `else` block
- Sent regular update notifications

### After Fix (CORRECT):

#### First Time Edit Profile (email + password)
1. ✅ **ONLY** Welcome email sent (with username, email, password)
2. ✅ Total: 1 correct email received

**Why it works:**
- `$isFirstProfileEdit` checks only password and email timestamps (ignore photo)
- `$isPasswordChanged` ensures password was updated
- Sends welcome email in `if` block
- Regular notifications skipped

#### Subsequent Updates (already edited before)
1. ✅ Email change notification (only if email changed)
2. ✅ Password change notification (only if password changed)
3. ✅ Total: Appropriate notifications only

## User Experience Flow

### First Time Profile Edit

1. **User logs in** with temporary password
2. **ProfileCompletionFilter** detects incomplete profile
3. **User redirected** to `/profile` with info message
4. **User fills**:
   - Email ✅ (required)
   - Password ✅ (required)
   - Photo upload ⏳ (can be done later)
5. **User clicks** "Simpan Semua Perubahan"
6. **System checks**:
   - `password_changed_at` was NULL → ✅ First edit
   - `email_changed_at` was NULL → ✅ First edit
   - Password is being changed → ✅ Yes
7. **Welcome email sent** with username, email, new password
8. **User redirected** to dashboard
9. **Success message**: "Selamat datang di SIMACCA! 🎉..."
10. ✅ User receives **1 welcome email only**

### Upload Photo Later

1. **User uploads** profile photo
2. **System updates** `profile_photo_uploaded_at`
3. **No email sent** (just flash success message)
4. **Profile now complete** (all 3 timestamps filled)

### Subsequent Updates

1. **User changes email** → Email change notification
2. **User changes password** → Password change notification
3. ✅ Appropriate notifications only, not welcome email

## Technical Details

### Detection Logic
```php
// Check original state (before update)
$isFirstProfileEdit = empty($userData['password_changed_at']) 
    && empty($userData['email_changed_at']);

// This is TRUE when:
// - User never changed password before (password_changed_at is NULL)
// - User never set/changed email before (email_changed_at is NULL)
// - Ignores photo upload status
```

### Welcome Email Trigger
```php
if ($isFirstProfileEdit          // First time editing
    && $isPasswordChanged        // Password is being changed now
    && !empty($updatedUser['email'])) // Email exists
{
    send_welcome_email(..., $plainPassword);
}
```

### Regular Notification Trigger
```php
else {
    // Email change notification
    if (email actually changed) {
        send_email_change_notification();
    }
    
    // Password change notification
    if (password changed) {
        send_password_changed_notification();
    }
}
```

## Benefits

### For Users
- ✅ No confusion - only 1 email on first edit
- ✅ All account info in one place
- ✅ Can upload photo later without triggering more emails
- ✅ Clear welcome experience

### For System
- ✅ Correct email notification flow
- ✅ No duplicate or wrong emails
- ✅ Better logging for debugging
- ✅ Simpler logic (no photo dependency)

### For Admins
- ✅ Easier to track first-time user onboarding
- ✅ Clear logs for troubleshooting
- ✅ Reduced email service load

## Testing Checklist

- [x] Syntax validation passed
- [x] First edit detection logic simplified
- [x] Welcome email only sent on first edit (password + email)
- [x] No email/password notifications on first edit
- [x] Regular notifications work on subsequent updates
- [x] Photo upload doesn't affect email notifications
- [x] Comprehensive logging added
- [x] Success messages updated

## Debugging Guide

### Check if Welcome Email Should Be Sent
Look for these log entries:
```
ProfileController update - Is first profile edit: YES
ProfileController update - Old password_changed_at: NULL
ProfileController update - Old email_changed_at: NULL
ProfileController update - Sending welcome email (first profile edit)
ProfileController update - Welcome email sent to: user@example.com
```

### Check if Regular Notifications Should Be Sent
Look for these log entries:
```
ProfileController update - Is first profile edit: NO
ProfileController update - Sending regular update notifications
ProfileController update - Email changed from: old@example.com to: new@example.com
ProfileController update - Password changed, sending notification
```

## Files Modified

1. ✅ `app/Controllers/ProfileController.php`
   - Changed `$isFirstProfileCompletion` to `$isFirstProfileEdit`
   - Removed photo upload requirement from first edit check
   - Added password change requirement to welcome email trigger
   - Added comprehensive logging
   - Updated success message

## Migration Notes

- No database changes required
- Existing users not affected
- New users will get correct email flow
- Photo upload is independent from email notifications

## Related Documentation
- `PROFILE_UPDATE_SINGLE_BUTTON.md` - Single save button feature
- `PROFILE_COMPLETION_WELCOME_EMAIL.md` - Original welcome email implementation
- `PROFILE_COMPLETION_WELCOME_EMAIL_FIX.md` - Previous attempt to fix

## Conclusion

This fix ensures that users editing their profile for the first time receive **only 1 welcome email** containing their username, email, and new password - regardless of whether they have uploaded a profile photo or not. The photo upload is now independent from email notifications, allowing users to complete their profile in stages without receiving multiple confusing emails.
