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

**Catatan:** Hasil pengujian dan keberhasilan perubahan database dicatat berdasarkan pemeriksaan langsung pada phpMyAdmin. Perintah SQL cukup dijalankan satu kali setelah memastikan struktur kolom belum memiliki nilai ENUM `lunas`.git

# C. Mengimplementasikan Model Pelunasan pada `application/models/Pembayaran_model.php`

## C.1. Mengambil Daftar Transaksi yang Memenuhi Syarat Pelunasan

### Deskripsi

Pada tahap ini, saya melakukan pengembangan pada file `Pembayaran_model.php` dengan menambahkan method `getDaftarPelunasan()`.

Method ini digunakan untuk mengambil daftar transaksi penyewaan kendaraan yang dapat diproses pelunasannya oleh admin. Data yang diambil meliputi kode transaksi, identitas penyewa, periode sewa, status transaksi, total transaksi, total pembayaran yang sudah valid, dan sisa kewajiban pembayaran.

### Implementasi

Saya menambahkan method `getDaftarPelunasan()` pada class `Pembayaran_model` dengan beberapa ketentuan berikut:

1. Mengambil data transaksi dari tabel `t_sewa`.
2. Menghubungkan data transaksi dengan identitas penyewa pada tabel `t_penyewa`.
3. Mengambil rincian transaksi dari tabel `t_pilih`.
4. Menghitung total transaksi berdasarkan perkalian `hrg_sewa * n_hari` pada setiap detail kendaraan.
5. Menghitung total pembayaran berdasarkan data pada tabel `t_bayar` yang memiliki status pembayaran `valid`.
6. Menghitung sisa kewajiban dengan mengurangi total transaksi dan total pembayaran valid.
7. Menggunakan prepared statement MySQLi untuk menjalankan query.

Rumus perhitungan sisa kewajiban:

**Sisa Kewajiban = Total Transaksi - Total Pembayaran Valid**

### Hasil

Method `getDaftarPelunasan()` digunakan untuk menyediakan data transaksi beserta sisa kewajiban yang akan ditampilkan pada halaman daftar pelunasan.

## C.2. Mengambil Detail Transaksi dan Riwayat Pembayaran

### Deskripsi

Pada tahap ini, saya menambahkan method `getDetailPelunasan(string $kodeSewa)` untuk mengambil informasi lengkap dari suatu transaksi berdasarkan kode transaksi sewa.

Method ini digunakan agar admin dapat melihat rincian transaksi dan riwayat pembayaran sebelum melakukan proses pelunasan.

### Implementasi

Data yang diambil melalui method ini meliputi:

1. Identitas dan data penyewa.
2. Tanggal mulai dan tanggal selesai sewa.
3. Waktu pengambilan kendaraan.
4. Status transaksi.
5. Total nilai transaksi.
6. Total pembayaran valid.
7. Sisa kewajiban pembayaran.
8. Riwayat pembayaran dari tabel `t_bayar`.
9. Rincian kendaraan yang disewa dari tabel `t_pilih` dan `t_mobil`.

Perhitungan total transaksi tetap menggunakan snapshot tarif pada tabel `t_pilih`, yaitu tarif yang tersimpan ketika transaksi dibuat, bukan menggunakan tarif kendaraan terbaru pada tabel `t_mobil`.

Selain itu, method ini menggunakan prepared statement MySQLi dan melakukan validasi terhadap kode transaksi sebelum menjalankan query. Apabila transaksi tidak ditemukan, method akan mengembalikan nilai `null`.

### Hasil

Method `getDetailPelunasan()` digunakan untuk menampilkan informasi transaksi secara lengkap, termasuk riwayat pembayaran dan sisa kewajiban yang harus dilunasi.

## C.3. Membuat Kode Pembayaran Pelunasan

### Deskripsi

Pada tahap ini, saya menambahkan method internal `buatKodeBayarPelunasan(string $tanggalKode)` untuk menghasilkan kode pembayaran pelunasan secara otomatis.

Kode pembayaran dibuat dengan format:

`PAY/YYYYMMDD/NN`

Keterangan:

- `PAY` merupakan awalan kode pembayaran.
- `YYYYMMDD` merupakan tanggal pembayaran.
- `NN` merupakan nomor urut pembayaran harian dengan dua digit.

