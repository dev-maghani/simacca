# Fix: Mode Guru Pengganti - Akses Semua Jadwal

## 🐛 Problem Statement
Ketika guru ingin menjadi pengganti untuk guru lain yang berhalangan, mereka tidak bisa memilih jadwal karena sistem hanya menampilkan jadwal milik guru yang sedang login. Pesan error muncul: **"Tidak ada jadwal untuk hari ini"** padahal ada jadwal di hari tersebut.

## 🎯 Solusi
Menambahkan **Mode Selection** yang memungkinkan guru memilih antara:
1. **Jadwal Saya Sendiri** (Normal Mode) - Mengajar sesuai jadwal reguler
2. **Guru Pengganti** (Substitute Mode) - Menggantikan guru lain yang berhalangan

## 🔧 Implementasi

### 1. Backend Controller (app/Controllers/Guru/AbsensiController.php)

#### A. Method `getJadwalByHari()` - Enhanced AJAX Endpoint
**Sebelum:**
```php
$jadwal = $this->jadwalModel->getByGuru($guru['id'], $hari);
```

**Sesudah:**
```php
// Check if this is for substitute teacher mode
$isSubstitute = $this->request->getGet('substitute') === 'true';

if ($isSubstitute) {
    // Get ALL schedules for this day (for substitute teachers)
    $jadwal = $this->jadwalModel->select('jadwal_mengajar.*, 
                                        mata_pelajaran.nama_mapel, 
                                        kelas.nama_kelas,
                                        guru.nama_lengkap as nama_guru')
        ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
        ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
        ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
        ->where('hari', $hari)
        ->orderBy('jam_mulai', 'ASC')
        ->findAll();
} else {
    // Get only this teacher's schedules
    $jadwal = $this->jadwalModel->getByGuru($guru['id'], $hari);
}
```

**Keuntungan:**
- Mode normal: Hanya tampilkan jadwal sendiri
- Mode pengganti: Tampilkan SEMUA jadwal dengan nama guru asli
- Response includes `isSubstitute` flag untuk frontend

#### B. Method `store()` - Smart Substitute Detection
**Sebelum:**
```php
// Verify jadwal belongs to this teacher
$jadwal = $this->jadwalModel->find($jadwalId);
if (!$jadwal || $jadwal['guru_id'] != $guru['id']) {
    $this->session->setFlashdata('error', 'Jadwal tidak valid.');
    return redirect()->back()->withInput();
}

$absensiData = [
    ...
    'guru_pengganti_id' => $this->request->getPost('guru_pengganti_id') ?: null,
];
```

**Sesudah:**
```php
// Verify jadwal exists
$jadwal = $this->jadwalModel->find($jadwalId);
if (!$jadwal) {
    $this->session->setFlashdata('error', 'Jadwal tidak valid.');
    return redirect()->back()->withInput();
}

// Determine if this is substitute mode
$isSubstituteMode = ($jadwal['guru_id'] != $guru['id']);

// Set guru_pengganti_id based on mode
$guruPenggantiId = null;
if ($isSubstituteMode) {
    // Substitute mode: current teacher is the substitute
    $guruPenggantiId = $guru['id'];
} else {
    // Normal mode: can optionally have a substitute (from form input)
    $guruPenggantiId = $this->request->getPost('guru_pengganti_id') ?: null;
}

$absensiData = [
    ...
    'guru_pengganti_id' => $guruPenggantiId,
];
```

**Keuntungan:**
- **Auto-detection**: Sistem otomatis tahu kapan mode pengganti
- **Smart logic**: Jika jadwal bukan milik guru login → mode pengganti
- **No manual input**: Guru tidak perlu pilih dirinya sendiri di dropdown

### 2. Frontend View (app/Views/guru/absensi/create.php)

