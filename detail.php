<?php
/**
 * FR-06: Detail Konser
 * FR-07: Akses Pembelian
 * FR-08: Batas Pembelian (Maksimal 4 tiket lunas per akun per konser)
 * NFR-03: Prepared statement PDO
 * NFR-04: Escaping output
 */

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/auth_check.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: index.php");
    exit;
}

// Ambil data konser
$stmt = $pdo->prepare("SELECT * FROM konsers WHERE id = ?");
$stmt->execute([$id]);
$konser = $stmt->fetch();

if (!$konser) {
    set_flash('error', 'Konser tidak ditemukan.');
    header("Location: index.php");
    exit;
}

$is_past = strtotime($konser['waktu_konser']) < time();
$is_sold_out = (int)$konser['sisa_tiket'] <= 0;
$user = auth_user();

// FR-08: Hitung jumlah tiket lunas yang sudah dimiliki akun untuk konser ini
$tiket_lunas_terbeli = 0;
if ($user && $user['role'] === 'pembeli') {
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(jumlah_tiket), 0) FROM orders WHERE user_id = ? AND konser_id = ? AND status = 'lunas'");
    $stmt->execute([$user['id'], $id]);
    $tiket_lunas_terbeli = (int)$stmt->fetchColumn();
}

$sisa_jatah_akun = max(0, 4 - $tiket_lunas_terbeli);
$max_beli_bisa = min((int)$konser['sisa_tiket'], $sisa_jatah_akun);

