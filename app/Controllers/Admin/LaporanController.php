<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AbsensiModel;
use App\Models\AbsensiDetailModel;
use App\Models\KelasModel;
use App\Models\GuruModel;
use App\Models\JadwalMengajarModel;
use App\Models\DashboardModel;

class LaporanController extends BaseController
{
    protected $absensiModel;
    protected $absensiDetailModel;
    protected $kelasModel;
    protected $guruModel;
    protected $jadwalModel;
    protected $dashboardModel;

    public function __construct()
    {
        $this->absensiModel = new AbsensiModel();
        $this->absensiDetailModel = new AbsensiDetailModel();
        $this->kelasModel = new KelasModel();
        $this->guruModel = new GuruModel();
        $this->jadwalModel = new JadwalMengajarModel();
        $this->dashboardModel = new DashboardModel();

        // Cek role admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/access-denied');
        }
    }

    /**
     * Laporan Absensi per periode/kelas
     */
    public function absensi()
    {
        $from = $this->request->getGet('from') ?: date('Y-m-01');
        $to = $this->request->getGet('to') ?: date('Y-m-t');
        $kelasId = $this->request->getGet('kelas_id');

        // Data filter & referensi
        $kelasList = $this->kelasModel->getListKelas();

        // Ambil ringkasan absensi (implementasikan sesuai model Anda)
        $summary = $this->absensiDetailModel->getRekapPerKelas($from, $to, $kelasId ?? null);

        $data = [
            'title' => 'Laporan Absensi',
            'pageTitle' => 'Laporan Absensi',
            'pageDescription' => 'Rekapitulasi absensi periode ' . date('d/m/Y', strtotime($from)) . ' - ' . date('d/m/Y', strtotime($to)),
            'user' => $this->getUserData(),
            'from' => $from,
            'to' => $to,
            'kelasId' => $kelasId,
            'kelasList' => $kelasList,
            'summary' => $summary,
        ];

        return view('admin/laporan/absensi', $data);
    }

    /**
     * Laporan Absensi Detail (Lengkap)
     */
    public function absensiDetail()
    {
        $from = $this->request->getGet('from') ?: date('Y-m-01');
        $to = $this->request->getGet('to') ?: date('Y-m-t');
        $kelasId = $this->request->getGet('kelas_id');

        // Data filter & referensi
        $kelasList = $this->kelasModel->getListKelas();

        // Ambil data laporan lengkap
        $laporanData = $this->absensiModel->getLaporanAbsensiLengkap($from, $to, $kelasId ?? null);

        $data = [
            'title' => 'Laporan Absensi Detail',
            'pageTitle' => 'Laporan Absensi Detail',
            'pageDescription' => 'Laporan absensi lengkap dengan detail kehadiran per sesi pembelajaran',
            'user' => $this->getUserData(),
            'from' => $from,
            'to' => $to,
            'kelasId' => $kelasId,
            'kelasList' => $kelasList,
            'laporanData' => $laporanData,
        ];

        return view('admin/laporan/absensi_detail', $data);
    }

    /**
     * Print Laporan Absensi Detail
     */
    public function printAbsensiDetail()
    {
        $from = $this->request->getGet('from') ?: date('Y-m-01');
        $to = $this->request->getGet('to') ?: date('Y-m-t');
        $kelasId = $this->request->getGet('kelas_id');

        // Data filter & referensi
        $kelasList = $this->kelasModel->getListKelas();

        // Ambil data laporan lengkap
        $laporanData = $this->absensiModel->getLaporanAbsensiLengkap($from, $to, $kelasId ?? null);

        // Hitung total statistik
        $totalStats = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 0,
            'total' => 0
        ];

        foreach ($laporanData as $row) {
            $totalStats['hadir'] += $row['jumlah_hadir'];
            $totalStats['sakit'] += $row['jumlah_sakit'];
            $totalStats['izin'] += $row['jumlah_izin'];
            $totalStats['alpa'] += $row['jumlah_alpa'];
        }

        $totalStats['total'] = $totalStats['hadir'] + $totalStats['sakit'] + $totalStats['izin'] + $totalStats['alpa'];
        $totalStats['percentage'] = $totalStats['total'] > 0 ? round(($totalStats['hadir'] / $totalStats['total']) * 100, 2) : 0;

        $data = [
            'title' => 'Cetak Laporan Absensi Detail',
            'from' => $from,
            'to' => $to,
            'kelasId' => $kelasId,
            'kelasList' => $kelasList,
            'laporanData' => $laporanData,
            'totalStats' => $totalStats,
        ];

        return view('admin/laporan/print_absensi_detail', $data);
    }

    /**
     * Laporan Statistik umum
     */
    public function statistik()
    {
        $data = [
            'title' => 'Laporan Statistik',
            'pageTitle' => 'Laporan Statistik',
            'pageDescription' => 'Statistik global aplikasi',
            'user' => $this->getUserData(),
            'stats' => $this->dashboardModel->getAdminStats(),
            'kelasSummary' => $this->dashboardModel->getKelasSummary(),
            'chartData' => $this->dashboardModel->getChartData(),
        ];

        return view('admin/laporan/statistik', $data);
    }
}
