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

## Update Log - Perbaikan Print & Foto (2026-01-11)

### Perbaikan yang Dilakukan:

#### 1. Foto Dokumentasi
- ✅ Menambahkan `esc()` pada URL foto untuk keamanan
- ✅ Button foto hanya tampil di screen (class `no-print`)
- ✅ Foto tampil sebagai thumbnail kecil (40x40px) saat print
- ✅ Menggunakan `print-only-inline` untuk menampilkan foto saat print

#### 2. Tampilan Print Formal
- ✅ **Header Print**: Judul formal "LAPORAN ABSENSI PEMBELAJARAN"
  - Menampilkan periode laporan
  - Menampilkan kelas yang dipilih
- ✅ **Footer Print**: Tanda tangan
  - Kepala Sekolah (kiri)
  - Petugas Admin (kanan)
  - Tanggal cetak otomatis

#### 3. Format Print Landscape
- ✅ Page setup: A4 Landscape
- ✅ Margin: 1cm (atas/bawah), 0.5cm (kiri/kanan)
- ✅ Font size: 7-8pt untuk print
- ✅ Border tabel hitam solid untuk print
- ✅ Background header tabel abu-abu

#### 4. Layout Tabel Print
- ✅ 14 kolom tampil sempurna dengan lebar proporsional:
  - No (3%), Tanggal (7%), Kelas (6%), Jam (8%)
  - Guru Mapel (10%), Mata Pelajaran (10%), Wali Kelas (10%)
  - H/S/I/A (masing-masing 3%)
  - Catatan Khusus (15%), Foto (8%), Guru Pengganti (11%)
- ✅ Header singkat untuk kolom kehadiran (H, S, I, A)
- ✅ Padding kompak (3px 2px) untuk efisiensi ruang
- ✅ Border collapse untuk tampilan rapi

#### 5. Style Print
- ✅ Badge kehadiran dengan border hitam untuk print
- ✅ No page break di tengah row tabel
- ✅ Overflow visible untuk print
- ✅ Text wrapping optimal
- ✅ Print-color-adjust untuk mempertahankan warna

#### 6. Keamanan & Kualitas
- ✅ Semua output menggunakan `esc()` untuk XSS prevention
- ✅ URL foto aman dan tervalidasi
- ✅ File Controller sudah ada untuk serve foto
- ✅ Modal image viewer untuk preview foto di screen

### File yang Dimodifikasi:
1. `app/Views/admin/laporan/absensi_detail.php` - Update lengkap view dengan print styles

### Cara Menggunakan Print:
1. Buka laporan di browser
2. Atur filter sesuai kebutuhan
3. Klik tombol "Cetak" atau Ctrl+P
4. Pastikan orientation: **Landscape**
5. Paper size: **A4**
6. Margins: Default atau Custom (1cm/0.5cm)
7. Print atau Save as PDF

### Preview Print:
- **Screen View**: Form filter, tombol, badge warna, icon foto
- **Print View**: Header formal, tabel border hitam, foto thumbnail, footer tanda tangan
- **Hidden saat Print**: Navigation, filter form, button actions
- **Visible saat Print**: Print header, tabel data, foto thumbnail, print footer
