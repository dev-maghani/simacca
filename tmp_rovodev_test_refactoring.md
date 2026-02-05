# Testing Guide - Refactored Controllers

## 🧪 Testing Checklist for Service Layer Refactoring

This guide will help you verify that all refactored controllers are working correctly.

---

## **1. Admin/KelasController Tests**

### **Test Cases:**
- [ ] **List All Kelas** - Navigate to `/admin/kelas`
  - ✅ Should display list of classes with student counts
  - ✅ Should show wali kelas information
  - ✅ No errors in console/logs

- [ ] **Create New Kelas** - Click "Tambah Kelas"
  - ✅ Form should load properly
  - ✅ Try creating with valid data
  - ✅ Try creating with duplicate name (should show error)
  - ✅ Success message on valid creation

- [ ] **Edit Kelas** - Click edit button on any kelas
  - ✅ Form pre-filled with existing data
  - ✅ Update kelas name
  - ✅ Assign/change wali kelas
  - ✅ Success message on update

- [ ] **Delete Kelas** - Try deleting a kelas
  - ✅ Should prevent deletion if students exist
  - ✅ Should delete if no students
  - ✅ Success message on deletion

- [ ] **View Kelas Detail** - Click view/show button
  - ✅ Should display full kelas information
  - ✅ Should list all students in the class

---

## **2. Admin/MataPelajaranController Tests**

### **Test Cases:**
- [ ] **List All Mata Pelajaran** - Navigate to `/admin/mata-pelajaran`
  - ✅ Should display list of subjects
  - ✅ Should show kategori (umum/kejuruan)
  - ✅ Pagination works

- [ ] **Create New Mapel** - Click "Tambah Mata Pelajaran"
  - ✅ Form loads properly
  - ✅ Try creating with valid data
  - ✅ Try duplicate kode_mapel (should error)
  - ✅ Success message appears

- [ ] **Edit Mapel** - Edit existing subject
  - ✅ Form pre-filled correctly
  - ✅ Can update all fields
  - ✅ Success message on update

- [ ] **Delete Mapel** - Try deleting a subject
  - ✅ Should prevent if used in jadwal
  - ✅ Should prevent if assigned to guru
  - ✅ Success message if allowed

---

## **3. Guru/JurnalController Tests**

### **Test Cases:**
- [ ] **List Jurnal** - Navigate to `/guru/jurnal` (login as guru)
  - ✅ Should display grouped by kelas
  - ✅ Should show total pertemuan per kelas
  - ✅ Date filtering works

- [ ] **Create Jurnal** - Create from absensi list
  - ✅ Form loads with absensi data
  - ✅ Can fill all fields
  - ✅ Can upload foto dokumentasi
  - ✅ Success message on creation
  - ✅ Should redirect to duplicate if exists

- [ ] **Edit Jurnal** - Edit existing jurnal
  - ✅ Form pre-filled correctly
  - ✅ Can update text fields
  - ✅ Can upload new photo
  - ✅ Can remove existing photo
  - ✅ Success message on update

- [ ] **View Jurnal List** - Click on a kelas
  - ✅ Shows all jurnal for that kelas
  - ✅ Displays properly formatted

- [ ] **Print Jurnal** - Click print button
  - ✅ Print view loads
  - ✅ Date filtering works
  - ✅ Data displays correctly

---

## **4. Siswa/IzinController Tests**

### **Test Cases:**
- [ ] **View Izin List** - Navigate to `/siswa/izin` (login as siswa)
  - ✅ Should display student's izin history
  - ✅ Should show status counts (pending/approved/rejected)
  - ✅ Status filtering works

- [ ] **Create Izin** - Click "Ajukan Izin"
  - ✅ Form loads properly
  - ✅ Can select date, jenis_izin
  - ✅ Can write alasan
  - ✅ Can upload berkas (optional)
  - ✅ Success message on creation
  - ✅ Should prevent duplicate for same date