#### A. Mode Selection UI
```html
<!-- Mode Selection -->
<div class="mb-6">
    <label class="block text-sm font-semibold text-gray-700 mb-3">
        <i class="fas fa-question-circle mr-2 text-blue-500"></i>
        Mode Input Absensi
    </label>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <button type="button" id="modeOwnSchedule" 
                class="mode-btn active ...">
            <div class="text-center">
                <i class="fas fa-chalkboard-teacher text-2xl text-blue-600 mb-2"></i>
                <p class="font-bold text-gray-800">Jadwal Saya Sendiri</p>
                <p class="text-xs text-gray-600 mt-1">Mengajar sesuai jadwal reguler</p>
            </div>
        </button>
        <button type="button" id="modeSubstitute" 
                class="mode-btn ...">
            <div class="text-center">
                <i class="fas fa-user-plus text-2xl text-purple-600 mb-2"></i>
                <p class="font-bold text-gray-800">Guru Pengganti</p>
                <p class="text-xs text-gray-600 mt-1">Menggantikan guru lain</p>
            </div>
        </button>
    </div>
</div>
```

**Features:**
- Visual toggle dengan icon yang jelas
- Active state dengan border dan background color
- Deskripsi untuk setiap mode

#### B. JavaScript Enhancement
```javascript
// Mode selection state
let isSubstituteMode = false;

// Handle mode selection
document.getElementById('modeOwnSchedule').addEventListener('click', function() {
    isSubstituteMode = false;
    updateModeUI();
    // Reset jadwal selection
    document.getElementById('jadwal_id').innerHTML = '<option value="">Pilih Jadwal</option>';
    document.getElementById('hari').value = '';
});

document.getElementById('modeSubstitute').addEventListener('click', function() {
    isSubstituteMode = true;
    updateModeUI();
    // Reset jadwal selection
    document.getElementById('jadwal_id').innerHTML = '<option value="">Pilih Jadwal</option>';
    document.getElementById('hari').value = '';
});

function updateModeUI() {
    const jadwalLabel = document.getElementById('jadwalLabel');
    
    if (isSubstituteMode) {
        jadwalLabel.innerHTML = '<i class="fas fa-exchange-alt mr-1 text-purple-500"></i> Jadwal yang Digantikan';
    } else {
        jadwalLabel.textContent = 'Jadwal';
    }
    // Update button styles...
}

// AJAX call with substitute parameter
document.getElementById('hari').addEventListener('change', function() {
    const url = `<?= base_url('guru/absensi/getJadwalByHari'); ?>?hari=${hari}&substitute=${isSubstituteMode}`;
    
    fetch(url, {...})
        .then(response => response.json())
        .then(data => {
            if (data.success && data.jadwal.length > 0) {
                let options = '<option value="">Pilih Jadwal</option>';
                data.jadwal.forEach(jadwal => {
                    if (data.isSubstitute && jadwal.nama_guru) {
                        // Show teacher name for substitute mode
                        options += `<option value="${jadwal.id}">${jadwal.nama_mapel} - ${jadwal.nama_kelas} (${waktu}) - Guru: ${jadwal.nama_guru}</option>`;
                    } else {
                        options += `<option value="${jadwal.id}">${jadwal.nama_mapel} - ${jadwal.nama_kelas} (${waktu})</option>`;
                    }
                });
                jadwalSelect.innerHTML = options;
            }
        });
});
```

**Features:**
- State management untuk mode selection
- Dynamic label update
- AJAX dengan parameter `substitute`
- Conditional display: tampilkan nama guru di mode pengganti

## 📊 Flow Diagram

### Mode Normal (Jadwal Sendiri)
```
[Guru Login] 
    ↓
[Pilih Mode: Jadwal Saya Sendiri]
    ↓
[Pilih Hari] → AJAX: getJadwalByHari?substitute=false
    ↓
[Sistem tampilkan HANYA jadwal guru tersebut]
    ↓
[Pilih Jadwal] → [Isi Absensi]
    ↓
[Optional: Pilih guru pengganti lain jika ada]
    ↓
[Simpan] → guru_pengganti_id = dari dropdown (opsional)
```

### Mode Pengganti
```
[Guru Login sebagai Pengganti]
    ↓
[Pilih Mode: Guru Pengganti]
    ↓
[Pilih Hari] → AJAX: getJadwalByHari?substitute=true
    ↓
[Sistem tampilkan SEMUA jadwal di hari tersebut + nama guru]
    ↓
[Pilih Jadwal Guru Lain] → [Isi Absensi]
    ↓
[Simpan] → sistem otomatis: guru_pengganti_id = ID guru yang login
```

## ✅ Test Scenarios

