<?php

namespace App\Services;

use App\Models\MataPelajaranModel;
use App\Models\GuruModel;
use App\Models\JadwalMengajarModel;

/**
 * MataPelajaranService
 * 
 * Business logic layer for managing mata pelajaran (subject) operations
 * Handles validation, data processing, and complex operations
 */
class MataPelajaranService extends BaseService
{
    protected MataPelajaranModel $mapelModel;
    protected GuruModel $guruModel;
    protected JadwalMengajarModel $jadwalModel;

    public function __construct()
    {
        parent::__construct();
        $this->mapelModel = new MataPelajaranModel();
        $this->guruModel = new GuruModel();
        $this->jadwalModel = new JadwalMengajarModel();
    }

    /**
     * Get all mata pelajaran with pagination and search
     * 
     * @param int $perPage Number of items per page
     * @param string|null $search Search term
     * @return array
     */
    public function getAllMapel(int $perPage = 50, ?string $search = null): array
    {
        try {
            $builder = $this->mapelModel;

            if ($search) {
                $builder->groupStart()
                    ->like('kode_mapel', $search)
                    ->orLike('nama_mapel', $search)
                    ->orLike('kategori', $search)
                    ->groupEnd();
            }

            $mapel = $builder->orderBy('kategori', 'ASC')
                ->orderBy('nama_mapel', 'ASC')
                ->paginate($perPage);

            return $this->success([
                'mapel' => $mapel,
                'pager' => $this->mapelModel->pager
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in MataPelajaranService::getAllMapel: ' . $e->getMessage());
            return $this->error('Gagal mengambil data mata pelajaran: ' . $e->getMessage());
        }
    }

    /**
     * Get mata pelajaran by ID
     * 
     * @param int $id
     * @return array
     */
    public function getMapelById(int $id): array
    {
        try {
            $mapel = $this->mapelModel->find($id);

            if (!$mapel) {
                return $this->error('Mata pelajaran tidak ditemukan', 404);
            }

            // Get guru yang mengajar mapel ini
            $guru = $this->guruModel->where('mata_pelajaran_id', $id)->findAll();
            $mapel['guru_pengajar'] = $guru;

            // Count jadwal mengajar
            $jadwalCount = $this->jadwalModel->where('mata_pelajaran_id', $id)->countAllResults();
            $mapel['jumlah_jadwal'] = $jadwalCount;

            return $this->success($mapel);
        } catch (\Exception $e) {
            log_message('error', 'Error in MataPelajaranService::getMapelById: ' . $e->getMessage());
            return $this->error('Gagal mengambil data mata pelajaran: ' . $e->getMessage());
        }
    }

    /**
     * Create new mata pelajaran
     * 
     * @param array $data
     * @return array
     */
    public function createMapel(array $data): array
    {
        try {
            $this->db->transStart();

            // Validate required fields
            if (empty($data['kode_mapel'])) {
                return $this->error('Kode mata pelajaran wajib diisi');
            }

            if (empty($data['nama_mapel'])) {
                return $this->error('Nama mata pelajaran wajib diisi');
            }

            if (empty($data['kategori'])) {
                return $this->error('Kategori wajib dipilih');
            }

            // Check if kode already exists
            $existing = $this->mapelModel->where('kode_mapel', $data['kode_mapel'])->first();
            if ($existing) {
                return $this->error('Kode mata pelajaran sudah digunakan');
            }

            // Normalize kategori
            $data['kategori'] = strtolower($data['kategori']);

            // Set created_at
            $data['created_at'] = date('Y-m-d H:i:s');

            $mapelId = $this->mapelModel->insert($data);

            if (!$mapelId) {
                $this->db->transRollback();
                $errors = $this->mapelModel->errors();
                return $this->error('Gagal membuat mata pelajaran: ' . implode(', ', $errors));
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->error('Gagal membuat mata pelajaran');
            }

            return $this->success([
                'id' => $mapelId,
                'message' => 'Mata pelajaran berhasil dibuat'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in MataPelajaranService::createMapel: ' . $e->getMessage());
            return $this->error('Gagal membuat mata pelajaran: ' . $e->getMessage());
        }
    }

    /**
     * Update mata pelajaran
     * 
     * @param int $id
     * @param array $data
     * @return array
     */
    public function updateMapel(int $id, array $data): array
    {
        try {
            $this->db->transStart();

            // Check if mapel exists
            $mapel = $this->mapelModel->find($id);
            if (!$mapel) {
                return $this->error('Mata pelajaran tidak ditemukan', 404);
            }

            // Check if kode already exists (excluding current mapel)
            if (isset($data['kode_mapel'])) {
                $existing = $this->mapelModel
                    ->where('kode_mapel', $data['kode_mapel'])
                    ->where('id !=', $id)
                    ->first();
                if ($existing) {
                    return $this->error('Kode mata pelajaran sudah digunakan');
                }
            }

            // Normalize kategori if provided
            if (isset($data['kategori'])) {
                $data['kategori'] = strtolower($data['kategori']);
            }

            $success = $this->mapelModel->update($id, $data);

            if (!$success) {
                $this->db->transRollback();
                $errors = $this->mapelModel->errors();
                return $this->error('Gagal mengupdate mata pelajaran: ' . implode(', ', $errors));
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->error('Gagal mengupdate mata pelajaran');
            }

            return $this->success([
                'id' => $id,
                'message' => 'Mata pelajaran berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in MataPelajaranService::updateMapel: ' . $e->getMessage());
            return $this->error('Gagal mengupdate mata pelajaran: ' . $e->getMessage());
        }
    }

    /**
     * Delete mata pelajaran
     * 
     * @param int $id
     * @return array
     */
    public function deleteMapel(int $id): array
    {
        try {
            $this->db->transStart();

            // Check if mapel exists
            $mapel = $this->mapelModel->find($id);
            if (!$mapel) {
                return $this->error('Mata pelajaran tidak ditemukan', 404);
            }

            // Check if there are guru teaching this mapel
            $guruCount = $this->guruModel->where('mata_pelajaran_id', $id)->countAllResults();
            if ($guruCount > 0) {
                return $this->error('Tidak dapat menghapus mata pelajaran yang masih memiliki guru pengajar. Silakan update data guru terlebih dahulu.');
            }

            // Check if there are jadwal for this mapel
            $jadwalCount = $this->jadwalModel->where('mata_pelajaran_id', $id)->countAllResults();
            if ($jadwalCount > 0) {
                return $this->error('Tidak dapat menghapus mata pelajaran yang masih memiliki jadwal mengajar. Silakan hapus jadwal terlebih dahulu.');
            }

            $success = $this->mapelModel->delete($id);

            if (!$success) {
                $this->db->transRollback();
                return $this->error('Gagal menghapus mata pelajaran');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->error('Gagal menghapus mata pelajaran');
            }

            return $this->success([
                'message' => 'Mata pelajaran berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in MataPelajaranService::deleteMapel: ' . $e->getMessage());
            return $this->error('Gagal menghapus mata pelajaran: ' . $e->getMessage());
        }
    }

    /**
     * Get mata pelajaran by kategori
     * 
     * @param string $kategori
     * @return array
     */
    public function getMapelByKategori(string $kategori): array
    {
        try {
            $mapel = $this->mapelModel->getByKategori(strtolower($kategori));

            return $this->success($mapel);
        } catch (\Exception $e) {
            log_message('error', 'Error in MataPelajaranService::getMapelByKategori: ' . $e->getMessage());
            return $this->error('Gagal mengambil data mata pelajaran: ' . $e->getMessage());
        }
    }

    /**
     * Get mata pelajaran statistics
     * 
     * @return array
     */
    public function getMapelStatistics(): array
    {
        try {
            $statistics = [
                'total_mapel' => $this->mapelModel->countAll(),
                'mapel_per_kategori' => []
            ];

            // Get count per kategori
            $kategoriStats = $this->mapelModel->countByKategori();

            foreach ($kategoriStats as $stat) {
                $statistics['mapel_per_kategori'][$stat['kategori']] = $stat['total'];
            }

            // Get mapel with most guru
            $mapelWithMostGuru = $this->db->table('mata_pelajaran')
                ->select('mata_pelajaran.*, COUNT(guru.id) as jumlah_guru')
                ->join('guru', 'guru.mata_pelajaran_id = mata_pelajaran.id', 'left')
                ->groupBy('mata_pelajaran.id')
                ->orderBy('jumlah_guru', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();

            $statistics['mapel_terpopuler'] = $mapelWithMostGuru;

            return $this->success($statistics);
        } catch (\Exception $e) {
            log_message('error', 'Error in MataPelajaranService::getMapelStatistics: ' . $e->getMessage());
            return $this->error('Gagal mengambil statistik mata pelajaran: ' . $e->getMessage());
        }
    }

    /**
     * Get mata pelajaran list for dropdown
     * 
     * @param string|null $kategori Filter by kategori
     * @return array
     */
    public function getMapelForDropdown(?string $kategori = null): array
    {
        try {
            $builder = $this->mapelModel->orderBy('nama_mapel', 'ASC');

            if ($kategori) {
                $builder->where('kategori', strtolower($kategori));
            }

            $mapel = $builder->findAll();
            $dropdown = [];

            foreach ($mapel as $m) {
                $dropdown[$m['id']] = $m['kode_mapel'] . ' - ' . $m['nama_mapel'];
            }

            return $this->success($dropdown);
        } catch (\Exception $e) {
            log_message('error', 'Error in MataPelajaranService::getMapelForDropdown: ' . $e->getMessage());
            return $this->error('Gagal mengambil data mata pelajaran: ' . $e->getMessage());
        }
    }

    /**
     * Bulk import mata pelajaran from array
     * 
     * @param array $mapelData Array of mapel data
     * @return array
     */
    public function bulkImportMapel(array $mapelData): array
    {
        try {
            $this->db->transStart();

            $successCount = 0;
            $failedCount = 0;
            $errors = [];

            foreach ($mapelData as $index => $data) {
                // Validate required fields
                if (empty($data['kode_mapel']) || empty($data['nama_mapel']) || empty($data['kategori'])) {
                    $errors[] = "Baris " . ($index + 1) . ": Data tidak lengkap";
                    $failedCount++;
                    continue;
                }

                // Check if kode already exists
                $existing = $this->mapelModel->where('kode_mapel', $data['kode_mapel'])->first();
                if ($existing) {
                    $errors[] = "Baris " . ($index + 1) . ": Kode {$data['kode_mapel']} sudah digunakan";
                    $failedCount++;
                    continue;
                }

                // Normalize kategori
                $data['kategori'] = strtolower($data['kategori']);
                $data['created_at'] = date('Y-m-d H:i:s');

                $result = $this->mapelModel->insert($data);

                if ($result) {
                    $successCount++;
                } else {
                    $errors[] = "Baris " . ($index + 1) . ": " . implode(', ', $this->mapelModel->errors());
                    $failedCount++;
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->error('Gagal melakukan import mata pelajaran');
            }

            return $this->success([
                'message' => "Import selesai. Berhasil: {$successCount}, Gagal: {$failedCount}",
                'success_count' => $successCount,
                'failed_count' => $failedCount,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in MataPelajaranService::bulkImportMapel: ' . $e->getMessage());
            return $this->error('Gagal melakukan import mata pelajaran: ' . $e->getMessage());
        }
    }
}
