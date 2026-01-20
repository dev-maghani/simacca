<?= $this->extend('templates/auth_layout') ?>

<?= $this->section('title') ?>
Akses Ditolak
<?= $this->endSection() ?>

<?= $this->section('header') ?>
<div class="flex justify-center">
    <i class="fas fa-ban text-5xl text-red-600"></i>
</div>
<h2 class="mt-6 text-3xl font-extrabold text-gray-900">
    Akses Ditolak
</h2>
<p class="mt-2 text-sm text-gray-600">
    Anda tidak memiliki izin untuk mengakses halaman ini
</p>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="text-center py-8">
    <p class="text-gray-600 mb-6">
        Halaman yang Anda coba akses memerlukan hak akses khusus. 
        Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.
    </p>
    
    <div class="space-y-3">
        <a href="<?= base_url('/'); ?>" 
           class="block w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition-colors">
            <i class="fas fa-home mr-2"></i>
            Kembali ke Beranda
        </a>
        
        <a href="<?= base_url('logout'); ?>" 
           class="block w-full py-3 px-4 border-2 border-gray-300 hover:bg-gray-100 text-gray-700 rounded-lg font-semibold transition-colors">
            <i class="fas fa-sign-out-alt mr-2"></i>
            Logout
        </a>
    </div>
</div>
<?= $this->endSection() ?>