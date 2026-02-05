# ⚡ Quick Test Checklist - 15 Minutes

## 🎯 Fast Testing Guide

### **Pre-Test Setup**
```bash
# 1. Start server
php spark serve

# 2. Open in browser
http://localhost:8080

# 3. Watch logs in separate terminal
tail -f writable/logs/log-*.php
```

---

## ✅ **5-Minute Smoke Test**

### **Test 1: Admin/KelasController (2 min)**
- [ ] Login as admin
- [ ] Go to `/admin/kelas`
- [ ] Click "Tambah Kelas"
- [ ] Fill form and submit
- [ ] Check success message appears
- [ ] Verify in list

### **Test 2: Admin/MataPelajaranController (1 min)**
- [ ] Go to `/admin/mata-pelajaran`
- [ ] Click "Tambah Mata Pelajaran"
- [ ] Fill form and submit
- [ ] Check success message
- [ ] Verify in list

### **Test 3: Guru/JurnalController (2 min)**
- [ ] Logout and login as guru
- [ ] Go to `/guru/jurnal`
- [ ] Verify list displays
- [ ] Click on any kelas (if exists)
- [ ] Check details load

---

## 🔍 **What to Look For**

### **✅ Good Signs:**
- Pages load without errors
- Forms submit successfully
- Data appears in lists
- Success messages show
- No red errors in logs

### **❌ Red Flags:**
- PHP errors on page
- Blank pages
- "Service not found" errors
- Data not saving
- Console JavaScript errors

---

## 🐛 **Common Quick Fixes**

### **If "Class not found":**
```php
// Add to controller top:
use App\Services\ServiceName;
```

### **If "Call to undefined method":**
```php
// Check you're calling right method:
$result = $this->serviceName->methodName();
```

### **If data not showing:**
```php
// Always check success first:
if ($result['success']) {
    $data = $result['data'];
} else {
    // Handle error
}
```

---

## 📊 **Test Results - Mark As You Go**

| Test | Status | Notes |
|------|--------|-------|
| Kelas Create | ⏳ | |
| Kelas Edit | ⏳ | |
| Mapel Create | ⏳ | |
| Mapel Edit | ⏳ | |
| Jurnal View | ⏳ | |
| Jurnal Create | ⏳ | |
| Izin Submit | ⏳ | |
| Izin Approve | ⏳ | |

**Legend:** ⏳ Pending | ✅ Pass | ❌ Fail

---

## 🚀 **After Quick Test**

### **If All Pass:**
- ✅ Mark refactoring complete
- ✅ Update TODO.md
- ✅ Continue with remaining controllers
- ✅ Run full unit tests

### **If Issues Found:**
1. Note which test failed
2. Check `writable/logs/` for error
3. Check browser console (F12)
4. Fix the specific issue
5. Re-test that feature

---

## 💡 **Pro Tips**

- Test one controller at a time
- Keep browser console open (F12)
- Watch logs in real-time
- Test happy path first (valid data)
- Then test error cases (invalid data)

---

## ⏱️ **Time Budget**

- Quick smoke test: **5 minutes**
- Full CRUD test: **15 minutes**
- Issue investigation: **5-10 minutes**
- Re-testing fixes: **5 minutes**

**Total: 15-30 minutes for comprehensive testing**

---

**Good luck! Remember: Fast feedback is better than perfect testing. Test the critical paths first! 🎯**
