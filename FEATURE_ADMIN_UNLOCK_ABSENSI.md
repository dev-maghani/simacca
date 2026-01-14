# Feature: Admin Unlock Absensi

## Overview
Fitur ini memungkinkan admin untuk membuka kembali absensi yang sudah terkunci (lebih dari 24 jam) agar guru bisa mengedit kembali.

## Problem Statement
- Sistem memiliki rule: absensi hanya bisa diedit dalam 24 jam setelah dibuat
- Jika guru perlu koreksi data setelah 24 jam, mereka tidak bisa edit
- Admin perlu cara untuk memberikan akses edit kembali kepada guru

## Solution
Admin dapat "unlock" absensi yang terkunci, yang akan:
1. **Reset timer 24 jam** dari waktu unlock (bukan dari `created_at`)
2. Guru mendapat **24 jam baru** untuk edit absensi tersebut
3. Tracking via kolom `unlocked_at` di database

## Database Changes

### Migration: `2026-01-14-131800_AddUnlockedAtToAbsensi.php`
Menambahkan kolom baru ke tabel `absensi`:

```sql
ALTER TABLE `absensi` 
ADD `unlocked_at` DATETIME NULL 
COMMENT 'Timestamp when admin unlocked this absensi for editing' 
AFTER `created_at`;
```

### Model Update: `AbsensiModel.php`
- Added `unlocked_at` to `$allowedFields`

## Logic Changes

### Helper/BaseController: `isAbsensiEditable()`
**Before:**
```php
$diffHours = (now - created_at) / 3600;
return $diffHours <= 24;
```

**After:**
```php
// If admin unlocked, check against unlocked_at instead
if (!empty($absensi['unlocked_at'])) {
    $diffHours = (now - unlocked_at) / 3600;
    return $diffHours <= 24; // 24 hours from unlock
}

// Otherwise, check created_at (original behavior)
$diffHours = (now - created_at) / 3600;
return $diffHours <= 24;
```

## New Features

### 1. Admin Kelola Absensi Page
**Route:** `/admin/absensi`

**Features:**
- List semua absensi dengan filter:
  - Tanggal (dari - sampai)
  - Kelas
  - Guru
  - Mata Pelajaran
  - Status Lock (Terkunci / Dapat Diedit)
- Status badge untuk setiap absensi:
  - 🔓 **Dapat Diedit** (hijau) - masih dalam 24 jam
  - 🔒 **Terkunci** (merah) - sudah lewat 24 jam
- Tampilkan berapa jam sudah berlalu sejak created/unlocked
- Info jika absensi pernah di-unlock sebelumnya

### 2. Unlock Single Absensi
**Route:** `GET /admin/absensi/unlock/{id}`

**Action:**
- Set `unlocked_at = NOW()`
- Redirect back dengan success message
- Message include: nama guru, mapel, kelas, tanggal

**Confirmation:**
```
Unlock absensi?

Guru: Ahmad Dahlan, S.Pd
Mapel: Matematika
Tanggal: 12 Jan 2026

Setelah unlock, guru dapat mengedit selama 24 jam.
```

### 3. Bulk Unlock (Multiple Selection)
**Route:** `POST /admin/absensi/bulk-unlock`

**Action:**
- Admin select multiple checkbox
- Click "Unlock Terpilih (N)" button
- All selected absensi get unlocked simultaneously
- Return JSON response with success count

## UI/UX Details

### Status Badge Display
```
✅ Dapat Diedit
   12.5 jam lalu

🔒 Terkunci  
   36.2 jam lalu
   ⓘ Pernah di-unlock
```

### Filter Form
- Default range: First day of month → Today
- Responsive design (mobile friendly)
- Reset button to clear all filters

### Sidebar Menu
New menu item untuk admin:
```
📋 Kelola Absensi
   /admin/absensi
```

## Security & Authorization

### Access Control
- **Only admin role** can access `/admin/absensi/*`
- Check via `role:admin` filter in routes
- Check via `$this->hasRole('admin')` in controller

### Audit Trail
- `unlocked_at` timestamp records when admin unlocked
- Can see in database which absensi were manually unlocked
- Future enhancement: log `unlocked_by` (admin user_id)

## User Flow

### Scenario 1: Single Unlock
1. Admin login → Navigate to "Kelola Absensi"
2. Set filter (optional): date range, kelas, guru
3. Find locked absensi (red badge 🔒)
4. Click "Unlock" button
5. Confirm action
6. Success message appears
7. Guru notification (future: email/notif)
8. Guru can edit absensi for 24 hours

### Scenario 2: Bulk Unlock
1. Admin filters absensi by date range
2. Check multiple locked absensi
3. Click "Unlock Terpilih (5)"
4. Confirm bulk action
5. All 5 absensi unlocked
6. Toast notification: "Berhasil unlock 5 absensi"

## Technical Implementation

### Files Created
1. `app/Controllers/Admin/AbsensiController.php` - New controller
2. `app/Views/admin/absensi/index.php` - List & unlock UI
3. `app/Database/Migrations/2026-01-14-131800_AddUnlockedAtToAbsensi.php` - DB migration

### Files Modified
1. `app/Models/AbsensiModel.php` - Added `unlocked_at` to allowedFields
2. `app/Helpers/auth_helper.php` - Updated `is_absensi_editable()` + sidebar menu
3. `app/Controllers/BaseController.php` - Updated `isAbsensiEditable()`
4. `app/Config/Routes.php` - Added 3 new routes

## Testing Checklist

- [x] Migration runs successfully
- [ ] Admin can access `/admin/absensi` page
- [ ] List absensi displays correctly with filters
- [ ] Status badges show correct lock status
- [ ] Single unlock button works
- [ ] Bulk unlock with checkboxes works
- [ ] After unlock, guru can edit absensi
- [ ] After 24 hours from unlock, absensi locks again
- [ ] Non-admin cannot access unlock features
- [ ] Mobile responsive layout works

## Future Enhancements

### 1. Extended Unlock Options
- Admin input custom hours (2h, 6h, 12h, 24h, unlimited)
- Unlock reason/notes field
- Notification to guru when unlocked

### 2. Audit Log
- Track `unlocked_by` (admin user_id)
- Log table: `absensi_unlock_log`
  - id, absensi_id, unlocked_by, unlocked_at, unlock_duration, reason

### 3. Auto-Lock After Period
- Cron job to re-lock after specified hours
- Email reminder to guru 2 hours before re-lock

### 4. Permission Granularity
- `can_unlock_absensi` permission
- Separate from admin role
- Wali kelas can unlock their class absensi only

## Related Documentation
- `BUGFIX_IMPORT_JADWAL_EXCEL_TIME.md` - Import jadwal time format fix
- `IMPORT_JADWAL_DOCUMENTATION.md` - Jadwal import feature docs

## Date Implemented
2026-01-14

## Version
1.0.0
