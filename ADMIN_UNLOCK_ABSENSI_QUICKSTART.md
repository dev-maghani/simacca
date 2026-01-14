# Quick Start: Admin Unlock Absensi

## Cara Menggunakan Fitur Unlock Absensi

### 1. Akses Halaman Kelola Absensi
1. Login sebagai **Admin**
2. Klik menu **"Kelola Absensi"** di sidebar
3. Atau akses langsung: `http://your-domain/admin/absensi`

### 2. Filter Absensi (Opsional)
Gunakan filter untuk mempermudah pencarian:
- **Tanggal Dari - Sampai**: Default bulan ini
- **Kelas**: Filter berdasarkan kelas tertentu
- **Guru**: Filter berdasarkan guru tertentu
- **Mata Pelajaran**: Filter berdasarkan mapel
- **Status**: Pilih "Terkunci" untuk lihat yang perlu di-unlock

### 3. Unlock Single Absensi
**Untuk unlock satu absensi:**

1. Cari absensi dengan badge merah 🔒 **"Terkunci"**
2. Klik tombol **"Unlock"** di kolom aksi
3. Konfirmasi popup akan muncul menampilkan:
   - Nama Guru
   - Mata Pelajaran
   - Tanggal Absensi
4. Klik **OK** untuk konfirmasi
5. Halaman refresh, badge berubah hijau 🔓 **"Dapat Diedit"**
6. Guru sekarang bisa edit absensi selama **24 jam**

### 4. Bulk Unlock (Unlock Banyak Sekaligus)
**Untuk unlock beberapa absensi sekaligus:**

1. **Centang checkbox** di sebelah kiri absensi yang terkunci
2. Tombol **"Unlock Terpilih (N)"** akan aktif (N = jumlah terpilih)
3. Klik tombol tersebut
4. Konfirmasi: "Unlock N absensi terpilih?"
5. Klik **OK**
6. Notifikasi muncul: "Berhasil unlock N absensi"
7. Halaman refresh otomatis

**Tip:** Gunakan checkbox **"Check All"** di header untuk pilih semua

### 5. Memahami Status Badge

| Badge | Arti | Aksi |
|-------|------|------|
| 🔓 **Dapat Diedit** (hijau) | Masih dalam 24 jam | Tidak perlu unlock |
| 🔒 **Terkunci** (merah) | Sudah lewat 24 jam | Perlu unlock |
| 🔒 **Terkunci** + ⓘ Pernah di-unlock | Sudah di-unlock sebelumnya tapi expired lagi | Bisa unlock lagi |

### 6. Informasi Waktu
Di bawah setiap badge, ada info waktu:
- **"12.5 jam lalu"** - waktu sejak absensi dibuat/di-unlock
- Jika > 24 jam, status otomatis terkunci

## FAQ

**Q: Apakah ada batasan berapa kali bisa unlock?**  
A: Tidak ada batasan. Admin bisa unlock berkali-kali sesuai kebutuhan.

**Q: Apakah guru dapat notifikasi saat absensi di-unlock?**  
A: Saat ini belum ada notifikasi otomatis. Admin perlu memberitahu guru secara manual (WA/SMS). Fitur notifikasi akan ditambahkan di versi berikutnya.

**Q: Apakah data unlock ter-record di sistem?**  
A: Ya, sistem menyimpan `unlocked_at` di database. Admin dapat melihat absensi mana yang pernah di-unlock.

**Q: Bagaimana jika guru tidak sempat edit dalam 24 jam setelah unlock?**  
A: Admin bisa unlock lagi kapan saja tanpa batasan.

**Q: Apakah unlock mempengaruhi data absensi yang sudah ada?**  
A: Tidak. Unlock hanya membuka akses edit, tidak mengubah data yang sudah tersimpan.

**Q: Bisakah guru request unlock sendiri?**  
A: Saat ini hanya admin yang bisa unlock. Guru perlu hubungi admin untuk request unlock.

## Troubleshooting

### Problem: Tombol Unlock tidak muncul
**Solusi:**
- Pastikan login sebagai **Admin** (bukan Guru/Wali Kelas/Siswa)
- Cek badge status: hanya absensi **Terkunci** yang ada tombol unlock
- Absensi yang masih **Dapat Diedit** tidak perlu/tidak ada tombol unlock

### Problem: Setelah unlock, guru masih tidak bisa edit
**Solusi:**
1. Pastikan unlock berhasil (cek badge berubah hijau)
2. Minta guru **logout** dan **login** lagi
3. Guru buka halaman absensi, tombol Edit seharusnya muncul
4. Jika masih tidak bisa, cek apakah sudah lewat 24 jam dari unlock

### Problem: Bulk unlock tidak bekerja
**Solusi:**
- Pastikan JavaScript enabled di browser
- Clear cache browser (Ctrl+F5)
- Coba unlock satu-persatu dulu untuk test

## Tips & Best Practices

### ✅ DO
- Filter berdasarkan tanggal untuk mempermudah pencarian
- Gunakan filter "Status: Terkunci" untuk fokus ke yang perlu di-unlock
- Catat/screenshot saat unlock untuk dokumentasi
- Informasikan guru via WA/SMS setelah unlock

### ❌ DON'T
- Jangan unlock semua absensi tanpa alasan jelas
- Jangan lupa beritahu guru setelah unlock
- Jangan unlock absensi yang sedang dalam proses investigasi/audit

## Keamanan & Audit

### Who Can Unlock?
**Hanya admin** yang bisa unlock absensi:
- Role: `admin`
- Akses via: `/admin/absensi`
- Protected by: `role:admin` filter

### Audit Trail
Sistem mencatat:
- `unlocked_at`: Kapan di-unlock
- Badge menampilkan "ⓘ Pernah di-unlock"

**Future:** Akan ada log lengkap siapa (admin) yang unlock, kapan, dan alasannya.

## Kontak Support
Jika ada masalah atau pertanyaan, hubungi:
- IT Support Team
- Email: support@simacca.sch.id

---
**Versi:** 1.0.0  
**Tanggal:** 14 Januari 2026  
**Fitur:** Admin Unlock Absensi
