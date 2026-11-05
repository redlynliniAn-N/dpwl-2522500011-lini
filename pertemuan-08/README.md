1. Saya Menambahkan Menu Navigasi Manajemen Penyewa

Deskripsi

Pada tahap pertama pengerjaan UTS, saya melakukan  penambahan menu navigasi baru bernama Manajemen Penyewa pada sidebar Admin aplikasi penyewaan kendaraan.

Implementasi

Penambahan menu dilakukan pada file View sidebar Admin dengan mengikuti pola navigasi yang telah tersedia pada menu Manajemen Kendaraan.

Perubahan yang dilakukan:

Menambahkan menu Manajemen Penyewa pada sidebar Admin.

Menambahkan ikon pengguna menggunakan Font Awesome.

Mengarahkan menu ke URL admin/penyewa.

Memberikan komentar ditambahkan pada P8 sebagai penanda kode yang dibuat pada Pertemuan 08.

Hasil

Menu Manajemen Penyewa berhasil ditambahkan pada navigasi sidebar Admin. Menu ini disiapkan sebagai akses menuju halaman pengelolaan data penyewa.# pertemuan-08


## 2. Formulir dan Tampilan Data Manajemen Penyewa

### Deskripsi
Pada tahap kedua pengerjaan UTS P8 ini saya melakukan pembuatan View Manajemen Penyewa dengan mengikuti pola tampilan Manajemen Kendaraan yang telah tersedia.

### Implementasi
- Membuat file `penyewa_view.php` pada folder `application/views/admin`.
- Menambahkan tabel data penyewa berdasarkan tabel `t_penyewa`.
- Menampilkan username, NIK, nama lengkap, nomor HP, alamat, foto KTP, dan foto SIM A.
- Menambahkan tombol Tambah, Ubah, dan Hapus.
- Menambahkan formulir tambah dan ubah penyewa.
- Menyesuaikan tampilan dengan AdminLTE.

### Hasil
View Manajemen Penyewa telah dibuat sebagai bagian dari pengembangan fitur Manajemen Penyewa pada Pertemuan 08.

### Git
- Commit: `Membuat tampilan dan form penyewa`
