<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
            padding: 20mm;
        }

        @page {
            size: A4;
            margin: 15mm;
        }

        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
            .page-break {
                page-break-after: always;
            }
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .header h2 {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .header p {
            font-size: 11pt;
        }

        .header .period-box {
            display: inline-block;
            border: 2px solid #000;
            padding: 8px 20px;
            margin-top: 10px;
        }

        /* Info Section */
        .info-section {
            margin-bottom: 20px;
        }

        .info-section table {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-section td {
            padding: 3px 0;
        }

        .info-section td:first-child {
            width: 150px;
            font-weight: bold;
        }

        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 8px 6px;
            text-align: left;
        }

        .data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .data-table .center {
            text-align: center;
        }

        .data-table .right {
            text-align: right;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10pt;
            font-weight: bold;
        }

        .status-hadir {
            background-color: #d4edda;
            color: #155724;
        }

        .status-sakit {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-izin {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-alpa {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Summary Section */
        .summary-section {
            margin-top: 20px;
            margin-bottom: 30px;
        }

        .summary-box {
            border: 2px solid #000;
            padding: 15px;
            background-color: #f9f9f9;
        }

        .summary-box h3 {
            font-size: 14pt;
            margin-bottom: 10px;
            text-align: center;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            text-align: center;
        }

        .summary-item {
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #fff;
        }

        .summary-item .label {
            font-size: 10pt;
            color: #666;
            margin-bottom: 5px;
        }

        .summary-item .value {
            font-size: 18pt;
            font-weight: bold;
            color: #000;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 50px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 20px;
        }

        .signature-box p {
            margin-bottom: 5px;
        }

        .signature-box .name {
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
        }

        /* Print Button */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background-color: #4f46e5;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14pt;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .print-button:hover {
            background-color: #4338ca;
        }

        /* Utility */
        .text-small {
            font-size: 10pt;
        }

        .mt-20 {
            margin-top: 20px;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .font-bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Print Button -->
    <button onclick="window.print()" class="print-button no-print">
        <i class="fas fa-print"></i> Cetak Laporan
    </button>

    <!-- Header -->
    <div class="header">
        <h1>Laporan Absensi Pembelajaran</h1>
        <h2>Sistem Informasi Akademik</h2>
        <p>Sekolah Menengah Kejuruan</p>
        <div class="period-box">
            <strong>Periode:</strong> <?= date('d/m/Y', strtotime($from)); ?> - <?= date('d/m/Y', strtotime($to)); ?>
            <?php if ($kelasId): ?>
                <br><strong>Kelas:</strong> <?= esc($kelasList[$kelasId] ?? '-'); ?>
            <?php else: ?>
                <br><strong>Semua Kelas</strong>
            <?php endif; ?>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-box">
            <h3>Ringkasan Kehadiran</h3>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="label">Hadir</div>
                    <div class="value" style="color: #10b981;"><?= $totalStats['hadir']; ?></div>
                </div>
                <div class="summary-item">
                    <div class="label">Sakit</div>
                    <div class="value" style="color: #f59e0b;"><?= $totalStats['sakit']; ?></div>
                </div>
                <div class="summary-item">
                    <div class="label">Izin</div>
                    <div class="value" style="color: #3b82f6;"><?= $totalStats['izin']; ?></div>
                </div>
                <div class="summary-item">
                    <div class="label">Alpa</div>
                    <div class="value" style="color: #ef4444;"><?= $totalStats['alpa']; ?></div>
                </div>
                <div class="summary-item">
                    <div class="label">Total</div>
                    <div class="value"><?= $totalStats['total']; ?></div>
                </div>
            </div>
            <p class="mt-20 text-small" style="text-align: center;">
                <strong>Persentase Kehadiran: <?= $totalStats['percentage']; ?>%</strong>
            </p>
        </div>
    </div>

    <!-- Info -->
    <div class="info-section">
        <p><strong>Total Sesi Pembelajaran:</strong> <?= count($laporanData); ?> Sesi</p>
        <p><strong>Tanggal Cetak:</strong> <?= date('d F Y, H:i'); ?> WIB</p>
    </div>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="width: 90px;">Tanggal</th>
                <th style="width: 80px;">Jam</th>
                <th>Kelas</th>
                <th>Mata Pelajaran</th>
                <th>Guru</th>
                <th style="width: 50px;" class="center">H</th>
                <th style="width: 50px;" class="center">S</th>
                <th style="width: 50px;" class="center">I</th>
                <th style="width: 50px;" class="center">A</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($laporanData)): ?>
                <?php $no = 1; ?>
                <?php foreach ($laporanData as $row): ?>
                    <?php
                    $total_siswa = $row['jumlah_hadir'] + $row['jumlah_sakit'] + $row['jumlah_izin'] + $row['jumlah_alpa'];
                    ?>
                    <tr>
                        <td class="center"><?= $no++; ?></td>
                        <td class="center"><?= date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                        <td class="center"><?= substr($row['jam_mulai'], 0, 5); ?> - <?= substr($row['jam_selesai'], 0, 5); ?></td>
                        <td><?= esc($row['nama_kelas']); ?></td>
                        <td><?= esc($row['nama_mapel']); ?></td>
                        <td><?= esc($row['nama_guru']); ?><?php if ($row['nama_guru_pengganti']): ?><br><span class="text-small" style="color: #666;">(Pengganti: <?= esc($row['nama_guru_pengganti']); ?>)</span><?php endif; ?></td>
                        <td class="center"><?= $row['jumlah_hadir']; ?></td>
                        <td class="center"><?= $row['jumlah_sakit']; ?></td>
                        <td class="center"><?= $row['jumlah_izin']; ?></td>
                        <td class="center"><?= $row['jumlah_alpa']; ?></td>
                    </tr>
                <?php endforeach; ?>
                
                <!-- Total Row -->
                <tr style="background-color: #e5e7eb; font-weight: bold;">
                    <td colspan="6" class="right" style="padding-right: 10px;">TOTAL</td>
                    <td class="center"><?= $totalStats['hadir']; ?></td>
                    <td class="center"><?= $totalStats['sakit']; ?></td>
                    <td class="center"><?= $totalStats['izin']; ?></td>
                    <td class="center"><?= $totalStats['alpa']; ?></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="10" class="center" style="padding: 20px; color: #999;">
                        Tidak ada data absensi dalam periode ini
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Keterangan -->
    <div class="info-section">
        <p><strong>Keterangan:</strong></p>
        <table style="margin-left: 20px; margin-top: 5px;">
            <tr>
                <td style="width: 30px;">H</td>
                <td style="width: 10px;">:</td>
                <td>Hadir</td>
            </tr>
            <tr>
                <td>S</td>
                <td>:</td>
                <td>Sakit</td>
            </tr>
            <tr>
                <td>I</td>
                <td>:</td>
                <td>Izin</td>
            </tr>
            <tr>
                <td>A</td>
                <td>:</td>
                <td>Alpa (Tanpa Keterangan)</td>
            </tr>
        </table>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <p>Mengetahui,<br>Kepala Sekolah</p>
            <div class="name">
                (.....................................)
            </div>
            <p style="margin-top: 5px; font-size: 10pt;">NIP. ...................................</p>
        </div>
        <div class="signature-box">
            <p><?= date('d F Y'); ?><br>Administrator</p>
            <div class="name">
                (.....................................)
            </div>
            <p style="margin-top: 5px; font-size: 10pt;">NIP. ...................................</p>
        </div>
    </div>

    <script>
        // Optional: Auto print when loaded (uncomment if needed)
        // window.onload = function() {
        //     window.print();
        // }
    </script>
</body>
</html>
