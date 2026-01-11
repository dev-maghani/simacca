<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\UserModel;

class ProfilController extends BaseController
{
    protected $siswaModel;
    protected $userModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // Get siswa data
        $userId = session()->get('user_id');
        $siswa = $this->siswaModel->getByUserId($userId);

        if (!$siswa) {
            return redirect()->to('/access-denied')->with('error', 'Data siswa tidak ditemukan');
        }

        $data = [
            'title' => 'Profil Saya',
            'siswa' => $siswa
        ];

        return view('siswa/profil/index', $data);
    }

    public function update()
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
            'email' => 'required|valid_email',
            'no_telp' => 'permit_empty|numeric|min_length[10]|max_length[15]',
            'alamat' => 'permit_empty|max_length[255]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Update siswa data
        $updateData = [
            'email' => $this->request->getPost('email'),
            'no_telp' => $this->request->getPost('no_telp'),
            'alamat' => $this->request->getPost('alamat')
        ];

        if ($this->siswaModel->update($siswa['id'], $updateData)) {
            // Update email in users table too
            $this->userModel->update($userId, ['email' => $updateData['email']]);
            
            return redirect()->to('/siswa/profil')->with('success', 'Profil berhasil diperbarui');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal memperbarui profil');
    }

    public function changePassword()
    {
        // Get siswa data
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan');
        }

        // Validation
        $validation = \Config\Services::validation();
        $validation->setRules([
            'current_password' => 'required',
            'new_password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('errors', $validation->getErrors());
        }

        // Verify current password
        if (!password_verify($this->request->getPost('current_password'), $user['password'])) {
            return redirect()->back()->with('error', 'Password lama tidak sesuai');
        }

        // Update password
        $newPassword = password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT);
        
        if ($this->userModel->update($userId, ['password' => $newPassword])) {
            return redirect()->to('/siswa/profil')->with('success', 'Password berhasil diubah');
        }

        return redirect()->back()->with('error', 'Gagal mengubah password');
    }
}
