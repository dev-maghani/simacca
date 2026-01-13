# 🚀 DEPLOYMENT GUIDE - SIMACCA
**Target Domain:** simacca.smkn8bone.sch.id  
**Production Deployment Guide**

---

## ⚠️ CRITICAL - READ BEFORE DEPLOYING

**Found 6 Critical Issues** yang HARUS diperbaiki sebelum upload:

### 🔴 Issue #1: Hardcoded localhost URLs
### 🔴 Issue #2: Development environment aktif
### 🔴 Issue #3: Database debug masih ON
### 🔴 Issue #4: Encryption key kosong
### 🔴 Issue #5: No HTTPS configuration
### 🔴 Issue #6: Cookie domain tidak di-set

**Status:** ✅ **Semua sudah dibuatkan solusi otomatis**

---

## 📋 PRE-DEPLOYMENT STEPS

### 1. ✅ Copy Production Config
```bash
# Copy template production ke .env
cp .env.production .env

# Edit file .env dan update values:
nano .env
```

**Update values berikut di .env:**
```env
CI_ENVIRONMENT = production
app.baseURL = 'https://simacca.smkn8bone.sch.id/'
app.forceGlobalSecureRequests = true

database.default.username = simacca_user
database.default.password = YOUR_STRONG_PASSWORD_HERE

session.cookieDomain = '.smkn8bone.sch.id'
session.cookieSecure = true
cookie.domain = '.smkn8bone.sch.id'
cookie.secure = true
```

---

### 2. ✅ Generate Encryption Key
```bash
php spark key:generate
```

Ini akan otomatis:
- Generate key 64-character yang aman
- Update .env file dengan key baru
- Validasi key length

**PENTING:** Backup key ini securely!

---

### 3. ✅ Run Deployment Checker
```bash
php deploy.php
```

Script ini akan check:
- ✓ .env configuration
- ✓ Environment settings
- ✓ Database config
- ✓ Directory permissions
- ✓ Security settings
- ✓ Commands availability

**Output contoh:**
```
✓ Application is READY for production deployment!
```

Atau jika ada masalah:
```
✗ Application is NOT ready for deployment!
CRITICAL ISSUES TO FIX:
1. Update .env: CI_ENVIRONMENT = production
2. Generate encryption key: php spark key:generate
```

---

## 🌐 SERVER SETUP

### 1. Upload Files
Upload semua files KECUALI:
- ❌ `.env` (buat baru di server)
- ❌ `writable/` contents (kecuali .htaccess)
- ❌ `.git/` folder
- ❌ `node_modules/` (jika ada)

### 2. Set Permissions
```bash
# Set ownership
chown -R www-data:www-data /path/to/simacca

# Set directory permissions
find /path/to/simacca -type d -exec chmod 755 {} \;

# Set file permissions
find /path/to/simacca -type f -exec chmod 644 {} \;

# Set writable directory
chmod -R 755 /path/to/simacca/writable
```

### 3. Apache Configuration
Create VirtualHost config: `/etc/apache2/sites-available/simacca.conf`

```apache
<VirtualHost *:80>
    ServerName simacca.smkn8bone.sch.id
    ServerAlias www.simacca.smkn8bone.sch.id
    
    # Redirect to HTTPS
    Redirect permanent / https://simacca.smkn8bone.sch.id/
</VirtualHost>

<VirtualHost *:443>
    ServerName simacca.smkn8bone.sch.id
    ServerAlias www.simacca.smkn8bone.sch.id
    
    DocumentRoot /path/to/simacca/public
    
    <Directory /path/to/simacca/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # SSL Configuration
    SSLEngine on
    SSLCertificateFile /path/to/ssl/certificate.crt
    SSLCertificateKeyFile /path/to/ssl/private.key
    SSLCertificateChainFile /path/to/ssl/ca_bundle.crt
    
    # Security Headers
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    
    # Logs
    ErrorLog ${APACHE_LOG_DIR}/simacca_error.log
    CustomLog ${APACHE_LOG_DIR}/simacca_access.log combined
</VirtualHost>
```

Enable site:
```bash
a2ensite simacca.conf
a2enmod rewrite ssl headers
systemctl restart apache2
```

---

### 4. SSL Certificate
```bash
# Using Let's Encrypt (Recommended)
apt-get install certbot python3-certbot-apache
certbot --apache -d simacca.smkn8bone.sch.id
```

---

### 5. Database Setup
```bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE simacca_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Create user with strong password
CREATE USER 'simacca_user'@'localhost' IDENTIFIED BY 'YOUR_STRONG_PASSWORD';

# Grant privileges
GRANT ALL PRIVILEGES ON simacca_db.* TO 'simacca_user'@'localhost';
FLUSH PRIVILEGES;

# Exit MySQL
EXIT;
```

---

### 6. Run Migrations
```bash
cd /path/to/simacca
php spark migrate
```

---

### 7. Seed Admin User (if needed)
```bash
php spark db:seed AdminSeeder
```

**Default admin credentials:**
- Username: admin
- Password: admin123

