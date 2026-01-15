<?php

/**
 * Controller Helper Functions
 * 
 * Reusable utility functions to reduce code duplication in controllers.
 * 
 * @package App\Helpers
 * @author SIMACCA Team
 * @version 1.0.0
 */

if (!function_exists('get_current_user_id')) {
    /**
     * Get current logged-in user ID
     * 
     * @return int|null User ID or null if not logged in
     */
    function get_current_user_id(): ?int
    {
        $userId = session()->get('user_id') ?? session()->get('userId');
        return $userId ? (int)$userId : null;
    }
}

if (!function_exists('get_current_user_role')) {
    /**
     * Get current logged-in user role
     * 
     * @return string|null User role or null if not logged in
     */
    function get_current_user_role(): ?string
    {
        return session()->get('role');
    }
}

if (!function_exists('is_logged_in')) {
    /**
     * Check if user is currently logged in
     * 
     * @return bool
     */
    function is_logged_in(): bool
    {
        return (bool)session()->get('isLoggedIn');
    }
}

if (!function_exists('require_auth')) {
    /**
     * Require user to be authenticated, redirect to login if not
     * 
     * @param string $message Error message to display
     * @return \CodeIgniter\HTTP\RedirectResponse|null Redirect response if not authenticated
     */
    function require_auth(string $message = 'Silakan login terlebih dahulu.')
    {
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', $message);
        }
        return null;
    }
}

if (!function_exists('require_role')) {
    /**
     * Require user to have specific role
     * 
     * @param string|array $allowedRoles Single role or array of allowed roles
     * @param string $message Error message to display
     * @return \CodeIgniter\HTTP\RedirectResponse|null Redirect response if role not matched
     */
    function require_role($allowedRoles, string $message = 'Anda tidak memiliki akses ke halaman ini.')
    {
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $allowedRoles = is_array($allowedRoles) ? $allowedRoles : [$allowedRoles];
        $userRole = get_current_user_role();

        if (!in_array($userRole, $allowedRoles)) {
            return redirect()->to('/access-denied')->with('error', $message);
        }

        return null;
    }
}

if (!function_exists('flash_success')) {
    /**
     * Set success flash message
     * 
     * @param string $message Success message
     * @return void
     */
    function flash_success(string $message): void
    {
        session()->setFlashdata('success', $message);
    }
}

if (!function_exists('flash_error')) {
    /**
     * Set error flash message
     * 
     * @param string $message Error message
     * @return void
     */
    function flash_error(string $message): void
    {
        session()->setFlashdata('error', $message);
    }
}

if (!function_exists('flash_warning')) {
    /**
     * Set warning flash message
     * 
     * @param string $message Warning message
     * @return void
     */
    function flash_warning(string $message): void
    {
        session()->setFlashdata('warning', $message);
    }
}

if (!function_exists('flash_info')) {
    /**
     * Set info flash message
     * 
     * @param string $message Info message
     * @return void
     */
    function flash_info(string $message): void
    {
        session()->setFlashdata('info', $message);
    }
}

if (!function_exists('redirect_with_success')) {
    /**
     * Redirect with success message
     * 
     * @param string $url Target URL
     * @param string $message Success message
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    function redirect_with_success(string $url, string $message): \CodeIgniter\HTTP\RedirectResponse
    {
        return redirect()->to($url)->with('success', $message);
    }
}

if (!function_exists('redirect_with_error')) {
    /**
     * Redirect with error message
     * 
     * @param string $url Target URL
     * @param string $message Error message
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    function redirect_with_error(string $url, string $message): \CodeIgniter\HTTP\RedirectResponse
    {
        return redirect()->to($url)->with('error', $message);
    }
}

if (!function_exists('redirect_back_with_error')) {
    /**
     * Redirect back with error message and input
     * 
     * @param string $message Error message
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    function redirect_back_with_error(string $message): \CodeIgniter\HTTP\RedirectResponse
    {
        return redirect()->back()->withInput()->with('error', $message);
    }
}

if (!function_exists('get_guru_by_user_id')) {
    /**
     * Get guru data by user ID
     * 
     * @param int|null $userId User ID (defaults to current user)
     * @return array|null Guru data or null if not found
     */
    function get_guru_by_user_id(?int $userId = null): ?array
    {
        $userId = $userId ?? get_current_user_id();
        if (!$userId) {
            return null;
        }

        $guruModel = new \App\Models\GuruModel();
        return $guruModel->getByUserId($userId);
    }
}

