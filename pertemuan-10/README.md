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
```GI

### Hasil

Implementasi Model Pelunasan telah didokumentasikan sebagai bagian dari pengembangan aplikasi pada Pertemuan 10. Model ini menjadi dasar untuk proses implementasi Controller Pelunasan pada tahap berikutnya.

---

**Catatan:** Hasil pengujian diisi berdasarkan pemeriksaan langsung pada aplikasi dan database. Seluruh method P9 pada `Pembayaran_model.php` tetap dipertahankan dan method P10 ditambahkan tanpa menghapus fitur pembayaran sebelumnya.

# D. Implementasi Controller Pelunasan

## D.1. Menampilkan Daftar Transaksi yang Belum Lunas

### Deskripsi

Pada tahap ini, saya menambahkan method `pelunasanIndex()` pada file `Pembayaran_controller.php`.

Method ini digunakan untuk menampilkan daftar transaksi yang masih memiliki kewajiban pelunasan. Controller bertugas menerima permintaan dari admin, memanggil Model untuk mengambil data transaksi, kemudian mengirimkan data tersebut ke View.

### Implementasi

Proses implementasi dilakukan dengan beberapa langkah berikut:

1. Memastikan pengguna telah login sebagai admin menggunakan `require_admin_login()`.
2. Membatasi permintaan agar hanya menggunakan metode HTTP `GET`.
3. Memanggil method `getDaftarPelunasan()` pada `Pembayaran_model.php`.
4. Mengambil pesan notifikasi dari session.
5. Mengirimkan data daftar pelunasan, username admin, dan pesan notifikasi ke View `admin/pelunasan.php`.
6. Menambahkan penanganan kesalahan menggunakan `try-catch` apabila data gagal ditampilkan.

Controller tidak melakukan perhitungan total transaksi maupun sisa kewajiban secara langsung karena proses tersebut merupakan tanggung jawab Model.

### Hasil

Method `pelunasanIndex()` digunakan untuk menampilkan daftar transaksi pelunasan kepada admin melalui halaman `admin/pelunasan.php`.

## D.2. Menampilkan Detail Pelunasan

### Deskripsi

Pada tahap ini, saya menambahkan method `pelunasanDetail(string $kodeSewa)` untuk menampilkan detail transaksi, riwayat pembayaran, serta sisa kewajiban yang harus dilunasi.

Halaman detail ini digunakan oleh admin untuk memeriksa informasi transaksi sebelum mencatat pembayaran pelunasan.

### Implementasi

Langkah-langkah implementasi:

1. Memastikan admin telah login menggunakan `require_admin_login()`.
2. Memastikan permintaan menggunakan metode HTTP `GET`.
3. Mengambil kode transaksi dari parameter URL.
4. Mengubah karakter `~` pada URL menjadi `/` agar sesuai dengan format kode transaksi yang tersimpan di database.
5. Memastikan kode transaksi tersedia.
6. Memanggil method `getDetailPelunasan($kodeSewa)` pada `Pembayaran_model.php`.
7. Mengembalikan respons HTTP `404` apabila transaksi tidak ditemukan.
8. Mengirimkan data detail transaksi dan pesan notifikasi ke View `admin/pelunasan_detail.php`.
9. Menambahkan penanganan kesalahan apabila detail transaksi gagal ditampilkan.

### Hasil

Method `pelunasanDetail()` digunakan untuk menampilkan informasi transaksi secara lengkap, termasuk data penyewa, rincian kendaraan, riwayat pembayaran, dan sisa kewajiban.

## D.3. Memproses Pelunasan

### Deskripsi

Pada tahap ini, saya menambahkan method `prosesPelunasan(string $kodeSewa)` untuk memproses pembayaran pelunasan yang diterima langsung oleh admin.

Proses pelunasan dapat dilakukan menggunakan metode pembayaran tunai maupun transfer. Khusus pembayaran transfer, admin mengunggah bukti transfer yang sebelumnya diterima dari penyewa.

### Implementasi

Proses implementasi dilakukan dengan ketentuan berikut:

1. Memastikan admin telah login menggunakan `require_admin_login()`.
2. Memastikan permintaan menggunakan metode HTTP `POST`.
3. Memeriksa ketersediaan kode transaksi.
4. Mengambil metode pembayaran dari input formulir melalui field `mtode_byr`.
5. Memastikan metode pembayaran hanya menggunakan nilai `tunai` atau `transfer`.
6. Apabila pembayaran menggunakan tunai, sistem tidak memerlukan unggahan bukti pembayaran.
7. Apabila pembayaran menggunakan transfer, Controller memanggil helper `simpanFileBukti('ft_byr')` untuk memvalidasi dan menyimpan bukti transfer.
8. Memanggil method `simpanPelunasan()` pada `Pembayaran_model.php` untuk mencatat pembayaran.
9. Menampilkan pesan keberhasilan yang berisi kode pembayaran, status pembayaran `valid`, dan status transaksi `lunas` apabila proses berhasil.
10. Menangani kesalahan menggunakan `try-catch` apabila proses pelunasan gagal.

### Validasi Unggahan Bukti Transfer

Untuk pembayaran transfer, bukti pembayaran divalidasi dengan ketentuan:

- Ukuran file maksimal 5 MB.
- Format gambar yang diperbolehkan adalah JPEG, PNG, dan WEBP.
- Struktur file gambar diperiksa sebelum disimpan.
- Nama file bukti pembayaran dikirimkan ke Model untuk dicatat pada database.

Apabila proses pelunasan gagal setelah file bukti diunggah, Controller memanggil helper `hapusBuktiBaru()` untuk menghapus file baru tersebut apabila diperlukan.

### Penerapan Post-Redirect-Get (PRG)

Pada proses pelunasan, diterapkan pola Post-Redirect-Get (PRG) menggunakan method `redirectDenganPesan()`.

Pola ini digunakan agar setelah formulir dikirim melalui metode POST, admin diarahkan kembali ke halaman detail pelunasan. Dengan demikian, pengiriman ulang formulir ketika halaman dimuat ulang dapat dihindari.

### Hasil

Method `prosesPelunasan()` bertugas menerima input pelunasan dari admin, memvalidasi metode pembayaran, menangani unggahan bukti transfer apabila diperlukan, serta memanggil Model untuk menyimpan pembayaran dan memperbarui status transaksi.

## D.4. Menampilkan Dokumen Transaksi

### Deskripsi

Pada tahap ini, saya menambahkan method `dokumenTransaksi(string $kodeSewa)` untuk menampilkan dokumen transaksi yang telah disiapkan dan dapat dicetak oleh admin.

Dokumen transaksi berisi informasi yang diambil dari beberapa tabel database, yaitu `t_sewa`, `t_penyewa`, `t_pilih`, `t_mobil`, dan `t_bayar`.

### Implementasi

Langkah-langkah implementasi:

1. Memastikan admin telah login menggunakan `require_admin_login()`.
2. Memastikan permintaan menggunakan metode HTTP `GET`.
3. Memeriksa ketersediaan kode transaksi.
4. Memanggil method `getDetailPelunasan($kodeSewa)` pada `Pembayaran_model.php`.
5. Memeriksa apakah data transaksi tersedia.
6. Mengembalikan respons HTTP `404` apabila data transaksi tidak ditemukan.
7. Mengirimkan data transaksi ke View `admin/dokumen_transaksi.php`.
8. Menambahkan penanganan kesalahan apabila dokumen transaksi gagal ditampilkan.

Controller hanya bertugas mengambil data dari Model dan menyajikannya melalui View. Pengaturan tampilan dokumen untuk kebutuhan cetak dilakukan pada bagian View.

### Hasil

Method `dokumenTransaksi()` digunakan untuk menampilkan dokumen transaksi berdasarkan kode sewa yang dipilih. Dokumen tersebut nantinya dapat dicetak menggunakan fasilitas cetak pada browser.

## D.5. Mempertahankan Fitur Pembayaran P9

### Deskripsi

Pengembangan Controller pada Pertemuan 10 dilakukan dengan melanjutkan fitur pembayaran yang telah dibuat pada Pertemuan 9.

Oleh karena itu, seluruh method pembayaran P9 tetap dipertahankan agar fitur pembayaran sebelumnya tidak mengalami kerusakan atau kehilangan fungsi.

### Implementasi

Method pembayaran P9 yang tetap dipertahankan meliputi:

1. `verifikasiIndex()` untuk menampilkan daftar pembayaran DP yang menunggu verifikasi admin.
2. `verifikasiDetail()` untuk menampilkan detail pembayaran dan bukti transfer.
3. `prosesVerifikasi()` untuk memproses keputusan verifikasi pembayaran oleh admin.
4. `index()` untuk menampilkan daftar pembayaran milik penyewa.
5. `detail()` untuk menampilkan detail pembayaran milik penyewa.
6. `uploadBukti()` untuk menerima unggahan bukti transfer dari penyewa.

Selain method tersebut, helper berikut juga tetap dipertahankan:

- `simpanFileBukti()`
- `hapusBuktiBaru()`
- `hapusBuktiLama()`
- `wajibLoginPenyewa()`
- `wajibMetode()`
- `redirectDenganPesan()`

### Perbedaan Proses Pembayaran P9 dan P10

Terdapat perbedaan pada pihak yang melakukan unggahan bukti pembayaran:

- **P9:** Penyewa mengunggah bukti pembayaran DP melalui aplikasi.
- **P10:** Admin mengunggah bukti transfer pelunasan yang telah diterima dari penyewa.

Keduanya tetap menggunakan helper penyimpanan bukti yang sama, tetapi memiliki alur proses dan hak akses yang berbeda.

### Hasil

Seluruh fitur pembayaran P9 tetap dipertahankan dan pengembangan P10 menambahkan proses pelunasan tanpa menghilangkan fungsi pembayaran yang telah tersedia sebelumnya.

## D.6. Melakukan Pemeriksaan Awal Controller

### Deskripsi

Setelah seluruh method Controller Pelunasan ditambahkan, saya melakukan pemeriksaan awal untuk memastikan setiap method telah mengikuti aturan akses, metode HTTP, validasi input, serta hubungan antara Controller dan Model.

### Implementasi dan Pengujian

Pemeriksaan dilakukan terhadap beberapa hal berikut:

1. Memastikan setiap method pelunasan menggunakan `require_admin_login()` untuk membatasi akses hanya kepada admin.
2. Memastikan method yang menampilkan halaman menggunakan metode HTTP `GET`.
3. Memastikan method pemrosesan pelunasan menggunakan metode HTTP `POST`.
4. Memastikan kode transaksi diperiksa sebelum diteruskan ke Model.
5. Memastikan metode pembayaran hanya menerima `tunai` atau `transfer`.
6. Memastikan unggahan bukti transfer hanya dilakukan apabila metode pembayaran yang dipilih adalah transfer.
7. Memastikan Controller memanggil method yang sesuai pada `Pembayaran_model.php`.
8. Memastikan data dikirimkan ke View sesuai dengan halaman yang ditampilkan.
9. Memastikan penanganan kesalahan dan pola Post-Redirect-Get (PRG) diterapkan pada proses pelunasan.

### Hasil Pemeriksaan

Hasil pemeriksaan awal Controller:

- Method `pelunasanIndex()`: (Isi hasil pemeriksaan).
- Method `pelunasanDetail()`: (Isi hasil pemeriksaan).
- Method `prosesPelunasan()`: (Isi hasil pemeriksaan).
- Method `dokumenTransaksi()`: (Isi hasil pemeriksaan).
- Perlindungan akses admin: (Isi hasil pemeriksaan).
- Validasi metode HTTP: (Isi hasil pemeriksaan).
- Validasi metode pembayaran: (Isi hasil pemeriksaan).
- Penanganan unggahan bukti transfer: (Isi hasil pemeriksaan).
- Penerapan PRG: (Isi hasil pemeriksaan).
- Pemertahanan fitur pembayaran P9: (Isi hasil pemeriksaan).

Pemeriksaan pada tahap ini merupakan pemeriksaan awal terhadap kode Controller. Pengujian fungsional melalui halaman aplikasi dilakukan setelah View dan Route selesai dibuat serta diintegrasikan.

## D.7. Memperbarui Dokumentasi dan Git

### Deskripsi

Setelah implementasi Controller Pelunasan selesai, saya memperbarui file `README.md` untuk mendokumentasikan seluruh perubahan yang dilakukan pada file `Pembayaran_controller.php`.

Dokumentasi mencakup penambahan method pelunasan, proses unggah bukti transfer, penyajian dokumen transaksi, serta pemertahanan fungsi pembayaran P9.

### Implementasi

Langkah-langkah yang dilakukan:

1. Memperbarui dokumentasi tahap D pada file `pertemuan-10/README.md`.
2. Mencatat penambahan method `pelunasanIndex()`.
3. Mencatat penambahan method `pelunasanDetail()`.
4. Mencatat penambahan method `prosesPelunasan()`.
5. Mencatat penambahan method `dokumenTransaksi()`.
6. Mendokumentasikan fitur pembayaran P9 yang tetap dipertahankan.
7. Mencatat hasil pemeriksaan awal dan kendala yang ditemukan, apabila ada.
8. Menyalin seluruh isi workspace `dpwl-nim/` ke folder arsip `dpwl-nim-nama/pertemuan-10/` dengan menimpa berkas yang sesuai.
9. Membuka Terminal pada direktori repositori Git.
10. Menyimpan perubahan menggunakan perintah Git.

### Perintah Git

```bash
git add .
git commit -m "Menambahkan controller pelunasan dan dokumen transaksi P10"
git push
```

### Hasil

Implementasi Controller Pelunasan telah didokumentasikan sebagai bagian dari pengembangan aplikasi pada Pertemuan 10. Controller ini menjadi penghubung antara Model Pelunasan dengan View, serta menangani proses pelunasan dan penyajian dokumen transaksi.

---

**Catatan:** Hasil pemeriksaan diisi berdasarkan kondisi kode yang sebenarnya. Pengujian fungsional melalui halaman aplikasi dilakukan setelah Route dan View selesai diintegrasikan.

# E. Membuat Route dan Integrasi Menu Pelunasan

## E.1. Menambahkan Route Pelunasan

### Deskripsi

Pada tahap ini, saya melakukan penambahan route untuk menghubungkan URL yang diakses oleh pengguna dengan method yang terdapat pada `Pembayaran_controller.php`.

Route diperlukan agar fitur pelunasan transaksi dapat diakses melalui halaman daftar pelunasan, halaman detail pelunasan, proses pelunasan, dan dokumen transaksi.

### Implementasi

Penambahan route dilakukan pada file:

`application/config/routes.php`

Route yang ditambahkan adalah sebagai berikut:

```php
$route['admin/pembayaran/pelunasan'] = 'Pembayaran_controller/pelunasanIndex';
$route['admin/pembayaran/pelunasan/proses/(:any)'] = 'Pembayaran_controller/prosesPelunasan/$1';
$route['admin/pembayaran/dokumen/(:any)'] = 'Pembayaran_controller/dokumenTransaksi/$1';
$route['admin/pembayaran/pelunasan/(:any)'] = 'Pembayaran_controller/pelunasanDetail/$1';
```

Fungsi dari masing-masing route:

1. **Route daftar pelunasan:** Mengarahkan pengguna ke method `pelunasanIndex()` untuk menampilkan daftar transaksi yang masih memiliki kewajiban pembayaran.
2. **Route proses pelunasan:** Mengarahkan permintaan proses pelunasan ke method `prosesPelunasan()` berdasarkan kode transaksi.
3. **Route dokumen transaksi:** Mengarahkan pengguna ke method `dokumenTransaksi()` untuk menampilkan dokumen transaksi.
4. **Route detail pelunasan:** Mengarahkan pengguna ke method `pelunasanDetail()` untuk menampilkan informasi detail transaksi.

Penulisan route dilakukan dengan memperhatikan urutannya. Route yang lebih spesifik ditempatkan sebelum route yang lebih umum agar tidak tertangkap oleh pola route lainnya.

Saya juga mempertahankan route P9 yang sudah tersedia agar fitur pembayaran sebelumnya tetap dapat digunakan.

## E.2. Mengintegrasikan Menu Pelunasan pada Sidebar Admin

### Deskripsi

Setelah menambahkan route, saya melakukan integrasi menu Pelunasan Transaksi pada navigasi sidebar admin.

Menu ini digunakan untuk memudahkan admin dalam mengakses halaman pengelolaan pelunasan transaksi penyewaan kendaraan.

### Implementasi

Penambahan menu dilakukan pada file:

`application/views/templates/sidebar.php`

Kode menu yang ditambahkan:

```php
<li>
    <a href="<?= htmlspecialchars(site_url('admin/pembayaran/pelunasan'), ENT_QUOTES, 'UTF-8') ?>">
        <i class="fa fa-credit-card"></i>
        <span>Pelunasan Transaksi</span>
    </a>
