# PRD Bersuara

## 1. Ringkasan produk

Bersuara adalah website tiket konser untuk wilayah Jabodetabek. Pengguna dapat mendaftar sebagai pembeli, login, melihat daftar konser, membeli tiket, melakukan pembayaran simulasi, dan membuka tiket digital.

Admin menggunakan halaman login yang sama dengan pembeli. Sistem mengarahkan admin ke halaman admin berdasarkan perannya. Admin dapat mengelola data konser. Bersuara adalah tugas UTS mata kuliah Pemrograman Web yang dikerjakan berdua. Proyek ini bukan proyek sungguhan dan tidak melibatkan uang sungguhan.

## 2. Latar belakang dan masalah

Bersuara memudahkan orang membeli tiket konser tanpa datang langsung ke tempat pembelian dan tanpa membeli tiket di website yang berbeda untuk konser yang berbeda.

## 3. Tujuan dan ukuran keberhasilan

### Tujuan

1. Menyediakan website tiket konser untuk wilayah Jabodetabek.
2. Memungkinkan pembeli mendaftar, login, melihat konser, membeli tiket, dan membuka tiket digital.
3. Memungkinkan admin mengelola data konser.

### Ukuran keberhasilan

1. Semua skenario uji dalam PRD lulus tanpa error.
2. Website berjalan dan dapat diakses di localhost.

## 4. Pengguna

### Pembeli

Pembeli mendaftar sendiri melalui halaman registrasi. Pembeli dapat login, melihat konser, membeli tiket, melakukan pembayaran simulasi, dan membuka riwayat pesanan.

`[BELUM DIISI: deskripsi tambahan pembeli]`

### Admin

Akun admin dibuat langsung di database melalui phpMyAdmin dengan kolom peran admin. Admin tidak mendaftar melalui halaman registrasi. Admin dapat login melalui halaman login yang sama dengan pembeli dan mengelola data konser.

`[BELUM DIISI: deskripsi tambahan admin]`

## 5. Ruang lingkup

### Masuk

1. Registrasi pembeli, login, dan logout.
2. Daftar, pencarian, dan filter konser.
3. Detail konser.
4. Pembelian tiket dengan batas empat tiket lunas per akun untuk setiap konser.
5. Pembayaran simulasi, tiket digital, dan riwayat pesanan.
6. Login admin, daftar konser, tambah, ubah, dan hapus konser.
7. Daftar pesanan admin sebagai fitur Could jika waktu cukup.
8. Sepuluh data konser contoh.
9. Tampilan responsif dan keamanan dasar.

### Di luar cakupan

1. Laporan penjualan.
2. Kelola akun pengguna.
3. Unggah poster konser.
4. Pembayaran sungguhan atau payment gateway.
5. Filter selain artis, lokasi, dan tanggal.
6. Kode QR pada tiket digital.
7. Pendaftaran admin melalui halaman registrasi.

## 6. User stories

1. Sebagai pembeli, saya ingin mendaftar dengan data diri, supaya saya dapat menggunakan website sebagai pembeli.
2. Sebagai pembeli, saya ingin login, supaya saya dapat mengakses fitur yang membutuhkan akun.
3. Sebagai pembeli, saya ingin logout, supaya session akun saya dihapus.
4. Sebagai pembeli, saya ingin melihat dan mencari daftar konser serta memakai filter, supaya saya dapat menemukan konser yang diinginkan.
5. Sebagai pembeli, saya ingin melihat detail konser, supaya saya dapat memeriksa informasi sebelum membeli.
6. Sebagai pembeli, saya ingin membeli tiket dan melakukan pembayaran simulasi, supaya saya memperoleh tiket sesuai kebutuhan.
7. Sebagai pembeli, saya ingin melihat riwayat pesanan, supaya saya dapat membuka kembali tiket digital.
8. Sebagai admin, saya ingin login melalui halaman yang sama, supaya sistem mengarahkan saya sesuai peran.
9. Sebagai admin, saya ingin melihat, menambah, mengubah, dan menghapus konser, supaya saya dapat mengelola data konser.
10. Sebagai admin, saya ingin melihat daftar pesanan, supaya saya dapat melihat data pesanan tanpa mengubahnya.

