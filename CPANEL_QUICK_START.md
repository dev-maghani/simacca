# 🚀 CPANEL QUICK START - 10 MENIT DEPLOYMENT

**Domain:** simacca.smkn8bone.sch.id  
**Estimasi:** 10-15 menit  
**Level:** Simple & Straightforward

---

## ✅ STRUKTUR FOLDER DI CPANEL

```
/home/your_username/
└── public_html/                    ← Upload semua files ke sini
    ├── .htaccess                   ← Root redirect (NEW)
    ├── .env                        ← Config (create from template)
    ├── .env.production             ← Template
    │
    ├── public/                     ← Set sebagai Document Root
    │   ├── index.php
    │   ├── .htaccess               ← Routing rules
    │   └── .user.ini               ← PHP settings (NEW)
    │
    ├── app/                        ← Application code
    ├── vendor/                     ← Dependencies
    ├── writable/                   ← MUST be writable!
    ├── system/
    └── tests/
```

---

## 📋 QUICK CHECKLIST (Copy-Paste Ready)

### ☑️ SEBELUM UPLOAD

**Di Komputer Lokal:**
```bash
# 1. Copy production config
cp .env.production .env

# 2. Generate encryption key
php spark key:generate

# 3. Edit .env - update ini:
nano .env
```

**Update di .env:**
```env
CI_ENVIRONMENT = production
app.baseURL = 'https://simacca.smkn8bone.sch.id/'
database.default.hostname = localhost
database.default.username = cpanel_username_dbname  ← CHANGE
database.default.password = your_db_password         ← CHANGE
database.default.database = cpanel_username_dbname  ← CHANGE
```

---

### ☑️ SETUP DI CPANEL (5 Langkah)

#### 1. Set PHP Version (2 menit)
```
cPanel → MultiPHP Manager
→ Select domain: simacca.smkn8bone.sch.id
→ Choose: PHP 8.1 atau 8.2
→ Apply
```

#### 2. Create Database (2 menit)
```
cPanel → MySQL Databases

A. Create Database:
   Name: simacca  (akan jadi cpanelusername_simacca)
   → Create Database

B. Create User:
   Username: simacca_user
   Password: [STRONG PASSWORD - SAVE THIS!]
   → Create User

C. Add User to Database:
   User: simacca_user
   Database: simacca
   Privileges: ALL PRIVILEGES
   → Make Changes
```

#### 3. Set Document Root (1 menit)
```
cPanel → Domains
→ Find: simacca.smkn8bone.sch.id
→ Click Edit
→ Document Root: /home/username/public_html/public
   (atau sesuai struktur Anda)
→ Save
```

#### 4. Install SSL Certificate (2 menit)
```
cPanel → SSL/TLS Status
→ Find: simacca.smkn8bone.sch.id
→ Click "Run AutoSSL"
→ Wait for completion (auto dari Let's Encrypt)
```

#### 5. Set File Permissions (1 menit)
```
cPanel → File Manager
→ Navigate ke: public_html/writable
→ Right click → Change Permissions
→ Set: 755 (or rwxr-xr-x)
→ Check "Recurse into subdirectories"
→ Apply
```

---

### ☑️ UPLOAD FILES (Via cPanel File Manager atau FTP)

**Via cPanel File Manager:**
```
1. cPanel → File Manager
2. Navigate to: public_html/
3. Upload all files (kecuali .git, node_modules)
4. Extract jika upload ZIP
```

**Struktur setelah upload:**
```
✅ public_html/.htaccess          ← NEW file (redirect)
✅ public_html/.env               ← YOUR config
✅ public_html/public/            ← Document root
✅ public_html/app/
✅ public_html/vendor/
✅ public_html/writable/          ← Permissions 755
```

---

### ☑️ VERIFY & TEST (3 menit)

#### Test 1: Basic Load
```
https://simacca.smkn8bone.sch.id

Expected: ✅ Welcome page atau login page
NOT: 500 error, 404, atau blank page
```

