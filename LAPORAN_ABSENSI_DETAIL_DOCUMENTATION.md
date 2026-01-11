# Dokumentasi Laporan Absensi Detail

## Overview
Fitur laporan absensi detail untuk admin yang menampilkan informasi lengkap dari setiap sesi pembelajaran termasuk kehadiran siswa, guru pengganti (jika ada), catatan khusus, dan foto dokumentasi.

## Fitur yang Ditambahkan

### 1. Database Migration
**File**: `app/Database/Migrations/2026-01-11-183700_AddGuruPenggantiToAbsensi.php`
- Menambahkan field `guru_pengganti_id` ke tabel `absensi`
- Field ini digunakan untuk mencatat guru pengganti ketika guru mata pelajaran utama berhalangan hadir

### 2. Model Enhancement
**File**: `app/Models/AbsensiModel.php`
- Menambahkan `guru_pengganti_id` ke `$allowedFields`
- Menambahkan method `getLaporanAbsensiLengkap($from, $to, $kelasId = null)` yang mengambil data lengkap dengan JOIN ke:
  - jadwal_mengajar
  - kelas
  - guru (guru mapel)
  - mata_pelajaran
  - guru (wali kelas) - LEFT JOIN
  - guru (guru pengganti) - LEFT JOIN
  - jurnal_kbm - LEFT JOIN
  - absensi_detail - LEFT JOIN dengan agregasi COUNT per status

### 3. Controller
**File**: `app/Controllers/Admin/LaporanController.php`
- Menambahkan method `absensiDetail()` yang:
  - Menerima filter: from date, to date, dan kelas_id
  - Default periode: bulan berjalan (tanggal 1 s/d akhir bulan)
  - Mengambil data dari model dan menampilkan ke view

### 4. View
**File**: `app/Views/admin/laporan/absensi_detail.php`
- Form filter dengan 3 parameter: Dari Tanggal, Sampai Tanggal, Kelas
- Tombol "Terapkan Filter" dan "Cetak"
- Tabel dengan 14 kolom:
  1. **No** - Nomor urut
  2. **Tanggal** - Tanggal pembelajaran
  3. **Kelas** - Nama kelas
  4. **Jam** - Jam mulai - jam selesai
  5. **Guru Mapel** - Nama guru mata pelajaran
  6. **Mata Pelajaran** - Nama mata pelajaran
  7. **Wali Kelas** - Nama wali kelas
  8. **Hadir** - Jumlah siswa hadir (badge hijau)
  9. **Sakit** - Jumlah siswa sakit (badge kuning)
  10. **Izin** - Jumlah siswa izin (badge biru)
  11. **Alpa** - Jumlah siswa alpa (badge merah)
  12. **Catatan Khusus** - Catatan dari jurnal KBM
  13. **Foto** - Icon untuk melihat foto dokumentasi (modal popup)
  14. **Guru Pengganti** - Nama guru pengganti jika ada (badge ungu)

### 5. Route
**File**: `app/Config/Routes.php`
- Route baru: `GET /admin/laporan/absensi-detail`
- Filter: `role:admin`
- Handler: `Admin\LaporanController::absensiDetail`

### 6. Menu Navigation
**File**: `app/Helpers/auth_helper.php`
- Menambahkan menu "Laporan Absensi Detail" di submenu "Laporan" untuk role admin
- Urutan menu:
  1. Rekap Absensi
  2. **Laporan Absensi Detail** (baru)
  3. Statistik

## Cara Menggunakan

### Akses Menu
1. Login sebagai admin
2. Klik menu "Laporan" di navigation bar
3. Pilih "Laporan Absensi Detail"

### Filter Data
1. **Dari Tanggal**: Pilih tanggal awal periode
2. **Sampai Tanggal**: Pilih tanggal akhir periode
3. **Kelas**: Pilih kelas tertentu atau "Semua Kelas"
4. Klik tombol "Terapkan"

### Melihat Foto Dokumentasi
- Klik icon gambar pada kolom "Foto"
- Foto akan ditampilkan dalam modal popup
- Klik tombol X atau area luar modal untuk menutup

### Cetak Laporan
- Klik tombol "Cetak" untuk print preview
- Style print sudah dioptimasi untuk ukuran kertas

## Struktur Data

### Tabel Absensi (Updated)
```sql
absensi:
  - id (PK)
  - jadwal_mengajar_id (FK)
  - tanggal
  - pertemuan_ke
  - materi_pembelajaran
  - created_by (FK users)
  - guru_pengganti_id (FK guru) -- BARU
  - created_at
```

### Query Hasil (Aggregate)
Setiap row berisi:
- Informasi pembelajaran (tanggal, kelas, jam, guru, mapel, wali kelas)
- Agregasi kehadiran (jumlah hadir, sakit, izin, alpa)
- Catatan khusus dari jurnal_kbm
- Foto dokumentasi dari jurnal_kbm
- Nama guru pengganti jika ada

## Teknologi
- **Backend**: CodeIgniter 4
- **Database**: MySQL dengan Query Builder
- **Frontend**: Tailwind CSS, Font Awesome
- **JavaScript**: Vanilla JS untuk modal image viewer

## Catatan Penting
1. Field `guru_pengganti_id` bersifat opsional (nullable)
2. Jika tidak ada guru pengganti, kolom akan menampilkan "-"
3. Jika tidak ada catatan khusus atau foto, kolom akan menampilkan "-"
4. Laporan diurutkan berdasarkan tanggal (DESC) dan jam mulai (ASC)
5. Badge warna memudahkan identifikasi visual status kehadiran

## URL Endpoint
```
GET /admin/laporan/absensi-detail
    ?from=2026-01-01
    &to=2026-01-31
    &kelas_id=1
```

## Testing
Server development berjalan di: `http://localhost:8080`

Untuk mengakses fitur:
1. Buka `http://localhost:8080/login`
2. Login sebagai admin
3. Akses `http://localhost:8080/admin/laporan/absensi-detail`

## Migration
Untuk menjalankan migration:
```bash
php spark migrate
```

Untuk rollback (jika diperlukan):
```bash
php spark migrate:rollback
```
