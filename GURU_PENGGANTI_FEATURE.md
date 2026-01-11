# Fitur Guru Pengganti (Piket Pengganti)

## Deskripsi
Fitur ini memungkinkan guru untuk mencatat guru pengganti ketika mengisi absensi. Guru pengganti yang dipilih akan tercatat sebagai "Piket Pengganti" dalam laporan absensi detail.

## Implementasi

### 1. Database
- Field `guru_pengganti_id` sudah tersedia di tabel `absensi` (migrasi: `2026-01-11-183700_AddGuruPenggantiToAbsensi.php`)
- Field ini bersifat opsional (nullable)
- Foreign key ke tabel `guru` dengan ON DELETE SET NULL

### 2. Controller (app/Controllers/Guru/AbsensiController.php)
- **create()**: Menambahkan `$guruList` ke data view
- **store()**: 
  - Otomatis mendeteksi mode pengganti (jika jadwal bukan milik guru yang login)
  - Di mode pengganti: otomatis set `guru_pengganti_id` = ID guru yang login
  - Di mode normal: ambil `guru_pengganti_id` dari form input (opsional)
- **edit()**: Menambahkan `$guruList` ke data view
- **update()**: Memperbarui `guru_pengganti_id` dari form input
- **getJadwalByHari()**: 
  - Menerima parameter `substitute` untuk mode pengganti
  - Mode normal: hanya tampilkan jadwal guru yang login
  - Mode pengganti: tampilkan SEMUA jadwal di hari tersebut dengan nama guru

### 3. Model (app/Models/AbsensiModel.php)
- **getAbsensiWithDetail()**: Menambahkan join dengan tabel guru untuk mendapatkan `nama_guru_pengganti`
- **getLaporanAbsensiLengkap()**: Sudah include `nama_guru_pengganti`
- **getLaporanAbsensiPerHari()**: Sudah include `nama_guru_pengganti`

### 4. Views
#### Form Input Absensi (app/Views/guru/absensi/create.php)
- **Mode Selection**: Toggle untuk memilih "Jadwal Saya Sendiri" atau "Guru Pengganti"
- **Mode Normal**: 
  - Menampilkan hanya jadwal guru yang login
  - Dropdown "Guru Pengganti (Opsional)" untuk memilih guru lain jika ada yang menggantikan
- **Mode Pengganti**: 
  - Menampilkan SEMUA jadwal di hari yang dipilih
  - Setiap jadwal menampilkan nama guru asli
  - Label berubah menjadi "Jadwal yang Digantikan"
  - Sistem otomatis mencatat guru yang login sebagai pengganti
- Dropdown guru pengganti berisi list semua guru dengan format: "Nama Lengkap (NIP)"
- JavaScript untuk handle mode switching dan AJAX load jadwal

#### Form Edit Absensi (app/Views/guru/absensi/edit.php)
- Menambahkan dropdown "Guru Pengganti (Opsional)"
- Dropdown akan menampilkan guru pengganti yang sudah dipilih sebelumnya (jika ada)

#### Detail Absensi (app/Views/guru/absensi/show.php)
- Menampilkan informasi guru pengganti jika ada
- Ditampilkan dengan badge "Piket Pengganti"
- Icon: user-plus dengan background purple

## Cara Penggunaan

### 1. Mode Jadwal Sendiri (Normal Mode)
Untuk guru yang mengajar sesuai jadwal reguler:

1. Guru masuk ke menu Absensi > Tambah Absensi
2. Pilih mode **"Jadwal Saya Sendiri"** (default)
3. Pilih hari dan jadwal mengajar
4. Pada bagian "Detail Absensi", terdapat dropdown "Guru Pengganti (Opsional)"
5. **Opsional**: Jika ada guru lain yang menggantikan Anda, pilih dari dropdown
6. Jika tidak ada, biarkan kosong (pilih "-- Tidak ada pengganti --")
7. Lanjutkan mengisi absensi siswa seperti biasa

### 2. Mode Guru Pengganti (Substitute Mode)
Untuk guru yang menggantikan guru lain yang berhalangan:

1. Guru masuk ke menu Absensi > Tambah Absensi
2. Pilih mode **"Guru Pengganti"**
3. Pilih hari yang akan digantikan
4. Sistem akan menampilkan **SEMUA jadwal** di hari tersebut (tidak hanya jadwal Anda)
5. Pilih jadwal yang akan digantikan (ditampilkan dengan nama guru asli)
6. Sistem **otomatis** mencatat Anda sebagai guru pengganti
7. Lanjutkan mengisi absensi siswa seperti biasa

