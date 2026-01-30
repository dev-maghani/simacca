# TODO - Sistem Monitoring Absensi dan Catatan Cara Ajar (SIMACCA)

> **Note:** Completed features and bug history have been moved to [ARCHIVE.md](ARCHIVE.md) (now organized into 4 specialized files)

## 📋 Daftar Isi
- [Current Priorities](#current-priorities)
- [Active Tasks](#active-tasks)
- [Future Enhancements](#future-enhancements)
- [Development Guidelines](#development-guidelines)

---

## 🎯 Current Priorities

> **See [ARCHIVE.md](ARCHIVE.md) for completed features, bug fixes, implementation details, and achievements**

## 🔥 PRIORITAS CRITICAL (Harus Segera)

### 1. Absensi Guru Mandiri ⭐ READY TO START (7 hari, 53 tasks)
**Status:** 📋 PLANNING COMPLETE - Ready for Implementation  
**Priority:** CRITICAL  
**Impact:** HIGH - Fitur baru yang sangat dibutuhkan sekolah  
**Complexity:** MEDIUM - Timeline jelas, dokumentasi lengkap  
**Duration:** 7 working days (53 tasks)

→ **Details moved to section below** (line 916)

---

### 2. Notification System 📧 NEW PRIORITY
**Status:** ❌ NOT STARTED  
**Priority:** CRITICAL (Moved UP from MEDIUM)  
**Impact:** HIGH - Blocker untuk banyak fitur lainnya  
**Complexity:** MEDIUM  
**Duration:** 5-7 hari estimasi

**Why Critical:**
- Email service sudah ready, tinggal implement logic
- Needed untuk izin siswa workflow (wali kelas notification)
- Needed untuk absensi reminder (guru belum input)
- Foundation untuk real-time alerts

**Implementation Scope:**
- [ ] **Email Notifications** (3 days)
  - [ ] Izin siswa notification ke wali kelas (auto-send saat submit)
  - [ ] Admin notification saat guru tidak input absensi H+1
  - [ ] Laporan bulanan email ke wali kelas & admin
  - [ ] Guru reminder 30 menit sebelum jadwal (cron job)
  
- [ ] **In-App Notification UI** (2 days)
  - [ ] Create notification bell icon in navbar
  - [ ] Notification dropdown/modal
  - [ ] Mark as read/unread functionality
  - [ ] Notification history page
  
- [ ] **Database & Models** (1 day)
  - [ ] Create `notifications` table migration
  - [ ] Create NotificationModel with CRUD
  - [ ] Add notification preferences table
  
- [ ] **Business Logic** (1 day)
  - [ ] Helper function: `send_notification($user_id, $type, $message, $link)`
  - [ ] Integrate ke IzinController (siswa submit izin)
  - [ ] Integrate ke AbsensiController (deadline H+1)
  - [ ] CLI command: `php spark notification:send-reminders`

**Files to Create:**
- `app/Database/Migrations/CreateNotificationsTable.php`
- `app/Models/NotificationModel.php`
- `app/Helpers/notification_helper.php`
- `app/Commands/NotificationReminder.php`
- `app/Views/components/notification_bell.php`
- `app/Views/notifications/index.php`

**Files to Modify:**
- `app/Controllers/Siswa/IzinController.php` (add notification after submit)
- `app/Controllers/WaliKelas/IzinController.php` (show notification badge)
- `app/Views/templates/main_layout.php` (add notification bell to navbar)

**Testing Checklist:**
- [ ] Test email sending untuk izin siswa
- [ ] Test notification badge count
- [ ] Test mark as read functionality
- [ ] Test CLI reminder command
- [ ] Test notification preferences

---

### 3. Pagination Complete 📄 QUICK WIN
**Status:** ⚠️ 40% DONE (2 of 5 controllers)  
**Priority:** CRITICAL (Moved UP from MEDIUM)  
**Impact:** MEDIUM-HIGH - User experience improvement  
**Complexity:** LOW - Quick win, pattern sudah ada  
**Duration:** 2-3 hari

**Why Critical:**
- Already 40% done (MataPelajaran, Jadwal)
- Quick win dengan impact besar ke UX
- Pattern sudah established, tinggal replicate

**Remaining Controllers:**
- [ ] **GuruController** (1 day)
  - Add pagination to `index()` method
  - Update view with pagination links
  - Test with 100+ guru records
  
- [ ] **SiswaController** (1 day)
  - Add pagination to `index()` method
  - Filter by kelas + pagination
  - Update view with pagination links
  
- [ ] **KelasController** (0.5 day)
  - Add pagination to `index()` method
  - Simple implementation (fewer records)

**Implementation Pattern:**
```php
// Controller
$perPage = 20;
$data['items'] = $this->model->paginate($perPage);
$data['pager'] = $this->model->pager;

// View
<?= $pager->links('default', 'default_full') ?>
```

**Files to Modify:**
- `app/Controllers/Admin/GuruController.php`
- `app/Controllers/Admin/SiswaController.php`
- `app/Controllers/Admin/KelasController.php`
- `app/Views/admin/guru/index.php`
- `app/Views/admin/siswa/index.php`
- `app/Views/admin/kelas/index.php`

---

## ⭐ PRIORITAS HIGH (Penting, setelah Critical)

### 4. REFACTORING PHASE 1 - Code Quality & Architecture (3 Weeks)
**Status:** 📋 PLANNING COMPLETE  
**Priority:** HIGH (Moved DOWN from TOP)  
**Impact:** HIGH - Long-term investment untuk maintainability  
**Complexity:** HIGH - 15 hari kerja  
**Duration:** 3 weeks

**Why Moved Down:**
- Refactoring is long-term investment, not urgent
- Bisa dilakukan paralel dengan fitur baru
- Better to ship features first, then improve code quality
- Service layer bisa diimplementasi incrementally

→ **Details kept below** (original refactoring section preserved)

**Status:** 📋 PLANNING COMPLETE - Ready for Implementation  
**Duration:** 15 working days (3 weeks)  
**Goal:** Establish architectural foundation with Service Layer & Repository Pattern  
**Documentation:** `REFACTORING_PLAN_PHASE1.md`

**Success Criteria:**
- ✅ 3 core services implemented (Guru, Siswa, Absensi)
- ✅ 4 repositories with interfaces
- ✅ Top 5 long methods refactored
- ✅ Controllers reduced by 30% (258 → 180 lines avg)
- ✅ All changes tested and documented

#### **Week 1: Service Layer Foundation** (Days 1-5)

**Ticket #1: Create Service Base Structure** ⭐ CRITICAL
- **Type:** Task | **Priority:** Critical | **Estimate:** 4 hours
- [ ] Create `app/Services/` directory
- [ ] Create `BaseService.php` with common methods
- [ ] Add service auto-loading to `Config/Autoload.php`
- [ ] Create `Config/Services.php` service container entries
- [ ] Documentation in `docs/architecture/SERVICE_LAYER.md`
- **Files to Create:**
  - `app/Services/BaseService.php`
  - `docs/architecture/SERVICE_LAYER.md`
- **Files to Modify:**
  - `app/Config/Autoload.php`
  - `app/Config/Services.php`

**Ticket #2: Create GuruService (Pilot Implementation)** ⭐ CRITICAL
- **Type:** Feature | **Priority:** Critical | **Estimate:** 12 hours
- **Dependencies:** Ticket #1
- [ ] Create `GuruService` class with all business logic
- [ ] Extract methods: `create()`, `update()`, `delete()`, `import()`
- [ ] Handle password generation
- [ ] Handle email sending
- [ ] Handle Excel import validation
- [ ] Refactor GuruController to use service
- [ ] Unit tests for GuruService (60% coverage)
- [ ] Integration tests for controller
- **Testing Checklist:**
  - [ ] Test create guru with valid data
  - [ ] Test create guru with duplicate NIP
  - [ ] Test password generation
  - [ ] Test email sending
  - [ ] Test update guru data
  - [ ] Test import Excel (valid file)
  - [ ] Test import Excel (invalid data)
- **Files to Create:**
  - `app/Services/GuruService.php`
  - `tests/unit/Services/GuruServiceTest.php`
- **Files to Modify:**
  - `app/Controllers/Admin/GuruController.php`
  - `app/Config/Services.php`
- **Impact:** Controller lines 258 → ~180 (30% reduction)

#### **Week 2: Service Layer Expansion** (Days 6-10)

**Ticket #3: Create SiswaService** (Planned)
- **Type:** Feature | **Priority:** High | **Estimate:** 10 hours
- Similar to GuruService pattern
- Extract business logic from SiswaController
- Handle kelas auto-create logic
- Excel import with validation
- Unit tests (60% coverage)

**Ticket #4: Create AbsensiService** (Planned)
- **Type:** Feature | **Priority:** High | **Estimate:** 10 hours
- Extract complex absensi logic
- Handle dual ownership (guru_pengganti)
- Status calculation logic
- Unit tests (60% coverage)

#### **Week 3: Repository Pattern & Refactoring** (Days 11-15)

**Ticket #5: Implement Repository Pattern** (Planned)
- **Type:** Task | **Priority:** Medium | **Estimate:** 8 hours
- Create repository interfaces
- Implement for 4 core models (Guru, Siswa, Absensi, Jadwal)
- Refactor services to use repositories
- Unit tests for repositories

**Ticket #6: Refactor Top 5 Long Methods** (Planned)
- **Type:** Refactoring | **Priority:** Medium | **Estimate:** 6 hours
- Identify methods > 100 lines
- Extract to smaller methods
- Add PHPDoc comments
- Improve readability

**Ticket #7: Testing, Documentation & Review** (Planned)
- **Type:** Task | **Priority:** High | **Estimate:** 8 hours
- Complete test coverage (target: 60%)
- Update documentation
- Code review
- Performance benchmarking

**Expected Benefits:**
- 🚀 30% reduction in controller complexity
- 📦 Reusable business logic across modules
- 🧪 60% test coverage (from 0%)
- 📚 Better documentation
- 🔧 Easier maintenance and debugging
- 🎯 Separation of concerns (Controller → Service → Repository → Model)

---

### 5. PDF Export 📄
**Status:** ❌ NOT IMPLEMENTED  
**Priority:** HIGH  
**Impact:** MEDIUM - Completeness (Excel already works)  
**Complexity:** MEDIUM  
**Duration:** 3-4 hari

**Why High Priority:**
- Excel export already works, PDF adds completeness
- Common user request (print-friendly format)
- Libraries available (mPDF or Dompdf)

**Implementation Scope:**
- [ ] **Setup PDF Library** (0.5 day)
  - Install mPDF via Composer: `composer require mpdf/mpdf`
  - Create PDF helper: `app/Helpers/pdf_helper.php`
  - Add function: `generate_pdf($html, $filename, $orientation)`
  
- [ ] **Admin Reports PDF** (2 days)
  - Laporan Absensi per kelas (landscape)
  - Laporan Statistik kehadiran (portrait)
  - Laporan Guru (list dengan photo)
  - Laporan Siswa per kelas
  
- [ ] **Print Templates** (1 day)
  - Create `app/Views/pdf/` folder
  - Template: `laporan_absensi.php`
  - Template: `laporan_statistik.php`
  - Template: `daftar_guru.php`
  - Template: `daftar_siswa.php`
  
- [ ] **Controller Integration** (0.5 day)
  - Add `exportPDF()` method to LaporanController
  - Add PDF button to view (next to Excel button)

**Files to Create:**
- `app/Helpers/pdf_helper.php`
- `app/Views/pdf/laporan_absensi.php`
- `app/Views/pdf/laporan_statistik.php`
- `app/Views/pdf/daftar_guru.php`
- `app/Views/pdf/daftar_siswa.php`

**Files to Modify:**
- `composer.json` (add mPDF dependency)
- `app/Controllers/Admin/LaporanController.php`
- `app/Views/admin/laporan/index.php` (add PDF button)

---

### 6. Testing Coverage 🧪
**Status:** ⚠️ ~5% coverage (only example tests)  
**Priority:** HIGH  
**Impact:** HIGH - Stability & confidence in refactoring  
**Complexity:** HIGH  
**Duration:** Ongoing (target 60% coverage)

**Implementation Approach:**
- Start with critical paths (auth, absensi, izin)
- Unit tests for models (CRUD operations)
- Integration tests for controllers
- Feature tests for user workflows

**Target Coverage:**
- Models: 70% coverage (CRUD + custom methods)
- Controllers: 50% coverage (happy path + error cases)
- Helpers: 80% coverage (pure functions)
- Overall: 60% coverage

**Priority Test Files:**
- [ ] `tests/unit/Models/AbsensiModelTest.php`
- [ ] `tests/unit/Models/GuruModelTest.php`
- [ ] `tests/unit/Models/SiswaModelTest.php`
- [ ] `tests/unit/Controllers/AuthControllerTest.php`
- [ ] `tests/feature/AbsensiWorkflowTest.php`
- [ ] `tests/feature/IzinWorkflowTest.php`

---

## 📌 PRIORITAS MEDIUM (Nice to have)

### 7. Breadcrumb Navigation 🍞
**Status:** ⚠️ Template ready, only 10% implemented  
**Priority:** MEDIUM  
**Impact:** LOW-MEDIUM - UX improvement  
**Complexity:** LOW  
**Duration:** 2-3 hari

**Implementation:**
- CSS already ready in template
- Add breadcrumb to all CRUD views (~40 views)
- Pattern: Home > Module > Action

---

### 8. Error Logging Improvement 📊
**Status:** ⚠️ Partial implementation  
**Priority:** MEDIUM  
**Impact:** MEDIUM - Debugging & monitoring  
**Complexity:** MEDIUM  
**Duration:** 2-3 hari

---

### 9. Dark Mode 🌙
**Status:** ❌ NOT IMPLEMENTED (Moved UP from LOW)  
**Priority:** MEDIUM  
**Impact:** LOW-MEDIUM - User comfort  
**Complexity:** MEDIUM  
**Duration:** 3-4 hari

**Why Moved Up:**
- Relatively easy with Tailwind CSS (dark: prefix)
- User comfort improvement
- Modern UI trend
- Can be implemented incrementally

---

## 🔽 PRIORITAS LOW (Future enhancement)

### 10. QR Code Absensi 📱
- Requires hardware/device testing
- Need QR scanner library
- Location validation (GPS)

### 11. Two-Factor Authentication 🔐
- Security enhancement
- SMS/Email/Authenticator app
- User adoption might be low

### 12. Automated Backup 🔄
- Manual backups exist
- Can automate with CLI + cron
- Lower priority than features

### 13. All Other Enhancements
- See sections below for 20+ additional features
- Portal Orang Tua, WhatsApp Integration, PWA, etc.

---

## 📋 Roadmap Summary

**CRITICAL (Next 2-3 weeks):**
1. ⭐ Absensi Guru Mandiri (7 days) - READY TO START
2. 📧 Notification System (5-7 days) - HIGH IMPACT
3. 📄 Pagination Complete (2-3 days) - QUICK WIN

**HIGH (Next 1-2 months):**
4. 🏗️ Refactoring Phase 1 (3 weeks) - Long-term investment
5. 📄 PDF Export (3-4 days) - Completeness
6. 🧪 Testing Coverage (Ongoing) - Stability

**MEDIUM (Next 3-6 months):**
7. 🍞 Breadcrumb Navigation
8. 📊 Error Logging
9. 🌙 Dark Mode

**LOW (Future/Backlog):**
10. QR Code, 2FA, Automated Backup, etc.

---

## 🚀 Active Tasks

> **Completed tasks moved to `ARCHIVE.md`. This section now contains only pending/in-progress work.**

### Current Status Summary
- ✅ **Security:** XSS (439 files), CSRF (41+ forms), File validation comprehensive
- ✅ **All Modules:** Admin, Guru, Wali Kelas, Siswa, Wakakur fully functional
- ⚠️ **Pagination:** 40% complete (2 of 5 controllers need pagination)
- ⚠️ **Breadcrumb:** Template ready but only 10% implemented
- ❌ **Notification System:** Email service ready but not used
- ❌ **PDF Export:** Excel works, PDF not implemented
- ❌ **Testing:** Minimal coverage (only example tests)

---

## 🐛 Known Issues

### Critical
- [ ] Check SQL injection vulnerabilities (ongoing review)

### High Priority
- [ ] Add proper error logging (⚠️ PARTIAL - security_helper logging exists)
- [ ] Fix timezone settings
- [ ] Optimize database queries (add indexes if needed)

### Medium Priority
- [ ] Improve loading performance
- [ ] Add caching for frequently accessed data
- [ ] Refactor duplicate code
- [ ] Add code comments untuk fungsi kompleks
- [ ] Standardize naming conventions
- [ ] Clean up unused imports

---

## 📚 Documentation Tasks

### Code Documentation
- [ ] Add PHPDoc comments untuk semua classes
- [ ] Document API endpoints (if any)

### User Documentation
- [ ] Create user manual untuk Admin
- [ ] Create user manual untuk Guru
- [ ] Create user manual untuk Wali Kelas
- [ ] Create user manual untuk Siswa
- [ ] Create video tutorials

### Developer Documentation
- [ ] Setup development environment guide
- [ ] Code contribution guidelines
- [ ] Testing guidelines

---

## 🚀 Fitur Baru yang Disarankan

### 📱 Mobile & Communication

#### 1. Absensi Guru Mandiri ⭐ IMPLEMENTATION IN PROGRESS (2026-01-30)

**Status:** 📋 PLANNING COMPLETE - Ready for Implementation  
**Estimated Duration:** 7 working days (53 tasks)  
**Priority:** HIGH  
**Documentation:**
- ✅ `docs/plans/ABSENSI_GURU_IMPLEMENTATION_PLAN.md` - Complete technical specification
- ✅ `docs/plans/ABSENSI_GURU_DETAILED_REVIEW.md` - Detailed review & analysis
- ✅ `docs/plans/ABSENSI_GURU_DECISIONS.md` - All business decisions finalized (19 decisions across 6 categories)
- ✅ `docs/plans/ABSENSI_GURU_TIMELINE.md` - Day-by-day implementation timeline (53 tasks)

**Implementation Timeline (7 Days - 53 Tasks):**

**📅 DAY 1: Database & Models Foundation (9 tasks)**
- [ ] Task 1: Create migration `CreateAbsensiGuruTable.php` (30 min)
- [ ] Task 2: Create migration `CreateIzinGuruTable.php` (30 min)
- [ ] Task 3: Run migrations (10 min)
- [ ] Task 4: Create `AbsensiGuruModel.php` basic CRUD (1 hour)
- [ ] Task 5: Add custom methods to AbsensiGuruModel (1.5 hours)
  - `checkIn()`, `checkOut()`, `getTodayAttendance()`, `getMonthlyAttendance()`
  - `getAllTodayAttendance()`, `getStatistics()`, `calculateStatus()`, `getForExport()`
- [ ] Task 6: Create `IzinGuruModel.php` (45 min)

**📅 DAY 2: Controllers Logic (6 tasks)**
- [ ] Task 7: Create `Guru/AbsensiGuruController.php` (1.5 hours)
  - Methods: `index()`, `checkIn()`, `checkOut()`, `history()`, `uploadSelfie()`
- [ ] Task 8: Create `Guru/IzinGuruController.php` (1 hour)
- [ ] Task 9: Create `Wakakur/AbsensiGuruController.php` - Part 1 (1 hour)
  - Methods: `index()`, `getTodayData()`, `manualSet()`
- [ ] Task 10: Create `Wakakur/IzinGuruController.php` (1 hour)
- [ ] Task 11: Add to `Wakakur/AbsensiGuruController.php` - Part 2 (45 min)
  - Methods: `laporan()`, `detail()`
- [ ] Task 12: Add Excel export method (45 min)

**📅 DAY 3: Views - Guru & Wakakur (8 tasks)**
- [ ] Task 13: Create `guru/absensi_guru/index.php` - Mobile-first layout (1.5 hours)
- [ ] Task 14: Update `guru/dashboard.php` - Add quick access widget (45 min)
- [ ] Task 15: Create history views (desktop table + mobile cards) (45 min)
- [ ] Task 16: Create `guru/izin_guru/create.php` form (30 min)
- [ ] Task 17: Create `wakakur/absensi_guru/index.php` - Real-time monitoring (1.5 hours)
- [ ] Task 18: Add AJAX auto-refresh every 30 seconds (30 min)
- [ ] Task 19: Create `wakakur/absensi_guru/laporan.php` (1 hour)
- [ ] Task 20: Create `wakakur/izin_guru/index.php` (45 min)

**📅 DAY 4: Camera Feature & Image Processing (8 tasks)**
- [ ] Task 21: Create `public/js/absensi-guru-camera.js` skeleton (30 min)
- [ ] Task 22: Implement `getUserMedia()` camera access (1 hour)
- [ ] Task 23: Implement capture, preview, retake flow (1.5 hours)
- [ ] Task 24: AJAX upload integration (1 hour)
- [ ] Task 25: Backend - Use `optimize_image()` helper (30 min)
- [ ] Task 26: Implement date hierarchy storage (YYYY/MM/DD) (45 min)
- [ ] Task 27: Add rate limiting logic (3 attempts per 5 min) (30 min)
- [ ] Task 28: Optional - Add EXIF validation (30 min)

**📅 DAY 5: Routes, Excel, Business Logic (9 tasks)**
- [ ] Task 29: Add Guru routes in `Config/Routes.php` (30 min)
- [ ] Task 30: Add Wakakur routes (30 min)
- [ ] Task 31: Add FileController route for serving photos (30 min)
- [ ] Task 32: Implement PhpSpreadsheet Excel export (1 hour)
- [ ] Task 33: Add color-coded status cells in Excel (30 min)
- [ ] Task 34: Add clickable foto URL links in Excel (30 min)
- [ ] Task 35: Business Logic - Auto-alpha at 10:00 WIB (45 min)
- [ ] Task 36: Add 8-hour minimum validation modal (30 min)
- [ ] Task 37: Add early_checkout fields logic (15 min)

**📅 DAY 6: Comprehensive Testing (8 tasks)**
- [ ] Task 38: Test Guru check-in flow (45 min)
- [ ] Task 39: Test check-out with 8-hour validation (45 min)
- [ ] Task 40: Test izin request workflow (30 min)
- [ ] Task 41: Test Wakakur manual set status (30 min)
- [ ] Task 42: Test real-time monitoring auto-refresh (30 min)
- [ ] Task 43: Test Excel export with filters (45 min)
- [ ] Task 44: Test camera on multiple devices (1.5 hours)
  - Mobile: Android Chrome, iOS Safari
  - Desktop: Chrome, Firefox, Edge
- [ ] Task 45: Test security features (rate limiting, EXIF, auth) (45 min)

**📅 DAY 7: Documentation & Deployment Prep (5 tasks)**
- [ ] Task 46: Create printed quick guide (A4 landscape, 1-page) (1 hour)
- [ ] Task 47: Update TODO.md with deployment notes (30 min)
- [ ] Task 48: Update CHANGELOG.md with v2.0.0 features (30 min)
- [ ] Task 49: Create .htaccess for photo security (15 min)
- [ ] Task 50: Create CLI command for photo cleanup (1 hour)
- [ ] Task 51: Create deployment checklist (45 min)
- [ ] Task 52: Prepare demo session materials (1 hour)
- [ ] Task 53: Final review & go-live readiness (1 hour)

**Key Features Implemented:**
- ✅ Self check-in/check-out with selfie photo validation
- ✅ Wakakur real-time monitoring dashboard (auto-refresh 30s)
- ✅ Hybrid izin workflow (Wakakur manual set + Guru submit request)
- ✅ 8-hour minimum work validation with early checkout warning
- ✅ Rate limiting anti-fraud (3 attempts per 5 min)
- ✅ Date hierarchy photo storage (2-year retention)
- ✅ Excel export (11 columns with foto URLs)
- ✅ Mobile-first responsive design
- ✅ Status auto-calculation (Hadir: ≤07:15, Terlambat: >07:15, Alpha: auto at 10:00)

**Deployment Strategy:**
- **Week 1 (Pilot):** 10 guru (20%) - Tech-savvy early adopters
- **Week 2 (Expansion):** +25 guru (70% total) - General population
- **Week 3 (Full Launch):** +15 guru (100%) - All remaining guru

**Training & Support:**
- Printed quick guide (1-page laminated, 60 copies)
- Demo session (30 min × 3 batches)
- IT support via WhatsApp (Week 1-3: Active, Week 4+: Passive)

**Next Action:** Begin Day 1 - Task 1 (Create migration file)

#### 2. Notifikasi WhatsApp
- [ ] Integrasi WhatsApp API (Fonnte/Wablas)
- [ ] Auto-notify orang tua ketika siswa tidak hadir
- [ ] Reminder untuk guru yang belum input absensi/jurnal
- [ ] Notifikasi persetujuan/penolakan izin siswa
- [ ] Broadcast pengumuman dari admin ke grup kelas

#### 2. Mobile-Friendly QR Code Absensi
- [ ] Generate QR Code unik per jadwal/pertemuan
- [ ] Siswa scan QR untuk absensi mandiri
- [ ] Validasi lokasi GPS (geofencing sekolah)
- [ ] Time-limited QR (expired setelah jam pelajaran)
- [ ] Fallback: Guru tetap bisa input manual jika ada kendala

#### 3. Mobile API (Progressive Web App)
- [ ] RESTful API endpoints untuk mobile app
- [ ] JWT authentication untuk API
- [ ] API documentation (Swagger/OpenAPI)
- [ ] Rate limiting dan API throttling
- [ ] Mobile-first responsive design enhancement

### 📊 Analytics & Reporting
#### 4. Dashboard Analytics Lanjutan
- [ ] Grafik tren kehadiran per bulan/semester
- [ ] Prediksi siswa berisiko (sering tidak hadir)
- [ ] Perbandingan performa antar kelas
- [ ] Heat map kehadiran (hari/jam paling banyak absen)
- [ ] Export grafik ke PNG/PDF

#### 5. Laporan Otomatis & Scheduling
- [ ] Auto-generate laporan bulanan
- [ ] Scheduled email report untuk wali kelas & admin
- [ ] Laporan ke orang tua via email/WhatsApp
- [ ] Template laporan yang customizable
- [ ] Arsip otomatis laporan per semester

#### 6. Rekap Penilaian Kehadiran
- [ ] Konversi persentase kehadiran ke nilai
- [ ] Bobot nilai kehadiran (konfigurable per mapel)
- [ ] Rapor kehadiran semester
- [ ] Sertifikat kehadiran terbaik
- [ ] Penghargaan perfect attendance

### 👥 Parent & Student Engagement
#### 7. Portal Orang Tua
- [ ] Login khusus orang tua (linked ke siswa)
- [ ] Dashboard monitoring kehadiran anak
- [ ] Riwayat izin dan persetujuan
- [ ] Komunikasi dengan wali kelas
- [ ] Download laporan kehadiran

#### 8. Sistem Poin & Reward
- [ ] Poin kehadiran untuk siswa
- [ ] Leaderboard kehadiran per kelas
- [ ] Badge/achievement system
- [ ] Penalty point untuk keterlambatan
- [ ] Redemption point untuk reward

### 🔔 Advanced Notification System
#### 9. Real-time Notification Center
- [ ] In-app notification bell icon
- [ ] Push notification (browser)
- [ ] Notification preferences per user
- [ ] Mark as read/unread
- [ ] Notification history & archive

#### 10. Smart Alerts & Reminders
- [ ] Alert siswa absent 3 hari berturut-turut
- [ ] Reminder guru 30 menit sebelum jadwal
- [ ] Alert admin jika guru tidak input absensi H+1
- [ ] Alert wali kelas ada izin pending
- [ ] Weekly summary notification

### 🎓 Academic Enhancement
#### 11. Manajemen Tugas & Penilaian
- [ ] Guru bisa assign tugas per pertemuan
- [ ] Upload file tugas dari siswa
- [ ] Penilaian tugas dengan rubrik
- [ ] Tracking deadline tugas
- [ ] Notifikasi tugas yang belum dikumpulkan

#### 12. Absensi dengan Catatan Perilaku
- [ ] Catatan perilaku siswa per pertemuan
- [ ] Tag behavior (positif/negatif)
- [ ] Point pelanggaran tata tertib
- [ ] Konseling log untuk siswa bermasalah
- [ ] Laporan BK (Bimbingan Konseling)

#### 13. Jadwal Ujian & Remedial
- [ ] Kalender ujian per mata pelajaran
- [ ] Tracking siswa yang perlu remedial
- [ ] Jadwal remedial dan hasil
- [ ] Block jadwal ujian (conflict detection)
- [ ] Reminder ujian untuk siswa

### 🔒 Security & Administration
#### 14. Audit Trail & Activity Log
- [ ] Log semua aktivitas CRUD
- [ ] Track IP address dan device
- [ ] Export audit log
- [ ] Suspicious activity detection
- [ ] GDPR-compliant data retention

#### 15. Advanced User Management
- [ ] Two-Factor Authentication (2FA)
- [ ] Password complexity enforcement
- [ ] Account lockout setelah failed login
- [ ] Session management (force logout)
- [ ] Bulk user import dengan validation

#### 16. Backup & Recovery System
- [ ] Automated database backup (daily/weekly)
- [ ] Backup to cloud storage (Google Drive/Dropbox)
- [ ] One-click restore dari backup
- [ ] Export all data to Excel/CSV
- [ ] Data archival untuk tahun ajaran lama

### 📅 Time & Schedule Management
#### 17. Kalender Akademik
- [ ] Master kalender tahun ajaran
- [ ] Libur nasional & cuti bersama
- [ ] Event sekolah (ujian, PTS, PAS)
- [ ] Block tanggal untuk absensi
- [ ] Sync dengan Google Calendar

#### 18. Manajemen Tahun Ajaran
- [ ] Multi-year support
- [ ] Archive data tahun ajaran sebelumnya
- [ ] Rollover siswa naik kelas otomatis
- [ ] Reset system untuk tahun baru
- [ ] Historical data comparison

#### 19. Jadwal Fleksibel
- [ ] Support jadwal blok (2 jam pelajaran)
- [ ] Jadwal khusus (upacara, ekstrakurikuler)
- [ ] Swap jadwal antar guru
- [ ] Jadwal pengganti untuk hari libur
- [ ] Template jadwal per semester

### 💼 Administrative Tools
#### 20. Import/Export Enhancement
- [ ] Import dari format lain (CSV, JSON)
- [ ] Validation preview sebelum import
- [ ] Bulk update via Excel
- [ ] Template Excel dengan formula
- [ ] Export dengan custom columns

#### 21. Surat Menyurat
- [ ] Generate surat izin otomatis
- [ ] Template surat panggilan orang tua
- [ ] Digital signature
- [ ] Tracking status surat
- [ ] Arsip surat keluar/masuk

#### 22. Keuangan & Administrasi
- [ ] Tracking honor guru pengganti
- [ ] Laporan jam mengajar per guru
- [ ] Perhitungan tunjangan kinerja
- [ ] Export untuk payroll
- [ ] Budget tracking untuk kegiatan

### 🎨 UI/UX Improvements
#### 23. Progressive Web App (PWA)
- [ ] Install ke home screen
- [ ] Offline mode (cache data)
- [ ] Service worker implementation
- [ ] App-like experience
- [ ] Background sync

#### 24. Customization & Branding
- [ ] Upload logo sekolah
- [ ] Custom color scheme
- [ ] Customizable dashboard widgets
- [ ] Multi-language support (ID/EN)
- [ ] Dark mode toggle

#### 25. Accessibility & Performance
- [ ] Keyboard navigation support
- [ ] Screen reader compatibility
- [ ] Performance optimization (lazy loading)
- [ ] Image compression otomatis
- [ ] CDN integration

### 🔗 Integration & Automation
#### 26. Third-Party Integration
- [ ] Google Classroom sync
- [ ] Microsoft Teams integration
- [ ] Zoom meeting link per jadwal
- [ ] E-learning platform integration
- [ ] SMS Gateway (selain WhatsApp)

#### 27. Smart Automation
- [ ] Auto-fill absensi dari hari sebelumnya
- [ ] Smart suggest materi berdasarkan RPP
- [ ] Auto-kategorisasi izin (sakit/izin/alpha)
- [ ] Predictive analytics untuk dropout risk
- [ ] ML-based anomaly detection

---

---

## 📝 Development Guidelines

### ✅ COMPLETED
- [x] **Template Layouts Created** (3 files)
  - `templates/main_layout.php` - Dashboard & CRUD pages
  - `templates/auth_layout.php` - Authentication pages ✅ NEW
  - `templates/print_layout.php` - Print pages ✅ NEW

- [x] **Reusable Components Created** (7 files)
  - `components/alerts.php` - Flash messages ✅ NEW
  - `components/buttons.php` - Button helpers ✅ NEW
  - `components/cards.php` - Card components ✅ NEW
  - `components/forms.php` - Form helpers with validation ✅ NEW
  - `components/modals.php` - Modal components ✅ NEW
  - `components/tables.php` - Table helpers ✅ NEW
  - `components/badges.php` - Status badges ✅ NEW

- [x] **Helper System Created**
  - `app/Helpers/component_helper.php` ✅ NEW
  - Auto-loaded in `Config/Autoload.php` ✅

- [x] **Auth Views Refactored** (3 files)
  - `auth/login.php` ✅ REFACTORED
  - `auth/forgot_password.php` ✅ REFACTORED
  - `auth/access_denied.php` ✅ REFACTORED

- [x] **Documentation Created**
  - `TEMPLATE_SYSTEM_GUIDE.md` (800+ lines) ✅ NEW
  - `TEMPLATE_REFACTORING_SUMMARY.md` ✅ NEW

### 🚧 IN PROGRESS
- [ ] **Refactor Dashboard Views** (4 files)
  - Use `stat_card()` component
  - Use `card_start()`/`card_end()`
  - Standardize chart sections

- [ ] **Refactor Index/List Views** (~15 files)
  - Use `table_start()`/`table_header()`
  - Use `status_badge()` for status columns
  - Use `empty_state()` when no data
  - Use `button_link()` for actions

- [ ] **Refactor Form Views** (~20 files)
  - Use `form_input()`, `form_select()`, etc.
  - Auto validation display
  - Use `button()` for submit/cancel

- [ ] **Refactor Print Views** (4 files)
  - Convert to use `print_layout.php`

### 📊 Benefits
- ✅ **50% code reduction** in views
- ✅ **Consistent UI/UX** across all pages
- ✅ **Easier maintenance** - update once, apply everywhere
- ✅ **Faster development** - reusable components
- ✅ **Better DX** - clear documentation & examples
- ✅ **Auto validation** - form helpers handle errors

### 📚 Documentation
See `TEMPLATE_SYSTEM_GUIDE.md` for:
- Complete usage guide
- All component examples
- Migration guide
- Best practices
- Troubleshooting
- Complete CRUD example

---

### Best Practices
- All controllers must extend BaseController
- Include proper authentication checks using session & filters
- Create corresponding view files for all controller actions
- Test all routes after creation
- Follow CodeIgniter 4 best practices
- Use models for database operations (no direct queries in controllers)

### Testing Checklist
- [ ] Test all CRUD operations ⚠️ MINIMAL (only example tests)
- [ ] Test authentication flows ❌ NO TESTS
- [ ] Test role-based access control ❌ NO TESTS
- [ ] Test file uploads ❌ NO TESTS
- [ ] Test data exports ❌ NO TESTS
- [ ] Test form validations ❌ NO TESTS
- [ ] Cross-browser testing ❌ MANUAL ONLY
- [ ] Mobile responsiveness testing ⚠️ MANUAL ONLY (no automated tests)

### Deployment Checklist
- [ ] Update .env for production
- [ ] Set CI_ENVIRONMENT=production
- [ ] Disable debug mode
- [ ] Setup database backup schedule
- [ ] Configure email service
- [ ] Setup SSL certificate
- [ ] Configure file upload limits
- [ ] Test all features in production

---

## 👥 Tim Pengembang
- Mohd. Abdul Ghani
- Dirwan Jaya

---

**Last Updated:** 2026-01-30 (Comprehensive Audit)

---

## 📧 Email Service Implementation ✨ NEW (2026-01-15)

### Complete Email System
- [x] **Email Service Configuration** ✅ COMPLETED
  - SMTP configuration in .env
  - Support Gmail, Outlook, Yahoo, Custom SMTP
  - Dynamic configuration loading
  - Email helper functions
  
- [x] **Password Reset System** ✅ COMPLETED
  - Secure token generation (SHA-256)
  - Token expiration (1 hour)
  - One-time use enforcement
  - Email enumeration protection
  - Complete forgot/reset password flow
  
- [x] **Email Templates** ✅ COMPLETED
  - Branded responsive email layout
  - Password reset email
  - Welcome email for new users
  - General notification email
  - Test email template
  
- [x] **Database & Models** ✅ COMPLETED
  - `password_reset_tokens` table migration
  - PasswordResetTokenModel with full CRUD
  - Automatic token cleanup methods
  
- [x] **CLI Commands** ✅ COMPLETED
  - `php spark email:test` - Test email configuration
  - `php spark token:cleanup` - Clean expired tokens
  
- [x] **Security Features** ✅ COMPLETED
  - Hashed token storage
  - Token expiration validation
  - One-time use enforcement
  - Email enumeration protection
  - Error logging
  
- [x] **Documentation** ✅ COMPLETED
  - EMAIL_SERVICE_DOCUMENTATION.md (comprehensive guide)
  - EMAIL_SERVICE_QUICKSTART.md (5-minute setup)
  - Configuration examples for all SMTP providers
  - Troubleshooting guide
  - API documentation

**Files Created/Modified:** 18 files
- 1 Migration
- 1 Model
- 1 Helper
- 5 Email Templates
- 1 Auth View
- 2 CLI Commands
- 2 Documentation Files
- 5 Modified Files (AuthController, Email Config, Autoload, .env.production, TODO.md)

**Last Updated:** 2026-01-30 (Comprehensive Audit)

---

## 📸 Recent Major Features (2026-01-15)

### Image Optimization System ✨ NEW
- [x] **Automatic Image Compression** ✅ SELESAI (2026-01-15)
  - Created image_helper.php with optimization functions
  - 70-85% file size reduction without visible quality loss
  - Integrated into ProfileController (profile photos)
  - Integrated into JurnalController (journal documentation)
  - Integrated into IzinController (permission letters)
  - Smart detection (images optimized, PDFs skipped)
  - Increased upload limit: 2MB → 5MB
  - Compression statistics logging
  - Support for JPEG, PNG, GIF, WebP formats
  - Maintains aspect ratio and transparency
  - Production ready

---

---

## 📊 Audit Summary (2026-01-30)

### ✅ What's Working Well:
1. **Security** - XSS (439 files), CSRF (41+ forms), File validation comprehensive
2. **Dashboards** - All 5 roles have complete, functional dashboards with statistics
3. **Excel Export** - Fully functional for Guru, Siswa, Kelas, Jadwal
4. **Image Optimization** - 70-85% compression on all uploads
5. **Mobile Responsiveness** - Desktop/Mobile layouts for key modules
6. **CLI Tools** - 6 maintenance commands for token, session, email, cache management

### ⚠️ Needs Attention:
1. **Pagination** - Only 40% complete (2 of 5 controllers)
2. **Breadcrumb** - Template ready but only 10% implemented
3. **Testing** - Minimal coverage (only example tests)
4. **Error Logging** - Partial implementation

### ❌ Missing Features (High Priority):
1. **Notification System** - Email service ready but no notifications implemented
2. **PDF Export** - Excel works, PDF not implemented
3. **Real-time Alerts** - No notification logic or UI

### 📈 Code Quality Metrics:
- **Total Controllers**: 38 controllers
- **XSS Protected Files**: 439 files (95%+ coverage)
- **CSRF Protected Forms**: 41+ forms
- **CLI Commands**: 6 tools
- **Test Coverage**: ~5% (only examples)

---

## 🎉 Recent Achievements (January 2026)

### Major Feature: Guru Pengganti/Piket System (2026-01-12)
Implementasi lengkap sistem guru pengganti untuk menangani situasi ketika guru berhalangan hadir:

#### What's New:
1. **Mode Selection Interface**
   - Toggle UI untuk memilih "Jadwal Saya Sendiri" atau "Guru Pengganti"
   - Visual feedback yang jelas dengan icon dan warna berbeda
   - Dynamic label berdasarkan mode yang dipilih

2. **Smart Backend Logic**
   - Auto-detect substitute mode berdasarkan guru_id jadwal
   - Auto-set guru_pengganti_id untuk mode pengganti
   - Dual ownership access control (creator OR schedule owner)
   - Enhanced queries dengan groupStart/groupEnd untuk OR conditions

3. **Complete Access Control**
   - Guru pengganti bisa lihat daftar absensi yang diinput
   - Guru asli bisa edit/delete absensi dari guru pengganti
   - Both can create jurnal KBM
   - Proper validation across all CRUD operations

4. **Integration Points**
   - Absensi module: show, edit, update, delete, print
   - Jurnal KBM module: create, edit, show, print
   - Laporan admin: menampilkan info guru pengganti
   - Database: field guru_pengganti_id dengan foreign key

#### Files Modified:
- Controllers: `AbsensiController.php`, `JurnalController.php`
- Models: `AbsensiModel.php` (enhanced getByGuru method)
- Views: `create.php`, `edit.php`, `show.php` (absensi & jurnal)
- Database: Migration file untuk guru_pengganti_id

#### Documentation:
- 7 comprehensive markdown files created
- Flow diagrams and test scenarios included
- Deployment guide with checklist
- Security considerations documented

### Security Enhancements (Previous Updates)
- CSRF protection across all forms
- Session key handling fixes
- Proper logout mechanism
- Redirect loop fixes
- XSS protection improvements
- Error message sanitization