- [ ] **View Izin Status** - Check created izin
  - ✅ Shows as "pending"
  - ✅ Displays all information correctly

---

## **5. WaliKelas/IzinController Tests**

### **Test Cases:**
- [ ] **View Pending Izin** - Navigate to `/walikelas/izin` (login as wali kelas)
  - ✅ Should display kelas students' izin
  - ✅ Should show status counts
  - ✅ Status filtering works

- [ ] **Approve Izin** - Click approve on pending izin
  - ✅ Modal/form appears
  - ✅ Can add catatan (optional)
  - ✅ Success message on approval
  - ✅ Status changes to "disetujui"

- [ ] **Reject Izin** - Click reject on pending izin
  - ✅ Modal/form appears
  - ✅ Can add catatan
  - ✅ Success message on rejection
  - ✅ Status changes to "ditolak"

---

## **🔍 Error Checking**

### **Check Application Logs:**
```bash
# View logs for any errors
tail -f writable/logs/log-*.php
```

### **Check Browser Console:**
- Open Developer Tools (F12)
- Check Console tab for JavaScript errors
- Check Network tab for failed requests

### **Database Verification:**
After each test, verify in database that:
- Records are created correctly
- Foreign keys are maintained
- Transactions committed properly

---

## **✅ Success Criteria**

All tests should pass with:
- ✅ No PHP errors in logs
- ✅ No JavaScript errors in console
- ✅ All CRUD operations work correctly
- ✅ Validation messages appear properly
- ✅ Success/error messages are user-friendly
- ✅ Data persists correctly in database
- ✅ Service layer responses are consistent

---

## **🐛 Common Issues to Check**

1. **Service not found errors**
   - Verify namespace: `use App\Services\ServiceName;`
   - Check service file exists

2. **Method does not exist**
   - Verify you're calling correct service method
   - Check method signature matches

3. **Data not displaying**
   - Check `$result['success']` before accessing `$result['data']`
   - Verify service returns expected structure

4. **Validation errors**
   - Service handles validation internally
   - Check error messages in `$result['message']`

5. **File upload issues**
   - Ensure writable/uploads directories exist
   - Check file permissions (755)
   - Verify upload path in controllers

---

## **📝 Manual Testing Steps**

### **Quick Smoke Test (15 minutes):**

1. **Login as Admin**
   - Create a new kelas ✅
   - Create a new mata pelajaran ✅
   - Verify both appear in lists ✅

2. **Login as Guru**
   - View jurnal list ✅
   - Create a new jurnal (if absensi exists) ✅
   - Upload a photo ✅

3. **Login as Siswa**
   - Submit an izin request ✅
   - View izin status ✅

4. **Login as Wali Kelas**
   - View pending izin ✅
   - Approve one izin ✅
   - Reject one izin ✅

5. **Verify in Database**
   - Check all records created ✅
   - Check status updates ✅

---

## **🚀 Next Steps After Testing**

If all tests pass:
- ✅ Mark Week 2 Service Layer as COMPLETE
- ✅ Update TODO.md with test results
- ✅ Consider refactoring remaining controllers
- ✅ Move to Week 3 tasks (Repository Pattern)

If tests fail:
- 🐛 Note which specific test failed
- 📋 Check error logs for details
- 🔧 Fix the issue in service or controller
- 🔄 Re-test until passing

---

## **📊 Test Results Log**

Record your results here:

| Controller | Status | Notes |
|-----------|--------|-------|
| Admin/KelasController | ⏳ | |
| Admin/MataPelajaranController | ⏳ | |
| Guru/JurnalController | ⏳ | |
| Siswa/IzinController | ⏳ | |
| WaliKelas/IzinController | ⏳ | |

**Legend:**
- ⏳ = Not tested yet
- ✅ = All tests passed
- ⚠️ = Some issues found
- ❌ = Failed

---

**Good luck with testing! 🎯**
