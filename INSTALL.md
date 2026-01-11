# 🚀 SIMACCA - Panduan Instalasi Cepat

## Instalasi Super Mudah (3 Langkah)

### 1️⃣ Install Dependencies
```bash
composer install
```

### 2️⃣ Konfigurasi Database
Copy file `env` menjadi `.env` dan edit:
```bash
cp env .env
```

Edit file `.env`:
```env
# Cukup edit bagian database saja
database.default.hostname = localhost
database.default.database = simacca_db
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
```

**CATATAN:** Pastikan database `simacca_db` sudah dibuat di MySQL:
```sql
CREATE DATABASE simacca_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3️⃣ Setup Otomatis (SATU PERINTAH!)
```bash
php spark setup
```

Atau dengan data dummy untuk testing:
```bash
php spark setup --with-dummy
```

**SELESAI!** ✨ Aplikasi siap digunakan.

---

## 🎮 Menjalankan Aplikasi

```bash
php spark serve
```

Buka browser: **http://localhost:8080**

---

## 🔑 Default Login

### Admin
- **Username:** `admin`
- **Password:** `admin123`

⚠️ **PENTING:** Segera ganti password setelah login pertama!

---

## 📋 Apa yang Dilakukan Command `php spark setup`?

Command ini otomatis:
1. ✅ Menjalankan semua migrations (15 tabel)
2. ✅ Membuat user admin default
3. ✅ Membuat data dummy (jika pakai flag `--with-dummy`)

Tidak perlu command terpisah untuk migrate dan seed!

---

## 🛠️ Commands Lainnya

### Setup dengan data dummy
```bash
php spark setup --with-dummy
```

### Reset dan setup ulang
```bash
php spark setup --force
```

### Cek status migrations (manual)
```bash
php spark migrate:status
```

### Rollback migrations (manual)
```bash
php spark migrate:rollback
```

---

## ❗ Troubleshooting

### Error: "Database connection failed"
**Solusi:**
1. Pastikan MySQL service berjalan
2. Cek kredensial di file `.env`
3. Pastikan database `simacca_db` sudah dibuat

```sql
-- Jalankan di MySQL
CREATE DATABASE simacca_db;
```

### Error: "Table already exists"
**Solusi:** Reset database
```bash
php spark setup --force
```

### Error: "Access denied for user"
**Solusi:** Pastikan user MySQL memiliki privilege
```sql
GRANT ALL PRIVILEGES ON simacca_db.* TO 'root'@'localhost';
FLUSH PRIVILEGES;
```

### Error: "composer: command not found"
**Solusi:** Install Composer
- Windows: Download dari https://getcomposer.org/
- Linux/Mac: 
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

---

## 📚 Dokumentasi Lengkap

- **README.md** - Overview project
- **FEATURES.md** - Daftar fitur lengkap
- **TODO.md** - Development roadmap
- **app/Database/Migrations/README.md** - Database schema detail

---

## 🎯 Quick Reference

| Langkah | Command |
|---------|---------|
| Install dependencies | `composer install` |
| Copy env file | `cp env .env` |
| Edit database config | Edit `.env` |
| Setup aplikasi | `php spark setup` |
| Jalankan server | `php spark serve` |
| Akses aplikasi | http://localhost:8080 |

---

## 🔒 Security Tips

1. ✅ Ganti password admin setelah login
2. ✅ Jangan gunakan flag `--with-dummy` di production
3. ✅ Set `CI_ENVIRONMENT = production` di .env production
4. ✅ Disable debug mode di production

---

## 💡 Tips Pengembangan

### Mode Development
```env
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'
```

### Mode Production
```env
CI_ENVIRONMENT = production
app.baseURL = 'https://yourdomain.com/'
```

---

## 🆘 Butuh Bantuan?

1. Cek dokumentasi di folder `docs/`
2. Lihat error log di `writable/logs/`
3. Baca troubleshooting di atas
4. Contact: [Your Contact Info]

---

**Version:** 1.1.0  
**Last Updated:** 2026-01-12  
**Made with ❤️ by SIMACCA Team**
