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
## 4. Pengembangan Controller Manajemen Penyewa

### Deskripsi
Pada tahap keempat pengerjaan UTS P8 dilakukan penambahan Controller Manajemen Penyewa pada aplikasi Admin.

### Implementasi
- Mempertahankan method `index()` yang sudah tersedia pada Controller Admin.
- Menambahkan constructor untuk memanggil `Penyewa_model`.
- Menambahkan autentikasi Admin menggunakan `require_admin_login()`.
- Menambahkan method `penyewa()` untuk menampilkan halaman Manajemen Penyewa.
- Mengambil data penyewa menggunakan method `getAll()`.
- Mengirim data penyewa ke View `admin/penyewa_view`.
- Menggunakan flash message untuk menampilkan informasi proses.

### Hasil
Controller Admin berhasil dikembangkan untuk menampilkan halaman Manajemen Penyewa dengan mengambil data dari Model Penyewa.

### Git
- Commit: `Menambahkan controller manajemen penyewa`
## 4. Pengembangan Controller Manajemen Penyewa

### Deskripsi
Pada tahap keempat pengerjaan UTS P8 dilakukan penambahan Controller Manajemen Penyewa pada aplikasi Admin.

### Implementasi
- Mempertahankan method `index()` yang sudah tersedia pada Controller Admin.
- Menambahkan constructor untuk memanggil `Penyewa_model`.
- Menambahkan autentikasi Admin menggunakan `require_admin_login()`.
- Menambahkan method `penyewa()` untuk menampilkan halaman Manajemen Penyewa.
- Mengambil data penyewa menggunakan method `getAll()`.
- Mengirim data penyewa ke View `admin/penyewa_view`.
- Menggunakan flash message untuk menampilkan informasi proses.

### Hasil
Controller Admin berhasil dikembangkan untuk menampilkan halaman Manajemen Penyewa dengan mengambil data dari Model Penyewa.

### Git
- Commit: `Menambahkan controller manajemen penyewa`

## 5. Implementasi CRUD Manajemen Penyewa

### Deskripsi
Pada tahap kelima pengerjaan UTS P8, saya melakukan implementasi fitur CRUD (Create, Read, Update, Delete) pada halaman Manajemen Penyewa.

### Implementasi
- Mengimplementasikan fitur menampilkan seluruh data penyewa.
- Mengimplementasikan fitur tambah data penyewa.
- Mengimplementasikan fitur ubah data penyewa.
- Mengimplementasikan fitur hapus data penyewa.
- Menghubungkan Controller Admin dengan Model Penyewa dan View Manajemen Penyewa.
- Menggunakan method Model Penyewa untuk mengelola data pada database.

### Hasil
Fitur CRUD Manajemen Penyewa berhasil diimplementasikan sehingga Admin dapat melakukan pengelolaan data penyewa melalui halaman Manajemen Penyewa.

### Git
- Commit: `Mengimplementasikan CRUD manajemen penyewa`

## 6. Validasi, Keamanan, dan Upload Dokumen Penyewa

### Deskripsi
Pada tahap keenam pengerjaan UTS P8, saya melakukan penerapan validasi input, keamanan akses, serta fitur upload dokumen identitas penyewa.

### Implementasi
- Melakukan validasi input pada formulir penyewa.
- Melakukan sanitasi data untuk mengurangi risiko input yang tidak sesuai.
- Membatasi akses halaman Manajemen Penyewa hanya untuk Admin.
- Menerapkan pola Post/Redirect/Get (PRG) setelah proses pengelolaan data.
- Menambahkan fitur upload foto KTP penyewa.
- Menambahkan fitur upload foto SIM A penyewa.
- Mengelola file foto KTP dan SIM A pada folder upload.
- Menampilkan foto KTP dan SIM A pada tabel data penyewa.

### Hasil
Fitur Manajemen Penyewa dilengkapi dengan validasi input, keamanan akses Admin, dan pengelolaan dokumen identitas penyewa.

### Git
- Commit: `Menambahkan validasi dan upload dokumen penyewa`

## 7. Pengujian Fitur Manajemen Penyewa

### Deskripsi
Pada tahap ketujuh pengerjaan UTS P8, saya melakukan pengujian terhadap fitur Manajemen Penyewa untuk memastikan seluruh fungsi berjalan sesuai kebutuhan.

### Implementasi
Pengujian dilakukan terhadap beberapa fitur berikut:
- Menguji tampilan data penyewa.
- Menguji penambahan data penyewa.
- Menguji perubahan data penyewa.
- Menguji penghapusan data penyewa.
- Menguji validasi formulir penyewa.
- Menguji upload foto KTP.
- Menguji upload foto SIM A.
- Menguji akses halaman Manajemen Penyewa menggunakan akun Admin.

### Hasil
Pengujian dilakukan untuk memastikan fitur Manajemen Penyewa dapat berjalan dengan baik dan sesuai dengan kebutuhan aplikasi penyewaan kendaraan.

### Git
- Commit: `Melakukan pengujian manajemen penyewa`

## 8. Export Database dan Dokumentasi

### Deskripsi
Pada tahap terakhir pengerjaan UTS P8, saya melakukan export database dan melengkapi dokumentasi pengerjaan project.

### Implementasi
- Melakukan export database aplikasi penyewaan kendaraan ke file SQL.
- Menyimpan file hasil export database sebagai dokumentasi database.
- Melengkapi dokumentasi pengerjaan pada folder `pertemuan-08`.
- Melakukan Git add, commit, dan push terhadap perubahan project.

### Hasil
Database dan dokumentasi pengerjaan UTS P8 berhasil disiapkan sebagai bagian dari pengumpulan tugas.

### Git
- Commit: `Melengkapi dokumentasi dan database UTS P8`