**⚠️ CHANGE PASSWORD IMMEDIATELY after first login!**

---

## 🔧 POST-DEPLOYMENT

### 1. Setup Cron Jobs
```bash
crontab -e
```

Add:
```cron
# Session cleanup - runs daily at 2 AM
0 2 * * * cd /path/to/simacca && php spark session:cleanup >> /path/to/simacca/writable/logs/cron.log 2>&1

# Database backup - runs daily at 3 AM
0 3 * * * mysqldump -u simacca_user -pYOUR_PASSWORD simacca_db | gzip > /path/to/backups/simacca_$(date +\%Y\%m\%d).sql.gz

# Clear old logs - runs weekly on Sunday
0 4 * * 0 find /path/to/simacca/writable/logs -name "*.log" -mtime +30 -delete
```

---

### 2. Test Application
Access: `https://simacca.smkn8bone.sch.id`

Test scenarios:
- ✅ Login as Admin
- ✅ Login as Guru
- ✅ Login as Siswa
- ✅ Login as Wali Kelas
- ✅ Create absensi
- ✅ Create jurnal
- ✅ Print documents
- ✅ Upload files
- ✅ Check all navigation links
- ✅ Test form submissions
- ✅ Verify HTTPS working
- ✅ Check mobile responsive

---

### 3. Monitor Logs
```bash
# Error logs
tail -f /path/to/simacca/writable/logs/log-*.log

# Apache error log
tail -f /var/log/apache2/simacca_error.log

# Apache access log
tail -f /var/log/apache2/simacca_access.log
```

---

## 🛡️ SECURITY CHECKLIST

### Before Going Live:
- [x] CI_ENVIRONMENT = production
- [x] baseURL uses HTTPS
- [x] DBDebug = false (auto)
- [x] Encryption key generated
- [x] Strong database password
- [x] SSL certificate installed
- [x] Secure cookies enabled
- [x] CSRF protection active
- [ ] Default admin password changed
- [ ] Backup schedule setup
- [ ] Monitoring setup
- [ ] Firewall configured

---

## 📊 MONITORING & MAINTENANCE

### Daily:
- Check error logs
- Monitor disk space
- Verify cron jobs ran

### Weekly:
- Review access logs
- Check application performance
- Test backup restore

### Monthly:
- Update dependencies (if any)
- Review security settings
- Archive old logs
- Database optimization

---

## 🆘 TROUBLESHOOTING

### Issue: "Page not found" errors
**Solution:** 
```bash
# Check Apache mod_rewrite enabled
a2enmod rewrite
systemctl restart apache2

# Verify .htaccess in public/
ls -la /path/to/simacca/public/.htaccess
```

### Issue: Session keeps logging out
**Solution:** Check `.env` file:
```env
session.cookieDomain = '.smkn8bone.sch.id'
session.cookieSecure = true
```

### Issue: Images not loading
**Solution:** 
```bash
# Check writable/uploads permissions
chmod -R 755 /path/to/simacca/writable/uploads
chown -R www-data:www-data /path/to/simacca/writable/uploads
```

### Issue: Database connection failed
**Solution:**
```bash
# Test database connection
mysql -u simacca_user -p simacca_db

# Check .env database credentials
nano /path/to/simacca/.env
```

---

## 📞 SUPPORT CONTACTS

**Server Issues:**
- Contact hosting provider
- Check Apache/PHP error logs

**Application Issues:**
- Check `writable/logs/log-*.log`
- Enable debug temporarily (set CI_ENVIRONMENT=development in .env)
- Revert after fixing

**Database Issues:**
- Check MySQL service: `systemctl status mysql`
- Check disk space: `df -h`
- Review slow query log

---

## ✅ DEPLOYMENT CHECKLIST

### Pre-Deployment:
- [ ] Run `php deploy.php` locally
- [ ] All checks pass
- [ ] Backup current production (if updating)
- [ ] Test in staging environment

### Deployment:
- [ ] Upload files to server
- [ ] Create .env from .env.production
- [ ] Generate encryption key
- [ ] Configure database
- [ ] Run migrations
- [ ] Set file permissions
- [ ] Configure Apache VirtualHost
- [ ] Install SSL certificate
- [ ] Setup cron jobs

### Post-Deployment:
- [ ] Test all user roles
- [ ] Verify HTTPS works
- [ ] Check error logs
- [ ] Monitor performance
- [ ] Change default passwords
- [ ] Document admin credentials (securely)
- [ ] Announce to users

---

## 🎉 SUCCESS CRITERIA

Application is successfully deployed when:
- ✅ Accessible via https://simacca.smkn8bone.sch.id
- ✅ HTTPS certificate valid (green padlock)
- ✅ All user roles can login
- ✅ No errors in logs
- ✅ All features working
- ✅ Session persistent
- ✅ Files can be uploaded
- ✅ Print functions work
- ✅ Mobile responsive
- ✅ Fast loading (<2s)

---

**Status:** 📋 **Ready to Deploy**

Follow this guide step by step for a successful deployment to production.
