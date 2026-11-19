<?php
// P9 BARU - application/controllers/Pembayaran_controller.php
// Sisi penyewa: daftar transaksi, halaman pembayaran DP, unggah bukti,
// dan penyajian bukti milik sendiri.

require_once APPPATH . 'models/Pembayaran_model.php';
require_once APPPATH . 'models/Transaksi_model.php';

class Pembayaran_controller extends Controller
{
  private Pembayaran_model $model;
  private Transaksi_model $transaksiModel;

  public function __construct()
  {
    require_once APPPATH . 'helpers/csrf_helper.php';
    require_once APPPATH . 'helpers/bukti_helper.php';

    $this->model = new Pembayaran_model();
    $this->transaksiModel = new Transaksi_model();
  }

  // GET transaksi/riwayat
  public function riwayat(): void
  {
    $userPlg = $this->wajibLoginPenyewa();
    $this->wajibMetode('GET');

    try {
      $this->view('transaksi/riwayat', [
        'daftar' => $this->model->getDaftarTransaksiPenyewa($userPlg),
        'jumlahKeranjang' => $this->transaksiModel
          ->getJumlahMobilKeranjang($userPlg),
      ]);
    } catch (Throwable $e) {
      error_log('Tampilan riwayat transaksi gagal: ' . $e->getMessage());
      http_response_code(500);
      exit('Riwayat transaksi belum dapat ditampilkan. Silakan coba kembali.');
    }
  }

  // GET transaksi/pembayaran?kode_sewa=...
  public function pembayaran(): void
  {
    $userPlg = $this->wajibLoginPenyewa();
    $this->wajibMetode('GET');

    $kodeSewa = trim((string) ($_GET['kode_sewa'] ?? ''));

    if (!$this->kodeValid($kodeSewa)) {
      $this->redirectDenganPesan(
        'transaksi/riwayat',
        'error',
        'Kode transaksi tidak valid.'
      );
    }

    try {
      $detail = $this->model->getDetailMilik($kodeSewa, $userPlg);

      if ($detail === null) {
        http_response_code(404);
        exit('Transaksi tidak ditemukan atau bukan milik akun ini.');
      }

      $this->view('transaksi/pembayaran', [
        'detail' => $detail,
        'jumlahKeranjang' => $this->transaksiModel
          ->getJumlahMobilKeranjang($userPlg),
      ]);
    } catch (Throwable $e) {
      error_log('Tampilan pembayaran gagal: ' . $e->getMessage());
      http_response_code(500);
      exit('Halaman pembayaran belum dapat ditampilkan. Silakan coba kembali.');
    }
  }

