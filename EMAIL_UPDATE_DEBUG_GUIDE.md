# 🔍 Email Update Debug Guide

**Date:** 2026-01-15  
**Issue:** Verifying if email updates save to database  
**Status:** 🔧 Debug logging added

---

## 📋 What Was Done

### 1. Code Review ✅
- ✅ Checked `ProfileController::update()` logic
- ✅ Verified `email` is in `UserModel::$allowedFields`
- ✅ Confirmed update logic separates password-only vs profile updates
- ✅ Code looks correct

### 2. Added Debug Logging ✅
Added detailed logging to `ProfileController::update()` method to track:
- User ID being updated
- Data being sent to database
- Whether it's a password-only update
- Database update success/failure
- Actual email value saved in database (verified by re-fetching)
- Session update status

---

## 🧪 How to Test Email Update

### Step 1: Login to Profile Page
1. Go to: `http://your-site/profile`
2. Login with your credentials

### Step 2: Update Email
1. Find the "Edit Profil" section
2. Change the email field to a new email (e.g., `newemail@example.com`)
3. Click **"Simpan Perubahan"**
4. You should see: "Profil updated! Looking good 😎✨"

### Step 3: Check Logs

**On Windows PowerShell:**
```powershell
Get-Content writable/logs/log-$(Get-Date -Format 'yyyy-MM-dd').log -Tail 100 | Select-String 'ProfileController'
```

**On Linux/Mac:**
```bash
tail -100 writable/logs/log-$(date +%Y-%m-%d).log | grep ProfileController
```

### Step 4: Analyze Log Output

You should see logs like this:

**✅ SUCCESS - Email saved correctly:**
```
INFO - 2026-01-15 12:00:00 --> ProfileController update - User ID: 123
INFO - 2026-01-15 12:00:00 --> ProfileController update - Update data: {"username":"john_doe","email":"newemail@example.com"}
INFO - 2026-01-15 12:00:00 --> ProfileController update - Is password change only: NO
INFO - 2026-01-15 12:00:00 --> ProfileController update - Database update: SUCCESS
INFO - 2026-01-15 12:00:00 --> ProfileController update - Verified email in DB: newemail@example.com
INFO - 2026-01-15 12:00:00 --> ProfileController update - Session email updated to: newemail@example.com
```

**❌ PROBLEM - Email not saved:**
```
INFO - 2026-01-15 12:00:00 --> ProfileController update - User ID: 123
INFO - 2026-01-15 12:00:00 --> ProfileController update - Update data: {"username":"john_doe","email":"newemail@example.com"}
INFO - 2026-01-15 12:00:00 --> ProfileController update - Is password change only: NO
ERROR - 2026-01-15 12:00:00 --> ProfileController update - Database update: FAILED
ERROR - 2026-01-15 12:00:00 --> ProfileController update - Errors: {"email":"Email validation error"}
```

---

## 🔍 What to Look For in Logs

### 1. Check Update Data
```
ProfileController update - Update data: {"username":"...","email":"..."}
```
- ✅ Email should be present in the JSON
- ❌ If email is missing, the form isn't sending it

### 2. Check Database Update Result
```
ProfileController update - Database update: SUCCESS
```
- ✅ Should say SUCCESS
- ❌ If FAILED, check the errors

### 3. Check Verified Email
```
ProfileController update - Verified email in DB: newemail@example.com
```
- ✅ Should match the email you entered
- ❌ If NULL or different, database isn't saving it

### 4. Check Session Update
```
ProfileController update - Session email updated to: newemail@example.com
```
- ✅ Should confirm session was updated
- ❌ If missing, session won't reflect the change

---

## 🐛 Common Issues & Solutions

### Issue 1: Email Not in Update Data
**Symptoms:**
```json
"Update data: {"username":"john_doe"}"  // No email!
```

**Cause:** Form not sending email field or it's empty

**Solution:**
- Check if email input field has `name="email"` attribute
- Check if email field is inside the `<form>` tag
- Verify no JavaScript is removing the email field

### Issue 2: Database Update Failed
**Symptoms:**
```
Database update: FAILED
Errors: {"email":"...validation error..."}
```

**Cause:** Validation rules failing

**Solutions:**
- Check if email format is valid
- Check UserModel validation rules
- Check if email is marked as required (it should be `permit_empty`)

### Issue 3: Email NULL in Database
**Symptoms:**
```
Verified email in DB: NULL
```

**Cause:** Email field not in `allowedFields` or database column doesn't exist

**Solutions:**
- Verify `email` is in `UserModel::$allowedFields` (it is ✅)
- Check database: `SHOW COLUMNS FROM users;` (should have `email` column)
- Check migration has been run

### Issue 4: Session Not Updated
**Symptoms:**
- Database has new email ✅
- Session still shows old email ❌
- Page refresh shows old email

**Cause:** Session not being updated after database change

**Solution:** Already fixed in code - session is updated when email changes

---

## 🔧 Manual Database Check

If you want to verify directly in the database:

### Check User Email
```sql
SELECT id, username, email FROM users WHERE id = YOUR_USER_ID;
```

### Update Email Manually (for testing)
```sql
UPDATE users SET email = 'test@example.com' WHERE id = YOUR_USER_ID;
```

### Check All User Emails
```sql
SELECT id, username, email, role FROM users;
```

---

## 📊 What the Fix Should Have Done

