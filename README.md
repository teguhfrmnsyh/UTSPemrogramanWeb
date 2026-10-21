# Bersuara — Website Tiket Konser

Repository ini merupakan proyek **Ujian Tengah Semester (UTS) mata kuliah Pemrograman Web** oleh mahasiswa Horizon University Indonesia, Faculty of Information and Technology.

## Identitas Kelompok

**Anggota 1**
- **Nama:** Muhammad Fauzan Putra
- **NIM:** 4112755201250160

**Anggota 2**
- **Nama:** Teguh Firmansyah S.P
- **NIM:** 4112755201250068

## Deskripsi Proyek

**Bersuara** adalah website pemesanan tiket konser untuk wilayah Jabodetabek. Website ini dirancang untuk memudahkan pengguna menemukan informasi konser, membeli tiket, melakukan pembayaran simulasi, dan mengakses tiket digital melalui satu platform.

Proyek ini dibuat untuk memenuhi tugas Ujian Tengah Semester mata kuliah Pemrograman Web. Seluruh proses pembayaran hanya berupa simulasi dan tidak melibatkan transaksi uang sungguhan.

## Tujuan

- Menyediakan platform informasi dan pemesanan tiket konser.
- Memudahkan pengguna mencari dan melihat detail konser.
- Memungkinkan pembelian tiket melalui sistem pembayaran simulasi.
- Menyediakan tiket digital dan riwayat pesanan.
- Memungkinkan admin mengelola data konser.

## Fitur Utama

### Pembeli
- Registrasi, login, dan logout.
- Melihat daftar dan detail konser.
- Mencari konser berdasarkan nama artis.
- Memfilter konser berdasarkan lokasi dan tanggal.
- Membeli tiket dengan batas maksimal empat tiket lunas per akun untuk setiap konser.
- Melakukan pembayaran simulasi melalui pilihan e-wallet atau bank.
- Mengakses tiket digital setelah pembayaran lunas.
- Melihat riwayat pesanan.

### Admin
- Login melalui halaman login yang sama dengan pembeli.
- Mengakses dashboard admin berdasarkan peran akun.
- Melihat daftar konser.
- Menambahkan konser baru.
- Mengubah informasi konser.
- Menghapus konser sesuai ketentuan sistem.
- Melihat daftar pesanan jika fitur tambahan berhasil diimplementasikan.

## Teknologi yang Digunakan

- **HTML** — struktur halaman website.
- **CSS** — desain dan tata letak website.
- **JavaScript** — interaktivitas halaman.
- **PHP** — pemrosesan logika aplikasi.
- **MySQL** — penyimpanan data.
- **phpMyAdmin** — pengelolaan database.
- **Laragon** — lingkungan pengembangan lokal.
- **Visual Studio Code** — editor kode.

## Instalasi dan Menjalankan Proyek

### 1. Persiapan

Pastikan perangkat telah memiliki:
- Laragon
- Visual Studio Code
- Browser web

### 2. Clone Repository

Jalankan perintah berikut melalui terminal:

```bash
git clone <URL_REPOSITORY>
```

Ganti `<URL_REPOSITORY>` dengan URL repository Git yang digunakan.

### 3. Pindahkan Folder Proyek

Letakkan folder proyek di direktori `www` milik Laragon, misalnya:

```text
C:\laragon\www\UTSPemrogramanWeb
```

Sesuaikan lokasi folder dengan instalasi Laragon di perangkat masing-masing.

### 4. Jalankan Laragon

Buka Laragon, kemudian jalankan layanan web server dan database MySQL.

### 5. Konfigurasi Database

1. Buka phpMyAdmin melalui lingkungan Laragon.
2. Buat database untuk proyek Bersuara.
3. Impor file SQL database proyek jika sudah tersedia.
4. Sesuaikan konfigurasi koneksi database pada file konfigurasi PHP.

> Nama database, nama file konfigurasi, dan file SQL mengikuti implementasi akhir proyek.

### 6. Jalankan Website

Buka browser dan akses alamat lokal sesuai konfigurasi Laragon, misalnya:

```text
http://localhost/UTSPemrogramanWeb/
```

Pastikan alamat dan struktur folder telah disesuaikan dengan proyek.

## Ketentuan Sistem

- Registrasi hanya tersedia untuk akun pembeli.
- Akun admin dibuat secara langsung melalui database.
- Kata sandi disimpan dalam bentuk hash.
- Query database menggunakan prepared statement.
- Halaman yang membutuhkan autentikasi dilindungi session.
- Pembayaran hanya berupa simulasi.
- Sistem tidak meminta data kartu, rekening, atau PIN sungguhan.
- Konser yang telah selesai tidak dapat dibeli.
- Tiket yang telah lunas dapat diakses melalui tiket digital dan riwayat pesanan.

## Pengujian

Pengujian dilakukan untuk memastikan fitur berjalan sesuai kebutuhan, meliputi:

- Registrasi dan validasi akun.
- Login, logout, dan pembatasan akses.
- Pencarian dan filter konser.
- Pembelian serta pembayaran simulasi.
- Validasi jumlah dan ketersediaan tiket.
- Pembuatan tiket digital dan riwayat pesanan.
- Pengelolaan konser oleh admin.
- Responsivitas tampilan pada perangkat HP, tablet, dan laptop.
- Keamanan dasar aplikasi dan koneksi database.

## Informasi Akademik

| Informasi | Keterangan |
|---|---|
| Universitas | Horizon University Indonesia |
| Fakultas | Faculty of Information and Technology |
| Mata Kuliah | Pemrograman Web |
| Jenis Proyek | Ujian Tengah Semester (UTS) |
| Nama Proyek | Bersuara |
| Repository | UTSPemrogramanWeb |

## Catatan

Proyek ini dibuat untuk keperluan akademik dan pembelajaran. Website bukan platform penjualan tiket komersial dan tidak memproses pembayaran sungguhan.

---

**Bersuara — Satu Platform, Beragam Konser.**
