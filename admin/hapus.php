<?php
/**
 * FR-17: Hapus Konser
 * Penghapusan memerlukan konfirmasi (POST + CSRF)
 * Konser dengan pesanan lunas TIDAK DAPAT DIHAPUS (TC-30)
 * Admin hanya dapat menghapus konser tanpa pesanan lunas (TC-29)
 * NFR-03: Prepared statement PDO
 * NFR-05: Session admin protection
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/auth_check.php';

require_admin('../login.php');

// T3-a: Hanya terima method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash('error', 'Metode permintaan tidak valid.');
    header("Location: index.php");
    exit;
}

// T3-b: Verifikasi CSRF
if (!verify_csrf($_POST['csrf_token'] ?? null)) {
    set_flash('error', 'Sesi formulir tidak valid. Muat ulang halaman lalu coba lagi.');
    header("Location: index.php");
    exit;
}

// T3-c: Ambil id dari POST
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    set_flash('error', 'Konser tidak ditemukan.');
    header("Location: index.php");
    exit;
}

// Cek data konser
$stmt = $pdo->prepare("SELECT id, nama_konser FROM konsers WHERE id = ?");
$stmt->execute([$id]);
$konser = $stmt->fetch();

if (!$konser) {
    set_flash('error', 'Konser tidak ditemukan.');
    header("Location: index.php");
    exit;
}

try {
    // T3-e: Transaksi pengecekan dan penghapusan
    $pdo->beginTransaction();

    // Kunci dan cek ulang pesanan lunas di dalam transaksi
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE konser_id = ? AND status = 'lunas' FOR UPDATE");
    $stmt_check->execute([$id]);
    $pesanan_lunas = (int)$stmt_check->fetchColumn();

    if ($pesanan_lunas > 0) {
        $pdo->rollBack();
        // FR-17, TC-30: Pesan penolakan yang sama
        set_flash('error', "Penghapusan ditolak! Konser \"{$konser['nama_konser']}\" tidak dapat dihapus karena sudah memiliki {$pesanan_lunas} pesanan lunas.");
        header("Location: index.php");
        exit;
    }

    // Hapus pesanan non-lunas jika ada
    $stmt_del_orders = $pdo->prepare("DELETE FROM orders WHERE konser_id = ? AND status != 'lunas'");
    $stmt_del_orders->execute([$id]);

    // Hapus data konser
    $stmt_del = $pdo->prepare("DELETE FROM konsers WHERE id = ?");
    $stmt_del->execute([$id]);

    $pdo->commit();

    set_flash('success', "Konser \"{$konser['nama_konser']}\" berhasil dihapus.");
    header("Location: index.php");
    exit;

} catch (PDOException $ex) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log($ex->getMessage());
    set_flash('error', 'Konser tidak dapat dihapus saat ini.');
    header("Location: index.php");
    exit;
}