if (!function_exists('get_siswa_by_user_id')) {
    /**
     * Get siswa data by user ID
     * 
     * @param int|null $userId User ID (defaults to current user)
     * @return array|null Siswa data or null if not found
     */
    function get_siswa_by_user_id(?int $userId = null): ?array
    {
        $userId = $userId ?? get_current_user_id();
        if (!$userId) {
            return null;
        }

        $siswaModel = new \App\Models\SiswaModel();
        return $siswaModel->getByUserId($userId);
    }
}

if (!function_exists('get_current_guru')) {
    /**
     * Get current logged-in guru data
     * Redirects to login if not found
     * 
     * @param string $errorMessage Error message if guru not found
     * @return array|RedirectResponse Guru data or redirect response
     */
    function get_current_guru(string $errorMessage = 'Data guru tidak ditemukan.')
    {
        $userId = get_current_user_id();
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $guru = get_guru_by_user_id($userId);
        if (!$guru) {
            return redirect()->to('/access-denied')->with('error', $errorMessage);
        }

        return $guru;
    }
}

if (!function_exists('get_current_siswa')) {
    /**
     * Get current logged-in siswa data
     * Redirects to login if not found
     * 
     * @param string $errorMessage Error message if siswa not found
     * @return array|\CodeIgniter\HTTP\RedirectResponse Siswa data or redirect response
     */
    function get_current_siswa(string $errorMessage = 'Data siswa tidak ditemukan.')
    {
        $userId = get_current_user_id();
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $siswa = get_siswa_by_user_id($userId);
        if (!$siswa) {
            return redirect()->to('/access-denied')->with('error', $errorMessage);
        }

        return $siswa;
    }
}

if (!function_exists('convert_day_to_indonesian')) {
    /**
     * Convert English day name to Indonesian
     * 
     * @param string $day English day name (Monday, Tuesday, etc.)
     * @return string Indonesian day name
     */
    function convert_day_to_indonesian(string $day): string
    {
        $days = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu'
        ];
        
        return $days[$day] ?? $day;
    }
}

if (!function_exists('get_current_day_indonesian')) {
    /**
     * Get current day in Indonesian
     * 
     * @return string Current day name in Indonesian
     */
    function get_current_day_indonesian(): string
    {
        return convert_day_to_indonesian(date('l'));
    }
}

if (!function_exists('get_month_date_range')) {
    /**
     * Get start and end date for current month
     * 
     * @param string|null $date Date to get range for (Y-m-d format), defaults to today
     * @return array ['start' => 'Y-m-d', 'end' => 'Y-m-d']
     */
    function get_month_date_range(?string $date = null): array
    {
        $date = $date ?? date('Y-m-d');
        return [
            'start' => date('Y-m-01', strtotime($date)),
            'end' => date('Y-m-t', strtotime($date))
        ];
    }
}

if (!function_exists('calculate_percentage')) {
    /**
     * Calculate percentage with proper rounding
     * 
     * @param int|float $part Part value
     * @param int|float $total Total value
     * @param int $decimals Number of decimal places
     * @return float Percentage (0-100)
     */
    function calculate_percentage($part, $total, int $decimals = 1): float
    {
        if ($total == 0) {
            return 0;
        }
        return round(($part / $total) * 100, $decimals);
    }
}

if (!function_exists('get_attendance_stats_query')) {
    /**
     * Get common attendance statistics query (SELECT statement)
     * Reusable across different controllers
     * 
     * @return string SQL SELECT statement for attendance stats
     */
    function get_attendance_stats_query(): string
    {
        return '
            COUNT(*) as total,
            SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir,
            SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as sakit,
            SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as izin,
            SUM(CASE WHEN status = "alpa" THEN 1 ELSE 0 END) as alpa
        ';
    }
}

