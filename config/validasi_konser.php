<?php
/**
 * FR-18: Validasi Data Konser Bersuara
 * Digunakan bersama oleh admin/tambah.php dan admin/ubah.php
 */

if (!defined('MAX_HARGA_KONSER')) {
    define('MAX_HARGA_KONSER', 100000000);
}
if (!defined('MAX_JUMLAH_TIKET')) {
    define('MAX_JUMLAH_TIKET', 100000);
}

/**
 * Memvalidasi data input konser
 *
 * @param array $input Data mentah dari $_POST
 * @return array ['errors' => array, 'data' => array]
 */
function validasi_data_konser(array $input): array {
    $errors = [];
    $data = [];

    // Opsi lokasi yang tersedia di form tambah/ubah
    $lokasi_valid = ['Jakarta', 'Bogor', 'Depok', 'Tangerang', 'Bekasi'];

    // Trim seluruh input
    $nama_konser = trim($input['nama_konser'] ?? '');
    $artis = trim($input['artis'] ?? '');
    $lokasi = trim($input['lokasi'] ?? '');
    $waktu_konser = trim($input['waktu_konser'] ?? '');
    $harga = trim((string)($input['harga'] ?? ''));
    $jenis_tiket = trim($input['jenis_tiket'] ?? '');
    $jumlah_tiket_awal = trim((string)($input['jumlah_tiket_awal'] ?? ''));

    // Validasi nama konser (max 150 karakter)
    if ($nama_konser === '') {
        $errors['nama_konser'] = 'Nama konser wajib diisi.';
    } elseif (mb_strlen($nama_konser) > 150) {
        $errors['nama_konser'] = 'Nama konser tidak boleh melebihi 150 karakter.';
    } else {
        $data['nama_konser'] = $nama_konser;
    }

    // Validasi artis / band (max 100 karakter)
    if ($artis === '') {
        $errors['artis'] = 'Nama artis / band wajib diisi.';
    } elseif (mb_strlen($artis) > 100) {
        $errors['artis'] = 'Nama artis / band tidak boleh melebihi 100 karakter.';
    } else {
        $data['artis'] = $artis;
    }

    // Validasi lokasi (max 100 karakter, harus salah satu opsi select)
    if ($lokasi === '') {
        $errors['lokasi'] = 'Lokasi konser wajib dipilih.';
    } elseif (!in_array($lokasi, $lokasi_valid, true) || mb_strlen($lokasi) > 100) {
        $errors['lokasi'] = 'Lokasi konser tidak valid.';
    } else {
        $data['lokasi'] = $lokasi;
    }

    // Validasi waktu konser (DateTime round-trip)
    if ($waktu_konser === '') {
        $errors['waktu_konser'] = 'Waktu konser wajib diisi.';
    } else {
        $valid_dt = null;
        $formats = ['Y-m-d\TH:i:s', 'Y-m-d\TH:i', 'Y-m-d H:i:s'];
        foreach ($formats as $fmt) {
            $dt = DateTime::createFromFormat($fmt, $waktu_konser);
            if ($dt && $dt->format($fmt) === $waktu_konser) {
                $valid_dt = $dt;
                break;
            }
        }
        if (!$valid_dt) {
            $errors['waktu_konser'] = 'Format waktu konser tidak valid.';
        } else {
            $data['waktu_konser'] = $valid_dt->format('Y-m-d H:i:s');
        }
    }

    // Validasi harga (hanya digit, tidak negatif, batas atas MAX_HARGA_KONSER)
    if ($harga === '') {
        $errors['harga'] = 'Harga tiket wajib diisi.';
    } elseif (!ctype_digit($harga)) {
        $errors['harga'] = 'Harga tiket harus berupa angka bulat dan tidak boleh negatif.';
    } elseif ((int)$harga > MAX_HARGA_KONSER) {
        $errors['harga'] = 'Harga tiket tidak boleh melebihi ' . MAX_HARGA_KONSER . '.';
    } else {
        $data['harga'] = (int)$harga;
    }

    // Validasi jenis tiket (max 50 karakter)
    if ($jenis_tiket === '') {
        $errors['jenis_tiket'] = 'Jenis tiket wajib diisi.';
    } elseif (mb_strlen($jenis_tiket) > 50) {
        $errors['jenis_tiket'] = 'Jenis tiket tidak boleh melebihi 50 karakter.';
    } else {
        $data['jenis_tiket'] = $jenis_tiket;
    }

    // Validasi jumlah tiket awal (hanya digit, tidak negatif, batas atas MAX_JUMLAH_TIKET)
    if ($jumlah_tiket_awal === '') {
        $errors['jumlah_tiket_awal'] = 'Jumlah tiket awal wajib diisi.';
    } elseif (!ctype_digit($jumlah_tiket_awal)) {
        $errors['jumlah_tiket_awal'] = 'Jumlah tiket awal harus berupa angka bulat dan tidak boleh negatif.';
    } elseif ((int)$jumlah_tiket_awal > MAX_JUMLAH_TIKET) {
        $errors['jumlah_tiket_awal'] = 'Jumlah tiket awal tidak boleh melebihi ' . MAX_JUMLAH_TIKET . '.';
    } else {
        $data['jumlah_tiket_awal'] = (int)$jumlah_tiket_awal;
    }

    return [
        'errors' => $errors,
        'data'   => $data
    ];
}
