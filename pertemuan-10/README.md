# README PERTEMUAN 10 (P10)
## Pelunasan dan Cetakan / Dokumen Transaksi Berbasis MVC

**Mata Kuliah:** Desain dan Pemrograman Web Lanjutan  
**Program Studi:** Sistem Informasi  
**Semester:** Gasal 2026/2027

**Identitas Mahasiswa**
- Nama: (Lini An-Nisa)
- NIM: (2522500011)
- Kelas: (SI3A)

---

# A. Memastikan Artefak P9 Siap Digunakan

## A.1. Menyiapkan Workspace dan Artefak P9

### Deskripsi

Pada tahap pertama pengerjaan Pertemuan 10, saya mempersiapkan kembali aplikasi penyewaan kendaraan yang telah dikembangkan pada Pertemuan 9 (P9).

Pengembangan P10 dilakukan dengan melanjutkan aplikasi yang sudah tersedia, bukan membuat aplikasi baru. Oleh karena itu, seluruh source code, struktur direktori, konfigurasi aplikasi, dan basis data dari P9 tetap digunakan sebagai dasar pengembangan.

### Implementasi

Langkah-langkah yang dilakukan:

1. Menyiapkan folder hasil pengerjaan Pertemuan 9.
2. Menyalin seluruh artefak P9 ke dalam workspace utama proyek.
3. Membuka workspace menggunakan Visual Studio Code.
4. Memastikan struktur direktori aplikasi tersedia.
5. Menjalankan Laragon dan mengaktifkan Apache serta MySQL.
6. Membuka aplikasi melalui browser menggunakan URL `http://localhost/dpwl-nim/` sesuai konfigurasi proyek.
7. Memastikan aplikasi dapat diakses dan koneksi basis data berjalan dengan baik.

### Hasil

Tahap persiapan ini bertujuan memastikan aplikasi P9 siap digunakan sebagai dasar pengembangan fitur pelunasan dan dokumen transaksi pada P10.

## A.2. Memeriksa Kesiapan Fitur P9

### Deskripsi

Sebelum mengembangkan fitur pelunasan, saya melakukan pemeriksaan terhadap fitur-fitur yang telah dibuat pada P9. Pemeriksaan ini bertujuan memastikan fitur sebelumnya tetap berjalan dengan baik sehingga tidak menimbulkan masalah ketika dilakukan pengembangan P10.

### Implementasi dan Pengujian

Pemeriksaan dilakukan terhadap beberapa fitur berikut:

1. **Login Admin**

   Memastikan admin dapat melakukan login dan masuk ke halaman administrasi.

2. **Login Penyewa**

   Memastikan penyewa dapat melakukan login dan mengakses halaman penyewa.

3. **Daftar dan Detail Transaksi**

   Memeriksa daftar transaksi serta detail transaksi melalui menu Pembayaran Saya.

4. **Pembayaran Uang Muka (DP)**

   Memastikan proses pembayaran DP dapat berjalan dan penyewa dapat mengunggah bukti pembayaran.

5. **Verifikasi Pembayaran DP**

   Memeriksa apakah admin dapat melihat dan melakukan verifikasi bukti pembayaran DP.

6. **Perubahan Status Pembayaran dan Transaksi**

   Memastikan status pembayaran dan status transaksi berubah sesuai hasil verifikasi admin.

7. **Informasi Kendaraan**

   Memastikan informasi kendaraan yang terdapat pada detail transaksi dapat ditampilkan dengan benar.

### Hasil Pengujian

Hasil pemeriksaan setiap fitur dicatat berdasarkan pengujian langsung pada aplikasi.

- Login Admin: (Isi hasil pengujian).
- Login Penyewa: (Isi hasil pengujian).
- Daftar dan Detail Transaksi: (Isi hasil pengujian).
- Pembayaran DP: (Isi hasil pengujian).
- Verifikasi Pembayaran DP: (Isi hasil pengujian).
- Perubahan Status Transaksi: (Isi hasil pengujian).
- Informasi Kendaraan: (Isi hasil pengujian).

## A.3. Menyiapkan Dokumentasi dan Git

### Deskripsi

