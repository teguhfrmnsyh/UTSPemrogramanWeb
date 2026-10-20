<?php
/**
 * FR-09: Halaman Pembayaran
 * FR-10: Pembayaran Simulasi
 * BR-01: First come first served
 * BR-02: Sisa tiket berkurang saat pembayaran lunas
 * BR-03: Cek ulang sisa tiket & batas 4 tiket saat tombol "Bayar" ditekan
 * BR-04: Tolak jika tiket tidak cukup
 * BR-05: Tolak jika melebihi batas 4 tiket
 * NFR-02, NFR-03, NFR-04, NFR-05
 */

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/auth_check.php';

// NFR-05, TC-07: Wajib login
require_login('login.php');

// T5-a: Admin tidak boleh membeli tiket
if (is_admin()) {
    set_flash('error', 'Akun admin tidak dapat membeli tiket.');
    header("Location: admin/index.php");
    exit;
}

$user = auth_user();

$konser_id = filter_input(INPUT_GET, 'konser_id', FILTER_VALIDATE_INT) 
             ?: filter_input(INPUT_POST, 'konser_id', FILTER_VALIDATE_INT);
$jumlah_tiket = filter_input(INPUT_GET, 'jumlah', FILTER_VALIDATE_INT) 
                ?: filter_input(INPUT_POST, 'jumlah_tiket', FILTER_VALIDATE_INT) ?: 1;

if (!$konser_id) {
    header("Location: index.php");
    exit;
}

// Ambil data konser
$stmt = $pdo->prepare("SELECT * FROM konsers WHERE id = ?");
$stmt->execute([$konser_id]);
$konser = $stmt->fetch();

if (!$konser) {
    set_flash('error', 'Konser tidak ditemukan.');
    header("Location: index.php");
    exit;
}

// Default value kontak dari profil akun (FR-09: tapi dapat diubah)
$nama_pemesan = $_POST['nama_pemesan'] ?? $user['nama_lengkap'];
$email_pemesan = $_POST['email_pemesan'] ?? $user['email'];
$telepon_pemesan = $_POST['telepon_pemesan'] ?? $user['nomor_telepon'];
$metode_pembayaran = $_POST['metode_pembayaran'] ?? 'e-wallet';

$errors = [];

