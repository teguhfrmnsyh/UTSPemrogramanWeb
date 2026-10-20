<?php
/**
 * FR-04: Daftar Konser
 * FR-05: Pencarian & Filter Konser
 * FR-07: Akses Pembelian (Selesai & Habis)
 * NFR-01: Responsif pada HP, Tablet, Laptop
 * NFR-03: Prepared statement PDO
 * NFR-04: Escaping output
 */

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/auth_check.php';

// Ambil parameter filter & sanitasi
$search_artist = trim($_GET['artist'] ?? '');
$filter_lokasi = trim($_GET['lokasi'] ?? '');
$filter_tanggal = trim($_GET['tanggal'] ?? '');

// Susun query dinamis menggunakan prepared statement (NFR-03)
$sql = "SELECT id, nama_konser, artis, lokasi, waktu_konser, harga, jenis_tiket, poster, jumlah_tiket_awal, sisa_tiket
        FROM konsers 
        WHERE 1=1";
$params = [];

if (!empty($search_artist)) {
    $sql .= " AND artis LIKE ?";
    $params[] = "%{$search_artist}%";
}

if (!empty($filter_lokasi)) {
    $sql .= " AND lokasi = ?";
    $params[] = $filter_lokasi;
}

if (!empty($filter_tanggal)) {
    $sql .= " AND DATE(waktu_konser) = ?";
    $params[] = $filter_tanggal;
}

// Urutkan: konser mendatang terlebih dahulu, baru konser lewat
$sql .= " ORDER BY CASE WHEN waktu_konser >= NOW() THEN 0 ELSE 1 END, waktu_konser ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$konsers = $stmt->fetchAll();

// FR-05: Ambil daftar artis unik dari database untuk rekomendasi Spotify-like
$stmt_artis = $pdo->prepare("SELECT DISTINCT artis FROM konsers ORDER BY artis ASC");
$stmt_artis->execute();
$daftar_artis = $stmt_artis->fetchAll(PDO::FETCH_COLUMN);