Pada tahap ini, saya menyiapkan dokumentasi pengerjaan P10 melalui file README.md. Dokumentasi digunakan untuk mencatat setiap proses pengembangan, perubahan source code, perubahan struktur basis data, hasil pengujian, serta kendala dan perbaikan yang dilakukan.

Selain dokumentasi, Git dan GitHub digunakan untuk menyimpan riwayat perubahan proyek secara bertahap.

### Implementasi

Beberapa hal yang dipersiapkan:

1. Membuat atau memperbarui file `README.md` pada folder `pertemuan-10`.
2. Mencatat identitas mahasiswa.
3. Mencatat kondisi awal aplikasi P9.
4. Mendokumentasikan hasil pemeriksaan fitur P9.
5. Menyiapkan dokumentasi untuk tahapan pengembangan P10.
6. Menyiapkan Git untuk menyimpan setiap perubahan pekerjaan.

### Perintah Git

Setelah seluruh persiapan selesai, perubahan disimpan menggunakan perintah:

```bash
git add .
git commit -m "Menyiapkan artefak P9 untuk baseline pengembangan P10"
git push
```

### Hasil

Dokumentasi awal P10 telah disiapkan sebagai dasar pencatatan seluruh tahapan pengembangan berikutnya. Setiap perubahan akan diperbarui pada README dan disimpan ke repositori GitHub secara bertahap.

---

**Catatan:** Hasil pengujian dan keberhasilan proses Git diisi sesuai kondisi aktual setelah dilakukan pemeriksaan.

# B. Menyesuaikan Struktur Basis Data untuk Status Transaksi Lunas

## B.1. Memeriksa ENUM Status Transaksi

### Deskripsi

Pada tahap ini, saya melakukan pemeriksaan terhadap struktur basis data, khususnya tabel `t_sewa` pada kolom `status_sewa`.

Pemeriksaan dilakukan karena pada Pertemuan 10 (P10) terdapat penambahan status transaksi baru, yaitu `lunas`. Status tersebut digunakan untuk menandai transaksi penyewaan kendaraan yang telah menyelesaikan seluruh kewajiban pembayarannya.

Sebelum melakukan perubahan struktur tabel, saya terlebih dahulu memeriksa nilai ENUM yang telah tersedia agar nilai status sebelumnya tetap dipertahankan.

### Implementasi

Langkah-langkah yang dilakukan:

1. Membuka aplikasi Laragon.
2. Memastikan Apache dan MySQL sudah berjalan.
3. Membuka phpMyAdmin melalui browser dengan alamat `http://localhost/phpmyadmin/`.
4. Memilih database proyek penyewaan kendaraan.
5. Membuka tabel `t_sewa`.
6. Memilih tab **Structure (Struktur)**.
7. Mencari kolom `status_sewa`.
8. Memeriksa tipe data dan nilai ENUM yang sudah tersedia.
9. Melakukan backup database sebelum mengubah struktur tabel.

### Hasil

Pemeriksaan dilakukan untuk mengetahui struktur awal kolom `status_sewa` sebelum penambahan status transaksi `lunas`.

## B.2. Menambahkan ENUM lunas pada t_sewa.status_sewa

### Deskripsi

Setelah melakukan pemeriksaan terhadap struktur awal tabel `t_sewa`, saya melakukan penyesuaian pada kolom `status_sewa` dengan menambahkan nilai ENUM baru, yaitu `lunas`.

Penambahan status ini bertujuan agar sistem dapat membedakan transaksi yang masih memiliki kewajiban pembayaran dengan transaksi yang telah menyelesaikan seluruh pembayaran.

### Implementasi

Perubahan struktur database dilakukan melalui tab SQL pada phpMyAdmin dengan menjalankan perintah berikut:

```sql
ALTER TABLE `t_sewa`
CHANGE `status_sewa` `status_sewa`
ENUM(
    'diajukan',
    'disetujui',
    'ditolak',
    'aktif',
    'selesai',
    'batal',
    'keranjang',
    'lunas'
)
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_520_ci
NULL DEFAULT 'keranjang';
```