</li>
```

Penjelasan bagian menu:

1. `site_url('admin/pembayaran/pelunasan')` digunakan untuk mengarahkan menu menuju halaman daftar pelunasan.
2. `htmlspecialchars()` digunakan untuk membantu mengamankan output URL yang ditampilkan.
3. `fa fa-credit-card` digunakan sebagai ikon kartu pembayaran.
4. `Pelunasan Transaksi` merupakan nama menu yang ditampilkan pada sidebar admin.

Menu ditambahkan dengan mengikuti struktur dan pola tampilan menu yang sudah tersedia serta ditempatkan pada bagian navigasi admin.

## E.3. Memeriksa Route dan Pengendalian

### Deskripsi

Setelah menambahkan route dan menu Pelunasan Transaksi, saya melakukan pemeriksaan awal untuk memastikan setiap route telah diarahkan ke method Controller yang sesuai.

Pemeriksaan ini juga dilakukan untuk memastikan bahwa fitur pelunasan tetap memiliki pengendalian akses admin dan tidak mengganggu route pembayaran P9 yang sudah tersedia.

### Implementasi dan Pemeriksaan

Hal-hal yang diperiksa meliputi:

1. **Route daftar pelunasan**

   Memastikan URL daftar pelunasan mengarah ke method `pelunasanIndex()`.

   ```php
   $route['admin/pembayaran/pelunasan'] = 'Pembayaran_controller/pelunasanIndex';
   ```

2. **Route detail pelunasan**

   Memastikan URL detail pelunasan mengarah ke method `pelunasanDetail()`.

   ```php
   $route['admin/pembayaran/pelunasan/(:any)'] = 'Pembayaran_controller/pelunasanDetail/$1';
   ```

3. **Route proses pelunasan**

   Memastikan URL proses pelunasan mengarah ke method `prosesPelunasan()` dan prosesnya hanya menerima metode HTTP POST melalui pengendalian pada Controller.

   ```php
   $route['admin/pembayaran/pelunasan/proses/(:any)'] = 'Pembayaran_controller/prosesPelunasan/$1';
   ```

4. **Route dokumen transaksi**

   Memastikan URL dokumen transaksi mengarah ke method `dokumenTransaksi()`.

   ```php
   $route['admin/pembayaran/dokumen/(:any)'] = 'Pembayaran_controller/dokumenTransaksi/$1';
   ```

5. **Menu Pelunasan Transaksi**

   Memastikan menu pada sidebar admin mengarah ke URL:

   `admin/pembayaran/pelunasan`

6. **Pengendalian akses admin**

   Memastikan seluruh method pelunasan dan dokumen transaksi tetap dilindungi oleh `require_admin_login()` agar hanya admin yang sudah login yang dapat mengakses fitur tersebut.

7. **Mempertahankan route P9**

   Memastikan seluruh route pembayaran P9 yang sudah tersedia tetap dipertahankan dan tidak dihapus atau diganti.

### Hasil Pemeriksaan

Hasil pemeriksaan dicatat setelah memastikan bahwa:

- Route daftar pelunasan sudah diarahkan ke method yang sesuai.
- Route detail pelunasan sudah diarahkan ke method yang sesuai.
- Route proses pelunasan sudah diarahkan ke method yang sesuai.
- Route dokumen transaksi sudah diarahkan ke method yang sesuai.
- Menu Pelunasan Transaksi sudah diarahkan ke URL yang benar.
- Pengendalian akses admin tetap diterapkan.
- Route P9 tetap dipertahankan.

Pengujian tampilan halaman dan proses pelunasan secara fungsional dilakukan setelah View pada Bagian F dan G selesai dibuat serta diintegrasikan.

## E.4. Memperbarui Dokumentasi dan Git

### Deskripsi

Setelah penambahan route dan integrasi menu selesai, saya memperbarui dokumentasi pada file `README.md` untuk mencatat seluruh perubahan yang dilakukan pada tahap E.

Dokumentasi mencakup penambahan route daftar pelunasan, route detail pelunasan, route proses pelunasan, route dokumen transaksi, integrasi menu pada sidebar admin, serta hasil pemeriksaan route dan navigasi.

### Implementasi

Langkah-langkah yang dilakukan:

1. Memperbarui dokumentasi tahap E pada file `pertemuan-10/README.md`.
2. Mencatat seluruh route baru yang ditambahkan pada `application/config/routes.php`.
3. Mencatat integrasi menu Pelunasan Transaksi pada `application/views/templates/sidebar.php`.
4. Menambahkan hasil pemeriksaan route dan navigasi.
5. Menyalin seluruh isi workspace `dpwl-nim/` ke folder arsip `dpwl-nim-nama/pertemuan-10/` dengan menimpa berkas yang sesuai.
6. Membuka Terminal pada direktori repositori Git.
7. Menyimpan perubahan menggunakan perintah Git berikut secara berurutan.

### Perintah Git

```bash
git add .
git commit -m "Menambahkan route dan menu pelunasan P10"
git push
```

### Hasil

Tahap E mendukung integrasi fitur pelunasan transaksi ke dalam aplikasi penyewaan kendaraan. Dengan adanya route dan menu Pelunasan Transaksi, admin dapat diarahkan menuju halaman pengelolaan pelunasan melalui navigasi aplikasi.

Setelah tahap ini selesai, pengerjaan dilanjutkan ke **Bagian F: Membuat View Daftar dan Detail Pelunasan**.

# F. Membuat View Daftar dan Detail Pelunasan

## Deskripsi

Pada tahap ini, saya membuat View untuk menampilkan daftar transaksi yang masih memiliki kewajiban pembayaran dan halaman detail transaksi yang akan diproses pelunasannya oleh admin.

View bertugas menampilkan data yang diterima dari Controller. Seluruh data diperoleh melalui Model sehingga View tidak melakukan query database maupun menghitung aturan bisnis secara mandiri.

## F.1. Membuat View Daftar Pelunasan

### Deskripsi

Saya membuat halaman daftar pelunasan dengan nama file `pelunasan.php` pada direktori:

`application/views/admin/pelunasan.php`

Halaman ini digunakan untuk menampilkan seluruh transaksi yang masih memiliki sisa kewajiban pembayaran.

### Implementasi

View menggunakan variabel `$daftarPelunasan` yang dikirimkan oleh `Pembayaran_controller.php`.

Informasi yang ditampilkan pada halaman daftar pelunasan meliputi:

1. Nomor urut transaksi.
2. Kode sewa.
3. Identitas penyewa, yaitu nama lengkap dan username.
4. Nomor HP penyewa.
5. Periode sewa, berupa tanggal mulai dan tanggal selesai.
6. Status transaksi.
7. Total transaksi.
8. Total pembayaran yang sudah valid.
9. Sisa kewajiban pembayaran.
10. Tombol **Detail / Proses** untuk membuka halaman detail pelunasan.

Saya juga menambahkan kondisi apabila belum terdapat transaksi yang memiliki sisa kewajiban. Sistem akan menampilkan pesan bahwa tidak ada transaksi yang perlu dilunasi.

Selain itu, pesan informasi atau notifikasi dari Controller ditampilkan menggunakan mekanisme flash message.

### Pengamanan Data

Untuk menjaga keamanan tampilan, data teks yang berasal dari database ditampilkan menggunakan `htmlspecialchars()` melalui fungsi bantuan `$e`.

Nilai nominal pembayaran ditampilkan menggunakan `number_format()` agar lebih mudah dibaca dalam format Rupiah.

Setiap transaksi memiliki tautan menuju halaman detail berdasarkan kode sewa yang bersangkutan.

### Hasil

Halaman daftar pelunasan digunakan sebagai tempat admin melihat transaksi yang masih memiliki kewajiban pembayaran serta memilih transaksi yang akan diproses lebih lanjut.

## F.2. Membuat View Detail Pelunasan

### Deskripsi

Setelah membuat halaman daftar pelunasan, saya membuat halaman detail transaksi dengan nama file `pelunasan_detail.php` pada direktori:

`application/views/admin/pelunasan_detail.php`

Halaman ini digunakan untuk menampilkan informasi lengkap mengenai transaksi penyewaan kendaraan sebelum admin melakukan pencatatan pelunasan.

### Implementasi

View menggunakan variabel `$detailPelunasan` yang dikirimkan oleh `Pembayaran_controller.php`.

Informasi yang ditampilkan meliputi:

1. **Informasi transaksi dan penyewa**
   - Kode transaksi.
   - Nama lengkap penyewa.
   - Username.
   - NIK.
   - Nomor HP.
   - Alamat.
   - Periode sewa.
   - Waktu pengambilan kendaraan.
   - Status transaksi.

2. **Rincian kendaraan**
   - Nomor polisi.
   - Nama kendaraan.
   - Tarif sewa per hari.
   - Lama sewa.
   - Subtotal setiap kendaraan.

3. **Ringkasan pembayaran**
   - Total transaksi.
   - Total pembayaran valid sebelumnya.
   - Sisa kewajiban pelunasan.

4. **Riwayat pembayaran**
   - Kode pembayaran.
   - Tanggal pembayaran.
   - Jenis pembayaran.
   - Metode pembayaran.
   - Nominal pembayaran.
   - Status pembayaran.
   - Tautan untuk melihat bukti pembayaran apabila tersedia.

### Implementasi Formulir Pelunasan

Pada halaman detail, saya menyediakan formulir pencatatan pelunasan yang dapat digunakan admin untuk mencatat pembayaran akhir penyewa.

Ketentuan formulir yang diterapkan:

1. Formulir menggunakan metode pengiriman `POST`.
2. Formulir diarahkan ke route proses pelunasan berdasarkan kode transaksi.
3. Formulir menggunakan `enctype="multipart/form-data"` untuk mendukung unggah bukti transfer.
4. Admin wajib memilih metode pembayaran.
5. Metode pembayaran yang tersedia adalah tunai dan transfer.
6. Jika metode transfer dipilih, sistem menampilkan input untuk mengunggah bukti transfer.
7. Bukti transfer wajib disertakan apabila metode pembayaran yang dipilih adalah transfer.
8. Nominal pelunasan tidak dimasukkan secara bebas oleh admin karena sistem menggunakan seluruh nilai sisa kewajiban yang telah dihitung oleh Model.

Bukti transfer berasal dari penyewa, misalnya melalui WhatsApp, kemudian diunggah oleh admin ke dalam aplikasi.

### Pengendalian Formulir Pelunasan

Saya juga menerapkan kondisi agar formulir pelunasan hanya ditampilkan apabila transaksi memenuhi ketentuan.

Ketentuan tersebut meliputi:

- Status transaksi memenuhi syarat, yaitu `disetujui` atau `lunas`.
- Masih terdapat sisa kewajiban pembayaran.
- Belum terdapat record pembayaran dengan jenis `pelunasan`.

Apabila transaksi sudah memiliki record pelunasan, formulir tidak ditampilkan kembali. Hal ini bertujuan untuk mencegah pencatatan pelunasan lebih dari satu kali pada transaksi yang sama.

Saya juga menambahkan tombol untuk melihat atau mencetak dokumen transaksi serta tombol untuk kembali ke halaman daftar pelunasan.

### Pengamanan Data

Seluruh data teks yang ditampilkan pada halaman detail menggunakan `htmlspecialchars()` untuk membantu mencegah penyisipan HTML atau JavaScript melalui data pengguna.

Format nominal pembayaran ditampilkan menggunakan `number_format()` agar lebih mudah dibaca.

### Hasil

Halaman detail pelunasan menyediakan informasi lengkap mengenai transaksi, kendaraan, penyewa, serta riwayat pembayaran. Admin juga dapat mencatat pelunasan melalui formulir yang telah disediakan sesuai ketentuan sistem.

## F.3. Menguji Tampilan dan Proses Pelunasan

### Deskripsi

Setelah membuat View daftar dan detail pelunasan, saya melakukan pemeriksaan terhadap tampilan dan fungsi yang tersedia pada kedua halaman tersebut.

### Daftar Pemeriksaan

Pemeriksaan dilakukan terhadap beberapa hal berikut:

1. Halaman daftar pelunasan menampilkan transaksi yang masih memiliki kewajiban pembayaran.
2. Tautan **Detail / Proses** membuka transaksi yang sesuai.
3. Informasi transaksi, penyewa, kendaraan, dan riwayat pembayaran ditampilkan dengan benar.
4. Formulir menyediakan pilihan metode pembayaran tunai dan transfer.
5. Metode transfer menyediakan input untuk mengunggah bukti transfer.
6. Formulir dikirim menggunakan metode `POST`.
7. Transaksi yang telah lunas tidak menampilkan formulir pelunasan kembali.
8. Pesan keberhasilan atau kegagalan ditampilkan sesuai hasil pemrosesan Controller.
9. Data teks ditampilkan dengan aman melalui escaping.

### Hasil Pengujian

Hasil pemeriksaan dicatat berdasarkan pengujian langsung pada aplikasi.

- Tampilan daftar pelunasan: [Isi hasil pengujian].
- Tautan detail transaksi: [Isi hasil pengujian].
- Informasi transaksi dan pembayaran: [Isi hasil pengujian].
- Pilihan metode pembayaran: [Isi hasil pengujian].
- Input bukti transfer: [Isi hasil pengujian].
- Pengiriman formulir POST: [Isi hasil pengujian].
- Pengendalian transaksi yang sudah lunas: [Isi hasil pengujian].
- Tampilan pesan dari Controller: [Isi hasil pengujian].
- Pengamanan output: [Isi hasil pengujian].

## F.4. Memperbarui Dokumentasi dan Git

### Deskripsi

Setelah pembuatan dan pemeriksaan View selesai, saya memperbarui dokumentasi pada file `README.md` untuk mencatat perubahan yang dilakukan pada tahap F.

Dokumentasi mencakup pembuatan halaman daftar pelunasan, halaman detail pelunasan, informasi yang ditampilkan, formulir pencatatan pelunasan, serta hasil pemeriksaan dan pengujian.

### Implementasi

Langkah-langkah yang dilakukan:

1. Memperbarui dokumentasi tahap F pada file `pertemuan-10/README.md`.
2. Mencatat pembuatan file `pelunasan.php`.
3. Mencatat pembuatan file `pelunasan_detail.php`.
4. Mencatat informasi dan formulir yang tersedia pada kedua halaman.
5. Mencatat hasil pemeriksaan dan pengujian aktual.
6. Menyalin seluruh isi workspace `dpwl-nim/` ke folder arsip `dpwl-nim-nama/pertemuan-10/` dengan menimpa berkas yang sesuai.
7. Membuka Terminal pada direktori repositori Git.
8. Menyimpan perubahan menggunakan perintah Git berikut secara berurutan.

### Perintah Git

```bash
git add .
git commit -m "Menambahkan view daftar dan detail pelunasan P10"
git push
```

### Hasil

Tahap F menghasilkan dua halaman View, yaitu halaman daftar pelunasan dan halaman detail pelunasan. Kedua halaman tersebut digunakan admin untuk melihat transaksi yang masih memiliki kewajiban pembayaran dan mencatat pelunasan sesuai data yang disediakan oleh Model dan Controller.

Perubahan View juga diperiksa agar tidak mengganggu fitur pembayaran P9 yang telah tersedia.

# G. Membuat Dokumen Transaksi

## Deskripsi

Pada tahap ini, saya membuat dokumen transaksi yang digunakan untuk menampilkan ringkasan informasi penyewaan kendaraan dan riwayat pembayaran berdasarkan data yang tersimpan di dalam basis data.

Dokumen transaksi dibuat menggunakan pendekatan MVC. Data diperoleh melalui Model dan dikirimkan oleh Controller ke View untuk ditampilkan kepada admin.

Dokumen ini juga dilengkapi dengan fitur cetak atau simpan sebagai PDF menggunakan fasilitas cetak pada browser.

## G.1. Membuat dan Mengimplementasikan View Dokumen Transaksi

### Deskripsi

Saya membuat file View baru dengan nama `dokumen_transaksi.php` pada direktori:

`application/views/admin/dokumen_transaksi.php`

View ini menerima data `$detailPelunasan` yang telah disiapkan oleh Model dan dikirimkan melalui method `dokumenTransaksi()` pada `Pembayaran_controller.php`.

### Implementasi

Dokumen transaksi disusun menjadi beberapa bagian informasi sebagai berikut.

**1. Informasi Transaksi**

Bagian ini menampilkan informasi transaksi penyewaan, meliputi:

- Kode transaksi.
- Tanggal mulai sewa.
- Tanggal selesai sewa.
- Waktu pengambilan kendaraan.
- Status transaksi.

**2. Identitas Penyewa**

Bagian ini menampilkan informasi identitas penyewa, meliputi:

- NIK.
- Nama lengkap.
- Username.
- Nomor telepon.
- Alamat.

**3. Rincian Kendaraan**

Rincian kendaraan ditampilkan dalam bentuk tabel dengan informasi:

- Nomor polisi.
- Nama kendaraan.
- Tarif sewa per hari.
- Lama sewa.
- Subtotal.

Data rincian kendaraan menggunakan array `$detailPelunasan['daftar_kendaraan']`.

**4. Riwayat Pembayaran**

Bagian ini menampilkan seluruh riwayat pembayaran yang tercatat pada transaksi, meliputi:

- Kode pembayaran.
- Tanggal pembayaran.
- Jenis pembayaran.
- Metode pembayaran.
- Nominal pembayaran.
- Status pembayaran.

Data riwayat pembayaran menggunakan array `$detailPelunasan['riwayat_pembayaran']`.

Dengan demikian, pembayaran uang muka (DP) dan pembayaran pelunasan dapat ditampilkan dalam satu dokumen transaksi.

**5. Ringkasan Pembayaran**

Bagian ini menampilkan ringkasan nilai pembayaran, yaitu:

- Total transaksi dari `$detailPelunasan['total_transaksi']`.
- Total pembayaran valid dari `$detailPelunasan['total_terbayar']`.
- Sisa kewajiban dari `$detailPelunasan['sisa_kewajiban']`.

Seluruh informasi ditampilkan berdasarkan transaksi yang dipilih. View hanya bertugas menyajikan data dan tidak melakukan query SQL atau menghitung ulang aturan bisnis.

### Pengamanan Data

Untuk menjaga keamanan tampilan dokumen, saya menerapkan `htmlspecialchars()` terhadap data teks yang ditampilkan.

Pengamanan ini bertujuan untuk mencegah penyisipan HTML atau Java