$user = auth_user();
$flash_success = get_flash('success');
$flash_error = get_flash('error');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bersuara - Website Tiket Konser Jabodetabek</title>
    <meta name="description" content="Platform tiket konser terpusat untuk wilayah Jabodetabek. Temukan jadwal artis favoritmu dan amankan tiketmu sekarang.">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <!-- NAVBAR UTAMA -->
    <header class="navbar">
        <div class="container">
            <a href="index.php" class="brand-logo">
                <div class="brand-text">Bersuara</div>
            </a>

            <ul class="nav-links">
                <li><a href="index.php" class="active">Katalog Konser</a></li>
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
                        <span class="role-tag <?= is_admin() ? 'role-admin' : 'role-pembeli' ?>">
                            <?= e($user['role']) ?>
                        </span>
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
        <!-- HEADER KATALOG -->
        <section class="page-header">
            <h1 class="page-title">Konser di Jabodetabek</h1>
            <p class="page-desc">Cari konser berdasarkan artis, wilayah, atau tanggal.</p>
        </section>

        <!-- FLASH NOTIFICATION -->
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

        <!-- FR-05: FILTER & SEARCH BAR -->
        <section class="filter-bar">
            <form action="index.php" method="GET" class="filter-grid">
                <!-- Pencarian Artis dengan Rekomendasi Mirip Spotify -->
                <div class="form-group autocomplete-wrapper">
                    <label for="artist-search" class="form-label">Cari Artis / Band</label>
                    <input type="text" 
                           id="artist-search" 
                           name="artist" 
                           class="form-control" 
                           placeholder="Ketik nama artis (misal: Tulus, Sheila on 7...)" 
                           value="<?= e($search_artist) ?>" 
                           data-artists="<?= e(json_encode($daftar_artis, JSON_UNESCAPED_UNICODE)) ?>" 
                           autocomplete="off">
                    <!-- Dropdown Saran Mirip Spotify -->
                    <div id="artist-suggestions" class="suggestions-dropdown"></div>
                </div>

                <!-- Filter Lokasi (Jabodetabek) -->
                <div class="form-group">
                    <label for="lokasi-filter" class="form-label">Wilayah</label>
                    <select name="lokasi" id="lokasi-filter" class="form-control">
                        <option value="">Semua Lokasi</option>
                        <option value="Jakarta" <?= $filter_lokasi === 'Jakarta' ? 'selected' : '' ?>>Jakarta</option>
                        <option value="Bogor" <?= $filter_lokasi === 'Bogor' ? 'selected' : '' ?>>Bogor</option>
                        <option value="Depok" <?= $filter_lokasi === 'Depok' ? 'selected' : '' ?>>Depok</option>
                        <option value="Tangerang" <?= $filter_lokasi === 'Tangerang' ? 'selected' : '' ?>>Tangerang</option>
                        <option value="Bekasi" <?= $filter_lokasi === 'Bekasi' ? 'selected' : '' ?>>Bekasi</option>
                    </select>
                </div>

                <!-- Filter Tanggal Tunggal -->
                <div class="form-group">
                    <label for="tanggal-filter" class="form-label">Tanggal Konser</label>
                    <input type="date" 
                           id="tanggal-filter" 
                           name="tanggal" 
                           class="form-control" 
                           value="<?= e($filter_tanggal) ?>">
                </div>

                <!-- Tombol Aksi -->
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">Cari Konser</button>
                    <?php if (!empty($search_artist) || !empty($filter_lokasi) || !empty($filter_tanggal)): ?>
                        <a href="index.php" class="btn btn-secondary" title="Reset Filter">Reset</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <!-- FR-04: GRID DAFTAR KONSER -->
        <section>
            <div class="section-header">
                <h2 class="section-title">
                    Daftar Konser Tersedia 
                    <span class="section-count">(<?= count($konsers) ?> Acara)</span>
                </h2>
            </div>

            <?php if (empty($konsers)): ?>
                <div class="empty-state">
                    <h3>Tidak Ada Konser yang Cocok</h3>
                    <p>Coba sesuaikan kata kunci artis, pilihan wilayah, atau pilih tanggal lain.</p>
                    <a href="index.php" class="btn btn-secondary">Tampilkan Semua Konser</a>
                </div>
            <?php else: ?>
                <div class="concerts-grid">
                    <?php foreach ($konsers as $k): 
                        $is_past = strtotime($k['waktu_konser']) < time();
                        $is_sold_out = (int)$k['sisa_tiket'] <= 0;
                        $poster_file = !empty($k['poster']) ? basename($k['poster']) : '';
                        $poster_ada  = $poster_file !== '' && is_file(__DIR__ . '/assets/img/poster/' . $poster_file);
                    ?>
                        <article class="concert-card <?= $is_past ? 'card-selesai' : '' ?>">
                            <div class="card-poster-wrapper">
                                <?php if ($poster_ada): ?>
                                    <img class="card-poster-img" src="assets/img/poster/<?= e($poster_file) ?>" alt="Poster <?= e($k['artis']) ?>" loading="lazy">
                                <?php else: ?>
                                    <div class="card-poster-placeholder">
                                        <span class="placeholder-artist"><?= e($k['artis']) ?></span>
                                        <span class="placeholder-label">Poster belum tersedia</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <div class="card-status-bar">
                                    <?php if ($is_past): ?>
                                        <span class="badge-status badge-selesai">Selesai</span>
                                    <?php elseif ($is_sold_out): ?>
                                        <span class="badge-status badge-habis">Habis</span>
                                    <?php endif; ?>
                                </div>
                                <div class="artist-name"><?= e($k['artis']) ?></div>
                                <h3 class="concert-title"><?= e($k['nama_konser']) ?></h3>

                                <ul class="concert-meta">
                                    <li>
                                        <span class="concert-meta-label">Lokasi</span>
                                        <span class="concert-meta-value"><?= e($k['lokasi']) ?></span>
                                    </li>
                                    <li>
                                        <span class="concert-meta-label">Jadwal</span>
                                        <span class="concert-meta-value"><?= format_tanggal($k['waktu_konser']) ?></span>
                                    </li>
                                    <li>
                                        <span class="concert-meta-label">Jenis tiket</span>
                                        <span class="concert-meta-value"><?= e($k['jenis_tiket']) ?></span>
                                    </li>
                                    <li>
                                        <span class="concert-meta-label">Sisa tiket</span>
                                        <span class="concert-meta-value"><strong><?= (int)$k['sisa_tiket'] ?></strong> / <?= (int)$k['jumlah_tiket_awal'] ?></span>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-footer">
                                <div class="price-box">
                                    <span class="price-label">Mulai Dari</span>
                                    <span class="price-value"><?= format_rupiah($k['harga']) ?></span>
                                </div>

                                <!-- Tombol Beli / Aksi (FR-07) -->
                                <?php if ($is_past): ?>
                                    <button class="btn btn-sm btn-secondary disabled" disabled>Konser Selesai</button>
                                <?php elseif ($is_sold_out): ?>
                                    <button class="btn btn-sm btn-secondary disabled" disabled>Tiket Habis</button>
                                <?php else: ?>
                                    <a href="detail.php?id=<?= $k['id'] ?>" class="btn btn-sm btn-primary">Beli Tiket</a>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p class="site-footer-text">Bersuara. Simulasi pembelian tiket konser untuk tugas UTS Pemrograman Web.</p>
        </div>
    </footer>

    <script src="assets/script.js"></script>
</body>
</html>
