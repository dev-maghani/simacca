<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Add New Status
 * 
 * Updates absensi_detail status enum to add new attendance statuses.
 * Expands the attendance tracking capabilities.
 * 
 * Dependencies: absensi_detail
 * Modified: status ENUM field
 * 
 * @package App\Database\Migrations
 * @author SIMACCA Team
 * @version 1.0.0
 */
class AddNewStatus extends Migration
{
    public function up()
    {
        // Perintah SQL untuk menambahkan nilai ENUM baru
        // Sesuaikan 'nama_tabel', 'nama_kolom', dan daftar nilai ENUM yang lengkap
        $sql = "ALTER TABLE `jadwal_mengajar` MODIFY COLUMN `hari` ENUM('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu') NOT NULL";
        $this->db->query($sql);
    }

    public function down()
    {
        // Perintah SQL untuk mengembalikan perubahan (menghapus nilai baru)
        // Pastikan untuk menghapus semua data yang menggunakan 'nilai_baru' sebelum rollback
        $sql = "ALTER TABLE `jadwal_mengajar` MODIFY COLUMN `hari` ENUM('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat') NOT NULL";
        $this->db->query($sql);
    }
}