## 7. Kebutuhan fungsional

Prioritas: Must, Should, dan Could.

### FR-01 Registrasi pembeli, Must

Sistem menyediakan kolom nama lengkap, email, nomor telepon, kata sandi, dan konfirmasi kata sandi. Registrasi hanya membuat akun pembeli.

### FR-02 Validasi registrasi, Must

Semua kolom wajib diisi. Format email harus valid dan belum terdaftar. Kata sandi minimal delapan karakter. Konfirmasi harus sama. Pesan error tampil di bawah kolom yang salah.

### FR-03 Login dan logout, Must

Login memakai email dan kata sandi. Logout menghapus session.

### FR-04 Daftar konser, Must

Daftar menampilkan nama konser, artis atau band, lokasi, harga, waktu konser, jenis tiket, dan sisa tiket. Konser yang sudah lewat tetap tampil dengan label “Selesai” dan tampilan lebih gelap.

### FR-05 Pencarian dan filter, Should

Sistem menyediakan pencarian artis dengan pencocokan sebagian dan rekomendasi seperti Spotify, filter lokasi, serta filter tanggal tunggal. Tidak ada filter lain.

### FR-06 Detail konser, Must

Halaman detail menampilkan semua data konser.

### FR-07 Akses pembelian, Must

Pengguna tanpa login yang menekan tombol beli diarahkan ke login. Jika sisa tiket nol, tombol beli nonaktif dan label “Habis” tampil. Konser yang sudah lewat tidak dapat dibeli.

### FR-08 Batas pembelian, Must

Pembeli dapat membeli lebih dari satu tiket, tetapi maksimal empat tiket lunas per akun untuk setiap konser. Hanya pesanan lunas yang dihitung. Pesanan yang melebihi batas ditolak dengan pesan yang jelas.

### FR-09 Halaman pembayaran, Must

Halaman menampilkan nama konser, artis, lokasi, waktu, harga per tiket, jumlah, dan total harga. Nama, email, dan nomor telepon terisi dari akun tetapi dapat diubah. Jumlah minimal satu dan tidak melebihi sisa tiket. Metode pembayaran adalah e-wallet atau bank.

### FR-10 Pembayaran simulasi, Must

Tombol “Bayar” mengubah status “menunggu pembayaran” menjadi “lunas”. Sistem tidak meminta nomor kartu, nomor rekening, atau PIN sungguhan. Setelah lunas, sisa tiket berkurang sesuai jumlah pembelian.

### FR-11 Tiket digital, Must

Setelah lunas, tiket menampilkan kode tiket atau kode pesanan unik, nama pembeli, detail konser, jumlah tiket, total bayar, dan status. Kode QR tidak digunakan.

### FR-12 Riwayat pesanan, Must

Pembeli yang login dapat membuka kembali tiket digital melalui riwayat pesanan.

### FR-13 Login dan pengalihan admin, Must

Admin login melalui halaman yang sama dan diarahkan berdasarkan peran. Pembeli dan pengguna tanpa login tidak dapat membuka halaman admin.

### FR-14 Daftar konser admin, Must

Admin melihat daftar konser dalam tabel dengan tombol ubah dan hapus.

### FR-15 Tambah konser, Must

Form tambah berisi nama konser, artis atau band, lokasi, harga, waktu konser, jenis tiket, dan jumlah tiket awal.

### FR-16 Ubah konser, Must

Form ubah sama dengan form tambah dan data lama sudah terisi.

### FR-17 Hapus konser, Must

Penghapusan memerlukan konfirmasi. Konser dengan pesanan lunas tidak dapat dihapus. Admin hanya dapat menghapus konser tanpa pesanan lunas.

### FR-18 Validasi data konser, Must