  // POST transaksi/pembayaran/unggah  (PRG: selalu berakhir dengan redirect)
  public function unggah(): void
  {
    $userPlg = $this->wajibLoginPenyewa();
    $this->wajibMetode('POST');

    $kodeSewa = trim((string) ($_POST['kode_sewa'] ?? ''));

    // Bila ukuran kiriman melebihi post_max_size, PHP mengosongkan $_POST
    // dan $_FILES sekaligus.
    if (
      $kodeSewa === '' &&
      empty($_FILES) &&
      (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0
    ) {
      $this->redirectDenganPesan(
        'transaksi/riwayat',
        'error',
        'Ukuran kiriman melebihi batas server. Gunakan berkas maksimal 5 MB.'
      );
    }

    if (!$this->kodeValid($kodeSewa)) {
      $this->redirectDenganPesan(
        'transaksi/riwayat',
        'error',
        'Kode transaksi tidak valid.'
      );
    }

    $kembali = 'transaksi/pembayaran?kode_sewa=' . rawurlencode($kodeSewa);

    if (!csrf_valid()) {
      $this->redirectDenganPesan(
        $kembali,
        'error',
        'Permintaan tidak valid atau kedaluwarsa. Muat ulang halaman dan coba lagi.'
      );
    }

    // Pemeriksaan awal (tanpa kunci) sebelum berkas dipindahkan ke disk.
    // Keputusan akhir tetap diambil model di dalam transaksi database.
    try {
      $detail = $this->model->getDetailMilik($kodeSewa, $userPlg);
    } catch (Throwable $e) {
      error_log('Pemeriksaan awal unggah bukti gagal: ' . $e->getMessage());
      $this->redirectDenganPesan(
        $kembali,
        'error',
        'Bukti pembayaran belum dapat diproses. Silakan coba kembali.'
      );
    }

    if ($detail === null) {
      $this->redirectDenganPesan(
        'transaksi/riwayat',
        'error',
        'Transaksi tidak ditemukan atau bukan milik akun ini.'
      );
    }

    if (
      !Pembayaran_model::bolehUnggah(
        $detail['sttus_byr'] ?? null,
        $detail['ft_byr'] ?? null,
        $detail['status_sewa'] ?? null
      )
    ) {
      $this->redirectDenganPesan(
        $kembali,
        'error',
        'Transaksi ini tidak dapat menerima bukti pembayaran saat ini.'
      );
    }

    $upload = bukti_bayar_unggah('ft_byr');

    if (!$upload['success']) {
      $this->redirectDenganPesan($kembali, 'error', (string) $upload['error']);
    }

    $namaBaru = (string) $upload['filename'];

    try {
      $berkasLama = $this->model->simpanBuktiDp($kodeSewa, $userPlg, $namaBaru);

      // Berkas lama (setelah penolakan) dihapus hanya setelah commit berhasil.
      bukti_bayar_hapus($berkasLama);

      $this->redirectDenganPesan(
        $kembali,
        'success',
        'Bukti pembayaran berhasil diunggah dan menunggu verifikasi admin.'
      );
    } catch (InvalidArgumentException $e) {
      bukti_bayar_hapus($namaBaru);
      $this->redirectDenganPesan($kembali, 'error', $e->getMessage());
    } catch (LogicException $e) {
      bukti_bayar_hapus($namaBaru);
      error_log('Konfigurasi pembayaran tidak sesuai: ' . $e->getMessage());
      $this->redirectDenganPesan(
        $kembali,
        'error',
        'Konfigurasi pembayaran belum sesuai dengan basis data. Hubungi admin.'
      );
    } catch (Throwable $e) {
      bukti_bayar_hapus($namaBaru);
      error_log('Unggah bukti pembayaran gagal: ' . $e->getMessage());
      $this->redirectDenganPesan(
        $kembali,
        'error',
        'Bukti pembayaran belum berhasil disimpan. Silakan coba kembali.'
      );
    }
  }

  // GET transaksi/bukti?kode_sewa=...  (hanya pemilik transaksi)
  public function bukti(): void
  {
    $userPlg = $this->wajibLoginPenyewa();
    $this->wajibMetode('GET');

    $kodeSewa = trim((string) ($_GET['kode_sewa'] ?? ''));

    if (!$this->kodeValid($kodeSewa)) {
      http_response_code(400);
      exit('Kode transaksi tidak valid.');
    }

    try {
      $detail = $this->model->getDetailMilik($kodeSewa, $userPlg);
    } catch (Throwable $e) {
      error_log('Pengambilan bukti pembayaran gagal: ' . $e->getMessage());
      http_response_code(500);
      exit('Bukti pembayaran belum dapat ditampilkan.');
    }

    if ($detail === null) {
      http_response_code(404);
      exit('Bukti pembayaran tidak ditemukan.');
    }

    bukti_bayar_kirim($detail['ft_byr'] ?? null);
  }

  // ---------------------------------------------------------------------
  // Pembantu (pola sama dengan Transaksi_controller)
  // ---------------------------------------------------------------------

  private function wajibLoginPenyewa(): string
  {
    $userPlg = trim((string) ($_SESSION['penyewa_user_plg'] ?? ''));

    if ($userPlg === '') {
      $_SESSION['flash_penyewa_nav'] =
        'Silakan login terlebih dahulu untuk mengakses pembayaran.';

      header('Location: ' . site_url('auth/login-penyewa'));
      exit;
    }

    return $userPlg;
  }

  private function wajibMetode(string $metodeDiizinkan): void
  {
    $metodeAktif = strtoupper(
      (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')
    );

    if ($metodeAktif !== $metodeDiizinkan) {
      http_response_code(405);
      header('Allow: ' . $metodeDiizinkan);
      exit('Method Not Allowed');
    }
  }

  // Kode transaksi dikirim lewat query string/body (bukan segmen URL) karena
  // formatnya INV/YYYYMMDD/NN memuat garis miring. Format tidak dikunci ketat;
  // keamanan query dijamin prepared statement.
  private function kodeValid(string $kode): bool
  {
    return $kode !== ''
      && strlen($kode) <= 30
      && preg_match('/\A[\x21-\x7E]+\z/', $kode) === 1;
  }

  private function redirectDenganPesan(
    string $uri,
    string $jenis,
    string $pesan
  ): never {
    $_SESSION['flash_transaksi'] = [
      'type' => $jenis,
      'message' => $pesan,
    ];

    header('Location: ' . site_url($uri));
    exit;
  }
}
