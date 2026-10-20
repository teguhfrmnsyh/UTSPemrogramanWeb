<?php
/**
 * FR-12: Riwayat Pesanan Pembeli
 * Menampilkan seluruh pesanan tiket digital yang pernah dibeli pengguna
 * NFR-03: Prepared statement PDO
 * NFR-04: Escaping output
 * NFR-05: Session protection
 */

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/auth_check.php';

require_login('login.php');

$user = auth_user();

// Ambil riwayat pesanan akun yang sedang login
$sql = "SELECT o.*, k.nama_konser, k.artis, k.lokasi, k.waktu_konser 
        FROM orders o
        JOIN konsers k ON o.konser_id = k.id
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user['id']]);
$riwayat = $stmt->fetchAll();

$flash_success = get_flash('success');
$flash_error = get_flash('error');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan Saya - Bersuara</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <header class="navbar">
        <div class="container">
            <a href="index.php" class="brand-logo">
                <div class="brand-text">Bersuara</div>
            </a>
            <ul class="nav-links">
                <li><a href="index.php">Katalog Konser</a></li>
                <li><a href="riwayat.php" class="active">Riwayat Pesanan</a></li>
            </ul>
            <div class="nav-actions">
                <div class="user-badge">
                    <div class="user-avatar"><?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?></div>
                    <span><?= e($user['nama_lengkap']) ?></span>
                </div>
                <a href="logout.php" class="btn btn-sm btn-outline">Keluar</a>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="section-header">
            <div>
                <h1>Riwayat Pesanan Tiket</h1>
                <p class="text-muted">Akses dan buka kembali tiket digital konser yang telah Anda beli.</p>
            </div>
            <a href="index.php" class="btn btn-sm btn-primary">+ Beli Tiket Lain</a>
        </div>

        <?php if ($flash_success): ?>
            <div class="alert alert-success">
                <span><?= e($flash_success) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($flash_error): ?>
            <div class="alert alert-danger">
                <span><?= e($flash_error) ?></span>
            </div>
        <?php endif; ?>

        <?php if (empty($riwayat)): ?>
            <div class="empty-state">
                <h3>Belum Ada Tiket yang Dibeli</h3>
                <p>
                    Anda belum memiliki riwayat pembelian tiket konser.
                </p>
                <a href="index.php" class="btn btn-primary">Jelajahi Konser Sekarang</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kode Pesanan</th>
                            <th>Konser & Artis</th>
                            <th>Waktu & Lokasi</th>
                            <th>Jumlah</th>
                            <th>Total Bayar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($riwayat as $item): ?>
                            <tr>
                                <td>
                                    <strong>
                                        <?= e($item['kode_pesanan']) ?>
                                    </strong>
                                    <br>
                                    <small class="text-muted">
                                        <?= date('d/m/Y H:i', strtotime($item['created_at'])) ?>
                                    </small>
                                </td>
                                <td>
                                    <strong><?= e($item['nama_konser']) ?></strong>
                                    <div class="text-muted">
                                        <?= e($item['artis']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div><?= format_tanggal($item['waktu_konser']) ?></div>
                                    <small class="text-muted"><?= e($item['lokasi']) ?></small>
                                </td>
                                <td>
                                    <strong><?= (int)$item['jumlah_tiket'] ?></strong> lembar
                                </td>
                                <td class="text-right">
                                    <strong><?= format_rupiah($item['total_bayar']) ?></strong>
                                </td>
                                <td>
                                    <span class="badge-status <?= $item['status'] === 'lunas' ? 'badge-lunas' : 'badge-menunggu' ?>">
                                        <?= strtoupper(e($item['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="tiket.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline">
                                        Buka Tiket
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p class="site-footer-text">Bersuara. Simulasi pembelian tiket konser untuk tugas UTS Pemrograman Web.</p>
        </div>
    </footer>

</body>
</html>
