<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Login'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>

<body class="h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-lg">
        <div>
            <div class="flex justify-center">
                <i class="fas fa-graduation-cap text-4xl text-indigo-600"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900"><?= $title ?? 'Test'; ?></h2>
            <p class="mt-2 text-center text-sm text-gray-600">Silahkan login untuk melanjutkan</p>
        </div>

        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?= session()->getFlashdata('error') ?></span>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?= session()->getFlashdata('success') ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('login/process'); ?>" method="POST" class="mt-8 space-y-6">
            <?= csrf_field(); ?>
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="username" class="sr-only">Username</label>
                    <input name="username" type="text" id="username" required placeholder="Username" value="<?= old('username'); ?>"
                        class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                    <?php if (isset($validation) && $validation->hasError('username')) : ?>
                        <p class="mt-1 text-sm text-red-600"><?= $validation->getError('username') ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input type="password" id="password" name="password" autocomplete="current-password" placeholder="Password" required
                        class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                    <?php if (isset($validation) && $validation->hasError('password')) : ?>
                        <p class="mt-1 text-sm text-red-600"><?= $validation->getError('password'); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input type="checkbox" id="remember-me" name="remember-me"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="remember-me" class="ml-2 block text-sm text-gray-900">Ingat Saya</label>
                </div>
                <div class="text-sm">
                    <a href="<?= base_url('forgot-password'); ?>"
                        class="font-medium text-indigo-600 hover:text-indigo-500"> Lupa Password?</a>
                </div>
            </div>

            <div>
                <button type="submit"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-sign-in-alt"></i>
                    </span>
                    Login
                </button>
            </div>
            <!-- <div class="text-center text-sm text-gray-600">
                <p>Demo Account: </p>
                <p class="text-xs mt-1">
                    Admin: admin/admin123 | Guru: dirwan.jaya1/guru123 | Wali Kelas: gani828/wali123 |Siswa: siswa1/siswa123
                </p>
            </div> -->
        </form>
    </div>
</body>

</html>