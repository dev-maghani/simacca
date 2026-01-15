<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\JadwalMengajarModel;
use App\Models\AbsensiDetailModel;
use App\Models\IzinSiswaModel;

class DashboardController extends BaseController
{
    protected $siswaModel;
    protected $jadwalModel;
    protected $absensiDetailModel;
    protected $izinSiswaModel;

    public function __construct()
    {
        helper('controller'); // Load controller helper
        
        $this->siswaModel = new SiswaModel();
        $this->jadwalModel = new JadwalMengajarModel();
        $this->absensiDetailModel = new AbsensiDetailModel();
        $this->izinSiswaModel = new IzinSiswaModel();
    }

    public function index()
    {
        // Get siswa data using helper
        $siswa = get_current_siswa('Data siswa tidak ditemukan');
        if ($siswa instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $siswa; // Return redirect if siswa not found
        }

        // Get jadwal hari ini
        $hariIndonesia = get_current_day_indonesian();
        
        $jadwalHariIni = $this->jadwalModel
            ->select('jadwal_mengajar.*, mata_pelajaran.nama_mapel, guru.nama_lengkap as nama_guru')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
            ->where('jadwal_mengajar.kelas_id', $siswa['kelas_id'])
            ->where('jadwal_mengajar.hari', $hariIndonesia)
            ->orderBy('jadwal_mengajar.jam_mulai', 'ASC')
            ->findAll();

        // Get statistik kehadiran bulan ini
        $dateRange = get_month_date_range();
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        $kehadiran = $this->absensiDetailModel
            ->select(get_attendance_stats_query())
            ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
            ->where('absensi_detail.siswa_id', $siswa['id'])
            ->where('absensi.tanggal >=', $startDate)
            ->where('absensi.tanggal <=', $endDate)
            ->first();

        // Calculate percentage using helper
        $persentaseKehadiran = calculate_percentage($kehadiran['hadir'] ?? 0, $kehadiran['total'] ?? 0);

        // Get izin status
        $izinPending = $this->izinSiswaModel
            ->where('siswa_id', $siswa['id'])
            ->where('status', 'pending')
            ->countAllResults();

        $izinDisetujui = $this->izinSiswaModel
            ->where('siswa_id', $siswa['id'])
            ->where('status', 'disetujui')
            ->countAllResults();

        // Get recent absensi (5 terakhir)
        $recentAbsensi = $this->absensiDetailModel
            ->select('absensi_detail.*, absensi.tanggal, mata_pelajaran.nama_mapel, absensi.pertemuan_ke')
            ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->where('absensi_detail.siswa_id', $siswa['id'])
            ->orderBy('absensi.tanggal', 'DESC')
            ->limit(5)
            ->findAll();

        $data = [
            'title' => 'Dashboard Siswa',
            'siswa' => $siswa,
            'jadwalHariIni' => $jadwalHariIni,
            'kehadiran' => $kehadiran,
            'persentaseKehadiran' => $persentaseKehadiran,
            'izinPending' => $izinPending,
            'izinDisetujui' => $izinDisetujui,
            'recentAbsensi' => $recentAbsensi
        ];

        return view('siswa/dashboard', $data);
    }

    // convertDayToIndonesian() removed - now using helper function
}
