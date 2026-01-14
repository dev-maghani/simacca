# 🔐 Email Authentication Fix - Quick Guide

**Issue:** Gmail Authentication Failure  
**Error:** `Username and Password not accepted`  
**Solution:** Use Gmail App Password (5 minutes)

---

## 🚨 The Problem

```
Failed to authenticate password. Error: 535-5.7.8 Username and Password not accepted.
```

**Your configuration is correct, but Gmail requires an App Password!**

---

## ✅ Quick Fix (3 Steps)

### Step 1: Enable 2-Step Verification (2 minutes)

1. Go to: https://myaccount.google.com/security
2. Click **"2-Step Verification"**
3. Click **"Get Started"** and follow instructions
4. Complete setup (you'll receive a code via SMS/phone)

### Step 2: Generate App Password (1 minute)

1. Go to: https://myaccount.google.com/apppasswords
   - **Or** Google Account → Security → 2-Step Verification → App passwords

2. Sign in if prompted

3. Under "Select app and device":
   - **Select app:** Mail
   - **Select device:** Other (Custom name)
   - **Type:** "SIMACCA Email"

4. Click **"Generate"**

5. **Copy the 16-character password** (looks like: `abcd efgh ijkl mnop`)
   - You won't see it again!
   - Spaces don't matter

### Step 3: Update .env File (1 minute)

Open your `.env` file and replace the password:

```env
# BEFORE (your regular password - DOESN'T WORK)
email.SMTPPass = your-regular-password

# AFTER (your App Password - WORKS!)
email.SMTPPass = 'abcd efgh ijkl mnop'
```

**Save the file!**

### Step 4: Test (30 seconds)

```bash
php spark email:test marcusmars563@gmail.com
```

**Expected:**
```
✓ Email test berhasil dikirim ke marcusmars563@gmail.com
Email configuration is working correctly!
```

---

## 📋 Your Current Configuration

**Diagnostics show everything is configured correctly:**

```
✓ From Email: noreply@smkn8bone.sch.id
✓ From Name: SIMACCA - SMK Negeri 8 Bone
✓ SMTP Host: smtp.gmail.com
✓ SMTP User: marcusmars563@gmail.com
✓ SMTP Password: SET (16 characters) ← Looks like App Password format!
✓ Port: 587 (TLS)
✓ Encryption: tls
```

**If your password is already 16 characters:**
- It might already be an App Password
- Try testing: `php spark email:test marcusmars563@gmail.com`
- If still failing, generate a NEW App Password

---

## 🔍 Why This Happens

Gmail stopped allowing regular passwords for "less secure apps" in 2022. Now you must use:

1. **App Passwords** (Recommended) ✅
2. **OAuth 2.0** (Complex, not needed for SMTP)

**Regular passwords don't work anymore for SMTP!**

---

## 🎯 Troubleshooting

### "App passwords option not showing"

**Cause:** 2-Step Verification not enabled  
**Fix:** Enable 2-Step Verification first, then try again

### "Still getting authentication error"

**Check:**
- [ ] Copied entire 16-character App Password
- [ ] No extra spaces in .env before/after password
- [ ] .env file saved
- [ ] Using correct Gmail email (marcusmars563@gmail.com)
- [ ] Generated NEW App Password (old one might be revoked)

### "Can I use my regular password?"

**No.** Gmail requires App Passwords for SMTP. Regular passwords won't work.

---

## 🆘 Alternative: Use Different Email Provider

If you don't want to use Gmail App Passwords:

### Option 1: Custom SMTP (cPanel/Plesk)
```env
email.SMTPHost = mail.yourdomain.com
email.SMTPUser = noreply@yourdomain.com
email.SMTPPass = regular-password-works-here
email.SMTPPort = 587
```

### Option 2: Mailtrap (Development/Testing)
```env
email.SMTPHost = smtp.mailtrap.io
email.SMTPUser = your-mailtrap-username
email.SMTPPass = your-mailtrap-password
email.SMTPPort = 2525
```

### Option 3: SendGrid/Mailgun (Production)
Requires API key setup - more complex

---

## 📞 Need Help?

### Run Diagnostics:
```bash
php spark email:diagnostics
```

### Check Logs:
```bash
# Real-time log monitoring
tail -f writable/logs/log-$(date +%Y-%m-%d).log | grep -i email

# Or on Windows PowerShell
Get-Content writable/logs/log-*.log -Tail 50 | Select-String "email"
```

### Test Email:
```bash
php spark email:test marcusmars563@gmail.com
```

---

## ✅ Summary

**What you need to do:**

1. **Enable 2-Step Verification** on Gmail
2. **Generate App Password** at https://myaccount.google.com/apppasswords
3. **Copy App Password** (16 characters)
4. **Update .env** file with App Password
5. **Test** with `php spark email:test`

**Time needed:** ~5 minutes  
**Difficulty:** Easy  
**Documentation:** See `GMAIL_APP_PASSWORD_SETUP.md` for detailed guide

---

**Last Updated:** 2026-01-15  
**Status:** Authentication Issue - Needs App Password