// Proses saat tombol "Bayar" ditekan (FR-10)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proses_bayar'])) {
    // T4: Verifikasi CSRF sebelum memproses apa pun
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors['transaksi'] = 'Sesi formulir tidak valid. Muat ulang halaman lalu coba lagi.';
    } else {
        $nama_pemesan = trim($_POST['nama_pemesan'] ?? '');
        $email_pemesan = trim($_POST['email_pemesan'] ?? '');
        $telepon_pemesan = trim($_POST['telepon_pemesan'] ?? '');
        $metode_pembayaran = $_POST['metode_pembayaran'] ?? '';
        $jumlah_tiket = (int)($_POST['jumlah_tiket'] ?? 1);

        // Validasi form dasar
        if (empty($nama_pemesan)) $errors['nama_pemesan'] = 'Nama pemesan wajib diisi.';
        if (empty($email_pemesan) || !filter_var($email_pemesan, FILTER_VALIDATE_EMAIL)) {
            $errors['email_pemesan'] = 'Email pemesan tidak valid.';
        }
        if (empty($telepon_pemesan)) $errors['telepon_pemesan'] = 'Nomor telepon pemesan wajib diisi.';
        if (!in_array($metode_pembayaran, ['e-wallet', 'bank'])) {
            $errors['metode_pembayaran'] = 'Pilih metode pembayaran (e-wallet atau bank).';
        }
        if ($jumlah_tiket < 1) {
            $errors['jumlah_tiket'] = 'Jumlah tiket minimal 1 lembar.';
        }

        if (empty($errors)) {
            try {
                // DATABASE TRANSACTION (Mencegah Race Condition - BR-01, BR-03)
                $pdo->beginTransaction();

                // 1. Kunci dan cek ulang sisa tiket konser terkini (FOR UPDATE)
                $stmt_lock = $pdo->prepare("SELECT id, sisa_tiket, harga, waktu_konser FROM konsers WHERE id = ? FOR UPDATE");
                $stmt_lock->execute([$konser_id]);
                $current_konser = $stmt_lock->fetch();

                if (!$current_konser) {
                    throw new Exception('Data konser tidak valid.');
                }

                // Cek apakah waktu sudah lewat (BR-08)
                if (strtotime($current_konser['waktu_konser']) < time()) {
                    throw new Exception('Konser ini telah selesai dan tidak dapat dibeli.');
                }

                // BR-04, TC-20: Cek ketersediaan tiket
                if ($jumlah_tiket > (int)$current_konser['sisa_tiket']) {
                    throw new Exception("Pesanan ditolak. Tiket tidak mencukupi (sisa tiket saat ini: {$current_konser['sisa_tiket']}).");
                }

                // BR-05, TC-19: Cek batas akumulatif 4 tiket per akun (hanya status 'lunas', SEBELUM insert pesanan baru)
                $stmt_count = $pdo->prepare("SELECT COALESCE(SUM(jumlah_tiket), 0) FROM orders WHERE user_id = ? AND konser_id = ? AND status = 'lunas' FOR UPDATE");
                $stmt_count->execute([$user['id'], $konser_id]);
                $sudah_dibeli = (int)$stmt_count->fetchColumn();

                if (($sudah_dibeli + $jumlah_tiket) > 4) {
                    throw new Exception("Pesanan ditolak. Melebihi batas maksimal 4 tiket lunas per akun (Anda sudah memiliki {$sudah_dibeli} tiket lunas).");
                }

                $total_bayar = $jumlah_tiket * (int)$current_konser['harga'];

                // Buat kode unik pesanan (FR-11)
                $kode_pesanan = 'BSR-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

                // T5-b: INSERT pesanan dengan status 'menunggu pembayaran'
                $stmt_order = $pdo->prepare("
                    INSERT INTO orders (kode_pesanan, user_id, konser_id, nama_pemesan, email_pemesan, telepon_pemesan, jumlah_tiket, total_bayar, metode_pembayaran, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'menunggu pembayaran')
                ");
                $stmt_order->execute([
                    $kode_pesanan,
                    $user['id'],
                    $konser_id,
                    $nama_pemesan,
                    $email_pemesan,
                    $telepon_pemesan,
                    $jumlah_tiket,
                    $total_bayar,
                    $metode_pembayaran
                ]);

                // T5-b: Ambil lastInsertId()
                $order_id = $pdo->lastInsertId();

                // T5-b: UPDATE konsers SET sisa_tiket = sisa_tiket - ? WHERE id = ? AND sisa_tiket >= ?
                $stmt_update = $pdo->prepare("UPDATE konsers SET sisa_tiket = sisa_tiket - ? WHERE id = ? AND sisa_tiket >= ?");
                $stmt_update->execute([$jumlah_tiket, $konser_id, $jumlah_tiket]);

                if ($stmt_update->rowCount() !== 1) {
                    throw new Exception("Pesanan ditolak. Tiket tidak mencukupi (sisa tiket saat ini: {$current_konser['sisa_tiket']}).");
                }

                // T5-b: UPDATE orders SET status = 'lunas' WHERE id = ?
                $stmt_lunas = $pdo->prepare("UPDATE orders SET status = 'lunas' WHERE id = ?");
                $stmt_lunas->execute([$order_id]);

                // Selesaikan transaksi
                $pdo->commit();

                set_flash('success', 'Pembayaran berhasil dikonfirmasi! Tiket digital Anda telah terbit.');
                header("Location: tiket.php?id={$order_id}");
                exit;

            } catch (PDOException $ex) {
                // T5-c: Tangkap PDOException
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                error_log($ex->getMessage());
                $errors['transaksi'] = 'Terjadi kesalahan pada sistem saat memproses pembayaran. Silakan coba lagi.';
            } catch (Exception $ex) {
                // T5-c: Tangkap Exception aturan bisnis
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $errors['transaksi'] = $ex->getMessage();
            }
        }
    }
}

