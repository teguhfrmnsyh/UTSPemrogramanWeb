<?php
/**
 * FR-03: Login & Logout
 * FR-13: Admin login melalui halaman yang sama dan diarahkan berdasarkan peran
 * NFR-02: Kata sandi diverifikasi via password_verify
 * NFR-03: Prepared statement PDO
 * NFR-04: Escaping data
 * NFR-05: Session protection
 */

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/auth_check.php';

// Jika sudah login, redirect sesuai peran
if (is_logged_in()) {
    if (is_admin()) {
        header("Location: admin/index.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email dan kata sandi wajib diisi.';
    } else {
        $stmt = $pdo->prepare("SELECT id, nama_lengkap, email, nomor_telepon, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Login sukses, simpan session
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id'            => $user['id'],
                'nama_lengkap'  => $user['nama_lengkap'],
                'email'         => $user['email'],
                'nomor_telepon' => $user['nomor_telepon'],
                'role'          => $user['role']
            ];

            // FR-13: Arahkan berdasarkan peran
            if ($user['role'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                // Arahkan ke halaman tujuan sebelumnya jika ada (misal redirect beli)
                $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
                unset($_SESSION['redirect_after_login']);
                header("Location: {$redirect}");
            }
            exit;
        } else {
            $error = 'Email atau kata sandi tidak cocok.';
        }
    }
}

$flash_success = get_flash('success');
$flash_error = get_flash('error');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Bersuara Tiket Konser</title>
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
                <a href="register.php" class="btn btn-sm btn-outline">Belum punya akun? Daftar</a>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="auth-wrapper">
            <div class="auth-card">
                <div class="auth-header">
                    <h1>Selamat Datang</h1>
                    <p>Masuk ke akun pembeli atau admin Bersuara</p>
                </div>

                <?php if ($flash_success): ?>
                    <div class="alert alert-success">
                        <span><?= e($flash_success) ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($flash_error || $error): ?>
                    <div class="alert alert-danger">
                        <span><?= e($flash_error ?: $error) ?></span>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST" class="form-stack">
                    <div class="form-group">
                        <label for="email" class="form-label">Alamat Email</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-control" 
                               value="<?= e($email) ?>" 
                               placeholder="nama@email.com" 
                               required 
                               autofocus>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Kata Sandi</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control" 
                               placeholder="Masukkan kata sandi" 
                               required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg">Masuk ke Akun</button>
                </form>

                <div class="auth-test-accounts">
                    <strong>Akun Contoh untuk Pengujian UTS:</strong><br>
                    <strong>Admin:</strong> admin@bersuara.com (pass: <code>admin123</code>)<br>
                    <strong>Pembeli:</strong> pembeli@bersuara.com (pass: <code>password123</code>)
                </div>

                <div class="text-muted">
                    Belum punya akun? <a href="register.php"><strong>Daftar</strong></a>
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
