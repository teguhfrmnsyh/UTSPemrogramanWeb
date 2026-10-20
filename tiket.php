<?php
/**
 * FR-11: Tiket Digital
 * Menampilkan kode pesanan unik, nama pembeli, detail konser, jumlah tiket, total bayar, status lunas
 * PENTING: KODE QR TIDAK DIGUNAKAN (sesuai batasan PRD)
 * NFR-04: Escaping output
 * NFR-05: Session protection
 */

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/auth_check.php';

require_login('login.php');

$order_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$order_id) {
    header("Location: index.php");
    exit;
}

$user = auth_user();

// Ambil data order beserta konser terkait
$sql = "SELECT o.*, k.nama_konser, k.artis, k.lokasi, k.waktu_konser, k.jenis_tiket 
        FROM orders o
        JOIN konsers k ON o.konser_id = k.id
        WHERE o.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    set_flash('error', 'Tiket pesanan tidak ditemukan.');
    header("Location: riwayat.php");
    exit;
}

// Hak akses: pembeli hanya dapat melihat tiket miliknya sendiri, kecuali jika admin
if (!is_admin() && (int)$order['user_id'] !== (int)$user['id']) {
    set_flash('error', 'Akses ditolak. Anda tidak berhak melihat tiket ini.');
    header("Location: riwayat.php");
    exit;
}

$flash_success = get_flash('success');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Tiket #<?= e($order['kode_pesanan']) ?> - Bersuara</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <header class="navbar no-print">
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
        <div class="no-print page-actions">
            <a href="riwayat.php" class="btn btn-sm btn-secondary">Kembali ke Riwayat Pesanan</a>
            <button onclick="window.print()" class="btn btn-sm btn-primary">Cetak / Simpan PDF</button>
        </div>

        <nav class="stepper no-print">
            <div class="stepper-item is-done">Pilih Konser</div>
            <div class="stepper-item is-done">Pembayaran</div>
            <div class="stepper-item is-active">E-Tiket</div>
        </nav>

        <?php if ($flash_success): ?>
            <div class="alert alert-success no-print">
                <span><?= e($flash_success) ?></span>
            </div>
        <?php endif; ?>

        <!-- FR-11: KARTU TIKET DIGITAL (TANPA QR CODE) -->
        <div class="ticket-wrapper">
            <div class="ticket-card">
                <!-- Header Tiket -->
                <div class="ticket-header">
                    <div>
                        <div class="ticket-brand">Bersuara</div>
                        <small>E-Tiket Konser</small>
                    </div>
                    <div class="ticket-badge">
                        Status: <?= strtoupper(e($order['status'])) ?>
                    </div>
                </div>

                <!-- Isi Tiket -->
                <div class="ticket-body">
                    <!-- Kode Tiket Unik -->
                    <div class="ticket-code-display">
                        <?= e($order['kode_pesanan']) ?>
                    </div>

                    <!-- Informasi Konser & Pemesan -->
                    <div class="ticket-info-grid">
                        <div class="ticket-field">
                            <label>Nama Konser</label>
                            <p><?= e($order['nama_konser']) ?></p>
                        </div>
                        <div class="ticket-field">
                            <label>Artis</label>
                            <p><?= e($order['artis']) ?></p>
                        </div>
                        <div class="ticket-field">
                            <label>Waktu Penyelenggaraan</label>
                            <p><?= format_tanggal($order['waktu_konser']) ?></p>
                        </div>
                        <div class="ticket-field">
                            <label>Lokasi Acara</label>
                            <p><?= e($order['lokasi']) ?>, Jabodetabek</p>
                        </div>
                    </div>

                    <!-- Pembatas Tiket Perforasi -->
                    <div class="ticket-divider"></div>

                    <div class="ticket-info-grid">
                        <div class="ticket-field">
                            <label>Nama Pemesan</label>
                            <p><?= e($order['nama_pemesan']) ?></p>
                        </div>
                        <div class="ticket-field">
                            <label>Email & Telepon</label>
                            <p><?= e($order['email_pemesan']) ?> (<?= e($order['telepon_pemesan']) ?>)</p>
                        </div>
                        <div class="ticket-field">
                            <label>Kategori Tiket</label>
                            <p><?= e($order['jenis_tiket']) ?></p>
                        </div>
                        <div class="ticket-field">
                            <label>Jumlah Tiket</label>
                            <p><?= (int)$order['jumlah_tiket'] ?> Lembar</p>
                        </div>
                        <div class="ticket-field">
                            <label>Metode Pembayaran</label>
                            <p><?= strtoupper(e($order['metode_pembayaran'])) ?> (Simulasi)</p>
                        </div>
                        <div class="ticket-field">
                            <label>Total Lunas</label>
                            <p><?= format_rupiah($order['total_bayar']) ?></p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <footer class="footer no-print">
        <div class="container">
            <p>&copy; 2026 Bersuara - Platform Tiket Konser Jabodetabek (UTS Pemrograman Web).</p>
        </div>
    </footer>

</body>
</html>
