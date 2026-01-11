<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaModel extends Model
{
    protected $table            = 'siswa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'nis',
        'nama_lengkap',
        'jenis_kelamin',
        'kelas_id',
        'tahun_ajaran',
        'created_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = false;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'nis'               => 'required',
        'nama_lengkap'      => 'required|min_length[3]',
        'jenis_kelamin'     => 'required|in_list[L,P]',
        'tahun_ajaran'      => 'required',
        'user_id'           => 'required|numeric|is_unique[siswa.user_id]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get all siswa with user and kelas data
     */
    public function getAllSiswa()
    {
        return $this->select('siswa.*, users.username, users.email, users.is_active, kelas.nama_kelas')
            ->join('users', 'users.id = siswa.user_id')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->findAll();
    }

    /**
     * Get siswa by user_id
     */
    public function getByUserId($userId)
    {
        return $this->select('siswa.*, kelas.nama_kelas, users.username, users.is_active')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->join('users', 'users.id = siswa.user_id', 'left')
            ->where('siswa.user_id', $userId)
            ->first();
    }

    /**
     * Get siswa by kelas
     */
    public function getByKelas($kelasId)
    {
        return $this->where('siswa.kelas_id', $kelasId)
            ->join('users', 'users.id = siswa.user_id')
            ->select('siswa.*, users.username')
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->findAll();
    }

    /**
     * Get Jumlah siswa per kelas
     */
    public function getCountByKelas()
    {
        return $this->select('kelas.nama_kelas, COUNT(siswa.id) as jumlah_siswa')
            ->join('kelas', 'kelas.id = siswa.kelas_id')
            ->groupBy('siswa.kelas_id')
            ->findAll();
    }

    /**
     * Search siswa by nama atau NIS
     */
    public function searchSiswa($keyword)
    {
        return $this->like('siswa.nama_lengkap', $keyword)
            ->orLike('siswa.nis', $keyword)
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->select('siswa.*, kelas.nama_kelas')
            ->findAll();
    }

    /**
     * Get siswa with wali kelas
     */
    public function getSiswaWithWaliKelas($siswaId = null)
    {
        $builder = $this->select('siswa.*, kelas.nama_kelas, guru.nama_lengkap as wali_kelas')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->join('guru', 'guru.id = kelas.wali_kelas_id', 'left');

        if ($siswaId) {
            return $builder->where('siswa.id', $siswaId)->first();
        }
        return $builder->findAll();
    }

    public function getCountKelasById($kelasId) {
        return $this
            ->where('kelas_id', $kelasId)
            ->countAllResults();
    }
}
