<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\AbsensiModel;
use App\Models\AbsensiDetailModel;
use App\Models\GuruModel;
use App\Models\JadwalMengajarModel;
use App\Models\KelasModel;
use App\Models\SiswaModel;

class LaporanController extends BaseController
{
    protected $absensiModel;
    protected $absensiDetailModel;
    protected $guruModel;
    protected $jadwalModel;
    protected $kelasModel;
    protected $siswaModel;

    public function __construct()
    {
        $this->absensiModel = new AbsensiModel();
        $this->absensiDetailModel = new AbsensiDetailModel();
        $this->guruModel = new GuruModel();
        $this->jadwalModel = new JadwalMengajarModel();
        $this->kelasModel = new KelasModel();
        $this->siswaModel = new SiswaModel();
    }

    public function index()
    {
        // Get guru data from session
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }

        // Get filter dari request
        $kelasId = $this->request->getGet('kelas_id');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        // Get jadwal mengajar guru (untuk filter kelas)
        $jadwalGuru = $this->jadwalModel->getByGuru($guru['id']);
        
        // Extract unique kelas from jadwal
        $kelasIds = array_unique(array_column($jadwalGuru, 'kelas_id'));
        $kelasList = [];
        foreach ($kelasIds as $id) {
            $kelas = $this->kelasModel->find($id);
            if ($kelas) {
                $kelasList[$id] = $kelas['nama_kelas'];
            }
        }

        $laporan = null;
        $rekap = null;

        // Generate laporan jika ada filter
        if ($kelasId && $startDate && $endDate) {
            // Use model method to generate report
            $result = $this->absensiModel->generateLaporanAbsensi($guru['id'], $kelasId, $startDate, $endDate);
            $laporan = $result['laporan'];
            $rekap = $result['rekap'];
        }

        $data = [
            'title' => 'Laporan Absensi',
            'guru' => $guru,
            'kelasList' => $kelasList,
            'kelasId' => $kelasId,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'laporan' => $laporan,
            'rekap' => $rekap
        ];

        return view('guru/laporan/index_enhanced', $data);
    }

    public function print()
    {
        // Get guru data from session
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', '❌ Data guru tidak ditemukan');
        }

        // Get filter dari request
        $kelasId = $this->request->getGet('kelas_id');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        // Validate parameters
        if (!$kelasId || !$startDate || !$endDate) {
            return redirect()->to('/guru/laporan')->with('error', '❌ Parameter tidak lengkap. Silakan pilih filter terlebih dahulu.');
        }

        // Get kelas name
        $kelas = $this->kelasModel->find($kelasId);
        if (!$kelas) {
            return redirect()->to('/guru/laporan')->with('error', '❌ Data kelas tidak ditemukan');
        }

        // Use model method to generate report
        $result = $this->absensiModel->generateLaporanAbsensi($guru['id'], $kelasId, $startDate, $endDate);
        $laporan = $result['laporan'];
        $rekap = $result['rekap'];

        $data = [
            'guru' => $guru,
            'namaKelas' => $kelas['nama_kelas'],
            'startDate' => $startDate,
            'endDate' => $endDate,
            'laporan' => $laporan,
            'rekap' => $rekap
        ];

        return view('guru/laporan/print', $data);
    }
}
