<?php
/**
 * Helper Otentikasi, Sesi, dan Keamanan Bersuara
 * NFR-02: Kata sandi disimpan hash
 * NFR-04: Data diamankan dengan escaping
 * NFR-05: Halaman terlindungi valid session
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * NFR-04: Helper escaping output mencegah XSS
 */
function e(?string $str): string {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Format mata uang Rupiah
 */
function format_rupiah($angka): string {
    return 'Rp ' . number_format((int)$angka, 0, ',', '.');
}

/**
 * Format tanggal Indonesia
 */
function format_tanggal($datetime_str): string {
    $bulan_indo = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $timestamp = strtotime($datetime_str);
    if (!$timestamp) return $datetime_str;
    
    $hari = date('d', $timestamp);
    $bulan = $bulan_indo[(int)date('m', $timestamp)];
    $tahun = date('Y', $timestamp);
    $jam = date('H:i', $timestamp);
    
    return "{$hari} {$bulan} {$tahun}, {$jam} WIB";
}

/**
 * Cek apakah pengguna sudah login
 */
function is_logged_in(): bool {
    return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
}

/**
 * Cek apakah pengguna adalah admin
 */
function is_admin(): bool {
    return is_logged_in() && ($_SESSION['user']['role'] ?? '') === 'admin';
}

/**
 * Ambil data user yang sedang login
 */
function auth_user(): ?array {
    return $_SESSION['user'] ?? null;
}

/**
 * NFR-05: Proteksi halaman pembeli (wajib login)
 */
function require_login(string $redirect_target = 'login.php'): void {
    if (!is_logged_in()) {
        $_SESSION['flash_error'] = 'Silakan login terlebih dahulu untuk mengakses halaman ini.';
        header("Location: {$redirect_target}");
        exit;
    }
}

/**
 * NFR-05: Proteksi halaman admin (wajib login & role admin)
 */
function require_admin(string $redirect_target = '../login.php'): void {
    if (!is_logged_in()) {
        $_SESSION['flash_error'] = 'Silakan login sebagai admin terlebih dahulu.';
        header("Location: {$redirect_target}");
        exit;
    }
    if (!is_admin()) {
        $_SESSION['flash_error'] = 'Akses ditolak. Anda tidak memiliki hak akses admin.';
        header("Location: ../index.php");
        exit;
    }
}

/**
 * Flash message helper
 */
function set_flash(string $type, string $message): void {
    $_SESSION["flash_{$type}"] = $message;
}

function get_flash(string $type): ?string {
    if (isset($_SESSION["flash_{$type}"])) {
        $msg = $_SESSION["flash_{$type}"];
        unset($_SESSION["flash_{$type}"]);
        return $msg;
    }
    return null;
}

/**
 * Helper CSRF Protection
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(?string $token): bool {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}
