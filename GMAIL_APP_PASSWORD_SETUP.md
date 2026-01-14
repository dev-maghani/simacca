# 🔐 Gmail App Password Setup - SIMACCA

**Error:** `Username and Password not accepted`  
**Solution:** Use Gmail App Password instead of regular password

---

## 🚨 Current Issue

```
Failed to authenticate password. Error: 535-5.7.8 Username and Password not accepted.
```

**Reason:** Gmail doesn't allow regular passwords for SMTP authentication from third-party apps. You need to use an **App Password**.

---

## ✅ Step-by-Step Solution

### Step 1: Enable 2-Step Verification

1. Go to your Google Account: https://myaccount.google.com/security
2. Under "Signing in to Google", click **2-Step Verification**
3. Click **Get Started** and follow the setup process
4. Complete the 2-Step Verification setup

**Important:** You MUST enable 2-Step Verification before you can create App Passwords.

---

### Step 2: Generate App Password

1. Go to: https://myaccount.google.com/apppasswords
   - Or navigate: Google Account → Security → 2-Step Verification → App passwords

2. You might need to sign in again

3. Under "Select app and device":
   - **App:** Select "Mail"
   - **Device:** Select "Other (Custom name)"
   - Enter: "SIMACCA Email System"

4. Click **Generate**

5. Google will show a 16-character password like: `abcd efgh ijkl mnop`

6. **COPY THIS PASSWORD** (you won't see it again!)

---

### Step 3: Update .env File

Open your `.env` file and update the email password:

```env
# OLD - Regular Gmail password (DOESN'T WORK)
email.SMTPPass = your-regular-password

# NEW - App Password (USE THIS)
email.SMTPPass = 'abcd efgh ijkl mnop'
```

**Important:**
- You can include or remove spaces in the app password - both work
- Put it in quotes if it contains spaces
- Example: `email.SMTPPass = 'abcdefghijklmnop'` or `email.SMTPPass = 'abcd efgh ijkl mnop'`

---

### Step 4: Test Email Configuration

```bash
php spark email:test your-email@gmail.com
```

**Expected Result:**
```
✓ Email test berhasil dikirim ke your-email@gmail.com
Email configuration is working correctly!
```

---

## 📋 Complete .env Configuration

Here's your complete email configuration:

```env
#--------------------------------------------------------------------
# EMAIL CONFIGURATION
#--------------------------------------------------------------------
email.fromEmail = noreply@smkn8bone.sch.id
email.fromName = 'SIMACCA - SMK Negeri 8 Bone'
email.protocol = smtp

# Gmail SMTP Configuration
email.SMTPHost = smtp.gmail.com
email.SMTPUser = marcusmars563@gmail.com
email.SMTPPass = 'YOUR-16-CHAR-APP-PASSWORD'
email.SMTPPort = 587
email.SMTPCrypto = tls
email.mailType = html
```

---

## 🔍 Troubleshooting

### Issue: "App passwords" option not available

**Solution:**
1. Make sure 2-Step Verification is enabled first
2. Wait a few minutes after enabling 2-Step Verification
3. Try accessing: https://myaccount.google.com/apppasswords directly
4. Make sure you're logged into the correct Google account

### Issue: Still getting authentication error after using App Password

**Checklist:**
- [ ] Copied the App Password correctly (all 16 characters)
- [ ] No extra spaces before/after the password in .env
- [ ] Used the correct Gmail email address
- [ ] .env file saved properly
- [ ] Restarted the application after changing .env

### Issue: "Less secure apps" message

**Solution:**
- You don't need to enable "Less secure apps" when using App Passwords
- App Passwords are the secure way to authenticate
- If you see this, you're probably using your regular password instead of App Password

---

## 🎯 Quick Reference

### Gmail App Password URLs

| Purpose | URL |
|---------|-----|
| Enable 2-Step Verification | https://myaccount.google.com/signinoptions/two-step-verification |
| Create App Password | https://myaccount.google.com/apppasswords |
| Google Account Security | https://myaccount.google.com/security |
| Gmail SMTP Help | https://support.google.com/mail/answer/7126229 |

---

## ⚙️ Alternative: Using Different Email Provider

If you prefer not to use Gmail, here are alternatives:

### Outlook/Office365
```env
email.SMTPHost = smtp.office365.com
email.SMTPUser = your-email@outlook.com
email.SMTPPass = your-password
email.SMTPPort = 587
email.SMTPCrypto = tls
```

### Yahoo Mail
```env
email.SMTPHost = smtp.mail.yahoo.com
email.SMTPUser = your-email@yahoo.com
email.SMTPPass = your-app-password
email.SMTPPort = 587
email.SMTPCrypto = tls
```
Note: Yahoo also requires App Password

### Custom SMTP (cPanel, Plesk, etc.)
```env
email.SMTPHost = mail.yourdomain.com
email.SMTPUser = noreply@yourdomain.com
email.SMTPPass = your-password
email.SMTPPort = 587
email.SMTPCrypto = tls
```

---

## 🔐 Security Best Practices

### DO:
- ✅ Use App Passwords for Gmail
- ✅ Keep .env file secure (never commit to git)
- ✅ Use 2-Step Verification
- ✅ Rotate App Passwords periodically
- ✅ Delete unused App Passwords

### DON'T:
- ❌ Use your regular Gmail password
- ❌ Share your App Password
- ❌ Commit .env to version control
- ❌ Enable "Less secure apps"
- ❌ Use the same App Password for multiple apps

---

## 📞 Need Help?

### Check Configuration:
```bash
php spark email:test your-email@gmail.com
```

### Check Logs:
```bash
tail -f writable/logs/log-$(date +%Y-%m-%d).log | grep -i email
```

### Common Error Messages:

| Error | Cause | Solution |
|-------|-------|----------|
| Username and Password not accepted | Using regular password | Use App Password |
| Cannot send mail with no "From" header | Missing configuration | Set email.fromEmail in .env |
| SMTP connect() failed | Wrong SMTP host/port | Check smtp.gmail.com:587 |
| Failed STARTTLS | Wrong crypto setting | Use tls not ssl |

---

## ✅ Verification Checklist

Before testing, ensure:

- [ ] 2-Step Verification enabled on Gmail account
- [ ] App Password generated from Google Account
- [ ] App Password copied to .env file (email.SMTPPass)
- [ ] .env file saved
- [ ] Correct Gmail address in email.SMTPUser
- [ ] SMTPHost = smtp.gmail.com
- [ ] SMTPPort = 587
- [ ] SMTPCrypto = tls

Then run:
```bash
php spark email:test your-email@gmail.com
```

---

## 🎉 Success!

Once configured correctly, you should see:
```
✓ Email test berhasil dikirim ke your-email@gmail.com
Email configuration is working correctly!
```

And receive an email with:
- Subject: "SIMACCA - Test Email Configuration"
- From: "SIMACCA - SMK Negeri 8 Bone"
- Professional branded layout

---

**Document Version:** 1.0  
**Last Updated:** 2026-01-15  
**Status:** Gmail Authentication Guide
