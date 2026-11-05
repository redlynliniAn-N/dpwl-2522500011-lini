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


## 3. Pengembangan Model Manajemen Penyewa

### Deskripsi
Pada tahap ketiga pengerjaan UTS P8 dilakukan pengembangan Model Manajemen Penyewa dengan memanfaatkan file `Penyewa_model.php` yang telah tersedia dari pengerjaan sebelumnya.

### Implementasi
- Mempertahankan method P6 yang sudah ada, seperti registrasi, login, dan pengelolaan profil penyewa.
- Menambahkan method `getAll()` untuk mengambil seluruh data penyewa.
- Menambahkan method `update()` untuk memperbarui data penyewa.
- Menambahkan method `delete()` untuk menghapus data penyewa.
- Menggunakan prepared statement untuk proses pembaruan dan penghapusan data.

### Hasil
Model Manajemen Penyewa berhasil dikembangkan dengan menambahkan fungsi pengambilan, pembaruan, dan penghapusan data penyewa untuk kebutuhan Admin pada P8.

### Git
- Commit: `Menambahkan model manajemen penyewa`
