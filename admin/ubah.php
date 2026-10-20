<?php
/**
 * FR-16: Ubah Konser (Form memuat data lama)
 * FR-18: Validasi Data Konser
 * NFR-03: Prepared statement PDO
 * NFR-04: Escaping data
 * NFR-05: Session admin protection
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/auth_check.php';
require_once __DIR__ . '/../config/validasi_konser.php';

require_admin('../login.php');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: index.php");
    exit;
}

// Ambil data konser lama
$stmt = $pdo->prepare("SELECT * FROM konsers WHERE id = ?");
$stmt->execute([$id]);
$konser = $stmt->fetch();

if (!$konser) {
    set_flash('error', 'Data konser tidak ditemukan.');
    header("Location: index.php");
    exit;
}

// Hitung tiket yang sudah lunas terjual
$stmt_sold = $pdo->prepare("SELECT COALESCE(SUM(jumlah_tiket), 0) FROM orders WHERE konser_id = ? AND status = 'lunas'");
$stmt_sold->execute([$id]);
$tiket_terjual = (int)$stmt_sold->fetchColumn();

$errors = [];
$nama_konser = $konser['nama_konser'];
$artis = $konser['artis'];
$lokasi = $konser['lokasi'];
// Format datetime-local untuk input HTML (YYYY-MM-DDTHH:MM)
$waktu_konser = date('Y-m-d\TH:i', strtotime($konser['waktu_konser']));
$harga = $konser['harga'];
$jenis_tiket = $konser['jenis_tiket'];
$jumlah_tiket_awal = $konser['jumlah_tiket_awal'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        set_flash('error', 'Sesi formulir tidak valid. Muat ulang halaman lalu coba lagi.');
        header("Location: index.php");
        exit;
    }

    $nama_konser = trim($_POST['nama_konser'] ?? '');
    $artis = trim($_POST['artis'] ?? '');
    $lokasi = trim($_POST['lokasi'] ?? '');
    $waktu_konser = trim($_POST['waktu_konser'] ?? '');
    $harga = trim($_POST['harga'] ?? '');
    $jenis_tiket = trim($_POST['jenis_tiket'] ?? '');
    $jumlah_tiket_awal = trim($_POST['jumlah_tiket_awal'] ?? '');

    // Validasi FR-18 via config/validasi_konser.php
    $hasil_validasi = validasi_data_konser($_POST);
    $errors = $hasil_validasi['errors'];
    $data = $hasil_validasi['data'];

    // Aturan khusus ubah.php: kuota awal tidak boleh kurang dari tiket terjual
    if (!isset($errors['jumlah_tiket_awal']) && isset($data['jumlah_tiket_awal'])) {
        if ($data['jumlah_tiket_awal'] < $tiket_terjual) {
            $errors['jumlah_tiket_awal'] = "Kuota awal tidak boleh kurang dari tiket yang sudah terjual ({$tiket_terjual} tiket).";
        }
    }

    if (empty($errors)) {
        // Hitung sisa tiket baru
        $sisa_tiket_baru = $data['jumlah_tiket_awal'] - $tiket_terjual;

        $sql = "UPDATE konsers 
                SET nama_konser = ?, artis = ?, lokasi = ?, waktu_konser = ?, harga = ?, jenis_tiket = ?, jumlah_tiket_awal = ?, sisa_tiket = ? 
                WHERE id = ?";
        $stmt_update = $pdo->prepare($sql);
        $stmt_update->execute([
            $data['nama_konser'],
            $data['artis'],
            $data['lokasi'],
            $data['waktu_konser'],
            $data['harga'],
            $data['jenis_tiket'],
            $data['jumlah_tiket_awal'],
            $sisa_tiket_baru,
            $id
        ]);

        set_flash('success', "Konser \"{$data['nama_konser']}\" berhasil diperbarui!");
        header("Location: index.php");
        exit;
    }
}

$user = auth_user();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Konser #<?= $id ?> - Admin Bersuara</title>
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
                <li><a href="pesanan.php">Daftar Pesanan</a></li>
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
        <div>
            <a href="index.php" class="btn btn-sm btn-secondary">Kembali ke Daftar Konser</a>

            <div class="panel-card">
                <h2>Ubah Data Konser #<?= $id ?></h2>
                <p class="text-muted">
                    Form telah terisi dengan data lama (FR-16). Perubahan data akan langsung diterapkan ke katalog konser.
                </p>

                <?php if ($tiket_terjual > 0): ?>
                    <div class="alert alert-info">
                        Konser ini sudah memiliki <strong><?= $tiket_terjual ?> tiket lunas</strong> yang terjual. Kuota minimal adalah <?= $tiket_terjual ?> tiket.
                    </div>
                <?php endif; ?>

                <form action="ubah.php?id=<?= $id ?>" method="POST" class="form-stack" novalidate>
                    <?= csrf_field() ?>
                    <!-- Nama Konser -->
                    <div class="form-group">
                        <label for="nama_konser" class="form-label">Nama Acara Konser</label>
                        <input type="text" 
                               id="nama_konser" 
                               name="nama_konser" 
                               class="form-control <?= isset($errors['nama_konser']) ? 'is-invalid' : '' ?>" 
                               value="<?= e($nama_konser) ?>" 
                               required>
                        <?php if (isset($errors['nama_konser'])): ?>
                            <div class="field-error"><?= e($errors['nama_konser']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Artis / Band -->
                    <div class="form-group">
                        <label for="artis" class="form-label">Artis / Band Utama</label>
                        <input type="text" 
                               id="artis" 
                               name="artis" 
                               class="form-control <?= isset($errors['artis']) ? 'is-invalid' : '' ?>" 
                               value="<?= e($artis) ?>" 
                               required>
                        <?php if (isset($errors['artis'])): ?>
                            <div class="field-error"><?= e($errors['artis']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Wilayah / Lokasi (Jabodetabek) -->
                    <div class="form-group">
                        <label for="lokasi" class="form-label">Wilayah (Jabodetabek)</label>
                        <select name="lokasi" id="lokasi" class="form-control <?= isset($errors['lokasi']) ? 'is-invalid' : '' ?>">
                            <option value="Jakarta" <?= $lokasi === 'Jakarta' ? 'selected' : '' ?>>Jakarta</option>
                            <option value="Bogor" <?= $lokasi === 'Bogor' ? 'selected' : '' ?>>Bogor</option>
                            <option value="Depok" <?= $lokasi === 'Depok' ? 'selected' : '' ?>>Depok</option>
                            <option value="Tangerang" <?= $lokasi === 'Tangerang' ? 'selected' : '' ?>>Tangerang</option>
                            <option value="Bekasi" <?= $lokasi === 'Bekasi' ? 'selected' : '' ?>>Bekasi</option>
                        </select>
                        <?php if (isset($errors['lokasi'])): ?>
                            <div class="field-error"><?= e($errors['lokasi']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Waktu Konser (Tanggal & Jam) -->
                    <div class="form-group">
                        <label for="waktu_konser" class="form-label">Waktu & Tanggal Konser</label>
                        <input type="datetime-local" 
                               id="waktu_konser" 
                               name="waktu_konser" 
                               class="form-control <?= isset($errors['waktu_konser']) ? 'is-invalid' : '' ?>" 
                               value="<?= e($waktu_konser) ?>" 
                               required>
                        <?php if (isset($errors['waktu_konser'])): ?>
                            <div class="field-error"><?= e($errors['waktu_konser']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Jenis Tiket -->
                    <div class="form-group">
                        <label for="jenis_tiket" class="form-label">Kategori / Jenis Tiket</label>
                        <input type="text" 
                               id="jenis_tiket" 
                               name="jenis_tiket" 
                               class="form-control <?= isset($errors['jenis_tiket']) ? 'is-invalid' : '' ?>" 
                               value="<?= e($jenis_tiket) ?>" 
                               required>
                        <?php if (isset($errors['jenis_tiket'])): ?>
                            <div class="field-error"><?= e($errors['jenis_tiket']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Harga Tiket -->
                    <div class="form-group">
                        <label for="harga" class="form-label">Harga Tiket Satuan (Rupiah)</label>
                        <input type="number" 
                               id="harga" 
                               name="harga" 
                               class="form-control <?= isset($errors['harga']) ? 'is-invalid' : '' ?>" 
                               value="<?= e($harga) ?>" 
                               min="0" 
                               required>
                        <?php if (isset($errors['harga'])): ?>
                            <div class="field-error"><?= e($errors['harga']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Jumlah Tiket Awal -->
                    <div class="form-group">
                        <label for="jumlah_tiket_awal" class="form-label">Jumlah Kuota Tiket Awal</label>
                        <input type="number" 
                               id="jumlah_tiket_awal" 
                               name="jumlah_tiket_awal" 
                               class="form-control <?= isset($errors['jumlah_tiket_awal']) ? 'is-invalid' : '' ?>" 
                               value="<?= e($jumlah_tiket_awal) ?>" 
                               min="<?= $tiket_terjual ?>" 
                               required>
                        <?php if (isset($errors['jumlah_tiket_awal'])): ?>
                            <div class="field-error"><?= e($errors['jumlah_tiket_awal']) ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg">Simpan Perubahan Konser</button>
                </form>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 Bersuara - Panel Administrasi (UTS Pemrograman Web).</p>
        </div>
    </footer>

</body>
</html>
