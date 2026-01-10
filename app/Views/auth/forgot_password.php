<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Lupa Password'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>

<body class="h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-d w-full space-y-8 bg-white p-8 rounded-lg shadow-lg">
        <div>
            <div class="flex justify-center">
                <i class="fas fa-key text-4xl text-indigo-600"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900"><?= $title ?? 'Test'; ?></h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Masukkan email Anda untuk mereset password
            </p>
        </div>
        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 px-3 rounded relative" role="alert">
                <span class="block sm:inline"><?= session()->getFlashdata('error'); ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('forgot-password/process'); ?>" method="POST">
            <?= csrf_field(); ?>

            <div class="rounded-md shadow-sm">
                <div>
                    <label for="email" class="sr-only">Email</label>
                    <input type="email" id="email" name="email" required placeholder="Email terdaftar" value="<?= old('email'); ?>"
                        class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                </div>
            </div>
            <div>
                <button type="submit"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-paper-plane"></i>
                    </span>
                    Kirim Link Reset
                </button>
            </div>
            <div class="text-center">
                <a href="<?= base_url('login'); ?>"
                    class="font-medium text-indigo-600 hover:text-indigo-500">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Login
                </a>
            </div>
        </form>
    </div>
</body>

</html>