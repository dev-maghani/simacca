<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in-up {
        animation: fadeInUp 0.5s ease-out;
    }

    .image-container {
        position: relative;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .image-container img {
        width: 100%;
        height: auto;
        display: block;
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .image-container:hover img {
        transform: scale(1.02);
    }

    .zoom-icon {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(255, 255, 255, 0.9);
        color: #4F46E5;
        padding: 0.75rem;
        border-radius: 50%;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .image-container:hover .zoom-icon {
        background: rgba(255, 255, 255, 1);
        transform: scale(1.1);
    }

    .lightbox {
        display: none;
        position: fixed;
        z-index: 9999;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }

    .lightbox.active {
        display: flex;
    }

    .lightbox img {
        max-width: 90%;
        max-height: 90%;
        border-radius: 0.5rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    }

    .close-lightbox {
        position: absolute;
        top: 2rem;
        right: 2rem;
        color: white;
        font-size: 2rem;
        cursor: pointer;
        background: rgba(255, 255, 255, 0.1);
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .close-lightbox:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: rotate(90deg);
    }

    .print-button {
        transition: all 0.3s ease;
    }

    .print-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
    }
</style>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-4 md:p-6 lg:p-8">
    <div class="max-w-5xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8 animate-fade-in-up">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center flex-1">
                    <a href="<?= base_url('guru/jurnal') ?>" 
                       class="mr-4 p-2 rounded-lg bg-white text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 transition-all duration-200 shadow-sm">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-800">
                            <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                <i class="fas fa-book-open mr-3"></i>
                                Detail Jurnal KBM
                            </span>
                        </h1>
                        <p class="text-gray-600 mt-2">Dokumentasi Kegiatan Pembelajaran</p>
                    </div>
                </div>
                
                <!-- Print Button -->
                <a href="<?= base_url('guru/jurnal/print/' . $jurnal['id']) ?>" 
                   target="_blank"
                   class="print-button flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl shadow-lg hover:from-indigo-700 hover:to-purple-700 font-medium">
                    <i class="fas fa-print mr-2"></i>
                    Print
                </a>
            </div>
        </div>

        <!-- Info Card -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-2xl p-6 mb-8 shadow-lg animate-fade-in-up" style="animation-delay: 0.1s;">
            <div class="flex items-center mb-4">
                <div class="bg-blue-600 text-white p-3 rounded-xl mr-4">
                    <i class="fas fa-info-circle text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800">Informasi Pembelajaran</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="flex items-center bg-white/60 backdrop-blur-sm rounded-xl p-4">
                    <div class="bg-blue-100 p-3 rounded-lg mr-4">
                        <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600 block">Tanggal</span>
                        <span class="text-lg font-bold text-gray-800"><?= date('d/m/Y', strtotime($jurnal['tanggal'])) ?></span>
                    </div>
                </div>

                <div class="flex items-center bg-white/60 backdrop-blur-sm rounded-xl p-4">
                    <div class="bg-purple-100 p-3 rounded-lg mr-4">
                        <i class="fas fa-book text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600 block">Mata Pelajaran</span>
                        <span class="text-lg font-bold text-gray-800"><?= esc($jurnal['nama_mapel']) ?></span>
                    </div>
                </div>

                <div class="flex items-center bg-white/60 backdrop-blur-sm rounded-xl p-4">
                    <div class="bg-green-100 p-3 rounded-lg mr-4">
                        <i class="fas fa-users text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600 block">Kelas</span>
                        <span class="text-lg font-bold text-gray-800"><?= esc($jurnal['nama_kelas']) ?></span>
                    </div>
                </div>

                <div class="flex items-center bg-white/60 backdrop-blur-sm rounded-xl p-4">
                    <div class="bg-orange-100 p-3 rounded-lg mr-4">
                        <i class="fas fa-chalkboard-teacher text-orange-600 text-xl"></i>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600 block">Guru</span>
                        <span class="text-lg font-bold text-gray-800"><?= esc($jurnal['nama_guru']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Materi Pembelajaran Card -->
            <div class="animate-fade-in-up" style="animation-delay: 0.2s;">
                <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 h-full">
                    <div class="flex items-center mb-6">
                        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 text-white p-3 rounded-xl mr-4">
                            <i class="fas fa-book-reader text-2xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">Materi Pembelajaran</h2>
                    </div>
                    
                    <div class="prose max-w-none">
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 border-2 border-gray-200">
                            <div class="text-gray-800 leading-relaxed whitespace-pre-wrap text-base">
                                <?= esc($jurnal['kegiatan_pembelajaran']) ?>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($jurnal['catatan_khusus']) && $jurnal['catatan_khusus'] !== '-'): ?>
                    <div class="mt-6">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-sticky-note text-amber-600 mr-2"></i>
                            <h3 class="text-lg font-semibold text-gray-700">Catatan Khusus</h3>
                        </div>
                        <div class="bg-amber-50 border-l-4 border-amber-500 rounded-lg p-4">
                            <p class="text-gray-700 text-sm leading-relaxed">
                                <?= esc($jurnal['catatan_khusus']) ?>
                            </p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Foto Dokumentasi Card -->
            <div class="animate-fade-in-up" style="animation-delay: 0.3s;">
                <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 h-full">
                    <div class="flex items-center mb-6">
                        <div class="bg-gradient-to-br from-pink-500 to-rose-600 text-white p-3 rounded-xl mr-4">
                            <i class="fas fa-camera text-2xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">Foto Dokumentasi</h2>
                    </div>

                    <?php if (!empty($jurnal['foto_dokumentasi'])): ?>
                        <div class="image-container" onclick="openLightbox()">
                            <img src="<?= base_url('files/jurnal/' . esc($jurnal['foto_dokumentasi'])) ?>" 
                                 alt="Foto Dokumentasi Pembelajaran"
                                 id="jurnalImage">
                            <div class="zoom-icon">
                                <i class="fas fa-search-plus text-xl"></i>
                            </div>
                        </div>
                        
                        <div class="mt-4 flex items-center justify-center text-sm text-gray-600">
                            <i class="fas fa-info-circle mr-2 text-indigo-600"></i>
                            Klik foto untuk memperbesar
                        </div>
                    <?php else: ?>
                        <div class="flex flex-col items-center justify-center py-16 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-dashed border-gray-300">
                            <div class="bg-gray-200 p-6 rounded-full mb-4">
                                <i class="fas fa-image text-gray-400 text-5xl"></i>
                            </div>
                            <p class="text-gray-500 font-medium text-lg">Tidak ada foto dokumentasi</p>
                            <p class="text-gray-400 text-sm mt-2">Dokumentasi visual tidak tersedia untuk jurnal ini</p>
                        </div>
                    <?php endif; ?>

                    <!-- Metadata -->
                    <div class="mt-6 pt-6 border-t-2 border-gray-100">
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <div class="flex items-center">
                                <i class="fas fa-clock text-indigo-600 mr-2"></i>
                                <span>Dibuat: <?= date('d/m/Y H:i', strtotime($jurnal['created_at'])) ?></span>
                            </div>
                            <?php if (!empty($jurnal['foto_dokumentasi'])): ?>
                            <a href="<?= base_url('files/jurnal/' . esc($jurnal['foto_dokumentasi'])) ?>" 
                               download
                               class="flex items-center text-indigo-600 hover:text-indigo-700 font-medium">
                                <i class="fas fa-download mr-2"></i>
                                Download Foto
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex justify-between items-center animate-fade-in-up" style="animation-delay: 0.4s;">
            <a href="<?= base_url('guru/jurnal') ?>" 
               class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all duration-200 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Daftar
            </a>
            
            <div class="flex gap-3">
                <a href="<?= base_url('guru/jurnal/edit/' . $jurnal['id']) ?>" 
                   class="px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl hover:from-amber-600 hover:to-orange-700 transition-all duration-200 shadow-lg font-medium">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Jurnal
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Lightbox for Image -->
<div id="lightbox" class="lightbox" onclick="closeLightbox()">
    <div class="close-lightbox">
        <i class="fas fa-times"></i>
    </div>
    <img src="" alt="Foto Dokumentasi" id="lightboxImage">
</div>

<script>
    function openLightbox() {
        const lightbox = document.getElementById('lightbox');
        const lightboxImage = document.getElementById('lightboxImage');
        const jurnalImage = document.getElementById('jurnalImage');
        
        lightboxImage.src = jurnalImage.src;
        lightbox.classList.add('active');
        
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        const lightbox = document.getElementById('lightbox');
        lightbox.classList.remove('active');
        
        // Restore body scroll
        document.body.style.overflow = 'auto';
    }

    // Close on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLightbox();
        }
    });

    // Prevent lightbox image click from closing
    document.getElementById('lightboxImage').addEventListener('click', function(e) {
        e.stopPropagation();
    });
</script>

<?= $this->endSection() ?>
