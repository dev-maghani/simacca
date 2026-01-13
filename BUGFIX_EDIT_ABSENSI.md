# Bug Fix: Edit Absensi Tidak Tersimpan

## 🐛 Masalah
Data absensi tidak tersimpan saat user melakukan edit di halaman `app/Views/guru/absensi/edit.php`

## 🔍 Root Cause Analysis

### Masalah Utama: **ENUM Case Sensitivity Mismatch**

1. **Database Schema** (ENUM constraint):
   ```sql
   status ENUM('hadir', 'izin', 'sakit', 'alpa')  -- lowercase
   ```

2. **Form mengirimkan** (sebelum fix):
   ```php
   ['Hadir', 'Izin', 'Sakit', 'Alpha']  // capitalized
   ```

3. **Hasil**: Database **reject** nilai karena tidak match dengan ENUM constraint

### Masalah Tambahan:
- JavaScript event listener men-disable tombol submit terlalu cepat
- Tidak ada validasi client-side yang memadai
- Error handling kurang informatif
- Perbedaan penamaan: `Alpha` vs `alpa`

## 🛠️ Solusi yang Diterapkan

### 1. Frontend (`app/Views/guru/absensi/edit.php`)

#### A. Normalisasi Status Values
```php
// BEFORE
foreach (['Hadir', 'Izin', 'Sakit', 'Alpha'] as $statusValue)

// AFTER
foreach (['hadir', 'izin', 'sakit', 'alpa'] as $statusValue)
```

#### B. Hidden Input Normalization
```php
// BEFORE
<input type="hidden" name="siswa[<?= $siswa['id'] ?>][status]" value="<?= $currentStatus ?>">

// AFTER  
<input type="hidden" name="siswa[<?= $siswa['id'] ?>][status]" value="<?= strtolower($currentStatus) ?>">
```

#### C. Display Label untuk UI
```php
// Tetap tampilkan dengan huruf besar untuk UI yang bagus
$displayLabel = ucfirst($statusValue);
if ($statusValue == 'alpa') {
    $displayLabel = 'Alpha';
}
```

#### D. JavaScript Button Styles
```javascript
// BEFORE
const desktopButtonStyles = {
    'Hadir': { ... },
    'Izin': { ... }
}

// AFTER
const desktopButtonStyles = {
    'hadir': { ... },
    'izin': { ... },
    'alpa': { ... }  // bukan 'Alpha'
}
```

#### E. Fix Submit Event Listener
```javascript
// Delay disable button agar form sempat tersubmit
setTimeout(function() {
    if (clickedButton && clickedButton.tagName === 'BUTTON') {
        clickedButton.disabled = true;
        // ...
    }
}, 100);  // 100ms delay
```

#### F. Validasi Client-Side
```javascript
// Cek apakah ada data sebelum submit
const hiddenInputs = document.querySelectorAll('.status-input');
let hasData = false;

hiddenInputs.forEach(input => {
    if (input.value && input.value !== '') {
        hasData = true;
    }
});

if (!hasData) {
    e.preventDefault();
    alert('Mohon isi setidaknya satu status kehadiran siswa!');
    return false;
}
```

### 2. Backend (`app/Controllers/Guru/AbsensiController.php`)

#### A. Status Normalization & Validation
```php
// Normalize status to lowercase
$status = strtolower(trim($data['status']));

// Handle Alpha -> alpa conversion
if ($status === 'alpha') {
    $status = 'alpa';
}

// Validate against allowed values
$validStatuses = ['hadir', 'izin', 'sakit', 'alpa'];
if (!in_array($status, $validStatuses)) {
    log_message('warning', 'Invalid status "' . $data['status'] . '" for siswa_id: ' . $siswaId);
    continue;
}
```

#### B. Enhanced Logging
```php
// Log setiap proses untuk debugging
log_message('info', 'Updating absensi ID: ' . $id . ' with ' . count($siswaData) . ' students');
log_message('debug', 'Updated siswa_id: ' . $siswaId . ' with status: ' . $status);
```

#### C. Improved Flash Messages
```php
$this->session->setFlashdata('success', 
    'Nice! Absen sudah diupdate 🎉 (Diubah: ' . $updateCount . ', Ditambah: ' . $insertCount . ')'
);
```

## 📋 Files Modified

1. ✅ `app/Views/guru/absensi/edit.php`
   - Normalized status values to lowercase
   - Added display labels for UI
   - Fixed JavaScript event handlers
   - Enhanced client-side validation

2. ✅ `app/Controllers/Guru/AbsensiController.php`
   - Added status normalization
   - Added validation for status values
   - Enhanced error logging
   - Improved flash messages

## 🧪 Testing Instructions

### Manual Testing:
1. Login sebagai guru
2. Buka `/guru/absensi`
3. Edit salah satu absensi (< 24 jam)
4. Ubah beberapa status siswa
5. Klik "Simpan Perubahan"
6. Verifikasi flash message muncul
7. Edit lagi untuk memastikan data tersimpan

### Browser Console Debugging:
1. Buka Developer Tools (F12)
2. Lihat console saat submit form
3. Verifikasi data format: `siswa[1][status]: hadir` (lowercase)

### Server Log Checking:
```bash
tail -f writable/logs/log-$(date +%Y-%m-%d).php
```

Look for:
- `INFO - Updating absensi ID: X with Y students`
- `DEBUG - Updated siswa_id: X with status: hadir`
- `INFO - Absensi updated successfully. Updated: X, Inserted: Y`

## ✅ Expected Results

- ✅ Form submit berhasil tanpa error
- ✅ Flash message: "Nice! Absen sudah diupdate 🎉 (Diubah: X, Ditambah: Y)"
- ✅ Data tersimpan di database dengan status lowercase
- ✅ Saat di-edit lagi, status ditampilkan dengan benar
- ✅ Button highlight sesuai status yang tersimpan
- ✅ Console log menampilkan data yang akan dikirim

## 🎯 Technical Notes

### Database ENUM Constraint
```sql
-- Migration: 2026-01-06-163214_CreateAbsensiDetailTable.php
'status' => [
    'type' => 'ENUM',
    'constraint' => ['hadir', 'izin', 'sakit', 'alpa'],  // MUST be lowercase
    'default' => 'alpa',
]
```

### Important: Case Sensitivity
- MySQL ENUM values are **case-sensitive**
- Always use lowercase for database operations
- Use capitalized labels only for UI display
- Convert `Alpha` → `alpa` before saving

### JavaScript Status Mapping
```javascript
// Always use lowercase in JavaScript
selectStatus(siswaId, 'hadir');  // ✅ Correct
selectStatus(siswaId, 'Hadir');  // ❌ Wrong
```

## 📝 Related Issues

- Initial issue also had JavaScript timing problem (button disabled too early)
- No client-side validation before
- Error messages were not informative enough

All fixed in this update! 🎉

---

**Date:** 2026-01-14  
**Author:** Rovo Dev  
**Status:** ✅ Fixed & Tested
