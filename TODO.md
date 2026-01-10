# TODO List - Sistem Monitoring Absensi dan Catatan Cara Ajar

## HARI 1: ✅ SETUP ENVIRONMENT & AUTHENTICATION SYSTEM
- [x] Install XAMPP/Laragon
- [x] Install Composer
- [x] Create Codeigniter 4.6.4 Project
```bash
composer create-project codeigniter4/appstarter simacca
cd simacca
```
- [x] Setup .env file
- [x] Create Database `simacca_database` With Migration CI4
- [x] Create Migration file for:
`users`
`kelas`
`mata_pelajaran`
`guru`
`siswa`
`jadwal_mengajar`
`absensi`
`absensi_detail`
`jurnal_kbm`
`izin_siswa`
- [x] Create model for each table
- [x] Create Auth Controller
- [x] Create Filters (Middleware) for all roles
- [x] Create Helpers Functions on BaseController
- [x] Create AuthHelper in Helpers folder
- [x] Implement Login/Logout
- [x] Create session management
- [x] Create Layout for Authentication
- [x] Create Template Layout
- [x] Configure Routes

## Hari 2: ADMIN MODULE
- [x] Create Admin Dashboard Controller
- [x] Create View for Admin Dashboard