if (!function_exists('format_attendance_stats')) {
    /**
     * Format attendance statistics with percentages
     * 
     * @param array $stats Raw stats from database
     * @return array Formatted stats with percentages
     */
    function format_attendance_stats(array $stats): array
    {
        $total = $stats['total'] ?? 0;
        
        return [
            'total' => (int)$total,
            'hadir' => (int)($stats['hadir'] ?? 0),
            'sakit' => (int)($stats['sakit'] ?? 0),
            'izin' => (int)($stats['izin'] ?? 0),
            'alpa' => (int)($stats['alpa'] ?? 0),
            'persentase_hadir' => calculate_percentage($stats['hadir'] ?? 0, $total),
            'persentase_sakit' => calculate_percentage($stats['sakit'] ?? 0, $total),
            'persentase_izin' => calculate_percentage($stats['izin'] ?? 0, $total),
            'persentase_alpa' => calculate_percentage($stats['alpa'] ?? 0, $total),
        ];
    }
}

if (!function_exists('validate_or_redirect')) {
    /**
     * Validate data, redirect back with errors if validation fails
     * 
     * @param array $rules Validation rules
     * @param array|null $data Data to validate (defaults to POST data)
     * @return \CodeIgniter\HTTP\RedirectResponse|null Redirect if validation fails, null if passes
     */
    function validate_or_redirect(array $rules, ?array $data = null)
    {
        $validation = \Config\Services::validation();
        $data = $data ?? service('request')->getPost();
        
        if (!$validation->setRules($rules)->run($data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }
        
        return null;
    }
}

if (!function_exists('log_activity')) {
    /**
     * Log user activity (placeholder for future activity logging system)
     * 
     * @param string $action Action performed
     * @param string $description Description of action
     * @param array $data Additional data
     * @return void
     */
    function log_activity(string $action, string $description, array $data = []): void
    {
        // TODO: Implement activity logging system
        log_message('info', sprintf(
            '[USER_ACTIVITY] User: %s, Action: %s, Description: %s, Data: %s',
            get_current_user_id() ?? 'guest',
            $action,
            $description,
            json_encode($data)
        ));
    }
}

if (!function_exists('get_status_badge_class')) {
    /**
     * Get CSS class for status badge
     * 
     * @param string $status Status value
     * @return string Tailwind CSS classes
     */
    function get_status_badge_class(string $status): string
    {
        $classes = [
            'hadir' => 'bg-green-100 text-green-800',
            'sakit' => 'bg-yellow-100 text-yellow-800',
            'izin' => 'bg-blue-100 text-blue-800',
            'alpa' => 'bg-red-100 text-red-800',
            'pending' => 'bg-gray-100 text-gray-800',
            'disetujui' => 'bg-green-100 text-green-800',
            'ditolak' => 'bg-red-100 text-red-800',
            'aktif' => 'bg-green-100 text-green-800',
            'tidak_aktif' => 'bg-red-100 text-red-800',
        ];
        
        return $classes[strtolower($status)] ?? 'bg-gray-100 text-gray-800';
    }
}

if (!function_exists('format_date_indonesia')) {
    /**
     * Format date to Indonesian format
     * 
     * @param string $date Date string (Y-m-d or timestamp)
     * @param bool $showDay Show day name
     * @return string Formatted date
     */
    function format_date_indonesia(string $date, bool $showDay = false): string
    {
        $months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $timestamp = strtotime($date);
        $day = date('j', $timestamp);
        $month = $months[(int)date('n', $timestamp) - 1];
        $year = date('Y', $timestamp);
        
        $formatted = "$day $month $year";
        
        if ($showDay) {
            $dayName = convert_day_to_indonesian(date('l', $timestamp));
            $formatted = "$dayName, $formatted";
        }
        
        return $formatted;
    }
}
