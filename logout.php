<?php
/**
 * FR-03: Logout
 * Menghapus seluruh session dan mengarahkan kembali ke halaman login
 */

require_once __DIR__ . '/config/auth_check.php';

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Mulai sesi baru hanya untuk pesan flash
session_start();
$_SESSION['flash_success'] = 'Anda telah berhasil keluar dari akun.';

header("Location: login.php");
exit;
