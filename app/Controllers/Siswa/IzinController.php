<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\IzinSiswaModel;

class IzinController extends BaseController
{
    protected $siswaModel;
    protected $izinSiswaModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->izinSiswaModel = new IzinSiswaModel();
    }

    public function index()
    {
        // Get siswa data
        $userId = session()->get('user_id');
        $siswa = $this->siswaModel->getByUserId($userId);

        if (!$siswa) {
            return redirect()->to('/access-denied')->with('error', 'Data siswa tidak ditemukan');
        }

        // Get filter status
        $status = $this->request->getGet('status') ?? null;

        // Get izin data
        $builder = $this->izinSiswaModel
            ->select('izin_siswa.*, users.username as approved_by_username')
            ->join('users', 'users.id = izin_siswa.approved_by', 'left')
            ->where('izin_siswa.siswa_id', $siswa['id'])
            ->orderBy('izin_siswa.created_at', 'DESC');

        if ($status) {
            $builder->where('izin_siswa.status', $status);
        }

        $izinData = $builder->findAll();

        // Count by status
        $countPending = $this->izinSiswaModel->where('siswa_id', $siswa['id'])->where('status', 'pending')->countAllResults();
        $countDisetujui = $this->izinSiswaModel->where('siswa_id', $siswa['id'])->where('status', 'disetujui')->countAllResults();
        $countDitolak = $this->izinSiswaModel->where('siswa_id', $siswa['id'])->where('status', 'ditolak')->countAllResults();

        $data = [
            'title' => 'Pengajuan Izin',
            'siswa' => $siswa,
            'izinData' => $izinData,
            'status' => $status,
            'countPending' => $countPending,
            'countDisetujui' => $countDisetujui,
            'countDitolak' => $countDitolak
        ];

        return view('siswa/izin/index', $data);
    }

    public function create()
    {
        // Get siswa data
        $userId = session()->get('user_id');
        $siswa = $this->siswaModel->getByUserId($userId);

        if (!$siswa) {
            return redirect()->to('/access-denied')->with('error', 'Data siswa tidak ditemukan');
        }

        $data = [
            'title' => 'Ajukan Izin',
            'siswa' => $siswa
        ];

        return view('siswa/izin/create', $data);
    }

    public function store()
    {
        // Get siswa data
        $userId = session()->get('user_id');
        $siswa = $this->siswaModel->getByUserId($userId);

        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan');
        }

        // Validation
        $validation = \Config\Services::validation();
        $validation->setRules([
            'tanggal' => 'required|valid_date',
            'jenis_izin' => 'required|in_list[Sakit,Izin]',
            'alasan' => 'required|min_length[10]',
            'berkas' => 'permit_empty|uploaded[berkas]|max_size[berkas,2048]|ext_in[berkas,jpg,jpeg,png,pdf]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Handle file upload
        $berkas = $this->request->getFile('berkas');
        $berkasName = null;

        if ($berkas && $berkas->isValid() && !$berkas->hasMoved()) {
            $berkasName = $berkas->getRandomName();
            $berkas->move(WRITEPATH . 'uploads/izin', $berkasName);
        }

        // Save izin
        $data = [
            'siswa_id' => $siswa['id'],
            'tanggal' => $this->request->getPost('tanggal'),
            'jenis_izin' => $this->request->getPost('jenis_izin'),
            'alasan' => $this->request->getPost('alasan'),
            'berkas' => $berkasName,
            'status' => 'pending'
        ];

        if ($this->izinSiswaModel->insert($data)) {
            return redirect()->to('/siswa/izin')->with('success', 'Izin berhasil diajukan. Menunggu persetujuan wali kelas.');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal mengajukan izin');
    }
}
