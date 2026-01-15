<?= $this->extend('emails/email_layout') ?>

<?= $this->section('content') ?>

<h2 style="color: #333; margin-top: 0;">Selamat Datang di SIMACCA! 🎉</h2>

<p>Halo, <strong><?= esc($fullName ?? $username) ?></strong>!</p>

<p>Terima kasih telah melengkapi profil Anda! 🎊</p>

<p>Profil Anda sekarang sudah lengkap. Berikut adalah informasi akun Anda:</p>

<div class="info-box">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px 0; font-weight: 600; width: 150px;">Username</td>
            <td style="padding: 8px 0;">: <strong><?= esc($username) ?></strong></td>
        </tr>
        <?php if (!empty($email ?? '')): ?>
        <tr>
            <td style="padding: 8px 0; font-weight: 600;">Email</td>
            <td style="padding: 8px 0;">: <strong><?= esc($email) ?></strong></td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($temporaryPassword)): ?>
        <tr>
            <td style="padding: 8px 0; font-weight: 600;">Password Baru</td>
            <td style="padding: 8px 0;">: <code style="background: #f8f9fa; padding: 4px 10px; border-radius: 4px; font-size: 14px;"><?= esc($temporaryPassword) ?></code></td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($role)): ?>
        <tr>
            <td style="padding: 8px 0; font-weight: 600;">Role</td>
            <td style="padding: 8px 0;">: <?= ucfirst(str_replace('_', ' ', esc($role))) ?></td>
        </tr>
        <?php endif; ?>
    </table>
</div>

<div style="background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; border-radius: 4px;">
    <strong style="color: #155724;">✅ Profil Lengkap!</strong>
    <p style="margin: 10px 0 5px; color: #155724;">Akun Anda telah siap digunakan dengan:</p>
    <ul style="margin: 5px 0 0; padding-left: 20px; color: #155724;">
        <li>Password telah diperbarui</li>
        <li>Email telah ditambahkan</li>
        <li>Foto profil telah diupload</li>
    </ul>
</div>

<p>Sekarang Anda dapat menggunakan semua fitur SIMACCA dengan lancar! 🚀</p>

<div style="text-align: center; margin: 25px 0;">
    <a href="<?= esc($loginUrl) ?>" class="button">Kembali ke Dashboard</a>
</div>

<div class="alert-box">
    <strong>🔐 Keamanan Akun:</strong>
    <ul style="margin: 10px 0 0; padding-left: 20px;">
        <li>Simpan password Anda dengan aman</li>
        <li>Jangan bagikan password Anda kepada siapapun</li>
        <li>Perbarui email jika ada perubahan</li>
        <li>Gunakan foto profil yang profesional</li>
    </ul>
</div>

<hr>

<p style="font-size: 13px; color: #6c757d;">
    Jika Anda mengalami kesulitan atau memiliki pertanyaan, silakan hubungi administrator sistem.
</p>

<?= $this->endSection() ?>
