<?php
/**
 * Konfigurasi Koneksi Database Bersuara
 * NFR-03: Query database memakai prepared statement (PDO)
 * NFR-06: Website berjalan melalui Laragon localhost
 */

date_default_timezone_set('Asia/Jakarta');

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'bersuara';
$db_port = 3306;

try {
    $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
    $pdo->exec("SET time_zone = '+07:00'");
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage() . "<br><small>Pastikan MySQL di Laragon sudah dinyalakan dan database 'bersuara' telah diimpor dari database.sql.</small>");
}
