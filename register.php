<?php
/**
 * FR-01: Registrasi Pembeli
 * FR-02: Validasi registrasi (error tampil di bawah masing-masing kolom)
 * NFR-02: Kata sandi disimpan dalam bentuk hash
 * NFR-03: Prepared statement PDO
 * NFR-04: Escaping data
 */

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/auth_check.php';

// Jika sudah login, redirect
if (is_logged_in()) {
    header("Location: index.php");
    exit;
}

$errors = [];
$nama_lengkap = '';
$email = '';
$nomor_telepon = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $nomor_telepon = trim($_POST['nomor_telepon'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

    // Validasi FR-02: Semua kolom wajib diisi
    if (empty($nama_lengkap)) {
        $errors['nama_lengkap'] = 'Nama lengkap wajib diisi.';
    }

    if (empty($email)) {
        $errors['email'] = 'Email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email tidak valid.';
    } else {
        // Cek email sudah terdaftar
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors['email'] = 'Email ini sudah terdaftar. Silakan gunakan email lain.';
        }
    }

    if (empty($nomor_telepon)) {
        $errors['nomor_telepon'] = 'Nomor telepon wajib diisi.';
    }

    if (empty($password)) {
        $errors['password'] = 'Kata sandi wajib diisi.';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Kata sandi minimal 8 karakter.';
    }

    if (empty($konfirmasi_password)) {
        $errors['konfirmasi_password'] = 'Konfirmasi kata sandi wajib diisi.';
    } elseif ($password !== $konfirmasi_password) {
        $errors['konfirmasi_password'] = 'Konfirmasi kata sandi tidak cocok.';
    }

    // Jika tidak ada error, buat akun pembeli
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $role = 'pembeli'; // FR-01: Registrasi hanya membuat akun pembeli

        $stmt = $pdo->prepare("INSERT INTO users (nama_lengkap, email, nomor_telepon, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nama_lengkap, $email, $nomor_telepon, $password_hash, $role]);

        set_flash('success', 'Pendaftaran berhasil! Silakan login untuk melanjutkan.');
        header("Location: login.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Pembeli - Bersuara Tiket Konser</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <!-- Header Navbar Ringkas -->
    <header class="navbar">
        <div class="container">
            <a href="index.php" class="brand-logo">
                <div class="brand-text">Bersuara</div>
            </a>
            <div class="nav-actions">
                <a href="login.php" class="btn btn-sm btn-outline">Sudah punya akun? Masuk</a>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="auth-wrapper">
            <div class="auth-card">
                <div class="auth-header">
                    <h1>Daftar Akun</h1>
                    <p>Buat akun pembeli untuk membeli tiket.</p>
                </div>

                <form action="register.php" method="POST" class="form-stack" novalidate>
                    <!-- Nama Lengkap -->
                    <div class="form-group">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                        <input type="text" 
                               id="nama_lengkap" 
                               name="nama_lengkap" 
                               class="form-control <?= isset($errors['nama_lengkap']) ? 'is-invalid' : '' ?>" 
                               value="<?= e($nama_lengkap) ?>" 
                               placeholder="Contoh: Budi Santoso"
                               required>
                        <?php if (isset($errors['nama_lengkap'])): ?>
                            <div class="field-error"><?= e($errors['nama_lengkap']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="form-label">Alamat Email</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                               value="<?= e($email) ?>" 
                               placeholder="nama@email.com"
                               required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="field-error"><?= e($errors['email']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Nomor Telepon -->
                    <div class="form-group">
                        <label for="nomor_telepon" class="form-label">Nomor Telepon (WhatsApp)</label>
                        <input type="tel" 
                               id="nomor_telepon" 
                               name="nomor_telepon" 
                               class="form-control <?= isset($errors['nomor_telepon']) ? 'is-invalid' : '' ?>" 
                               value="<?= e($nomor_telepon) ?>" 
                               placeholder="081234567890"
                               required>
                        <?php if (isset($errors['nomor_telepon'])): ?>
                            <div class="field-error"><?= e($errors['nomor_telepon']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Kata Sandi -->
                    <div class="form-group">
                        <label for="password" class="form-label">Kata Sandi (Minimal 8 Karakter)</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                               placeholder="Minimal 8 karakter"
                               required>
                        <?php if (isset($errors['password'])): ?>
                            <div class="field-error"><?= e($errors['password']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Konfirmasi Kata Sandi -->
                    <div class="form-group">
                        <label for="konfirmasi_password" class="form-label">Konfirmasi Kata Sandi</label>
                        <input type="password" 
                               id="konfirmasi_password" 
                               name="konfirmasi_password" 
                               class="form-control <?= isset($errors['konfirmasi_password']) ? 'is-invalid' : '' ?>" 
                               placeholder="Ulangi kata sandi"
                               required>
                        <?php if (isset($errors['konfirmasi_password'])): ?>
                            <div class="field-error"><?= e($errors['konfirmasi_password']) ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg">Daftar Sekarang</button>
                </form>

                <div class="text-muted">
                    Sudah memiliki akun? <a href="login.php"><strong>Masuk di sini</strong></a>
                </div>
            </div>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p class="site-footer-text">Bersuara. Simulasi pembelian tiket konser untuk tugas UTS Pemrograman Web.</p>
        </div>
    </footer>

</body>
</html>
