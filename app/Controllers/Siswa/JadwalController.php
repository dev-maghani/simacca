<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\JadwalMengajarModel;

class JadwalController extends BaseController
{
    protected $siswaModel;
    protected $jadwalModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->jadwalModel = new JadwalMengajarModel();
    }

    public function index()
    {
        // Get siswa data
        $userId = session()->get('user_id');
        $siswa = $this->siswaModel->getByUserId($userId);

        if (!$siswa) {
            return redirect()->to('/access-denied')->with('error', 'Data siswa tidak ditemukan');
        }

        // Get all jadwal for this class, grouped by day
        $jadwalAll = $this->jadwalModel
            ->select('jadwal_mengajar.*, mata_pelajaran.nama_mapel, mata_pelajaran.kode_mapel, guru.nama_lengkap as nama_guru')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
            ->where('jadwal_mengajar.kelas_id', $siswa['kelas_id'])
            ->orderBy('FIELD(jadwal_mengajar.hari, "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu")')
            ->orderBy('jadwal_mengajar.jam_mulai', 'ASC')
            ->findAll();

        // Group by day
        $jadwalByDay = [
            'Senin' => [],
            'Selasa' => [],
            'Rabu' => [],
            'Kamis' => [],
            'Jumat' => [],
            'Sabtu' => []
        ];

        foreach ($jadwalAll as $jadwal) {
            if (isset($jadwalByDay[$jadwal['hari']])) {
                $jadwalByDay[$jadwal['hari']][] = $jadwal;
            }
        }

        // Get current day
        $hariIni = date('l');
        $hariIndonesia = $this->convertDayToIndonesian($hariIni);

        $data = [
            'title' => 'Jadwal Pelajaran',
            'siswa' => $siswa,
            'jadwalByDay' => $jadwalByDay,
            'hariIni' => $hariIndonesia
        ];

        return view('siswa/jadwal/index', $data);
    }

    private function convertDayToIndonesian($day)
    {
        $days = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];
        return $days[$day] ?? $day;
    }
}
