# 🎓 SIMACCA - Sistem Monitoring Absensi dan Catatan Cara Ajar

[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue)](https://php.net)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.6.4-red)](https://codeigniter.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

**Framework:** CodeIgniter 4.6.4  
**Database:** MySQL  
**Styling:** Tailwind CSS  
**Version:** 1.5.0  
**Last Updated:** 2026-01-15

---

## 📖 Tentang SIMACCA

SIMACCA adalah sistem informasi berbasis web untuk monitoring absensi siswa dan pencatatan kegiatan belajar mengajar (KBM). Sistem ini dirancang untuk mempermudah guru, wali kelas, dan admin sekolah dalam mengelola data kehadiran siswa serta membuat laporan yang akurat.

### ✨ Fitur Utama

- 🔐 **Multi-Role System** - Admin, Guru Mapel, Wali Kelas, Siswa
- 📊 **Dashboard Interaktif** - Statistik real-time untuk setiap role
- ✅ **Absensi Digital** - Input cepat dengan UI mobile-friendly
- 📝 **Jurnal KBM** - Dokumentasi kegiatan belajar mengajar
- 📸 **Auto Image Optimization** - Kompresi otomatis 70-85%
- 👨‍🏫 **Guru Pengganti** - Sistem untuk guru piket/pengganti
- 📱 **Responsive Design** - Optimized untuk desktop & mobile
- 📧 **Email Notifications** - Password reset & notifikasi otomatis
- 📄 **Export Reports** - Download laporan dalam format Excel/PDF

---

## 🚀 Quick Start

**Ingin langsung coba? Ikuti 8 langkah ini (waktu: ~5 menit):**

```bash
# 1. Clone repository
git clone https://github.com/gh4ni404/simacca.git
cd simacca

# 2. Install dependencies
composer install

# 3. Setup environment
cp env .env
php spark key:generate

# 4. Edit .env - konfigurasi database
nano .env  # atau gunakan text editor favorit

# 5. Buat database MySQL
mysql -u root -p -e "CREATE DATABASE simacca_db"

# 6. Setup database dengan data dummy
php spark setup --with-dummy

# 7. Jalankan server
php spark serve

# 8. Buka browser: http://localhost:8080
# Login: admin / admin123
```

⚠️ **Jangan lupa ganti password setelah login pertama!**

📖 **Butuh detail lengkap?** → [docs/guides/QUICK_START.md](docs/guides/QUICK_START.md)

---

## 📚 Dokumentasi

### 🎯 Untuk Pemula

| Dokumen | Deskripsi | Waktu |
|---------|-----------|-------|
| [**Quick Start Guide**](docs/guides/QUICK_START.md) | Panduan instalasi super cepat untuk testing | 5 menit |
| [**Getting Started**](docs/guides/GETTING_STARTED.md) | Pilih skenario instalasi yang tepat | 2 menit |
| [**System Requirements**](docs/guides/REQUIREMENTS.md) | Cek kebutuhan sistem & compatibility | 5 menit |

### 📖 Panduan Lengkap

| Dokumen | Untuk Siapa? | Waktu |
|---------|--------------|-------|
| [**Panduan Instalasi**](docs/guides/PANDUAN_INSTALASI.md) | Developer & Admin | 15-30 menit |
| [**Deployment Guide**](docs/guides/DEPLOYMENT_GUIDE.md) | System Admin | 30-60 menit |
| [**Email Service Setup**](docs/guides/EMAIL_SERVICE_QUICKSTART.md) | Admin | 10 menit |
| **Import Jadwal** (see in-app guide) | Admin | 5 menit |

### 📋 Referensi

| Dokumen | Deskripsi |
|---------|-----------|
| [**FEATURES.md**](FEATURES.md) | Daftar lengkap semua fitur sistem |
| [**CHANGELOG.md**](CHANGELOG.md) | History perubahan & update aplikasi |
| [**TODO.md**](TODO.md) | Roadmap pengembangan & bug tracking |
| [**CONTRIBUTING.md**](CONTRIBUTING.md) | Panduan untuk kontributor |

### 🗂️ Dokumentasi Lengkap

Dokumentasi telah diorganisir ke dalam folder `docs/` dengan struktur berikut:

```
docs/
├── guides/          📖 Panduan instalasi, deployment, setup
├── features/        ✨ Dokumentasi fitur-fitur baru
├── bugfixes/        🐛 Log perbaikan bug & fixes
├── email/           📧 Dokumentasi email service & notifikasi
└── archive/         📦 Dokumentasi legacy (untuk referensi)
```

**👉 Lihat semua dokumentasi:** [docs/guides/DOKUMENTASI_INDEX.md](docs/guides/DOKUMENTASI_INDEX.md)

---

## 🎯 Fitur Unggulan

### 📱 Mobile-First Design (v1.4.0)
- Card-based layout untuk mobile
- Touch-friendly buttons (48px+ targets)
- Progress tracking yang selalu visible
- Smooth animations & visual feedback

### 📸 Auto Image Optimization (v1.5.0)
- **Auto-rotate EXIF orientation** - Foto landscape otomatis benar
- Kompresi otomatis 70-85% tanpa loss kualitas
- Profile photos: 800x800px @ 85% quality
- Journal photos: 1920x1920px @ 85% quality
- Support: JPEG, PNG, GIF, WebP

### 👨‍🏫 Guru Pengganti System (v1.2.0)
- Mode selection UI (Normal vs Pengganti)
- Auto-detect substitute teacher
- Dual ownership access control
- Full integration dengan absensi & jurnal

### 🎨 Modern UI/UX (v1.3.0)
- Visual status buttons dengan color coding
- Bulk actions (Semua Hadir/Izin/Sakit/Alpha)
- Toast notifications & real-time feedback
- 60-70% faster attendance marking

---

## 🛠️ Technology Stack

- **Backend:** PHP 8.1+ (CodeIgniter 4.6.4)
- **Database:** MySQL 5.7+ / MariaDB 10.3+
- **Frontend:** Tailwind CSS 3.x
- **JavaScript:** Vanilla JS (No frameworks)
- **Image Processing:** PHP GD Library + EXIF
- **Email:** SMTP (Gmail, Mailtrap, etc)

---

## 🔗 Quick Access URLs

Setelah server berjalan (`php spark serve`):

- **Login:** http://localhost:8080/login
- **Admin Dashboard:** http://localhost:8080/admin/dashboard
- **Guru Dashboard:** http://localhost:8080/guru/dashboard
- **Wali Kelas Dashboard:** http://localhost:8080/walikelas/dashboard
- **Siswa Dashboard:** http://localhost:8080/siswa/dashboard

**Default Login:**
- Username: `admin`
- Password: `admin123`

---

## 🎯 Command Reference

| Command | Description |
|---------|-------------|
| `php spark setup` | Setup lengkap (migrations + seeding) |
| `php spark setup --with-dummy` | Setup dengan data dummy untuk testing |
| `php spark setup --force` | Reset database dan setup ulang |
| `php spark serve` | Jalankan development server |
| `php spark migrate:status` | Cek status migrations |
| `php spark cache:clear` | Clear application cache |
| `php spark email:test` | Test email configuration |
| `php spark token:cleanup` | Clean expired tokens |

---

## 📊 Module Status

| Module | Status | Progress | Last Update |
|--------|--------|----------|-------------|
| Authentication | ✅ Complete | 100% | 2026-01-15 |
| Admin Module | ✅ Complete | 100% | 2026-01-15 |
| Guru Mapel Module | ✅ Complete | 100% | 2026-01-15 |
| Guru Pengganti/Piket | ✅ Complete | 100% | 2026-01-12 |
| Wali Kelas Module | ✅ Complete | 100% | 2026-01-11 |
| Siswa Module | ✅ Complete | 100% | 2026-01-11 |
| Profile & Photo | ✅ Complete | 100% | 2026-01-15 |
| Image Optimization | ✅ Complete | 100% | 2026-01-15 |
| Email Service | ✅ Complete | 100% | 2026-01-15 |
| Mobile UI | ✅ Complete | 100% | 2026-01-14 |

**Legend:**
- ✅ Complete - Fully functional & tested
- 🚧 In Progress - Under development
- 📋 Planned - Not yet started

---

## 🆘 Troubleshooting

### Database Connection Failed
```bash
# 1. Pastikan MySQL berjalan
sudo systemctl start mysql  # Linux
# atau net start mysql       # Windows

# 2. Cek kredensial di .env
nano .env

# 3. Buat database
mysql -u root -p -e "CREATE DATABASE simacca_db"
```

### Permission Errors (writable/)
```bash
# Linux/Mac
chmod -R 777 writable/

# Windows
# Right-click writable → Properties → Security → Edit permissions
```

### Composer Not Found
Download dan install dari [getcomposer.org](https://getcomposer.org/)

### Session/CSRF Errors
```bash
# Clear cache dan regenerate key
php spark cache:clear
php spark key:generate
```

📖 **Troubleshooting lengkap:** [docs/guides/PANDUAN_INSTALASI.md#troubleshooting](docs/guides/PANDUAN_INSTALASI.md)

---

## 🤝 Contributing

Kami sangat welcome kontribusi dari developer lain! Berikut cara berkontribusi:

1. Fork repository ini
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

📖 **Detail lengkap:** [CONTRIBUTING.md](CONTRIBUTING.md)

---

## 📝 License

Project ini dilisensikan under MIT License. Lihat [LICENSE](LICENSE) file untuk detail.

---

## 👥 Tim Pengembang

- **Mohd. Abdul Ghani** - Lead Developer
- **Dirwan Jaya** - Developer

---

## 📞 Support & Contact

Untuk pertanyaan, bug report, atau feature request:

- 📧 **Email:** [Email developer jika ada]
- 🐛 **Issues:** [GitHub Issues](https://github.com/gh4ni404/simacca/issues)
- 💬 **Discussions:** [GitHub Discussions](https://github.com/gh4ni404/simacca/discussions)

---

## 🌟 Star History

Jika project ini bermanfaat, jangan lupa kasih ⭐ di GitHub!

---

<div align="center">

**Made with ❤️ for Indonesian Education**

[⬆ Back to top](#-simacca---sistem-monitoring-absensi-dan-catatan-cara-ajar)

</div>