Semua kolom wajib diisi. Harga dan jumlah tiket harus berupa angka dan tidak negatif. Waktu konser harus berupa tanggal dan jam yang valid.

### FR-19 Daftar pesanan admin, Could

Jika dikerjakan, admin dapat melihat daftar pesanan yang berisi pembeli, konser, jumlah tiket, total bayar, dan status. Halaman hanya dapat dilihat.

## 8. Aturan bisnis

1. **BR-01:** Sistem menggunakan prinsip siapa cepat dia dapat.
2. **BR-02:** Sisa tiket berkurang hanya saat pembayaran lunas.
3. **BR-03:** Sistem mengecek ulang sisa tiket dan batas empat tiket saat tombol “Bayar” ditekan.
4. **BR-04:** Pesanan ditolak dengan pesan jelas jika tiket habis atau tidak mencukupi.
5. **BR-05:** Pesanan ditolak dengan pesan jelas jika batas empat tiket terlampaui.
6. **BR-06:** Pembeli belum login yang menekan beli diarahkan ke login.
7. **BR-07:** Sisa tiket nol membuat tombol beli nonaktif dan menampilkan label “Habis”.
8. **BR-08:** Konser yang sudah lewat tidak dapat dibeli, tetap tampil dengan label “Selesai”, dan tampil lebih gelap.
9. **BR-09:** Setiap konser memiliki satu jenis tiket, satu harga, dan satu jumlah sisa tiket.
10. **BR-10:** Admin dibuat langsung di database dan tidak mendaftar melalui registrasi.

## 9. Kebutuhan non-fungsional

1. **NFR-01:** Tampilan, tombol, dan form dapat digunakan pada HP, tablet, dan laptop. Pengujian memakai mode perangkat Developer Tools.
2. **NFR-02:** Kata sandi disimpan dalam bentuk hash.
3. **NFR-03:** Query database memakai prepared statement.
4. **NFR-04:** Data yang ditampilkan diamankan dengan escaping.
5. **NFR-05:** Halaman pembayaran, riwayat pesanan, dan admin dilindungi session valid.
6. **NFR-06:** Website berjalan melalui Laragon dan dapat diakses di localhost.

## 10. Kriteria penerimaan

1. **FR-01 sampai FR-02:** Registrasi valid membuat akun. Kolom kosong, email tidak valid atau sudah terdaftar, kata sandi kurang dari delapan karakter, dan konfirmasi berbeda ditolak. Error tampil di bawah kolom salah.
2. **FR-03:** Login valid berhasil, login salah ditolak, logout menghapus session, dan halaman terlindungi menolak session tidak valid.
3. **FR-04 sampai FR-05:** Daftar menampilkan data yang ditentukan. Pencarian sebagian, rekomendasi artis, filter lokasi, dan tanggal tunggal menghasilkan data sesuai. Konser lewat menampilkan “Selesai” dengan tampilan lebih gelap. Konser habis menampilkan “Habis” dan tombol nonaktif.
4. **FR-06:** Detail menampilkan semua data konser yang sesuai.
5. **FR-07 sampai FR-08:** Pengguna tanpa login diarahkan ke login. Jumlah valid dapat dibeli. Konser lewat, konser habis, dan pembelian yang melewati empat tiket lunas ditolak dengan pesan jelas.
6. **FR-09:** Ringkasan lengkap tampil, data akun terisi dan dapat diubah, jumlah minimal satu dan tidak lebih dari sisa tiket, serta e-wallet atau bank dapat dipilih.
7. **FR-10:** Status awal “menunggu pembayaran” berubah menjadi “lunas” setelah pembayaran valid. Data keuangan sungguhan tidak diminta. Sisa tiket berkurang. Pemeriksaan dilakukan saat tombol ditekan.
8. **FR-11 sampai FR-12:** Tiket hanya tersedia setelah lunas, memiliki kode unik dan data yang ditentukan, dapat dibuka dari riwayat, serta tidak memiliki QR.
9. **FR-13 sampai FR-18:** Admin dapat login dan mengelola konser. Pembeli tanpa peran admin ditolak. Validasi form bekerja. Form ubah memuat data lama. Penghapusan meminta konfirmasi dan menolak konser dengan pesanan lunas.
10. **FR-19:** Jika dikerjakan, daftar pesanan menampilkan lima data yang ditentukan dan hanya dapat dilihat.

