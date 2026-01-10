<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $title ?? 'Jurnal KBM' ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= base_url('guru/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Jurnal KBM</li>
    </ol>

    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-1"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-1"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Filter Jurnal
        </div>
        <div class="card-body">
            <form method="GET" action="<?= base_url('guru/jurnal') ?>">
                <div class="row">
                    <div class="col-md-5">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" value="<?= $startDate ?? '' ?>">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" name="end_date" class="form-control" value="<?= $endDate ?? '' ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Jurnal List -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-book me-1"></i>
            Daftar Jurnal KBM
        </div>
        <div class="card-body">
            <?php if (empty($jurnal)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-1"></i>
                Belum ada jurnal KBM. Silakan buat jurnal setelah melakukan absensi.
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th>Tujuan Pembelajaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($jurnal as $j): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('d/m/Y', strtotime($j['tanggal'])) ?></td>
                            <td><?= $j['nama_mapel'] ?></td>
                            <td><?= $j['nama_kelas'] ?></td>
                            <td><?= substr($j['tujuan_pembelajaran'], 0, 100) ?>...</td>
                            <td>
                                <a href="<?= base_url('guru/jurnal/edit/' . $j['id']) ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