$total_harga = $jumlah_tiket * (int)$konser['harga'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Tiket - Bersuara</title>
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
                <li><a href="riwayat.php">Riwayat Pesanan</a></li>
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
        <div class="page-actions">
            <a href="detail.php?id=<?= $konser['id'] ?>" class="btn btn-sm btn-secondary">Kembali ke Detail Konser</a>
        </div>

        <nav class="stepper">
            <div class="stepper-item is-done">Pilih Konser</div>
            <div class="stepper-item is-active">Pembayaran</div>
            <div class="stepper-item">E-Tiket</div>
        </nav>

        <!-- Banner Informasi Simulasi (FR-10, TC-18) -->
        <div class="alert alert-info">
            <strong>Simulasi Pembayaran (Tugas UTS Web):</strong>
            Sistem ini menggunakan pembayaran simulasi instan. Sistem <strong>TIDAK</strong> akan meminta nomor kartu kredit, nomor rekening, atau PIN sungguhan. Menekan tombol "Bayar Tiket Sekarang" akan langsung mengesahkan pesanan menjadi <strong>LUNAS</strong>.
        </div>

        <?php if (isset($errors['transaksi'])): ?>
            <div class="alert alert-danger">
                <span><?= e($errors['transaksi']) ?></span>
            </div>
        <?php endif; ?>

        <form action="pembayaran.php" method="POST">
            <input type="hidden" name="konser_id" value="<?= $konser['id'] ?>">
            <input type="hidden" name="proses_bayar" value="1">
            <?= csrf_field() ?>

            <div class="detail-layout detail-layout-split">
                <!-- Kolom Kiri: Form Data Pemesan & Metode Pembayaran (FR-09) -->
                <div class="panel-card form-stack">
                    <h2>Data Pemesan & Kontak Tiket</h2>
                    <p class="text-muted">
                        Data awal terisi dari akun Anda. Anda dapat mengubahnya jika tiket diperuntukkan atas nama orang lain.
                    </p>

                    <!-- Nama Pemesan -->
                    <div class="form-group">
                        <label for="nama_pemesan" class="form-label">Nama Pemesan (Sesuai KTP)</label>
                        <input type="text" 
                               id="nama_pemesan" 
                               name="nama_pemesan" 
                               class="form-control <?= isset($errors['nama_pemesan']) ? 'is-invalid' : '' ?>" 
                               value="<?= e($nama_pemesan) ?>" 
                               required>
                        <?php if (isset($errors['nama_pemesan'])): ?>
                            <div class="field-error"><?= e($errors['nama_pemesan']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Email Pemesan -->
                    <div class="form-group">
                        <label for="email_pemesan" class="form-label">Email Penerima Tiket</label>
                        <input type="email" 
                               id="email_pemesan" 
                               name="email_pemesan" 
                               class="form-control <?= isset($errors['email_pemesan']) ? 'is-invalid' : '' ?>" 
                               value="<?= e($email_pemesan) ?>" 
                               required>
                        <?php if (isset($errors['email_pemesan'])): ?>
                            <div class="field-error"><?= e($errors['email_pemesan']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Nomor Telepon -->
                    <div class="form-group">
                        <label for="telepon_pemesan" class="form-label">Nomor WhatsApp Aktif</label>
                        <input type="tel" 
                               id="telepon_pemesan" 
                               name="telepon_pemesan" 
                               class="form-control <?= isset($errors['telepon_pemesan']) ? 'is-invalid' : '' ?>" 
                               value="<?= e($telepon_pemesan) ?>" 
                               required>
                        <?php if (isset($errors['telepon_pemesan'])): ?>
                            <div class="field-error"><?= e($errors['telepon_pemesan']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Metode Pembayaran Simulasi: e-wallet atau bank (FR-09) -->
                    <h3>Pilih Metode Pembayaran</h3>
                    <div class="payment-methods-grid">
                        <label>
                            <input type="radio" name="metode_pembayaran" value="e-wallet" class="payment-radio" <?= $metode_pembayaran === 'e-wallet' ? 'checked' : '' ?>>
                            <div class="payment-label-box">
                                <strong class="payment-method-title">E-Wallet</strong>
                                <span class="payment-method-desc">GoPay / OVO / Dana / QRIS</span>
                            </div>
                        </label>

                        <label>
                            <input type="radio" name="metode_pembayaran" value="bank" class="payment-radio" <?= $metode_pembayaran === 'bank' ? 'checked' : '' ?>>
                            <div class="payment-label-box">
                                <strong class="payment-method-title">Transfer Bank</strong>
                                <span class="payment-method-desc">BCA / Mandiri / BNI / BRI</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Kolom Kanan: Ringkasan Pesanan (FR-09) -->
                <div class="panel-card">
                    <h2>Ringkasan Pesanan</h2>

                    <div>
                        <div class="artist-name"><?= e($konser['artis']) ?></div>
                        <h3 class="concert-title"><?= e($konser['nama_konser']) ?></h3>
                        <p class="text-muted">
                            <?= e($konser['lokasi']) ?> | <?= format_tanggal($konser['waktu_konser']) ?>
                        </p>
                    </div>

                    <div class="summary-row">
                        <span class="text-muted">Jenis Tiket</span>
                        <strong><?= e($konser['jenis_tiket']) ?></strong>
                    </div>

                    <div class="summary-row">
                        <span class="text-muted">Harga Satuan</span>
                        <span id="harga-satuan" data-price="<?= $konser['harga'] ?>">
                            <?= format_rupiah($konser['harga']) ?>
                        </span>
                    </div>

                    <div class="summary-row">
                        <span class="text-muted">Jumlah Tiket</span>
                        <div>
                            <input type="number" 
                                   id="jumlah-tiket" 
                                   name="jumlah_tiket" 
                                   class="form-control" 
                                   min="1" 
                                   max="<?= (int)$konser['sisa_tiket'] ?>" 
                                   value="<?= $jumlah_tiket ?>" 
                                   required>
                        </div>
                    </div>

                    <div class="summary-row total-row">
                        <span>Total Pembayaran</span>
                        <span id="total-harga-display"><?= format_rupiah($total_harga) ?></span>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary btn-block btn-lg">
                            Bayar Tiket Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p class="site-footer-text">Bersuara. Simulasi pembelian tiket konser untuk tugas UTS Pemrograman Web.</p>
        </div>
    </footer>

    <script src="assets/script.js"></script>
</body>
</html>