#### Test 2: Database Connection
```
Try login dengan admin/guru account

Expected: ✅ Login success, redirect ke dashboard
NOT: Database connection error
```

#### Test 3: Session Persistence
```
Login → Navigate ke halaman lain → Refresh

Expected: ✅ Still logged in
NOT: Random logout
```

#### Test 4: File Upload
```
Create jurnal → Upload foto dokumentasi

Expected: ✅ Upload success
NOT: Permission denied
```

#### Test 5: Print Function
```
Open jurnal → Click "Cetak Semua"

Expected: ✅ Print page dengan tabel
NOT: Blank page atau error
```

---

## 🚨 TROUBLESHOOTING CEPAT

### Issue: "500 Internal Server Error"
**Fix:**
```
1. Check writable/ permissions → Set 755
2. Check .env file ada dan valid
3. Check PHP version → Must be 8.1+
4. Check error_log di cPanel
```

### Issue: "Database connection failed"
**Fix:**
```
1. Verify database name di .env matches cPanel
   Format: cpanelusername_dbname
2. Check username/password correct
3. Hostname must be: localhost (NOT IP)
```

### Issue: "Page not found" untuk semua URLs
**Fix:**
```
1. Check Document Root → Must point to /public
2. Check mod_rewrite enabled di PHP config
3. Check .htaccess file ada di public/
```

### Issue: Random logout / Session lost
**Fix:**
```
1. Check writable/session permissions → 755
2. Verify SSL installed and working
3. Check .env:
   session.cookieDomain = '.smkn8bone.sch.id'
   session.cookieSecure = true
```

### Issue: Upload file gagal
**Fix:**
```
1. Check writable/uploads/ permissions → 755
2. Check .user.ini:
   upload_max_filesize = 10M
   post_max_size = 20M
3. Check disk space di cPanel
```

---

## 🎯 CONNECTION LOSS PREVENTION - AUTO

**Sudah Built-in:**
- ✅ Database persistent connection (pConnect)
- ✅ Connection timeout 10 seconds (auto-retry)
- ✅ Session file handler (stable di shared hosting)
- ✅ Auto-disable debug di production
- ✅ HTTPS force redirect
- ✅ Secure cookies auto-detection
- ✅ Error recovery mechanisms

**Tidak Perlu Konfigurasi Tambahan!**

---

## ✅ POST-DEPLOYMENT CHECKLIST

Setelah upload, cek ini:
- [ ] https://simacca.smkn8bone.sch.id loads (HTTPS green padlock)
- [ ] Login sebagai Admin works
- [ ] Login sebagai Guru works
- [ ] Login sebagai Siswa works
- [ ] Login sebagai Wali Kelas works
- [ ] Create absensi works
- [ ] Create jurnal works
- [ ] Upload foto works
- [ ] Print jurnal works
- [ ] Session persists (no random logout)
- [ ] No 500 errors in logs
- [ ] Performance acceptable (< 2s load)

---

## 📞 QUICK HELP

### Files Location:
- **Error Log:** `writable/logs/log-YYYY-MM-DD.log`
- **PHP Error:** Check cPanel Error Log
- **Apache Error:** cPanel → Metrics → Errors

### Check dari Browser:
```javascript
// Console check (F12)
console.log('Base URL:', document.querySelector('base')?.href);
console.log('HTTPS:', window.location.protocol);
```

### Emergency Commands (via cPanel Terminal):
```bash
# Check permissions
ls -la writable/

# Check PHP version
php -v

# Clear cache
php spark cache:clear

# Clean sessions
php spark session:cleanup
```

---

## 🎉 SUCCESS!

Jika semua test pass:
✅ **Aplikasi sudah LIVE dan AMAN**

User tidak akan mengalami:
- ❌ Connection timeout
- ❌ Database disconnect
- ❌ Random logout
- ❌ Upload failures
- ❌ Routing errors
- ❌ Security issues

---

**Total Time:** 10-15 menit  
**Difficulty:** ⭐⭐ (Easy with this guide)  
**Result:** ✅ Production-ready application

**Next:** Announce ke users! 🚀