### Test 1: Mode Normal - Tanpa Pengganti
```
Input:
- Mode: Jadwal Saya Sendiri
- Guru: Guru A (ID: 1)
- Jadwal: Matematika Kelas X
- Guru Pengganti: (kosong)

Expected Result:
- jadwal_mengajar.guru_id = 1 (Guru A)
- absensi.created_by = 1 (Guru A)
- absensi.guru_pengganti_id = NULL

Display:
- Guru: Guru A
- Guru Pengganti: (tidak ditampilkan)
```

### Test 2: Mode Normal - Dengan Pengganti
```
Input:
- Mode: Jadwal Saya Sendiri
- Guru: Guru A (ID: 1)
- Jadwal: Matematika Kelas X
- Guru Pengganti: Guru B (ID: 2)

Expected Result:
- jadwal_mengajar.guru_id = 1 (Guru A)
- absensi.created_by = 1 (Guru A)
- absensi.guru_pengganti_id = 2 (Guru B)

Display:
- Guru: Guru A
- Guru Pengganti: Guru B (badge: Piket Pengganti)
```

### Test 3: Mode Pengganti
```
Input:
- Mode: Guru Pengganti
- Guru Login: Guru B (ID: 2)
- Hari: Senin
- Jadwal Dipilih: Matematika Kelas X (guru_id = 1, Guru A)

Expected Result:
- jadwal_mengajar.guru_id = 1 (Guru A - guru asli)
- absensi.created_by = 2 (Guru B - yang input)
- absensi.guru_pengganti_id = 2 (Guru B - otomatis)

Display:
- Guru: Guru A
- Guru Pengganti: Guru B (badge: Piket Pengganti)
```

## 🎨 UI Changes

### Before
```
[Pilih Hari] [Pilih Jadwal] [Tanggal]
↓
Hanya jadwal guru yang login
Error jika tidak ada jadwal
```

### After
```
[Mode Selection: Jadwal Sendiri | Guru Pengganti]
↓
[Pilih Hari] [Pilih Jadwal (label dynamic)] [Tanggal]
↓
Mode Normal: Jadwal sendiri
Mode Pengganti: SEMUA jadwal + nama guru asli
```

## 📝 Summary

### Changes Made
1. ✅ Backend: Enhanced `getJadwalByHari()` dengan parameter `substitute`
2. ✅ Backend: Smart detection di `store()` untuk auto-set guru_pengganti_id
3. ✅ Frontend: Mode selection UI dengan toggle buttons
4. ✅ Frontend: JavaScript untuk mode switching dan AJAX enhancement
5. ✅ Frontend: Dynamic label dan conditional display
6. ✅ Documentation: Updated GURU_PENGGANTI_FEATURE.md

### Benefits
- **Fleksibilitas**: Guru bisa input absensi sendiri atau sebagai pengganti
- **User-Friendly**: Interface yang jelas dengan visual feedback
- **Otomatis**: Sistem otomatis detect mode dan set guru pengganti
- **Akurat**: Data tercatat dengan benar untuk pelaporan
- **Audit Trail**: Jelas siapa guru asli dan siapa pengganti

### Files Modified
1. `app/Controllers/Guru/AbsensiController.php`
   - Method `getJadwalByHari()` - Added substitute mode support
   - Method `store()` - Smart substitute detection
   
2. `app/Views/guru/absensi/create.php`
   - Added mode selection UI
   - Enhanced JavaScript for mode switching
   - Dynamic label and conditional display
   
3. `GURU_PENGGANTI_FEATURE.md`
   - Updated usage instructions
   - Added substitute mode documentation
   - Enhanced testing scenarios

## 🚀 Deployment Notes

### No Breaking Changes
- Existing functionality tetap bekerja (backward compatible)
- Default mode: "Jadwal Saya Sendiri" (existing behavior)
- Database schema tidak berubah (menggunakan field yang sudah ada)

### Testing Required
1. Test mode normal dengan dan tanpa pengganti
2. Test mode pengganti untuk berbagai jadwal
3. Verify laporan admin menampilkan data dengan benar
4. Test dengan multiple guru secara simultan

---

**Created**: 2026-01-12  
**Issue**: Guru pengganti tidak bisa akses jadwal guru lain  
**Status**: ✅ Resolved  
**Impact**: High (core functionality untuk piket/pengganti)