### Before Fix
```
User changes email → Submits form → 
Password form has hidden email field → 
Hidden field overwrites email change → 
Email reverts to old value ❌
```

### After Fix
```
Profile Update:
User changes email → Submits profile form → 
No hidden fields interfere → 
Email updated in database ✅

Password Change:
User changes password → Submits password form → 
password_change_only=1 flag set → 
Controller only updates password → 
Email remains unchanged (as expected) ✅
```

---

## 🧪 Complete Test Scenarios

### Test 1: Email Update Only
1. Login to profile
2. Change email: `old@example.com` → `new@example.com`
3. Click "Simpan Perubahan"
4. Check logs for SUCCESS
5. **Verify:** Email updated in database
6. **Verify:** Page shows new email
7. **Verify:** Logout and login - new email in session

### Test 2: Password Change Only
1. Login to profile
2. Enter new password in "Ubah Password" form
3. Click "Ubah Password"
4. Check logs: should show `password_change_only: YES`
5. **Verify:** Email unchanged in database
6. **Verify:** Password updated (can login with new password)

### Test 3: Email Then Password
1. Change email → Save → Check logs
2. **Verify:** Email updated
3. Change password → Save → Check logs
4. **Verify:** Email still has new value (not reverted!)
5. **Verify:** Password updated

### Test 4: Empty Email
1. Clear email field (leave empty)
2. Click "Simpan Perubahan"
3. Check logs
4. **Expected:** Email should remain unchanged or be cleared (based on `permit_empty`)

---

## 🔍 Log Commands Reference

### Windows PowerShell
```powershell
# View last 50 lines with ProfileController
Get-Content writable/logs/log-$(Get-Date -Format 'yyyy-MM-dd').log -Tail 50 | Select-String 'ProfileController'

# Real-time monitoring
Get-Content writable/logs/log-$(Get-Date -Format 'yyyy-MM-dd').log -Wait -Tail 10

# Search for specific user ID
Get-Content writable/logs/log-$(Get-Date -Format 'yyyy-MM-dd').log | Select-String 'User ID: 123'

# Show only errors
Get-Content writable/logs/log-$(Get-Date -Format 'yyyy-MM-dd').log -Tail 100 | Select-String 'ERROR.*ProfileController'
```

### Linux/Mac Bash
```bash
# View last 50 lines with ProfileController
tail -50 writable/logs/log-$(date +%Y-%m-%d).log | grep ProfileController

# Real-time monitoring
tail -f writable/logs/log-$(date +%Y-%m-%d).log | grep ProfileController

# Search for specific user ID
grep "User ID: 123" writable/logs/log-$(date +%Y-%m-%d).log

# Show only errors
tail -100 writable/logs/log-$(date +%Y-%m-%d).log | grep "ERROR.*ProfileController"
```

---

## ✅ Expected Results

After the fix, email updates should work as follows:

### Profile Update Form
- ✅ Email can be changed
- ✅ Saves to database immediately
- ✅ Updates session
- ✅ Visible on page refresh
- ✅ Persists across logins

### Password Change Form
- ✅ Only updates password
- ✅ Does NOT affect email
- ✅ Does NOT affect username
- ✅ Email remains with current value

---

## 📝 Debug Logging Details

### What Gets Logged

**On Profile Update:**
```
INFO - ProfileController update - User ID: {id}
INFO - ProfileController update - Update data: {json}
INFO - ProfileController update - Is password change only: NO
INFO - ProfileController update - Database update: SUCCESS
INFO - ProfileController update - Verified email in DB: {email}
INFO - ProfileController update - Session username updated
INFO - ProfileController update - Session email updated to: {email}
```

**On Password Change:**
```
INFO - ProfileController update - User ID: {id}
INFO - ProfileController update - Update data: {json with password}
INFO - ProfileController update - Is password change only: YES
INFO - ProfileController update - Database update: SUCCESS
INFO - ProfileController update - Verified email in DB: {current_email}
```

**On Error:**
```
ERROR - ProfileController update - Database update: FAILED
ERROR - ProfileController update - Errors: {validation_errors}
```

---

## 🎯 Next Steps

### 1. Test Email Update
- Follow the test steps above
- Check the logs
- Verify email is saved

### 2. Report Results
If email is **NOT saving**:
- Share the log output
- Include what you entered vs what was saved
- Note any error messages

If email **IS saving**:
- ✅ Issue is resolved!
- Can remove debug logging if desired
- Update documentation

### 3. Remove Debug Logging (Optional)
Once confirmed working, you can optionally remove the debug logs to reduce log file size. But they don't hurt to keep for troubleshooting.

---

## 📞 Support

**Files Modified:**
- `app/Controllers/ProfileController.php` (added debug logging)
- `app/Views/profile/index.php` (removed hidden email field)

**Documentation:**
- `PROFILE_EMAIL_UPDATE_FIX.md` - Complete fix documentation
- `EMAIL_UPDATE_DEBUG_GUIDE.md` - This debug guide

**Commands:**
```powershell
# View logs
Get-Content writable/logs/log-$(Get-Date -Format 'yyyy-MM-dd').log -Tail 50 | Select-String 'ProfileController'

# Test email update
# Just use the profile page and check logs!
```

---

**Guide Version:** 1.0  
**Last Updated:** 2026-01-15  
**Status:** Debug logging active - ready for testing
