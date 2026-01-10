<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JadwalMengajarModel;
use App\Models\GuruModel;
use App\Models\MataPelajaranModel;
use App\Models\KelasModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class JadwalController extends BaseController
{
    protected $jadwalModel;
    protected $guruModel;
    protected $mapelModel;
    protected $kelasModel;
    protected $session;

    public function __construct()
    {
        $this->jadwalModel = new JadwalMengajarModel();
        $this->guruModel = new GuruModel();
        $this->mapelModel = new MataPelajaranModel();
        $this->kelasModel = new KelasModel();
        $this->session = session();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check if user is logged in and has admin role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
            return redirect()->to('/login');
        }

        $perPage = $this->request->getGet('per_page') ?? 10;
        $search = $this->request->getGet('search');
        $semester = $this->request->getGet('semester');
        $tahunAjaran = $this->request->getGet('tahun_ajaran');

        $data = [
            'title' => 'Manajemen Jadwal Mengajar',
            'pageTitle' => 'Jadwal Mengajar',
            'pageDescription' => 'Kelola jadwal mengajar guru',
            'jadwal' => $this->jadwalModel->getAllJadwal($perPage, $search, $semester, $tahunAjaran),
            'pager' => $this->jadwalModel->pager,
            'search' => $search,
            'perPage' => $perPage,
            'semester' => $semester,
            'tahunAjaran' => $tahunAjaran,
            'hariList' => $this->jadwalModel->getHariList(),
            'semesterList' => $this->jadwalModel->getSemesterList(),
            'tahunAjaranList' => $this->jadwalModel->getTahunAjaranList()
        ];

        return view('admin/jadwal/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check if user is logged in and has admin role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'Tambah Jadwal Mengajar',
            'pageTitle' => 'Tambah Jadwal Mengajar',
            'pageDescription' => 'Isi form untuk menambahkan jadwal mengajar baru',
            'validation' => \Config\Services::validation(),
            'guruOptions' => $this->guruModel->getGuruDropdown(),
            'mapelOptions' => $this->mapelModel->getAllMapelForDropdown(),
            'kelasOptions' => $this->kelasModel->getListKelas(),
            'hariList' => $this->jadwalModel->getHariList(),
            'semesterList' => $this->jadwalModel->getSemesterList(),
            'tahunAjaranList' => $this->jadwalModel->getTahunAjaranList(),
            'currentYear' => date('Y')
        ];

        return view('admin/jadwal/create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        // Check if user is logged in and has admin role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
            return redirect()->to('/login');
        }

        // Validate input
        if (!$this->validate($this->jadwalModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get form data
        $guruId = $this->request->getPost('guru_id');
        $kelasId = $this->request->getPost('kelas_id');
        $hari = $this->request->getPost('hari');
        $jamMulai = $this->request->getPost('jam_mulai');
        $jamSelesai = $this->request->getPost('jam_selesai');

        // Check for schedule conflict for teacher
        if ($this->jadwalModel->checkConflict($guruId, $hari, $jamMulai, $jamSelesai)) {
            $this->session->setFlashdata('error', 'Guru memiliki jadwal lain pada waktu yang sama!');
            return redirect()->back()->withInput();
        }

        // Check for schedule conflict for class
        if ($this->jadwalModel->checkKelasConflict($kelasId, $hari, $jamMulai, $jamSelesai)) {
            $this->session->setFlashdata('error', 'Kelas memiliki jadwal lain pada waktu yang sama!');
            return redirect()->back()->withInput();
        }

        // Prepare data
        $data = [
            'guru_id' => $guruId,
            'mata_pelajaran_id' => $this->request->getPost('mata_pelajaran_id'),
            'kelas_id' => $kelasId,
            'hari' => $hari,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'semester' => $this->request->getPost('semester'),
            'tahun_ajaran' => $this->request->getPost('tahun_ajaran')
        ];

        // Save to database
        if ($this->jadwalModel->save($data)) {
            $this->session->setFlashdata('success', 'Jadwal mengajar berhasil ditambahkan!');
            return redirect()->to('/admin/jadwal');
        } else {
            $this->session->setFlashdata('error', 'Gagal menambahkan jadwal mengajar.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Check if user is logged in and has admin role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
            return redirect()->to('/login');
        }

        $jadwal = $this->jadwalModel->getJadwalWithDetail($id);

        if (!$jadwal) {
            throw new PageNotFoundException('Jadwal mengajar tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Jadwal Mengajar',
            'pageTitle' => 'Edit Jadwal Mengajar',
            'pageDescription' => 'Edit data jadwal mengajar',
            'jadwal' => $jadwal,
            'validation' => \Config\Services::validation(),
            'guruOptions' => $this->guruModel->getGuruDropdown(),
            'mapelOptions' => $this->mapelModel->getAllMapelForDropdown(),
            'kelasOptions' => $this->kelasModel->getListKelas(),
            'hariList' => $this->jadwalModel->getHariList(),
            'semesterList' => $this->jadwalModel->getSemesterList(),
            'tahunAjaranList' => $this->jadwalModel->getTahunAjaranList()
        ];

        return view('admin/jadwal/edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        // Check if user is logged in and has admin role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
            return redirect()->to('/login');
        }

        // Check if exists
        $jadwal = $this->jadwalModel->find($id);
        if (!$jadwal) {
            throw new PageNotFoundException('Jadwal mengajar tidak ditemukan');
        }

        // Validate input
        if (!$this->validate($this->jadwalModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get form data
        $guruId = $this->request->getPost('guru_id');
        $kelasId = $this->request->getPost('kelas_id');
        $hari = $this->request->getPost('hari');
        $jamMulai = $this->request->getPost('jam_mulai');
        $jamSelesai = $this->request->getPost('jam_selesai');

        // Check for schedule conflict for teacher (excluding current)
        if ($this->jadwalModel->checkConflict($guruId, $hari, $jamMulai, $jamSelesai, $id)) {
            $this->session->setFlashdata('error', 'Guru memiliki jadwal lain pada waktu yang sama!');
            return redirect()->back()->withInput();
        }

        // Check for schedule conflict for class (excluding current)
        if ($this->jadwalModel->checkKelasConflict($kelasId, $hari, $jamMulai, $jamSelesai, $id)) {
            $this->session->setFlashdata('error', 'Kelas memiliki jadwal lain pada waktu yang sama!');
            return redirect()->back()->withInput();
        }

        // Prepare data
        $data = [
            'id' => $id,
            'guru_id' => $guruId,
            'mata_pelajaran_id' => $this->request->getPost('mata_pelajaran_id'),
            'kelas_id' => $kelasId,
            'hari' => $hari,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'semester' => $this->request->getPost('semester'),
            'tahun_ajaran' => $this->request->getPost('tahun_ajaran')
        ];

        // Update database
        if ($this->jadwalModel->save($data)) {
            $this->session->setFlashdata('success', 'Jadwal mengajar berhasil diperbarui!');
            return redirect()->to('/admin/jadwal');
        } else {
            $this->session->setFlashdata('error', 'Gagal memperbarui jadwal mengajar.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        // Check if user is logged in and has admin role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
            return redirect()->to('/login');
        }

        // Check if exists
        $jadwal = $this->jadwalModel->find($id);
        if (!$jadwal) {
            throw new PageNotFoundException('Jadwal mengajar tidak ditemukan');
        }

        // Check if jadwal has related absensi data
        $db = \Config\Database::connect();
        $checkAbsensi = $db->table('absensi')
            ->where('jadwal_mengajar_id', $id)
            ->countAllResults();

        if ($checkAbsensi > 0) {
            $this->session->setFlashdata('error', 'Jadwal tidak dapat dihapus karena sudah memiliki data absensi!');
            return redirect()->back();
        }

        // Delete from database
        if ($this->jadwalModel->delete($id)) {
            $this->session->setFlashdata('success', 'Jadwal mengajar berhasil dihapus!');
        } else {
            $this->session->setFlashdata('error', 'Gagal menghapus jadwal mengajar.');
        }

        return redirect()->to('/admin/jadwal');
    }

    /**
     * Get jadwal by guru (AJAX)
     */
    public function getByGuru()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $guruId = $this->request->getGet('guru_id');
        $jadwal = $this->jadwalModel->getByGuru($guruId);

        return $this->response->setJSON([
            'success' => true,
            'data' => $jadwal
        ]);
    }

    /**
     * Get jadwal by kelas (AJAX)
     */
    public function getByKelas()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $kelasId = $this->request->getGet('kelas_id');
        $jadwal = $this->jadwalModel->getByKelas($kelasId);

        return $this->response->setJSON([
            'success' => true,
            'data' => $jadwal
        ]);
    }

    /**
     * Check schedule conflict (AJAX)
     */
    public function checkConflict()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $guruId = $this->request->getPost('guru_id');
        $kelasId = $this->request->getPost('kelas_id');
        $hari = $this->request->getPost('hari');
        $jamMulai = $this->request->getPost('jam_mulai');
        $jamSelesai = $this->request->getPost('jam_selesai');
        $excludeId = $this->request->getPost('exclude_id');

        $conflictGuru = $this->jadwalModel->checkConflict($guruId, $hari, $jamMulai, $jamSelesai, $excludeId);
        $conflictKelas = $this->jadwalModel->checkKelasConflict($kelasId, $hari, $jamMulai, $jamSelesai, $excludeId);

        return $this->response->setJSON([
            'success' => true,
            'conflict_guru' => $conflictGuru,
            'conflict_kelas' => $conflictKelas
        ]);
    }

    /**
     * Export jadwal to Excel
     */
    public function export()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
            return redirect()->to('/login');
        }

        $semester = $this->request->getGet('semester');
        $tahunAjaran = $this->request->getGet('tahun_ajaran');

        $jadwal = $this->jadwalModel->select('jadwal_mengajar.*, 
                                            guru.nama_lengkap as nama_guru,
                                            guru.nip,
                                            mata_pelajaran.nama_mapel,
                                            mata_pelajaran.kode_mapel,
                                            kelas.nama_kelas,
                                            kelas.tingkat,
                                            kelas.jurusan')
            ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
            ->orderBy('jadwal_mengajar.hari', 'ASC')
            ->orderBy('jadwal_mengajar.jam_mulai', 'ASC');

        if ($semester) {
            $jadwal->where('jadwal_mengajar.semester', $semester);
        }

        if ($tahunAjaran) {
            $jadwal->where('jadwal_mengajar.tahun_ajaran', $tahunAjaran);
        }

        $jadwal = $jadwal->findAll();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Hari');
        $sheet->setCellValue('C1', 'Jam');
        $sheet->setCellValue('D1', 'Kelas');
        $sheet->setCellValue('E1', 'Guru');
        $sheet->setCellValue('F1', 'Mata Pelajaran');
        $sheet->setCellValue('G1', 'Semester');
        $sheet->setCellValue('H1', 'Tahun Ajaran');

        // Set data
        $no = 1;
        $row = 2;
        foreach ($jadwal as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['hari']);
            $sheet->setCellValue('C' . $row, $item['jam_mulai'] . ' - ' . $item['jam_selesai']);
            $sheet->setCellValue('D' . $row, $item['nama_kelas']);
            $sheet->setCellValue('E' . $row, $item['nama_guru'] . ' (' . $item['nip'] . ')');
            $sheet->setCellValue('F' . $row, $item['nama_mapel'] . ' (' . $item['kode_mapel'] . ')');
            $sheet->setCellValue('G' . $row, $item['semester']);
            $sheet->setCellValue('H' . $row, $item['tahun_ajaran']);
            $row++;
        }

        // Set column width
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(15);

        // Style header
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE2E8F0']
            ]
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        // Set filename
        $filename = 'jadwal-mengajar-' . date('Y-m-d-H-i-s') . '.xlsx';

        // Redirect output to a client's web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
