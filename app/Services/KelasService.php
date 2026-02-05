<?php

namespace App\Services;

use App\Models\KelasModel;
use App\Models\GuruModel;
use App\Models\SiswaModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

/**
 * KelasService
 * 
 * Business logic layer for managing kelas (class) operations
 * Handles validation, data processing, and complex operations
 */
class KelasService extends BaseService
{
    protected KelasModel $kelasModel;
    protected GuruModel $guruModel;
    protected SiswaModel $siswaModel;

    public function __construct()
    {
        parent::__construct();
        $this->kelasModel = new KelasModel();
        $this->guruModel = new GuruModel();
        $this->siswaModel = new SiswaModel();
    }

    /**
     * Get all kelas with pagination and search
     * 
     * @param int $perPage Number of items per page
     * @param string|null $search Search term
     * @return array
     */
    public function getAllKelas(int $perPage = 20, ?string $search = null): array
    {
        try {
            $builder = $this->kelasModel
                ->select('kelas.*, 
                         guru.nama_lengkap as nama_wali_kelas,
                         guru.nip as nip_wali_kelas,
                         COUNT(siswa.id) as jumlah_siswa')
                ->join('guru', 'guru.id = kelas.wali_kelas_id', 'left')
                ->join('siswa', 'siswa.kelas_id = kelas.id', 'left')
                ->groupBy('kelas.id');

            if ($search) {
                $builder->groupStart()
                    ->like('kelas.nama_kelas', $search)
                    ->orLike('kelas.jurusan', $search)
                    ->orLike('kelas.tingkat', $search)
                    ->orLike('guru.nama_lengkap', $search)
                    ->groupEnd();
            }

            $builder->orderBy('kelas.tingkat', 'ASC')
                ->orderBy('kelas.nama_kelas', 'ASC');

            return $this->success([
                'kelas' => $builder->paginate($perPage),
                'pager' => $this->kelasModel->pager
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in KelasService::getAllKelas: ' . $e->getMessage());
            return $this->error('Gagal mengambil data kelas: ' . $e->getMessage());
        }
    }

    /**
     * Get kelas by ID with complete details
     * 
     * @param int $id
     * @return array
     */
    public function getKelasById(int $id): array
    {
        try {
            $kelas = $this->kelasModel->getKelasWithJumlahSiswa($id);

            if (!$kelas) {
                return $this->error('Kelas tidak ditemukan', 404);
            }

            // Get list of students in this class
            $siswa = $this->siswaModel->where('kelas_id', $id)
                ->orderBy('nama_lengkap', 'ASC')
                ->findAll();

            $kelas['siswa'] = $siswa;

            return $this->success($kelas);
        } catch (\Exception $e) {
            log_message('error', 'Error in KelasService::getKelasById: ' . $e->getMessage());
            return $this->error('Gagal mengambil data kelas: ' . $e->getMessage());
        }
    }

    /**
     * Create new kelas
     * 
     * @param array $data
     * @return array
     */
    public function createKelas(array $data): array
    {
        try {
            $this->db->transStart();

            // Validate wali kelas if provided
            if (!empty($data['wali_kelas_id'])) {
                $waliKelas = $this->guruModel->find($data['wali_kelas_id']);
                if (!$waliKelas) {
                    return $this->error('Guru tidak ditemukan');
                }

                // Check if guru is already wali kelas for another class
                $existingKelas = $this->kelasModel->where('wali_kelas_id', $data['wali_kelas_id'])->first();
                if ($existingKelas) {
                    return $this->error('Guru ini sudah menjadi wali kelas di kelas ' . $existingKelas['nama_kelas']);
                }
            }

            // Check if kelas name already exists
            $existingKelas = $this->kelasModel->where('nama_kelas', $data['nama_kelas'])->first();
            if ($existingKelas) {
                return $this->error('Nama kelas sudah digunakan');
            }

            $kelasId = $this->kelasModel->insert($data);

            if (!$kelasId) {
                $this->db->transRollback();
                return $this->error('Gagal membuat kelas: ' . implode(', ', $this->kelasModel->errors()));
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->error('Gagal membuat kelas');
            }

            return $this->success([
                'id' => $kelasId,
                'message' => 'Kelas berhasil dibuat'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in KelasService::createKelas: ' . $e->getMessage());
            return $this->error('Gagal membuat kelas: ' . $e->getMessage());
        }
    }

    /**
     * Update kelas
     * 
     * @param int $id
     * @param array $data
     * @return array
     */
    public function updateKelas(int $id, array $data): array
    {
        try {
            $this->db->transStart();

            // Check if kelas exists
            $kelas = $this->kelasModel->find($id);
            if (!$kelas) {
                return $this->error('Kelas tidak ditemukan', 404);
            }

            // Validate wali kelas if provided
            if (!empty($data['wali_kelas_id'])) {
                $waliKelas = $this->guruModel->find($data['wali_kelas_id']);
                if (!$waliKelas) {
                    return $this->error('Guru tidak ditemukan');
                }

                // Check if guru is already wali kelas for another class (excluding current class)
                $existingKelas = $this->kelasModel
                    ->where('wali_kelas_id', $data['wali_kelas_id'])
                    ->where('id !=', $id)
                    ->first();
                if ($existingKelas) {
                    return $this->error('Guru ini sudah menjadi wali kelas di kelas ' . $existingKelas['nama_kelas']);
                }
            }

            // Check if kelas name already exists (excluding current class)
            if (isset($data['nama_kelas'])) {
                $existingKelas = $this->kelasModel
                    ->where('nama_kelas', $data['nama_kelas'])
                    ->where('id !=', $id)
                    ->first();
                if ($existingKelas) {
                    return $this->error('Nama kelas sudah digunakan');
                }
            }

            $success = $this->kelasModel->update($id, $data);

            if (!$success) {
                $this->db->transRollback();
                return $this->error('Gagal mengupdate kelas: ' . implode(', ', $this->kelasModel->errors()));
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->error('Gagal mengupdate kelas');
            }

            return $this->success([
                'id' => $id,
                'message' => 'Kelas berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in KelasService::updateKelas: ' . $e->getMessage());
            return $this->error('Gagal mengupdate kelas: ' . $e->getMessage());
        }
    }

    /**
     * Delete kelas
     * 
     * @param int $id
     * @return array
     */
    public function deleteKelas(int $id): array
    {
        try {
            $this->db->transStart();

            // Check if kelas exists
            $kelas = $this->kelasModel->find($id);
            if (!$kelas) {
                return $this->error('Kelas tidak ditemukan', 404);
            }

            // Check if there are students in this class
            $siswaCount = $this->siswaModel->where('kelas_id', $id)->countAllResults();
            if ($siswaCount > 0) {
                return $this->error('Tidak dapat menghapus kelas yang masih memiliki siswa. Silakan pindahkan atau hapus siswa terlebih dahulu.');
            }

            $success = $this->kelasModel->delete($id);

            if (!$success) {
                $this->db->transRollback();
                return $this->error('Gagal menghapus kelas');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->error('Gagal menghapus kelas');
            }

            return $this->success([
                'message' => 'Kelas berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in KelasService::deleteKelas: ' . $e->getMessage());
            return $this->error('Gagal menghapus kelas: ' . $e->getMessage());
        }
    }

    /**
     * Get kelas statistics
     * 
     * @return array
     */
    public function getKelasStatistics(): array
    {
        try {
            $statistics = [
                'total_kelas' => $this->kelasModel->countAll(),
                'kelas_per_tingkat' => [],
                'kelas_tanpa_wali' => $this->kelasModel->getKelasWithoutWali(),
                'rata_rata_siswa_per_kelas' => 0
            ];

            // Get count per tingkat
            $tingkatStats = $this->kelasModel
                ->select('tingkat, COUNT(*) as total')
                ->groupBy('tingkat')
                ->orderBy('tingkat', 'ASC')
                ->findAll();

            foreach ($tingkatStats as $stat) {
                $statistics['kelas_per_tingkat'][$stat['tingkat']] = $stat['total'];
            }

            // Calculate average students per class
            $avgResult = $this->db->table('kelas')
                ->select('AVG(siswa_count) as avg_siswa')
                ->join('(SELECT kelas_id, COUNT(*) as siswa_count FROM siswa GROUP BY kelas_id) as siswa_stats', 
                       'siswa_stats.kelas_id = kelas.id', 'left')
                ->get()
                ->getRowArray();

            $statistics['rata_rata_siswa_per_kelas'] = round($avgResult['avg_siswa'] ?? 0, 2);

            return $this->success($statistics);
        } catch (\Exception $e) {
            log_message('error', 'Error in KelasService::getKelasStatistics: ' . $e->getMessage());
            return $this->error('Gagal mengambil statistik kelas: ' . $e->getMessage());
        }
    }

    /**
     * Get kelas list for dropdown
     * 
     * @param int|null $tingkat Filter by tingkat
     * @return array
     */
    public function getKelasForDropdown(?int $tingkat = null): array
    {
        try {
            $builder = $this->kelasModel->orderBy('tingkat, nama_kelas');

            if ($tingkat) {
                $builder->where('tingkat', $tingkat);
            }

            $kelas = $builder->findAll();
            $dropdown = [];

            foreach ($kelas as $k) {
                $dropdown[$k['id']] = $k['nama_kelas'] . ' - ' . $k['jurusan'];
            }

            return $this->success($dropdown);
        } catch (\Exception $e) {
            log_message('error', 'Error in KelasService::getKelasForDropdown: ' . $e->getMessage());
            return $this->error('Gagal mengambil data kelas: ' . $e->getMessage());
        }
    }

    /**
     * Assign wali kelas to a class
     * 
     * @param int $kelasId
     * @param int $guruId
     * @return array
     */
    public function assignWaliKelas(int $kelasId, int $guruId): array
    {
        try {
            // Check if kelas exists
            $kelas = $this->kelasModel->find($kelasId);
            if (!$kelas) {
                return $this->error('Kelas tidak ditemukan', 404);
            }

            // Check if guru exists
            $guru = $this->guruModel->find($guruId);
            if (!$guru) {
                return $this->error('Guru tidak ditemukan', 404);
            }

            // Check if guru is already wali kelas for another class
            $existingKelas = $this->kelasModel->where('wali_kelas_id', $guruId)->first();
            if ($existingKelas && $existingKelas['id'] != $kelasId) {
                return $this->error('Guru ini sudah menjadi wali kelas di kelas ' . $existingKelas['nama_kelas']);
            }

            return $this->updateKelas($kelasId, ['wali_kelas_id' => $guruId]);
        } catch (\Exception $e) {
            log_message('error', 'Error in KelasService::assignWaliKelas: ' . $e->getMessage());
            return $this->error('Gagal menugaskan wali kelas: ' . $e->getMessage());
        }
    }

    /**
     * Remove wali kelas from a class
     * 
     * @param int $kelasId
     * @return array
     */
    public function removeWaliKelas(int $kelasId): array
    {
        try {
            // Check if kelas exists
            $kelas = $this->kelasModel->find($kelasId);
            if (!$kelas) {
                return $this->error('Kelas tidak ditemukan', 404);
            }

            return $this->updateKelas($kelasId, ['wali_kelas_id' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Error in KelasService::removeWaliKelas: ' . $e->getMessage());
            return $this->error('Gagal menghapus wali kelas: ' . $e->getMessage());
        }
    }

    /**
     * Get kelas by wali kelas ID
     * 
     * @param int $guruId
     * @return array
     */
    public function getKelasByWaliKelas(int $guruId): array
    {
        try {
            $kelas = $this->kelasModel->getByWaliKelas($guruId);

            if (!$kelas) {
                return $this->error('Guru ini tidak menjadi wali kelas', 404);
            }

            // Get student count
            $siswaCount = $this->siswaModel->where('kelas_id', $kelas['id'])->countAllResults();
            $kelas['jumlah_siswa'] = $siswaCount;

            return $this->success($kelas);
        } catch (\Exception $e) {
            log_message('error', 'Error in KelasService::getKelasByWaliKelas: ' . $e->getMessage());
            return $this->error('Gagal mengambil data kelas: ' . $e->getMessage());
        }
    }
}
