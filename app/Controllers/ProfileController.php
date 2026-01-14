<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\GuruModel;
use App\Models\SiswaModel;

class ProfileController extends BaseController
{
    protected $userModel;
    protected $guruModel;
    protected $siswaModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->guruModel = new GuruModel();
        $this->siswaModel = new SiswaModel();
    }

    /**
     * Display profile page
     */
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Login dulu ya.');
        }

        $userId = session()->get('userId');
        $role = session()->get('role');

        $data = [
            'title' => 'Profil Saya',
            'user' => $this->getUserData(),
            'userData' => $this->userModel->find($userId),
            'validation' => \Config\Services::validation()
        ];

        // Get additional data based on role
        switch ($role) {
            case 'guru_mapel':
            case 'wali_kelas':
                $guru = $this->guruModel->getGuruWithMapel($this->guruModel->where('user_id', $userId)->first()['id'] ?? null);
                $data['guru'] = $guru;
                break;

            case 'siswa':
                $siswa = $this->siswaModel->getSiswaWithKelas($this->siswaModel->where('user_id', $userId)->first()['id'] ?? null);
                $data['siswa'] = $siswa;
                break;
        }

        return view('profile/index', $data);
    }

    /**
     * Update profile
     */
    public function update()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Login dulu dong.');
        }

        $userId = session()->get('userId');
        $role = session()->get('role');

        $rules = [
            'email' => 'valid_email',
        ];

        // Jika username berubah
        $userData = $this->userModel->find($userId);
        if ($this->request->getPost('username') != $userData['username']) {
            $rules['username'] = 'required|is_unique[users.username,id,' . $userId . ']';
        }

        // Jika password diisi
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
            $rules['confirm_password'] = 'required|matches[password]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Update user data
        $updateData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email')
        ];

        // Update password jika diisi
        if ($this->request->getPost('password')) {
            $updateData['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $this->userModel->update($userId, $updateData);

        // Update session data
        session()->set([
            'username' => $updateData['username'],
            'email' => $updateData['email']
        ]);

        session()->setFlashdata('success', 'Profil updated! Looking good 😎✨');
        return redirect()->back();
    }

    /**
     * Upload profile photo
     */
    public function uploadPhoto()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Login dulu ya.');
        }

        $userId = session()->get('userId');

        // Validation rules for photo
        $rules = [
            'profile_photo' => [
                'label' => 'Foto Profil',
                'rules' => 'uploaded[profile_photo]|max_size[profile_photo,5120]|is_image[profile_photo]|mime_in[profile_photo,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'uploaded' => 'Pilih foto terlebih dahulu',
                    'max_size' => 'Ukuran foto maksimal 5MB',
                    'is_image' => 'File harus berupa gambar',
                    'mime_in' => 'Format foto harus JPG, JPEG, atau PNG'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('profile_photo');

        if ($file->isValid() && !$file->hasMoved()) {
            // Get old photo to delete
            $userData = $this->userModel->find($userId);
            $oldPhoto = $userData['profile_photo'] ?? null;

            // Generate new filename
            $newName = 'profile_' . $userId . '_' . time() . '.' . $file->getExtension();

            // Move file to upload directory
            $uploadPath = WRITEPATH . 'uploads/profile/';
            
            // Create directory if not exists
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            try {
                // Move uploaded file first
                $file->move($uploadPath, $newName);
                
                $filePath = $uploadPath . $newName;
                
                // Get original file size
                $originalSize = filesize($filePath);
                
                // Optimize image (compress without losing visible quality)
                helper('image');
                $optimized = optimize_profile_photo($filePath, $filePath);
                
                if ($optimized) {
                    $newSize = filesize($filePath);
                    $savings = round((($originalSize - $newSize) / $originalSize) * 100, 2);
                    log_message('info', "Profile photo optimized: {$newName} - {$savings}% smaller");
                }

                // Update database
                $this->userModel->update($userId, ['profile_photo' => $newName]);

                // Update session
                session()->set('profile_photo', $newName);

                // Delete old photo if exists
                if ($oldPhoto && file_exists($uploadPath . $oldPhoto)) {
                    unlink($uploadPath . $oldPhoto);
                }

                session()->setFlashdata('success', 'Foto profil berhasil diupdate! 📸✨');
            } catch (\Exception $e) {
                log_message('error', 'Profile photo upload error: ' . $e->getMessage());
                session()->setFlashdata('error', 'Gagal mengupload foto. Silakan coba lagi.');
            }
        } else {
            session()->setFlashdata('error', 'File tidak valid atau sudah dipindahkan.');
        }

        return redirect()->back();
    }

    /**
     * Delete profile photo
     */
    public function deletePhoto()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Login dulu ya.');
        }

        $userId = session()->get('userId');
        $userData = $this->userModel->find($userId);
        $photo = $userData['profile_photo'] ?? null;

        if ($photo) {
            $uploadPath = WRITEPATH . 'uploads/profile/';
            $filePath = $uploadPath . $photo;

            // Delete file if exists
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Update database
            $this->userModel->update($userId, ['profile_photo' => null]);

            // Update session
            session()->set('profile_photo', null);

            session()->setFlashdata('success', 'Foto profil berhasil dihapus.');
        } else {
            session()->setFlashdata('error', 'Tidak ada foto profil untuk dihapus.');
        }

        return redirect()->back();
    }
}