### Implementasi

Pembuatan kode pembayaran dilakukan dengan ketentuan berikut:

1. Mengambil kode pembayaran terakhir berdasarkan tanggal yang sama.
2. Menentukan nomor urut berikutnya.
3. Apabila belum terdapat pembayaran pada tanggal tersebut, nomor urut dimulai dari `01`.
4. Nomor urut pembayaran dibatasi sampai dengan `99`.
5. Menggunakan named lock `dpwl_payment_code_sequence` untuk mencegah benturan nomor urut antara pembayaran DP pada proses checkout P9 dan pembayaran pelunasan pada P10.

Method ini bersifat private sehingga hanya dapat digunakan dari dalam class `Pembayaran_model`.

### Hasil

Method `buatKodeBayarPelunasan()` digunakan untuk menghasilkan kode pembayaran pelunasan secara otomatis dengan format yang telah ditentukan.

## C.4. Menghitung Sisa Kewajiban dan Memvalidasi Pelunasan

### Deskripsi

Pada tahap ini, saya menambahkan method `simpanPelunasan()` pada file `Pembayaran_model.php`.

Method ini bertanggung jawab untuk memproses dan menyimpan pembayaran pelunasan yang dicatat langsung oleh admin. Pembayaran dapat dilakukan melalui metode tunai maupun transfer.

Pembayaran pelunasan harus dilakukan satu kali untuk seluruh sisa kewajiban. Sistem tidak menyediakan pembayaran pelunasan secara bertahap atau melalui cicilan.

### Implementasi

Proses pelunasan dilakukan dengan beberapa ketentuan berikut:

1. Kode transaksi wajib tersedia dan transaksi harus ditemukan.
2. Status transaksi harus memenuhi syarat pelunasan.
3. Total transaksi harus lebih dari nol.
4. Sisa kewajiban harus lebih dari nol.
5. Nominal pembayaran harus sama dengan seluruh sisa kewajiban.
6. Metode pembayaran hanya menerima nilai `tunai` atau `transfer`.
7. Bukti transfer wajib tersedia apabila metode pembayaran yang digunakan adalah transfer.
8. Jenis pembayaran disimpan dengan nilai `pelunasan` pada kolom `jns_byr`.
9. Status pembayaran disimpan dengan nilai `valid` pada kolom `sttus_byr`.
10. Satu transaksi hanya boleh memiliki satu record pembayaran pelunasan.
11. Nominal pelunasan tidak boleh melebihi kapasitas kolom `MEDIUMINT`, yaitu 8.388.607.

Bukti transfer diserahkan oleh penyewa kepada admin melalui media komunikasi yang disepakati, misalnya WhatsApp. Selanjutnya, admin mengunggah bukti transfer tersebut melalui aplikasi.

### Proses Penyimpanan Pelunasan

Penyimpanan pelunasan dilakukan menggunakan transaksi basis data agar proses pencatatan pembayaran dan perubahan status transaksi berjalan secara atomik.

Urutan prosesnya adalah:

1. Memperoleh named lock untuk pembuatan kode pembayaran.
2. Memulai transaksi basis data.
3. Mengunci dan memeriksa data transaksi menggunakan `SELECT ... FOR UPDATE`.
4. Memvalidasi status transaksi, total transaksi, total pembayaran valid, dan sisa kewajiban.
5. Memastikan belum terdapat record pelunasan sebelumnya.
6. Membuat kode pembayaran pelunasan.
7. Menyimpan record pelunasan pada tabel `t_bayar`.
8. Memperbarui status transaksi pada tabel `t_sewa` menjadi `lunas`.
9. Memastikan perubahan status transaksi berhasil.
10. Melakukan `COMMIT` apabila seluruh proses berhasil dan melepaskan named lock.

Apabila terjadi kegagalan pada salah satu proses, sistem melakukan `ROLLBACK` agar data pembayaran dan perubahan status transaksi tidak tersimpan sebagian.

### Hasil

Setelah proses pelunasan berhasil disimpan, pembayaran tercatat pada tabel `t_bayar` dan status transaksi pada tabel `t_sewa` berubah menjadi `lunas`.