// Tangani aksi submit beli tiket
$error_beli = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // FR-07, BR-06: Belum login diarahkan ke login
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = "detail.php?id={$id}";
        header("Location: login.php");
        exit;
    }

    // T5-a: Admin tidak boleh membeli tiket
    if (is_admin()) {
        $error_beli = 'Akun admin tidak dapat membeli tiket.';
    } elseif ($is_past) {
        $error_beli = 'Konser sudah selesai dan tidak dapat dibeli.';
    } elseif ($is_sold_out) {
        $error_beli = 'Tiket konser ini telah habis.';
    } elseif ($sisa_jatah_akun <= 0) {
        $error_beli = 'Anda sudah mencapai batas maksimal 4 tiket lunas untuk konser ini.';
    } else {
        $jumlah = filter_input(INPUT_POST, 'jumlah_tiket', FILTER_VALIDATE_INT);
        if (!$jumlah || $jumlah < 1) {
            $error_beli = 'Jumlah tiket minimal 1 lembar.';
        } elseif ($jumlah > $konser['sisa_tiket']) {
            $error_beli = 'Jumlah tiket melebihi sisa tiket yang tersedia.';
        } elseif (($tiket_lunas_terbeli + $jumlah) > 4) {
            $error_beli = "Pesanan ditolak. Batas maksimal per akun adalah 4 tiket. Anda sudah memiliki {$tiket_lunas_terbeli} tiket lunas.";
        } else {
            // Lanjut ke halaman pembayaran
            header("Location: pembayaran.php?konser_id={$id}&jumlah={$jumlah}");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($konser['nama_konser']) ?> - Detail Konser Bersuara</title>
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
                <?php if (is_logged_in() && !is_admin()): ?>
                    <li><a href="riwayat.php">Riwayat Pesanan</a></li>
                <?php endif; ?>
                <?php if (is_admin()): ?>
                    <li><a href="admin/index.php">Panel Admin</a></li>
                <?php endif; ?>
            </ul>
            <div class="nav-actions">
                <?php if (is_logged_in()): ?>
                    <div class="user-badge">
                        <div class="user-avatar"><?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?></div>
                        <span><?= e($user['nama_lengkap']) ?></span>
                    </div>
                    <a href="logout.php" class="btn btn-sm btn-outline">Keluar</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-sm btn-outline">Masuk</a>
                    <a href="register.php" class="btn btn-sm btn-primary">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="page-actions">
            <a href="index.php" class="btn btn-sm btn-secondary">Kembali ke Katalog</a>
        </div>

        <?php if ($error_beli): ?>
            <div class="alert alert-danger">
                <span><?= e($error_beli) ?></span>
            </div>
        <?php endif; ?>

        <?php
        $poster_file = !empty($konser['poster']) ? basename($konser['poster']) : '';
        $poster_ada  = $poster_file !== '' && is_file(__DIR__ . '/assets/img/poster/' . $poster_file);
        ?>
        <div class="detail-layout">
            <div class="detail-poster-wrapper">
                <?php if ($poster_ada): ?>
                    <img class="detail-poster-img" src="assets/img/poster/<?= e($poster_file) ?>" alt="Poster <?= e($konser['artis']) ?>">
                <?php else: ?>
                    <div class="card-poster-placeholder">
                        <span class="placeholder-artist"><?= e($konser['artis']) ?></span>
                        <span class="placeholder-label">Poster belum tersedia</span>
                    </div>
                <?php endif; ?>
            </div>
            <div>
                <!-- Kolom Informasi Konser (FR-06) -->
                <div class="panel-card">
                <div class="meta-grid">
                    <div>
                        <span class="meta-item-label">Lokasi</span>
                        <strong class="meta-item-value"><?= e($konser['lokasi']) ?></strong>
                    </div>
                    <div>
                        <span class="meta-item-label">Jenis tiket</span>
                        <strong class="meta-item-value"><?= e($konser['jenis_tiket']) ?></strong>
                    </div>
                    <?php if ($is_past): ?>
                        <div>
                            <span class="meta-item-label">Status</span>
                            <span class="badge-status badge-selesai">Selesai</span>
                        </div>
                    <?php elseif ($is_sold_out): ?>
                        <div>
                            <span class="meta-item-label">Status</span>
                            <span class="badge-status badge-habis">Habis</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="artist-name"><?= e($konser['artis']) ?></div>
                <h1 class="concert-title"><?= e($konser['nama_konser']) ?></h1>

                <h2>Detail Acara</h2>
                <div class="meta-grid">
                    <div>
                        <span class="meta-item-label">Jadwal Konser</span>
                        <strong class="meta-item-value"><?= format_tanggal($konser['waktu_konser']) ?></strong>
                    </div>
                    <div>
                        <span class="meta-item-label">Lokasi Penyelenggaraan</span>
                        <strong class="meta-item-value"><?= e($konser['lokasi']) ?>, Jabodetabek</strong>
                    </div>
                    <div>
                        <span class="meta-item-label">Kategori Tiket</span>
                        <strong class="meta-item-value"><?= e($konser['jenis_tiket']) ?></strong>
                    </div>
                    <div>
                        <span class="meta-item-label">Ketersediaan Kuota</span>
                        <strong class="meta-item-value <?= $is_sold_out ? 'text-danger' : '' ?>">
                            <?= (int)$konser['sisa_tiket'] ?> dari <?= (int)$konser['jumlah_tiket_awal'] ?> tiket
                        </strong>
                    </div>
                </div>
            </div>

                <!-- Kolom Pemesanan Tiket (FR-07, FR-08) -->
                <div class="panel-card">
                <h2>Pesan Tiket</h2>

                <div class="summary-row">
                    <span class="text-muted">Harga per Tiket</span>
                    <strong id="harga-satuan" data-price="<?= $konser['harga'] ?>">
                        <?= format_rupiah($konser['harga']) ?>
                    </strong>
                </div>

                <?php if (is_logged_in() && !is_admin()): ?>
                    <div class="alert alert-info">
                        Anda telah membeli <strong><?= $tiket_lunas_terbeli ?></strong> dari maksimal 4 tiket lunas untuk konser ini.
                    </div>
                <?php endif; ?>

                <?php if (is_admin()): ?>
                    <!-- T5-a: Admin tidak boleh membeli tiket -->
                    <div class="alert alert-danger">
                        <span>Akun admin tidak dapat membeli tiket konser.</span>
                    </div>
                    <button class="btn btn-secondary btn-block btn-lg disabled" disabled>Akun Admin Tidak Dapat Membeli</button>
                <?php elseif ($is_past): ?>
                    <!-- TC-11: Konser Selesai -->
                    <div class="alert alert-danger">
                        <span>Konser ini telah selesai diselenggarakan dan tidak dapat dibeli lagi.</span>
                    </div>
                    <button class="btn btn-secondary btn-block btn-lg disabled" disabled>Konser Telah Berakhir</button>
                <?php elseif ($is_sold_out): ?>
                    <!-- TC-12: Tiket Habis -->
                    <div class="alert alert-danger">
                        <span>Maaf, tiket untuk konser ini sudah habis terjual.</span>
                    </div>
                    <button class="btn btn-secondary btn-block btn-lg disabled" disabled>Tiket Habis</button>
                <?php elseif (is_logged_in() && $sisa_jatah_akun <= 0): ?>
                    <!-- TC-19: Batas 4 tiket tercapai -->
                    <div class="alert alert-danger">
                        <span>Anda telah mencapai batas maksimal 4 tiket lunas untuk konser ini.</span>
                    </div>
                    <button class="btn btn-secondary btn-block btn-lg disabled" disabled>Batas Pembelian Tercapai</button>
                <?php else: ?>
                    <form action="detail.php?id=<?= $konser['id'] ?>" method="POST" class="form-stack">
                        <div class="form-group">
                            <label for="jumlah-tiket" class="form-label">
                                Jumlah Tiket (Maks. <?= $max_beli_bisa ?> tiket)
                            </label>
                            <input type="number" 
                                   id="jumlah-tiket" 
                                   name="jumlah_tiket" 
                                   class="form-control" 
                                   min="1" 
                                   max="<?= $max_beli_bisa ?>" 
                                   value="1" 
                                   required>
                            <small class="text-muted">
                                Maksimal 4 tiket lunas per akun untuk setiap konser (FR-08).
                            </small>
                        </div>

                        <div class="summary-row total-row">
                            <span>Estimasi Total</span>
                            <span id="total-harga-display"><?= format_rupiah($konser['harga']) ?></span>
                        </div>

                        <div>
                            <?php if (is_logged_in()): ?>
                                <button type="submit" class="btn btn-primary btn-block btn-lg">
                                    Lanjut ke Pembayaran
                                </button>
                            <?php else: ?>
                                <!-- TC-14, BR-06: Belum login diarahkan ke login saat tekan beli -->
                                <button type="submit" class="btn btn-primary btn-block btn-lg">
                                    Login untuk Membeli Tiket
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p class="site-footer-text">Bersuara. Simulasi pembelian tiket konser untuk tugas UTS Pemrograman Web.</p>
        </div>
    </footer>

    <script src="assets/script.js"></script>
</body>
</html>
