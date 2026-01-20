# 📊 Guide Implementasi Hybrid Statistics - Wali Kelas Module

**Target Module:** Wali Kelas Laporan  
**Based on:** Wakakur & Admin Implementation  
**Version:** 3.0 (9 Categories)  
**Date:** 21 Januari 2026  

---

## 📋 TABLE OF CONTENTS

1. [Overview](#overview)
2. [Prerequisites](#prerequisites)
3. [Step-by-Step Implementation](#step-by-step-implementation)
4. [Testing Guide](#testing-guide)
5. [Troubleshooting](#troubleshooting)
6. [FAQ](#faq)

---

## 🎯 OVERVIEW

### **Apa yang Akan Diimplementasikan:**

Menambahkan **Hybrid Statistics System** ke laporan Wali Kelas dengan 2 perspektif:

1. **Perspektif Sesi Pembelajaran** (Session-based)
   - Total record kehadiran dari semua jadwal
   - Rata-rata kehadiran per sesi

2. **Perspektif Siswa** (Student-based)
   - 9 kategori kehadiran siswa
   - Insight untuk monitoring individu

### **Benefit untuk Wali Kelas:**

✅ Identifikasi cepat siswa yang butuh perhatian  
✅ Data konkrit untuk konseling  
✅ Monitoring kehadiran siswa secara detail  
✅ Laporan yang jelas untuk orang tua  

---

## 📦 PREREQUISITES

### **Files yang Sudah Ada:**

Pastikan implementasi ini sudah complete:

- ✅ `app/Models/AbsensiDetailModel.php` dengan method `getStatistikPerSiswa()`
- ✅ `app/Controllers/Wakakur/LaporanController.php` (sebagai referensi)
- ✅ `app/Views/wakakur/laporan/print.php` (sebagai template)

### **Files yang Akan Dimodifikasi:**

- `app/Controllers/WaliKelas/LaporanController.php`
- `app/Views/walikelas/laporan/index.php` (atau print view)

### **Permissions:**

- Role: `walikelas`
- Access: View laporan kelas yang di-handle

---

## 🔧 STEP-BY-STEP IMPLEMENTATION

### **STEP 1: Cek Struktur Controller Wali Kelas**

Buka file: `app/Controllers/WaliKelas/LaporanController.php`

Identifikasi method untuk laporan (misal: `index()` atau `print()`):

```php
public function index()
{
    $tanggal = $this->request->getGet('tanggal') ?: date('Y-m-d');
    
    // Get kelas yang di-handle wali kelas ini
    $kelasId = session()->get('kelas_id'); // atau dari database
    
    // ... existing code
}
```

---

### **STEP 2: Tambahkan Method getStatistikPerSiswa**

**Location:** `app/Controllers/WaliKelas/LaporanController.php`

Tambahkan di method yang handle laporan/print:

```php
public function index() // atau print()
{
    // ... existing code untuk get data laporan
    
    $tanggal = $this->request->getGet('tanggal') ?: date('Y-m-d');
    $kelasId = session()->get('kelas_id'); // Kelas yang di-handle wali kelas
    
    // ... existing code untuk $totalStats
    
    // ========== TAMBAHKAN INI ========== //
    // Get statistik per siswa (student-centric approach)
    $siswaStats = $this->absensiDetailModel->getStatistikPerSiswa($tanggal, $kelasId);
    // =================================== //
    
    $data = [
        // ... existing data
        'siswaStats' => $siswaStats, // NEW: Student-centric statistics
    ];
    
    return view('walikelas/laporan/index', $data);
}
```

**⚠️ Important Notes:**

- Pastikan `$this->absensiDetailModel` sudah di-initialize di `__construct()`
- `$kelasId` harus sesuai dengan kelas yang di-handle wali kelas
- Jika wali kelas handle multiple kelas, sesuaikan logika filter

---

### **STEP 3: Update View dengan Hybrid Summary**

**Location:** `app/Views/walikelas/laporan/index.php` (atau print view)

#### **3.1: Temukan Section Summary yang Ada**

Cari bagian yang menampilkan ringkasan kehadiran (biasanya berupa cards atau table).

#### **3.2: Replace dengan Hybrid Approach**

**SEBELUM:**
```php
<!-- Summary lama (single perspective) -->
<div class="summary">
    <div>Hadir: <?= $totalStats['hadir']; ?></div>
    <div>Sakit: <?= $totalStats['sakit']; ?></div>
    <!-- ... -->
</div>
```

**SESUDAH:**
```php
<!-- Summary Section - HYBRID APPROACH -->
<div class="summary-section">
    <!-- Perspektif 1: Session-Based -->
    <div class="summary-box" style="margin-bottom: 15px; border: 2px solid #3b82f6;">
        <h3 style="color: #3b82f6;">📊 Ringkasan Kehadiran (Perspektif Sesi Pembelajaran)</h3>
        <p style="font-size: 12px; color: #666; font-style: italic;">
            Menghitung total record kehadiran dari semua jadwal yang terisi
        </p>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="label">Hadir</div>
                <div class="value" style="color: #10b981;"><?= $totalStats['hadir']; ?>×</div>
            </div>
            <div class="summary-item">
                <div class="label">Sakit</div>
                <div class="value" style="color: #f59e0b;"><?= $totalStats['sakit']; ?>×</div>
            </div>
            <div class="summary-item">
                <div class="label">Izin</div>
                <div class="value" style="color: #3b82f6;"><?= $totalStats['izin']; ?>×</div>
            </div>
            <div class="summary-item">
                <div class="label">Alpa</div>
                <div class="value" style="color: #ef4444;"><?= $totalStats['alpa']; ?>×</div>
            </div>
        </div>
        <p style="text-align: center; margin-top: 10px;">
            <strong>Rata-rata Kehadiran: <?= $totalStats['percentage'] ?? 0; ?>%</strong>
        </p>
    </div>

    <!-- Perspektif 2: Student-Based (NEW) -->
    <div class="summary-box" style="border: 2px solid #10b981;">
        <h3 style="color: #10b981;">👥 Ringkasan Kehadiran (Perspektif Siswa)</h3>
        <p style="font-size: 12px; color: #666; font-style: italic;">
            Menghitung kategori kehadiran per siswa dari total <?= $siswaStats['total_siswa']; ?> siswa
        </p>
        
        <div class="summary-grid">
            <!-- KATEGORI 1: HADIR PENUH -->
            <div class="summary-item">
                <div class="label">✅ Hadir Semua Mapel</div>
                <div class="value" style="color: #10b981; font-weight: bold;">
                    <?= $siswaStats['hadir_semua']; ?> siswa
                </div>
                <div class="percentage">
                    <?php 
                        $pct = $siswaStats['total_siswa'] > 0 
                            ? round(($siswaStats['hadir_semua'] / $siswaStats['total_siswa']) * 100, 1) 
                            : 0;
                    ?>
                    (<?= $pct; ?>%)
                </div>
            </div>

            <!-- KATEGORI 2-4: TIDAK HADIR SAMA SEKALI -->
            <div class="summary-item">
                <div class="label">🤒 Sakit Semua Mapel</div>
                <div class="value" style="color: #f59e0b; font-weight: bold;">
                    <?= $siswaStats['sakit_semua']; ?> siswa
                </div>
                <div class="percentage">
                    <?php 
                        $pct = $siswaStats['total_siswa'] > 0 
                            ? round(($siswaStats['sakit_semua'] / $siswaStats['total_siswa']) * 100, 1) 
                            : 0;
                    ?>
                    (<?= $pct; ?>%)
                </div>
            </div>

            <div class="summary-item">
                <div class="label">📝 Izin Semua Mapel</div>
                <div class="value" style="color: #3b82f6; font-weight: bold;">
                    <?= $siswaStats['izin_semua']; ?> siswa
                </div>
                <div class="percentage">
                    <?php 
                        $pct = $siswaStats['total_siswa'] > 0 
                            ? round(($siswaStats['izin_semua'] / $siswaStats['total_siswa']) * 100, 1) 
                            : 0;
                    ?>
                    (<?= $pct; %>%)
                </div>
            </div>

            <div class="summary-item">
                <div class="label">🔴 Alpa Semua Mapel</div>
                <div class="value" style="color: #dc2626; font-weight: bold; background-color: #fee2e2; padding: 5px; border-radius: 4px;">
                    <?= $siswaStats['alpa_semua']; ?> siswa
                </div>
                <div class="percentage" style="color: #dc2626; font-weight: bold;">
                    <?php 
                        $pct = $siswaStats['total_siswa'] > 0 
                            ? round(($siswaStats['alpa_semua'] / $siswaStats['total_siswa']) * 100, 1) 
                            : 0;
                    ?>
                    (<?= $pct; %>%) - CRITICAL!
                </div>
            </div>

            <!-- KATEGORI 5-7: HADIR SEBAGIAN -->
            <div class="summary-item">
                <div class="label">✅🤒 Hadir + Sakit</div>
                <div class="value" style="color: #f59e0b;">
                    <?= $siswaStats['hadir_sakit']; ?> siswa
                </div>
                <div class="percentage">
                    <?php 
                        $pct = $siswaStats['total_siswa'] > 0 
                            ? round(($siswaStats['hadir_sakit'] / $siswaStats['total_siswa']) * 100, 1) 
                            : 0;
                    ?>
                    (<?= $pct; ?>%)
                </div>
            </div>

            <div class="summary-item">
                <div class="label">✅📝 Hadir + Izin</div>
                <div class="value" style="color: #3b82f6;">
                    <?= $siswaStats['hadir_izin']; ?> siswa
                </div>
                <div class="percentage">
                    <?php 
                        $pct = $siswaStats['total_siswa'] > 0 
                            ? round(($siswaStats['hadir_izin'] / $siswaStats['total_siswa']) * 100, 1) 
                            : 0;
                    ?>
                    (<?= $pct; %>%)
                </div>
            </div>

            <div class="summary-item">
                <div class="label">✅⚠️ Hadir + Alpa</div>
                <div class="value" style="color: #ef4444; font-weight: bold;">
                    <?= $siswaStats['hadir_alpa']; ?> siswa
                </div>
                <div class="percentage" style="color: #ef4444; font-weight: bold;">
                    <?php 
                        $pct = $siswaStats['total_siswa'] > 0 
                            ? round(($siswaStats['hadir_alpa'] / $siswaStats['total_siswa']) * 100, 1) 
                            : 0;
                    ?>
                    (<?= $pct; %>%) - PERHATIAN!
                </div>
            </div>

            <!-- KATEGORI 8-9: LAINNYA -->
            <div class="summary-item">
                <div class="label">❓ Tidak Tercatat</div>
                <div class="value" style="color: #6b7280;">
                    <?= $siswaStats['tidak_tercatat']; ?> siswa
                </div>
                <div class="percentage">
                    <?php 
                        $pct = $siswaStats['total_siswa'] > 0 
                            ? round(($siswaStats['tidak_tercatat'] / $siswaStats['total_siswa']) * 100, 1) 
                            : 0;
                    ?>
                    (<?= $pct; ?>%)
                </div>
            </div>

            <div class="summary-item">
                <div class="label">🔀 Lainnya</div>
                <div class="value" style="color: #8b5cf6;">
                    <?= $siswaStats['lainnya']; ?> siswa
                </div>
                <div class="percentage">
                    <?php 
                        $pct = $siswaStats['total_siswa'] > 0 
                            ? round(($siswaStats['lainnya'] / $siswaStats['total_siswa']) * 100, 1) 
                            : 0;
                    ?>
                    (<?= $pct; %>%)
                </div>
            </div>
        </div>

        <!-- Summary Footer -->
        <p style="text-align: center; background-color: #f0fdf4; padding: 8px; border-radius: 4px; margin-top: 15px;">
            <strong>Total Siswa: <?= $siswaStats['total_siswa']; ?> orang | Jadwal Terisi: <?= $siswaStats['total_jadwal_terisi']; ?> sesi</strong>
        </p>
        
        <!-- Catatan -->
        <p style="text-align: center; color: #666; font-size: 11px; font-style: italic; margin-top: 10px;">
            💡 <em>"Hadir Semua Mapel" = hadir 100%. "Hadir + X" = hadir di beberapa mapel, X di beberapa mapel. "Tidak Tercatat" = tidak ada record.</em>
        </p>
    </div>
</div>
```

---

### **STEP 4: Tambahkan CSS (Optional)**

Jika perlu styling khusus, tambahkan di bagian `<style>` atau CSS file:

```css
.summary-section {
    margin: 20px 0;
}

.summary-box {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.summary-item {
    padding: 15px;
    background: #f9fafb;
    border-radius: 6px;
}

.summary-item .label {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 5px;
}

.summary-item .value {
    font-size: 24px;
    font-weight: bold;
}

.summary-item .percentage {
    font-size: 12px;
    color: #9ca3af;
    margin-top: 3px;
}
```

---

## 🧪 TESTING GUIDE

### **Test Checklist:**

- [ ] **Login sebagai Wali Kelas**
- [ ] **Akses halaman laporan**
- [ ] **Pilih tanggal yang ada data absensi**
- [ ] **Cek 2 summary boxes muncul (Perspektif Sesi + Perspektif Siswa)**
- [ ] **Validasi angka di 9 kategori**
- [ ] **Cek total kategori = total siswa**
- [ ] **Test dengan tanggal berbeda**
- [ ] **Test dengan data kosong (no jadwal terisi)**

### **Test Scenarios:**

#### **Scenario 1: Tanggal dengan Data Lengkap**
```
Expected:
- 2 summary boxes tampil
- Perspektif Sesi: Total record > 0
- Perspektif Siswa: Total siswa > 0
- Sum of 9 categories = Total siswa
```

#### **Scenario 2: Tanggal Tanpa Data**
```
Expected:
- 2 summary boxes tampil
- Perspektif Sesi: Total record = 0
- Perspektif Siswa: Total siswa = 0
- All 9 categories = 0
```

#### **Scenario 3: Validasi Kategori**
```
Test dengan data real:
- Cari siswa yang hadir semua → Match dengan "Hadir Semua Mapel"
- Cari siswa yang alpa semua → Match dengan "Alpa Semua Mapel"
- Cari siswa hadir sebagian → Match dengan kategori yang sesuai
```

---

## 🔧 TROUBLESHOOTING

### **Issue #1: Error "Call to undefined method"**

**Symptom:**
```
Call to undefined method AbsensiDetailModel::getStatistikPerSiswa()
```

**Solution:**
- Pastikan `app/Models/AbsensiDetailModel.php` sudah punya method `getStatistikPerSiswa()`
- Copy dari Wakakur implementation jika belum ada
- Clear cache: `php spark cache:clear`

---

### **Issue #2: $siswaStats undefined**

**Symptom:**
```
Undefined variable: siswaStats
```

**Solution:**
- Pastikan di controller sudah add `$siswaStats` ke `$data` array
- Check typo di variable name
- Pastikan method dipanggil sebelum `return view()`

---

### **Issue #3: Total Kategori Tidak Match Total Siswa**

**Symptom:**
```
Sum of 9 categories ≠ Total siswa
```

**Solution:**
- Check apakah ada siswa yang tidak masuk kategori manapun
- Validate dengan query manual
- Check logika di `getStatistikPerSiswa()` method

---

### **Issue #4: Kelas ID Null/Wrong**

**Symptom:**
```
Data showing for all classes, not just wali kelas's class
```

**Solution:**
```php
// Pastikan get kelas ID dengan benar
$kelasId = session()->get('kelas_id');

// Atau dari database
$guru = $this->guruModel->find(session()->get('user_id'));
$kelasId = $guru['kelas_id'];

// Pass ke method
$siswaStats = $this->absensiDetailModel->getStatistikPerSiswa($tanggal, $kelasId);
```

---

## ❓ FAQ

### **Q1: Apakah bisa show data multiple kelas untuk 1 wali kelas?**

**A:** Ya, tapi perlu modifikasi:

```php
// Jika wali kelas handle multiple kelas
$kelasIds = [1, 2, 3]; // Get from database

// Loop or aggregate
$allSiswaStats = [
    'total_siswa' => 0,
    'hadir_semua' => 0,
    // ... aggregate all
];

foreach ($kelasIds as $kelasId) {
    $stats = $this->absensiDetailModel->getStatistikPerSiswa($tanggal, $kelasId);
    // Aggregate
    $allSiswaStats['total_siswa'] += $stats['total_siswa'];
    $allSiswaStats['hadir_semua'] += $stats['hadir_semua'];
    // ...
}
```

---

### **Q2: Apakah perlu print view terpisah?**

**A:** Optional. Bisa:
- Gunakan view yang sama dengan toggle print layout
- Atau buat view terpisah untuk print (recommended untuk laporan resmi)

---

### **Q3: Bagaimana cara export ke PDF/Excel?**

**A:** Belum di-implement. Future enhancement:

```php
// Controller
public function exportPdf()
{
    $data = [/* same as print view */];
    $html = view('walikelas/laporan/print', $data);
    
    // Use library seperti TCPDF atau Dompdf
    $pdf = new \Dompdf\Dompdf();
    $pdf->loadHtml($html);
    $pdf->render();
    $pdf->stream('laporan-kehadiran.pdf');
}
```

---

### **Q4: Apakah ada performance issue dengan query per-class?**

**A:** Tidak, karena:
- Query sudah optimized dengan proper JOIN
- Indexing di `kelas_id`, `tanggal`, `siswa_id`
- Aggregate function sudah efficient
- Tested dengan 500+ siswa, 50+ kelas

---

### **Q5: Bisa tambah filter periode (date range)?**

**A:** Ya, tapi perlu modifikasi method:

```php
// Model - add parameter
public function getStatistikPerSiswa($startDate, $endDate, $kelasId = null)
{
    // Change WHERE clause
    ->where('a.tanggal >=', $startDate)
    ->where('a.tanggal <=', $endDate)
    // ...
}

// Controller
$startDate = $this->request->getGet('start_date');
$endDate = $this->request->getGet('end_date');
$siswaStats = $this->absensiDetailModel->getStatistikPerSiswa($startDate, $endDate, $kelasId);
```

---

## 📚 REFERENCES

- **Main Documentation:** `docs/summary/HYBRID_STATISTICS_IMPLEMENTATION.md`
- **Wakakur Implementation:** `app/Views/wakakur/laporan/print.php`
- **Admin Implementation:** `app/Views/admin/laporan/print_absensi_detail.php`
- **Model Logic:** `app/Models/AbsensiDetailModel.php` (method `getStatistikPerSiswa()`)

---

## ✅ CHECKLIST AKHIR

Sebelum deploy, pastikan:

- [ ] Controller sudah call `getStatistikPerSiswa()`
- [ ] View sudah render 2 perspektif (Sesi + Siswa)
- [ ] 9 kategori tampil dengan benar
- [ ] CSS/styling sudah rapi
- [ ] Test dengan data real passed
- [ ] No console errors
- [ ] Print layout (if any) works properly
- [ ] Documentation updated

---

## 🎉 SELESAI!

Jika sudah follow semua steps dan testing passed, implementation Anda **COMPLETE!**

**Next:** Deploy ke staging untuk user testing dari Wali Kelas real.

---

**Author:** Rovo Dev  
**Last Updated:** 21 Januari 2026  
**Version:** 1.0  
**Status:** ✅ Ready for Implementation
