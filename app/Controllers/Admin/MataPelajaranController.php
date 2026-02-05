<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\MataPelajaranService;
use CodeIgniter\Exceptions\PageNotFoundException;

class MataPelajaranController extends BaseController
{
    protected $mapelService;
    protected $session;

    public function __construct()
    {
        $this->mapelService = new MataPelajaranService();
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

        $perPage = $this->request->getGet('per_page') ?? 50;
        $search = $this->request->getGet('search');

        $result = $this->mapelService->getAllMapel($perPage, $search);
        $statsResult = $this->mapelService->getMapelStatistics();

        $data = [
            'title' => 'Manajemen Mata Pelajaran',
            'pageTitle' => 'Mata Pelajaran',
            'pageDescription' => 'Kelola data mata pelajaran',
            'mapel' => $result['success'] ? $result['data'] : ['mapel' => [], 'pager' => null],
            'search' => $search,
            'perPage' => $perPage,
            'stats' => $statsResult['success'] ? $statsResult['data']['mapel_per_kategori'] : [],
        ];

        return view('admin/mata_pelajaran/index', $data);
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
            'title' => 'Tambah Mata Pelajaran',
            'pageTitle' => 'Tambah Mata Pelajaran',
            'pageDescription' => 'Isi form untuk menambahkan mata pelajaran baru',
            'validation' => \Config\Services::validation()
        ];

        return view('admin/mata_pelajaran/create', $data);
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

        $data = [
            'kode_mapel' => $this->request->getPost('kode_mapel'),
            'nama_mapel' => $this->request->getPost('nama_mapel'),
            'kategori' => $this->request->getPost('kategori')
        ];

        $result = $this->mapelService->createMapel($data);

        if ($result['success']) {
            $this->session->setFlashdata('success', 'Sip! Mapel baru sudah masuk.');
            return redirect()->to('/admin/mata-pelajaran');
        } else {
            $this->session->setFlashdata('error', $result['message']);
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

        $result = $this->mapelService->getMapelById($id);

        if (!$result['success']) {
            throw new PageNotFoundException('Mata pelajaran tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Mata Pelajaran',
            'pageTitle' => 'Edit Mata Pelajaran',
            'pageDescription' => 'Edit data mata pelajaran',
            'mapel' => $result['data'],
            'validation' => \Config\Services::validation()
        ];

        return view('admin/mata_pelajaran/edit', $data);
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

        $data = [
            'kode_mapel' => $this->request->getPost('kode_mapel'),
            'nama_mapel' => $this->request->getPost('nama_mapel'),
            'kategori' => $this->request->getPost('kategori')
        ];

        $result = $this->mapelService->updateMapel($id, $data);

        if ($result['success']) {
            $this->session->setFlashdata('success', 'Done! Mapel sudah diperbarui.');
            return redirect()->to('/admin/mata-pelajaran');
        } else {
            $this->session->setFlashdata('error', $result['message']);
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

        $result = $this->mapelService->deleteMapel($id);

        if ($result['success']) {
            $this->session->setFlashdata('success', 'Done, Mata pelajaran sudah dihapus!');
        } else {
            $this->session->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/admin/mata-pelajaran');
    }
}