## 11. Skenario uji

1. **TC-01, FR-01, FR-02:** Isi registrasi valid dan pastikan akun dibuat.
2. **TC-02, FR-02:** Kosongkan kolom dan pastikan error tampil di bawah kolom tersebut.
3. **TC-03, FR-02:** Gunakan email tidak valid dan pastikan ditolak.
4. **TC-04, FR-02:** Gunakan email terdaftar dan pastikan ditolak.
5. **TC-05, FR-02:** Gunakan kata sandi kurang dari delapan karakter dan konfirmasi berbeda, lalu pastikan error tampil.
6. **TC-06, FR-03:** Login pembeli, logout, dan pastikan session terhapus.
7. **TC-07, FR-03, FR-13:** Tanpa session valid, buka pembayaran, riwayat, dan admin. Pastikan akses ditolak atau diarahkan ke login.
8. **TC-08, FR-04:** Buka daftar dan pastikan semua data konser tampil.
9. **TC-09, FR-05:** Masukkan sebagian nama artis dan pastikan hasil serta rekomendasi tampil.
10. **TC-10, FR-05:** Pilih lokasi dan satu tanggal, lalu pastikan hasil sesuai.
11. **TC-11, FR-04, BR-08:** Buka konser lewat dan pastikan label, tampilan gelap, serta larangan beli berlaku.
12. **TC-12, FR-04, FR-07, BR-07:** Buka konser dengan sisa nol dan pastikan label “Habis” serta tombol nonaktif.
13. **TC-13, FR-06:** Buka detail dan pastikan semua data tampil.
14. **TC-14, FR-07, BR-06:** Tanpa login tekan beli dan pastikan diarahkan ke login.
15. **TC-15, FR-09:** Isi pembayaran valid dan pastikan ringkasan, data akun, serta metode tampil.
16. **TC-16, FR-09:** Pilih jumlah lebih besar dari sisa dan pastikan ditolak.
17. **TC-17, FR-10, BR-02:** Bayar pesanan valid dan pastikan status lunas serta sisa berkurang.
18. **TC-18, FR-10:** Pastikan sistem tidak meminta data kartu, rekening, atau PIN sungguhan.
19. **TC-19, FR-08, BR-05:** Akun dengan tiga tiket lunas mencoba membeli dua lagi dan pastikan ditolak.
20. **TC-20, FR-10, BR-03, BR-04:** Ubah ketersediaan sebelum bayar dan pastikan pemeriksaan ulang menolak bila tidak cukup.
21. **TC-21, FR-11:** Setelah lunas, buka tiket dan pastikan kode unik serta semua data tampil tanpa QR.
22. **TC-22, FR-12:** Login kembali dan pastikan tiket dapat dibuka dari riwayat.
23. **TC-23, FR-13:** Login dengan akun admin dan pastikan diarahkan ke halaman admin.
24. **TC-24, FR-13:** Pembeli dan pengguna tanpa login mencoba halaman admin dan pastikan ditolak.
25. **TC-25, FR-14:** Buka daftar admin dan pastikan tabel serta tombol ubah dan hapus tampil.
26. **TC-26, FR-15, FR-18:** Tambah konser dengan data valid dan pastikan tersimpan.
27. **TC-27, FR-18:** Uji kolom kosong, data bukan angka, angka negatif, dan waktu tidak valid. Pastikan ditolak.
28. **TC-28, FR-16:** Buka ubah, pastikan data lama terisi, simpan perubahan, dan periksa daftar.
29. **TC-29, FR-17:** Hapus konser tanpa pesanan lunas setelah konfirmasi dan pastikan terhapus.
30. **TC-30, FR-17:** Hapus konser dengan pesanan lunas dan pastikan ditolak.
31. **TC-31, FR-19:** Jika dikerjakan, buka daftar pesanan dan pastikan lima data tampil serta halaman hanya dapat dilihat.
32. **TC-32, NFR-01:** Uji mode HP, tablet, dan laptop. Pastikan teks, tombol, dan form dapat digunakan.
33. **TC-33, NFR-02 sampai NFR-05:** Periksa hash kata sandi, prepared statement, escaping, dan perlindungan session.
34. **TC-34, NFR-06:** Jalankan melalui Laragon, buka localhost, dan pastikan website dapat digunakan.

