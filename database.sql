-- ========================================================
-- PRD Bersuara - Database Schema & Initial Data
-- UTS Pemrograman Web
-- DBMS: MySQL / MariaDB (Laragon & phpMyAdmin)
-- ========================================================

CREATE DATABASE IF NOT EXISTS bersuara CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bersuara;

-- --------------------------------------------------------
-- 1. Tabel users
-- NFR-02: Kata sandi disimpan dalam bentuk hash
-- BR-10: Akun admin dibuat langsung di database, bukan registrasi
-- --------------------------------------------------------
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS konsers;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    nomor_telepon VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('pembeli', 'admin') NOT NULL DEFAULT 'pembeli',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Tabel konsers
-- BR-09: Setiap konser memiliki satu jenis tiket, satu harga, dan satu sisa tiket
-- --------------------------------------------------------
CREATE TABLE konsers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_konser VARCHAR(150) NOT NULL,
    artis VARCHAR(100) NOT NULL,
    lokasi VARCHAR(100) NOT NULL,
    waktu_konser DATETIME NOT NULL,
    harga INT UNSIGNED NOT NULL,
    jenis_tiket VARCHAR(50) NOT NULL DEFAULT 'General Admission',
    poster VARCHAR(255) NULL DEFAULT NULL,
    jumlah_tiket_awal INT UNSIGNED NOT NULL,
    sisa_tiket INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. Tabel orders
-- FR-08: Batas 4 tiket lunas per akun per konser
-- FR-09: Nama, email, telepon pemesan disimpan per transaksi
-- FR-10: Status 'menunggu pembayaran' -> 'lunas'
-- FR-17: ON DELETE RESTRICT agar konser dengan pesanan lunas tidak bisa dihapus
-- --------------------------------------------------------
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_pesanan VARCHAR(30) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    konser_id INT NOT NULL,
    nama_pemesan VARCHAR(100) NOT NULL,
    email_pemesan VARCHAR(100) NOT NULL,
    telepon_pemesan VARCHAR(20) NOT NULL,
    jumlah_tiket INT UNSIGNED NOT NULL,
    total_bayar INT UNSIGNED NOT NULL,
    metode_pembayaran ENUM('e-wallet', 'bank') NOT NULL,
    status ENUM('menunggu pembayaran', 'lunas') NOT NULL DEFAULT 'menunggu pembayaran',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_orders_konser FOREIGN KEY (konser_id) REFERENCES konsers(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- DATA SEEDER / DATA AWAL
-- ========================================================

-- Akun Default:
-- Admin: admin@bersuara.com | Password: admin123 (hash password_hash BCRYPT)
-- Pembeli: pembeli@bersuara.com | Password: password123 (hash password_hash BCRYPT)
INSERT INTO users (id, nama_lengkap, email, nomor_telepon, password, role) VALUES
(1, 'Admin Bersuara', 'admin@bersuara.com', '081234567890', '$2y$10$GdG.SuxMC3LscrzqWhWAdu.Buhg.lN2uDTSHw03JgFfcwFvzU393S', 'admin'),
(2, 'Budi Santoso', 'pembeli@bersuara.com', '081987654321', '$2y$10$1gCvDtFIE3s3IzyDRiI9oum3tVOkX65WjugB45mWIF8Uxb3VYDrPe', 'pembeli');

-- 10 Data Konser Contoh (Mencakup Jabodetabek, Aktif, Habis [TC-12], dan Selesai [TC-11]):
INSERT INTO konsers (id, nama_konser, artis, lokasi, waktu_konser, harga, jenis_tiket, poster, jumlah_tiket_awal, sisa_tiket) VALUES
-- 1. Konser Aktif - Jakarta
(1, 'Tulus Tur Manusia 2026', 'Tulus', 'Jakarta', '2026-11-14 19:30:00', 350000, 'Festival A', 'tulus.jpg', 150, 85),

-- 2. Konser Habis (TC-12: sisa_tiket = 0, label "Habis", tombol nonaktif)
(2, 'Tunggu Aku Di Jakarta', 'Sheila on 7', 'Jakarta', '2026-11-20 20:00:00', 450000, 'Festival', 'sheila-on-7.jpg', 200, 0),

-- 3. Konser Selesai (TC-11: waktu lewat, label "Selesai", tampilan gelap, larangan beli)
(3, 'Pesta Rakyat 30 Tahun Berkarya', 'Dewa 19', 'Jakarta', '2026-09-15 19:00:00', 500000, 'VIP Silver', 'dewa-19.jpeg', 250, 0),

-- 4. Konser Aktif - Tangerang
(4, 'Menari Bersama Maliq Tour', 'Maliq & D''Essentials', 'Tangerang', '2026-11-28 19:30:00', 300000, 'Presale Festival', 'maliq-dessentials.jpg', 100, 42),

-- 5. Konser Aktif - Bogor
(5, 'Lagipula Hidup Akan Berakhir Live', 'Hindia', 'Bogor', '2026-12-05 19:00:00', 275000, 'General Admission', 'hindia.jpg', 120, 60),

-- 6. Konser Aktif - Depok
(6, 'Sialnya, Hidup Harus Tetap Berjalan', 'Bernadya', 'Depok', '2026-11-18 19:00:00', 225000, 'Festival Musik', 'bernadya.jpeg', 80, 25),

-- 7. Konser Aktif - Bekasi
(7, 'Membangun & Menghancurkan Tour', '.Feast', 'Bekasi', '2026-12-12 20:00:00', 200000, 'Moshpit Festival', 'feast.jpg', 100, 70),

-- 8. Konser Aktif - Tangerang (ICE BSD)
(8, 'Birdy South East Asia Tour', 'Pamungkas', 'Tangerang', '2026-12-19 19:30:00', 320000, 'Cat 1 Center', 'pamungkas.jpg', 150, 110),

-- 9. Konser Aktif - Jakarta (GBK)
(9, 'Kita Bersama Berkelana Live', 'Nadin Amizah', 'Jakarta', '2026-12-23 18:30:00', 290000, 'Duduk Romantis', 'nadin-amizah.jpeg', 90, 35),

-- 10. Konser Aktif - Bogor (SICC)
(10, 'LEXICON & Beyond Orchestral', 'Isyana Sarasvati', 'Bogor', '2026-12-30 20:00:00', 600000, 'Symphony VIP', 'isyana-sarasvati.jpg', 80, 50);
