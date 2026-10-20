<?php
/**
 * FR-13: Proteksi Admin & Pengalihan
 * FR-14: Daftar Konser Admin
 * NFR-03: Prepared statement PDO
 * NFR-04: Escaping output
 * NFR-05: Valid session protection
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/auth_check.php';

// NFR-05, FR-13, TC-07, TC-24: Tolak jika bukan admin
require_admin('../login.php');

$user = auth_user();

// Ambil seluruh konser beserta jumlah pesanan lunas untuk validasi hapus
$sql = "SELECT k.*, 
        (SELECT COUNT(*) FROM orders o WHERE o.konser_id = k.id AND o.status = 'lunas') AS total_pesanan_lunas
        FROM konsers k
        ORDER BY k.waktu_konser DESC";
$stmt = $pdo->query($sql);
$konsers = $stmt->fetchAll();

$flash_success = get_flash('success');
$flash_error = get_flash('error');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Konser - Panel Admin Bersuara</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

    <header class="navbar">
        <div class="container">
            <a href="index.php" class="brand-logo">
                <div class="brand-text">Bersuara</div>
            </a>
            <ul class="nav-links">
                <li><a href="index.php" class="active">Kelola Konser</a></li>
                <li><a href="pesanan.php">Daftar Pesanan (FR-19)</a></li>
                <li><a href="../index.php" target="_blank">Lihat Website</a></li>
            </ul>
            <div class="nav-actions">
                <div class="user-badge">
                    <div class="user-avatar"><?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?></div>
                    <span><?= e($user['nama_lengkap']) ?></span>
                    <span class="role-tag role-admin">Admin</span>
                </div>
                <a href="../logout.php" class="btn btn-sm btn-outline">Keluar</a>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="section-header">
            <div>
                <h1>Manajemen Data Konser</h1>
                <p class="text-muted">Kelola jadwal acara, kuota tiket, dan detail artis di wilayah Jabodetabek.</p>
            </div>
            <a href="tambah.php" class="btn btn-primary">+ Tambah Konser Baru</a>
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

        <!-- FR-14: TABEL DAFTAR KONSER ADMIN (TC-25) -->
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Konser & Artis</th>
                        <th>Lokasi</th>
                        <th>Waktu Pelaksanaan</th>
                        <th>Harga</th>
                        <th>Jenis Tiket</th>
                        <th>Sisa / Kuota Awal</th>
                        <th>Status Order</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($konsers)): ?>
                        <tr>
                            <td colspan="9" class="text-muted">
                                Belum ada data konser. Silakan tambahkan konser baru.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($konsers as $k): 
                            $is_past = strtotime($k['waktu_konser']) < time();
                            $has_paid_orders = (int)$k['total_pesanan_lunas'] > 0;
                        ?>
                            <tr>
                                <td>#<?= $k['id'] ?></td>
                                <td>
                                    <strong><?= e($k['nama_konser']) ?></strong>
                                    <div class="text-muted">
                                        <?= e($k['artis']) ?>
                                    </div>
                                </td>
                                <td><?= e($k['lokasi']) ?></td>
                                <td>
                                    <div><?= format_tanggal($k['waktu_konser']) ?></div>
                                    <?php if ($is_past): ?>
                                        <span class="badge-status badge-selesai">Selesai</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right"><strong><?= format_rupiah($k['harga']) ?></strong></td>
                                <td><?= e($k['jenis_tiket']) ?></td>
                                <td>
                                    <strong class="<?= (int)$k['sisa_tiket'] <= 0 ? 'text-danger' : '' ?>">
                                        <?= (int)$k['sisa_tiket'] ?>
                                    </strong> / <?= (int)$k['jumlah_tiket_awal'] ?>
                                </td>
                                <td>
                                    <?php if ($has_paid_orders): ?>
                                        <span class="badge-status badge-lunas" title="Ada pesanan lunas">
                                            <?= $k['total_pesanan_lunas'] ?> Lunas
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-status">
                                            0 Lunas
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <!-- FR-14, FR-16, FR-17: Tombol Ubah dan Hapus (TC-25, TC-28, TC-29, TC-30) -->
                                    <div class="actions-cell">
                                        <a href="ubah.php?id=<?= $k['id'] ?>" class="btn btn-sm btn-secondary">
                                            Ubah
                                        </a>
                                        
                                        <?php if ($has_paid_orders): ?>
                                            <!-- FR-17: Tidak dapat dihapus karena ada pesanan lunas -->
                                            <button class="btn btn-sm btn-danger disabled" 
                                                    disabled 
                                                    title="Konser tidak dapat dihapus karena memiliki pesanan lunas (FR-17)">
                                                    Hapus
                                            </button>
                                        <?php else: ?>
                                            <form method="POST" action="hapus.php">
                                                <input type="hidden" name="id" value="<?= $k['id'] ?>">
                                                <?= csrf_field() ?>
                                                <button type="submit" 
                                                        class="btn btn-sm btn-danger btn-delete-confirm" 
                                                        data-concert-name="<?= e($k['nama_konser']) ?>">
                                                    Hapus
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
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

    <script src="../assets/script.js"></script>
</body>
</html>