## 12. Alur pengguna utama

### Pembeli

Pembeli membuka website, mendaftar, login, melihat daftar, mencari atau memfilter konser, membuka detail, memilih jumlah tiket, memeriksa pembayaran, mengubah data bila perlu, memilih e-wallet atau bank, lalu menekan “Bayar”. Sistem memeriksa sisa tiket dan batas empat tiket. Jika berhasil, status menjadi “lunas”, sisa tiket berkurang, dan tiket digital tampil. Pembeli dapat membuka tiket dari riwayat setelah login kembali, lalu logout.

### Admin

Admin membuka login, masuk dengan akun admin, dan sistem memeriksa peran. Admin melihat daftar konser, menambah, mengubah, atau menghapus konser tanpa pesanan lunas setelah konfirmasi. Jika FR-19 dikerjakan, admin dapat melihat daftar pesanan. Admin logout.

## 13. Batasan teknis

1. Bahasa: HTML, CSS, JavaScript, dan PHP.
2. Database: MySQL melalui phpMyAdmin.
3. Alat: Laragon dan VS Code.
4. Tidak memakai framework.
5. Website berjalan di localhost.
6. Struktur database disepakati sebelum menulis kode.
7. Setiap konser memiliki satu jenis tiket, satu harga, dan satu sisa tiket.
8. Admin dibuat melalui phpMyAdmin.
9. Pembayaran hanya simulasi dan tidak menggunakan QR.
10. Disiapkan 10 data konser contoh.

## 14. Asumsi, dependensi, dan risiko

### Asumsi

Website memakai Laragon, database tersedia melalui phpMyAdmin, data contoh dapat fiktif, dan akun admin dibuat sebelum pengujian admin.

### Dependensi

Laragon, phpMyAdmin, browser dengan Developer Tools, kesepakatan struktur database, serta Git atau satu folder bersama yang rapi.

### Risiko

1. Kode kedua anggota dapat tidak cocok jika struktur database belum disepakati.
2. Waktu pengerjaan singkat sampai Minggu, 25 Oktober 2026.

## 15. Jadwal

1. **Senin sampai Selasa:** PRD final, kesepakatan struktur database, registrasi, dan login.
2. **Rabu sampai Kamis:** daftar konser, detail, pembelian dengan pembayaran simulasi, tiket digital, dan riwayat.
3. **Jumat:** admin tambah, ubah, hapus konser, serta daftar pesanan jika waktu cukup.
4. **Sabtu:** pengujian, perbaikan bug, pengecekan responsif, dan laporan.
5. **Minggu:** finalisasi dan pengumpulan.

Tenggat: Minggu, 25 Oktober 2026.

## 16. Cara kerja tim

Dua orang berkolaborasi melengkapi semua bagian tanpa pembagian tetap. Struktur database disepakati sebelum kode ditulis. Pengerjaan memakai Git atau satu folder bersama yang rapi. Deliverable terdiri dari kode, database beserta datanya, dan laporan. Isi laporan berada di luar PRD.

## 17. Pertanyaan terbuka

1. `[BELUM DIISI: deskripsi tambahan pembeli selain peran sebagai pembeli]`
2. `[BELUM DIISI: deskripsi tambahan admin selain peran sebagai admin]`

Jumlah penanda `[BELUM DIISI]` yang tersisa: **2**.
