<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Laporan Absensi Detail</h1>
    <p class="text-gray-600">Laporan absensi lengkap dengan detail kehadiran per sesi pembelajaran</p>
</div>

<!-- Filter -->
<div class="bg-white rounded-xl shadow p-6 mb-6">
    <form class="grid grid-cols-1 md:grid-cols-4 gap-4" method="get" action="<?= base_url('admin/laporan/absensi-detail'); ?>">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
            <input type="date" name="from" value="<?= esc($from); ?>" class="w-full border rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
            <input type="date" name="to" value="<?= esc($to); ?>" class="w-full border rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
            <select name="kelas_id" class="w-full border rounded-lg px-3 py-2">
                <option value="">Semua Kelas</option>
                <?php if (!empty($kelasList)): ?>
                    <?php foreach ($kelasList as $id => $nama): ?>
                        <option value="<?= $id; ?>" <?= ($kelasId == $id ? 'selected' : ''); ?>><?= esc($nama); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                <i class="fas fa-filter mr-2"></i>Terapkan
            </button>
            <button type="button" onclick="window.print()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-print mr-2"></i>Cetak
            </button>
        </div>
    </form>
</div>

<!-- Tabel Laporan -->
<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Detail Absensi Pembelajaran</h2>
            <span class="text-sm text-gray-500">Periode: <?= date('d/m/Y', strtotime($from)); ?> - <?= date('d/m/Y', strtotime($to)); ?></span>
        </div>
        <?php if (!empty($laporanData)): ?>
            <p class="text-sm text-gray-600 mt-2">Total: <?= count($laporanData); ?> sesi pembelajaran</p>
        <?php endif; ?>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guru Mapel</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wali Kelas</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Hadir</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sakit</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Izin</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Alpa</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan Khusus</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guru Pengganti</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($laporanData)): ?>
                    <?php $no = 1; ?>
                    <?php foreach ($laporanData as $row): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900"><?= $no++; ?></td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= date('d/m/Y', strtotime($row['tanggal'])); ?>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= esc($row['nama_kelas']); ?>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= date('H:i', strtotime($row['jam_mulai'])); ?> - <?= date('H:i', strtotime($row['jam_selesai'])); ?>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900">
                                <?= esc($row['nama_guru']); ?>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900">
                                <?= esc($row['nama_mapel']); ?>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900">
                                <?= $row['nama_wali_kelas'] ? esc($row['nama_wali_kelas']) : '<span class="text-gray-400">-</span>'; ?>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <?= (int)$row['jumlah_hadir']; ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <?= (int)$row['jumlah_sakit']; ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <?= (int)$row['jumlah_izin']; ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <?= (int)$row['jumlah_alpa']; ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900">
                                <?php if (!empty($row['catatan_khusus'])): ?>
                                    <div class="max-w-xs">
                                        <p class="text-gray-700 line-clamp-2" title="<?= esc($row['catatan_khusus']); ?>">
                                            <?= esc($row['catatan_khusus']); ?>
                                        </p>
                                    </div>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center text-sm">
                                <?php if (!empty($row['foto_dokumentasi'])): ?>
                                    <button onclick="showImageModal('<?= base_url('files/jurnal/' . $row['foto_dokumentasi']); ?>')" 
                                            class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-image text-lg"></i>
                                    </button>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900">
                                <?php if (!empty($row['nama_guru_pengganti'])): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-user-tie mr-1"></i>
                                        <?= esc($row['nama_guru_pengganti']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="14" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                            <p>Belum ada data absensi untuk periode ini.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal untuk menampilkan foto -->
<div id="imageModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" onclick="closeImageModal()">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Foto Dokumentasi</h3>
            <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="mt-3 text-center">
            <img id="modalImage" src="" alt="Foto Dokumentasi" class="max-w-full h-auto rounded-lg mx-auto">
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print {
            display: none !important;
        }
        body {
            font-size: 10pt;
        }
        table {
            font-size: 9pt;
        }
    }
</style>

<script>
function showImageModal(imageUrl) {
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}
</script>
<?= $this->endSection() ?>