Pada tahap P10, proses pelunasan hanya mengubah status transaksi menjadi `lunas`. Status kendaraan pada tabel `t_mobil` tidak diubah karena proses serah terima kendaraan merupakan materi pada Pertemuan 11 (P11).

## C.5. Menguji Model Pelunasan

### Deskripsi

Setelah seluruh method pelunasan selesai ditambahkan, saya melakukan pemeriksaan terhadap Model Pelunasan untuk memastikan setiap method dapat menjalankan tugasnya sesuai ketentuan pada Modul P10.

### Implementasi dan Pengujian

Pemeriksaan dilakukan terhadap beberapa hal berikut:

1. Memastikan `getDaftarPelunasan()` mengambil transaksi yang memenuhi syarat pelunasan dan masih memiliki sisa kewajiban.
2. Memastikan `getDetailPelunasan()` menampilkan total transaksi, total pembayaran valid, sisa kewajiban, riwayat pembayaran, dan rincian kendaraan.
3. Memastikan kode pembayaran mengikuti format `PAY/YYYYMMDD/NN`.
4. Memastikan nominal pelunasan harus sama dengan seluruh sisa kewajiban.
5. Memastikan metode pembayaran selain `tunai` dan `transfer` ditolak.
6. Memastikan transaksi yang sudah memiliki record pelunasan tidak dapat dilunasi kembali.
7. Memastikan pencatatan pelunasan mengubah status transaksi menjadi `lunas`.
8. Memastikan status kendaraan tidak berubah akibat proses pelunasan.
9. Memastikan kegagalan proses menyebabkan transaksi basis data dibatalkan melalui `ROLLBACK`.

### Hasil Pengujian

Hasil pemeriksaan Model Pelunasan:

- Method `getDaftarPelunasan()`: (Isi hasil pemeriksaan).
- Method `getDetailPelunasan()`: (Isi hasil pemeriksaan).
- Pembuatan kode pembayaran: (Isi hasil pemeriksaan).
- Validasi metode pembayaran: (Isi hasil pemeriksaan).
- Validasi nominal pelunasan: (Isi hasil pemeriksaan).
- Pencegahan pelunasan ganda: (Isi hasil pemeriksaan).
- Perubahan status transaksi menjadi `lunas`: (Isi hasil pemeriksaan).
- Pemeriksaan status kendaraan: (Isi hasil pemeriksaan).
- Pengujian `ROLLBACK`: (Isi hasil pemeriksaan).

## C.6. Memperbarui Dokumentasi dan Git

### Deskripsi

Setelah implementasi Model Pelunasan selesai, saya memperbarui file `README.md` untuk mendokumentasikan method yang ditambahkan, proses perhitungan sisa kewajiban, validasi pembayaran, proses penyimpanan pelunasan, serta hasil pemeriksaan.

### Implementasi

Langkah-langkah yang dilakukan:

1. Memperbarui dokumentasi tahap C pada file `pertemuan-10/README.md`.
2. Mencatat method baru yang ditambahkan pada `Pembayaran_model.php`.
3. Mendokumentasikan proses perhitungan total transaksi dan sisa kewajiban.
4. Mendokumentasikan proses validasi dan penyimpanan pembayaran pelunasan.
5. Mencatat hasil pemeriksaan setiap method.
6. Menyalin seluruh isi workspace `dpwl-nim/` ke folder arsip `dpwl-nim-nama/pertemuan-10/` dengan menimpa berkas yang sesuai.
7. Menyimpan perubahan ke repositori Git.

### Perintah Git

```bash
git add .
git commit -m "Mengimplementasikan model pelunasan P10"
git push
```

### Hasil

Implementasi Model Pelunasan telah didokumentasikan sebagai bagian dari pengembangan aplikasi pada Pertemuan 10. Model ini menjadi dasar untuk proses implementasi Controller Pelunasan pada tahap berikutnya.

---

**Catatan:** Hasil pengujian diisi berdasarkan pemeriksaan langsung pada aplikasi dan database. Seluruh method P9 pada `Pembayaran_model.php` tetap dipertahankan dan method P10 ditambahkan tanpa menghapus fitur pembayaran sebelumnya.