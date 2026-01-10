<?php

namespace App\Models;

use CodeIgniter\Model;

class KelasModel extends Model
{
    protected $table            = 'kelas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_kelas',
        'tingkat',
        'jurusan',
        'wali_kelas_id'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

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
        'nama_kelas'    => 'required|is_unique[kelas.nama_kelas]',
        'tingkat'       => 'required|in_list[10,11,12]',
        'jurusan'       => 'required',
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
     * Get all kelas with wali kelas data
     */
    public function getAllKelas()
    {
        return $this->select('kelas.*, guru.nama_lengkap as nama_wali_kelas')
            ->join('guru', 'guru.id = kelas.wali_kelas_id', 'left')
            ->orderBy('kelas.tingkat, kelas.nama_kelas')
            ->findAll();
    }

    /**
     * Get kelas by wali_kelas_id
     */
    public function getByWaliKelas($guruId)
    {
        return $this->where('wali_kelas_id', $guruId)->first();
    }

    /**
     * Get kelas by tingkat
     */
    public function getByTingkat($tingkat)
    {
        return $this->where('tingkat', $tingkat)->findAll();
    }

    /**
     * Get kelas with jumlah siswa
     */
    public function getKelasWithJumlahSiswa($kelasId = null)
    {
        $builder = $this->select('kelas.*, 
                             COUNT(siswa.id) as jumlah_siswa,
                             guru.nama_lengkap as nama_wali_kelas,
                             guru.nip as nip_wali_kelas,
                             mata_pelajaran.nama_mapel')
            ->join('siswa', 'siswa.kelas_id = kelas.id', 'left')
            ->join('guru', 'guru.id = kelas.wali_kelas_id', 'left')
            ->join('mata_pelajaran', 'mata_pelajaran.id = guru.mata_pelajaran_id', 'left')
            ->groupBy('kelas.id')
            ->orderBy('kelas.tingkat, kelas.nama_kelas');

        if ($kelasId) {
            return $builder->where('kelas.id', $kelasId)->first();
        }

        return $builder->findAll();
    }

    /**
     * Get kelas yang belum punya wali kelas
     */

    public function getKelasWithoutWali()
    {
        return $this->where('wali_kelas_id IS NULL')
            ->orWhere('wali_kelas_id', 0)
            ->findAll();
    }

    /**
     * Get list kelas untuk dropdown
     */
    public function getListKelas()
    {
        $kelas = $this->orderBy('tingkat, nama_kelas')->findAll();
        $list = [];

        foreach ($kelas as $k) {
            $list[$k['id']] = $k['nama_kelas'] . ' - ' . $k['jurusan'];
        }
        return $list;
    }
}
