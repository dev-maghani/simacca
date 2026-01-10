<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiDetailModel extends Model
{
    protected $table            = 'absensi_detail';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'absensi_id',
        'siswa_id',
        'status',
        'keterangan',
        'waktu_absen',
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
        'absensi_id'    => 'required|numeric',
        'siswa_id'      => 'required|numeric',
        'status'        => 'required|in_list[hadir,izin,sakit,alpa]'
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
     * Get detail absensi by absensi_id
     */
    public function getByAbsensi($absensiId)
    {
        return $this->select('absensi_detail.*,
                            siswa.nama_lengkap,
                            siswa.nis')
            ->join('siswa', 'siswa.id = absensi_detail.siswa_id')
            ->where('absensi_id', $absensiId)
            ->orderBy('siswa.nama_lengkap')
            ->findAll();
    }

    /**
     * Get absensi siswa dalam periode tertentu
     */
    public function getAbsensiSiswa($siswaId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('absensi_detail.*,
                                absensi.tanggal,
                                absensi.pertemuan_ke,
                                mata_pelajaran.nama_mapel,
                                guru.nama_lengkap as nama_guru')
            ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
            ->where('absensi_detail.siswa_id', $siswaId)
            ->orderBy('absensi.tanggal', 'DESC');

        if ($startDate && $endDate) {
            $builder->where("absensi.tanggal BETWEEN '$startDate' AND '$endDate'");
        }

        return $builder->findAll();
    }

    /**
     * Get statistik kehadiran siswa
     */
    public function getStatistikSiswa($siswaId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('status, COUNT(*) as jumlah')
            ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
            ->where('siswa_id', $siswaId)
            ->groupBy('status');

        if ($startDate && $endDate) {
            $builder->where("absensi.tanggal BETWEEN '$startDate' AND '$endDate'");
        }

        $result = $builder->findAll();

        $statistik = [
            'hadir' => 0,
            'izin'  => 0,
            'sakit' => 0,
            'alpa'  => 0,
            'total' => 0,
        ];

        foreach ($result as $row) {
            $statistik[$row['status']] = $row['jumlah'];
            $statistik['total'] += $row['jumlah'];
        }

        return $statistik;
    }

    /**
     * Insert batch absensi detail
     */
    public function insertBatchAbsensi($absensiId, $dataSiswa)
    {
        $batchData = [];
        foreach ($dataSiswa as $siswaId => $status) {
            $batchData[] = [
                'absensi_id' => $absensiId,
                'siswa_id'      => $siswaId,
                'status'    => $status['status'],
                'keterangan' => $status['keterangan'] ?? null,
                'waktu_absen' =>  date('Y-m-d H:i:s')
            ];
        }

        return $this->insertBatch($batchData);
    }

    /**
     * Update absensi detail
     */
    public function updateAbsensi($absensiId, $siswaId, $data)
    {
        return $this->where('absensi_id', $absensiId)
            ->where('siswa_id', $siswaId)
            ->set($data)
            ->update();
    }

    public function getAbsensiToday($today)
    {
        return $this->select('absensi_detail.*')
            ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->where('absensi.tanggal', $today)
            ->countAllResults();;
    }

    public function getAbsensiThisMonth($first, $last, $kelasId)
    {
        return $this->select('absensi_detail.*')
            ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->where('jadwal_mengajar.kelas_id', $kelasId)
            ->where('absensi.tanggal >=', $first)
            ->where('absensi.tanggal <=', $last)
            ->countAllResults();;
    }

    public function getStatistikBulanan($month, $year)
    {
        return $this->select('status, COUNT(*) as total')
            ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
            ->where('MONTH(absensi.tanggal)', $month)
            ->where('YEAR(absensi.tanggal)', $year)
            ->groupBy('status')
            ->findAll();
    }

    public function getDetailStats($id) {
        return $this->select('status, COUNT(*) as total')
            ->where('absensi_id', $id)
            ->groupBy('status')
            ->findAll();
    }
}