Perintah SQL tersebut digunakan untuk mempertahankan seluruh nilai ENUM yang sudah tersedia sebelumnya dan menambahkan nilai `lunas` sebagai status transaksi baru.

Penambahan status ini mendukung proses pengembangan aplikasi pada beberapa pertemuan, yaitu:

1. **P9:** Transaksi diajukan dan pembayaran uang muka (DP) diverifikasi.
2. **P10:** Transaksi yang berhasil dilunasi akan memiliki status `lunas`.
3. **P11:** Proses serah terima kendaraan dilaksanakan sesuai alur transaksi.
4. **P12:** Proses pengembalian kendaraan dilaksanakan hingga transaksi selesai.

Pada tahap P10, perubahan hanya dilakukan pada kolom `status_sewa` di tabel `t_sewa`. Status kendaraan pada tabel `t_mobil` tidak diubah karena pengelolaannya dilanjutkan pada Pertemuan 11.

### Hasil

Setelah perintah SQL berhasil dijalankan, kolom `status_sewa` memiliki tambahan nilai ENUM `lunas` sehingga dapat digunakan untuk menandai transaksi yang telah menyelesaikan pembayaran.

## B.3. Memverifikasi Perubahan Struktur Basis Data

### Deskripsi

Setelah menambahkan nilai ENUM `lunas`, saya melakukan pemeriksaan kembali terhadap struktur tabel `t_sewa`.

Verifikasi ini dilakukan untuk memastikan bahwa perubahan struktur database berhasil diterapkan dan seluruh nilai ENUM sebelumnya tetap tersedia.

### Implementasi dan Pengujian

Langkah-langkah pemeriksaan:

1. Membuka kembali database proyek melalui phpMyAdmin.
2. Memilih tabel `t_sewa`.
3. Membuka tab **Structure (Struktur)**.
4. Mencari kolom `status_sewa`.
5. Memastikan nilai ENUM `lunas` sudah tersedia.
6. Memastikan seluruh nilai ENUM sebelumnya tetap dipertahankan.
7. Memastikan perubahan hanya dilakukan pada kolom yang direncanakan.

### Hasil Pengujian

Hasil pemeriksaan struktur database:

- Nama tabel: `t_sewa`.
- Nama kolom: `status_sewa`.
- Tipe data: `ENUM`.
- Penambahan nilai ENUM: `lunas`.
- Nilai ENUM sebelumnya: Tetap dipertahankan.
- Perubahan pada tabel `t_mobil`: Tidak dilakukan.

## B.4. Memperbarui Dokumentasi dan Git

### Deskripsi

Setelah proses penyesuaian dan pemeriksaan struktur database selesai, saya memperbarui dokumentasi pada file `README.md` untuk mencatat perubahan database yang dilakukan pada tahap B.

Dokumentasi ini mencakup pemeriksaan struktur awal, penambahan status transaksi `lunas`, perintah SQL yang digunakan, serta hasil verifikasi perubahan database.

### Implementasi

Langkah-langkah yang dilakukan:

1. Memperbarui dokumentasi tahap B pada file `pertemuan-10/README.md`.
2. Mencatat perubahan ENUM pada kolom `status_sewa`.
3. Mencatat perintah SQL yang digunakan.
4. Menambahkan hasil pemeriksaan struktur database.
5. Menyalin seluruh isi workspace `dpwl-nim/` ke folder arsip `dpwl-nim-nama/pertemuan-10/` dengan menimpa berkas yang sesuai.
6. Membuka Terminal pada direktori repositori Git.
7. Menyimpan perubahan menggunakan perintah Git.

### Perintah Git

```bash
git add .
git commit -m "Menambahkan status lunas pada transaksi P10"
git push
```

### Hasil

Tahap penyesuaian struktur basis data telah didokumentasikan sebagai bagian dari pengembangan aplikasi P10. Penambahan status `lunas` menjadi dasar untuk implementasi Model Pelunasan pada tahap berikutnya.

---

**Catatan:** Hasil pengujian dan keberhasilan perubahan database dicatat berdasarkan pemeriksaan langsung pada phpMyAdmin. Perintah SQL cukup dijalankan satu kali setelah memastikan struktur kolom belum memiliki nilai ENUM `lunas`.