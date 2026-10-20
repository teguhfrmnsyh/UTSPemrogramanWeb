<?php
/**
 * FR-19: Daftar Pesanan Admin (Fitur Could)
 * Admin dapat melihat daftar pesanan yang berisi pembeli, konser, jumlah tiket, total bayar, dan status
 * TC-31: Halaman hanya dapat dilihat (view-only)
 * NFR-03: Prepared statement PDO
 * NFR-04: Escaping output
 * NFR-05: Session admin protection
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/auth_check.php';

require_admin('../login.php');

$user = auth_user();

// FR-19: Query 5 data yang ditentukan (pembeli, konser, jumlah tiket, total bayar, status)
$sql = "SELECT o.id, o.kode_pesanan, o.nama_pemesan, o.email_pemesan, o.telepon_pemesan,
               k.nama_konser, k.artis, o.jumlah_tiket, o.total_bayar, o.metode_pembayaran,
               o.status, o.created_at
        FROM orders o
        JOIN konsers k ON o.konser_id = k.id
        ORDER BY o.created_at DESC";
$stmt = $pdo->query($sql);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pesanan - Admin Bersuara</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

    <header class="navbar">
        <div class="container">
            <a href="index.php" class="brand-logo">
                <div class="brand-text">Bersuara</div>
            </a>
            <ul class="nav-links">
                <li><a href="index.php">Kelola Konser</a></li>
                <li><a href="pesanan.php" class="active">Daftar Pesanan (FR-19)</a></li>
                <li><a href="../index.php" target="_blank">Lihat Website</a></li>
            </ul>
            <div class="nav-actions">
                <div class="user-badge">
                    <div class="user-avatar"><?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?></div>
                    <span><?= e($user['nama_lengkap']) ?></span>
                </div>
                <a href="../logout.php" class="btn btn-sm btn-outline">Keluar</a>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="section-header">
            <div>
                <h1>Daftar Pesanan Tiket Masuk</h1>
                <p class="text-muted">
                    Halaman pemantauan transaksi pesanan pembeli (FR-19: Hanya dapat dilihat tanpa modifikasi).
                </p>
            </div>
            <a href="index.php" class="btn btn-sm btn-secondary">Kembali ke Kelola Konser</a>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode Pesanan</th>
                        <th>Data Pembeli</th>
                        <th>Konser & Artis</th>
                        <th>Jumlah Tiket</th>
                        <th>Total Bayar</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Waktu Transaksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="8" class="text-muted">
                                Belum ada transaksi pesanan yang masuk ke sistem.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td>
                                    <strong>
                                        <?= e($o['kode_pesanan']) ?>
                                    </strong>
                                </td>
                                <td>
                                    <strong><?= e($o['nama_pemesan']) ?></strong>
                                    <div class="text-muted">
                                        <?= e($o['email_pemesan']) ?> - <?= e($o['telepon_pemesan']) ?>
                                    </div>
                                </td>
                                <td>
                                    <strong><?= e($o['nama_konser']) ?></strong>
                                    <div class="text-muted">
                                        <?= e($o['artis']) ?>
                                    </div>
                                </td>
                                <td>
                                    <strong><?= (int)$o['jumlah_tiket'] ?></strong> lembar
                                </td>
                                <td class="text-right">
                                    <strong><?= format_rupiah($o['total_bayar']) ?></strong>
                                </td>
                                <td>
                                    <span class="badge-status">
                                        <?= strtoupper(e($o['metode_pembayaran'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-status <?= $o['status'] === 'lunas' ? 'badge-lunas' : 'badge-menunggu' ?>">
                                        <?= strtoupper(e($o['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= date('d/m/Y H:i:s', strtotime($o['created_at'])) ?>
                                    </small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 Bersuara - Panel Administrasi (UTS Pemrograman Web).</p>
        </div>
    </footer>

</body>
</html>