**Catatan Penting untuk Mode Pengganti:**
- Anda bisa melihat dan memilih jadwal guru mana saja
- Nama guru asli ditampilkan untuk memudahkan identifikasi
- Sistem otomatis merekam Anda sebagai guru pengganti
- Data akan tercatat dalam laporan sebagai "Piket Pengganti"

### 2. Edit Absensi
1. Buka detail absensi yang ingin diedit
2. Klik tombol "Edit"
3. Pada form edit, dropdown "Guru Pengganti" akan menampilkan pilihan yang sudah dipilih sebelumnya
4. Guru bisa mengubah atau menghapus guru pengganti
5. Simpan perubahan

### 3. Melihat Detail Absensi
- Jika ada guru pengganti, informasi akan ditampilkan di bagian "Informasi Absensi"
- Ditampilkan dengan label "Guru Pengganti" dan badge "Piket Pengganti"

### 4. Laporan Absensi Detail (Admin)
- Guru pengganti akan tercatat dalam laporan absensi detail
- Informasi ini berguna untuk monitoring dan administrasi sekolah

## Keuntungan Fitur
1. **Fleksibilitas**: Dua mode berbeda untuk jadwal sendiri dan mode pengganti
2. **Otomatis**: Sistem otomatis mencatat guru pengganti di mode pengganti
3. **Transparansi**: Tercatat dengan jelas siapa guru pengganti dalam setiap pertemuan
4. **User-Friendly**: Interface yang jelas membedakan antara kedua mode
5. **Administrasi**: Memudahkan pencatatan untuk keperluan administrasi dan pelaporan
6. **Monitoring**: Admin dapat memonitor guru yang sering menjadi pengganti
7. **Opsional**: Di mode normal, field pengganti bersifat opsional

## Catatan
- Field guru pengganti bersifat **opsional**, tidak wajib diisi
- Guru pengganti hanya bisa dipilih dari daftar guru yang terdaftar di sistem
- Data guru pengganti akan muncul di laporan absensi detail yang diakses oleh admin
- Jika guru pengganti dihapus dari sistem, field akan otomatis menjadi NULL (tidak error)

## Testing

### Test Skenario 1: Mode Normal dengan Pengganti
1. Login sebagai Guru A
2. Pilih mode "Jadwal Saya Sendiri"
3. Pilih salah satu jadwal Guru A
4. Di dropdown "Guru Pengganti", pilih Guru B
5. Isi absensi siswa dan simpan
6. Lihat detail absensi → Guru A (pengajar) dengan Guru B (pengganti)

### Test Skenario 2: Mode Normal tanpa Pengganti
1. Login sebagai Guru A
2. Pilih mode "Jadwal Saya Sendiri"
3. Pilih salah satu jadwal Guru A
4. Biarkan dropdown "Guru Pengganti" kosong
5. Isi absensi siswa dan simpan
6. Lihat detail absensi → Hanya Guru A, tanpa pengganti

### Test Skenario 3: Mode Pengganti
1. Login sebagai Guru B (yang akan menggantikan)
2. Pilih mode "Guru Pengganti"
3. Pilih hari (misalnya Senin)
4. Sistem menampilkan SEMUA jadwal di hari Senin (termasuk jadwal Guru A, C, D, dll)
5. Pilih jadwal Guru A yang akan digantikan
6. Isi absensi siswa dan simpan
7. Lihat detail absensi → Guru A (pengajar asli) dengan Guru B (pengganti)

### Test Skenario 4: Laporan Admin
1. Login sebagai admin
2. Buka Laporan > Absensi Detail
3. Pilih tanggal dan kelas
4. Pastikan kolom "Guru Pengganti" menampilkan nama guru pengganti
5. Verifikasi badge "Piket Pengganti" muncul jika ada guru pengganti

## File yang Dimodifikasi
1. `app/Controllers/Guru/AbsensiController.php`
2. `app/Models/AbsensiModel.php`
3. `app/Views/guru/absensi/create.php`
4. `app/Views/guru/absensi/edit.php`
5. `app/Views/guru/absensi/show.php`

## Database Migration
- Migration file: `app/Database/Migrations/2026-01-11-183700_AddGuruPenggantiToAbsensi.php`
- Pastikan migration sudah dijalankan dengan: `php spark migrate`
