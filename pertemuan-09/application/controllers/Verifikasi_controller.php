<?php
// P9 BARU - application/controllers/Verifikasi_controller.php
// Sisi admin: daftar pembayaran DP, detail + bukti, verifikasi, dan penolakan.
// Semua method wajib login admin.

require_once APPPATH . 'models/Pembayaran_model.php';

class Verifikasi_controller extends Controller
{
  private Pembayaran_model $model;

  public function __construct()
  {
    require_once APPPATH . 'helpers/auth_helper.php';
    require_once APPPATH . 'helpers/flash_helper.php';
    require_once APPPATH . 'helpers/csrf_helper.php';
    require_once APPPATH . 'helpers/bukti_helper.php';

    $this->model = new Pembayaran_model();
  }

  // GET admin/pembayaran
  public function index(): void
  {
    require_admin_login();
    $this->wajibMetode('GET');

    try {
      $this->view('admin/pembayaran_view', [
        'username' => $_SESSION['admin_username'],
        'flash' => get_flash_message(),
        'mode' => null,
        'daftar' => $this->model->getDaftarVerifikasi(),
        'detail' => null,
      ]);
    } catch (Throwable $e) {
      error_log('Tampilan daftar verifikasi gagal: ' . $e->getMessage());
      http_response_code(500);
      exit('Daftar pembayaran belum dapat ditampilkan. Silakan coba kembali.');
    }
  }

  // GET admin/pembayaran/detail?kode_bayar=...
  public function detail(): void
  {
    require_admin_login();
    $this->wajibMetode('GET');

    $kodeBayar = trim((string) ($_GET['kode_bayar'] ?? ''));

    if (!$this->kodeValid($kodeBayar)) {
      $this->redirectDenganPesan(
        'danger',
        'Kode pembayaran tidak valid.',
        'admin/pembayaran'
      );
    }

    try {
      $detail = $this->model->getDetailByKodeBayar($kodeBayar);

      if ($detail === null) {
        $this->redirectDenganPesan(
          'danger',
          'Data pembayaran tidak ditemukan.',
          'admin/pembayaran'
        );
      }

      $this->view('admin/pembayaran_view', [
        'username' => $_SESSION['admin_username'],
        'flash' => get_flash_message(),
        'mode' => 'detail',
        'daftar' => [],
        'detail' => $detail,
      ]);
    } catch (Throwable $e) {
      error_log('Tampilan detail verifikasi gagal: ' . $e->getMessage());
      http_response_code(500);
      exit('Detail pembayaran belum dapat ditampilkan. Silakan coba kembali.');
    }
  }

  // POST admin/pembayaran/verifikasi
  public function verifikasi(): void
  {
    $this->proses('verifikasi');
  }

  // POST admin/pembayaran/tolak
  public function tolak(): void
  {
    $this->proses('tolak');
  }

  // GET admin/pembayaran/bukti?kode_bayar=...
  public function bukti(): void
  {
    require_admin_login();
    $this->wajibMetode('GET');

    $kodeBayar = trim((string) ($_GET['kode_bayar'] ?? ''));

    if (!$this->kodeValid($kodeBayar)) {
      http_response_code(400);
      exit('Kode pembayaran tidak valid.');
    }

    try {
      $detail = $this->model->getDetailByKodeBayar($kodeBayar);
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

  private function proses(string $aksi): void
  {
    require_admin_login();
    $this->wajibMetode('POST');

    $kodeBayar = trim((string) ($_POST['kode_bayar'] ?? ''));

    if (!$this->kodeValid($kodeBayar)) {
      $this->redirectDenganPesan(
        'danger',
        'Kode pembayaran tidak valid.',
        'admin/pembayaran'
      );
    }

    $kembaliDetail = 'admin/pembayaran/detail?kode_bayar='
      . rawurlencode($kodeBayar);

    if (!csrf_valid()) {
      $this->redirectDenganPesan(
        'danger',
        'Permintaan tidak valid atau kedaluwarsa. Muat ulang halaman dan coba lagi.',
        $kembaliDetail
      );
    }

    try {
      if ($aksi === 'verifikasi') {
        $this->model->verifikasi($kodeBayar);
        $pesan = 'Pembayaran DP ' . $kodeBayar . ' berhasil diverifikasi.';
      } else {
        $this->model->tolak($kodeBayar);
        $pesan = 'Pembayaran DP ' . $kodeBayar
          . ' ditolak. Penyewa dapat mengunggah ulang bukti.';
      }

      $this->redirectDenganPesan('success', $pesan, 'admin/pembayaran');
    } catch (InvalidArgumentException $e) {
      $this->redirectDenganPesan('danger', $e->getMessage(), $kembaliDetail);
    } catch (LogicException $e) {
      error_log('Konfigurasi pembayaran tidak sesuai: ' . $e->getMessage());
      $this->redirectDenganPesan(
        'danger',
        'Konfigurasi status pembayaran belum sesuai dengan basis data. '
          . 'Periksa log kesalahan PHP untuk rinciannya.',
        $kembaliDetail
      );
    } catch (Throwable $e) {
      error_log('Putusan verifikasi pembayaran gagal: ' . $e->getMessage());
      $this->redirectDenganPesan(
        'danger',
        'Putusan verifikasi belum berhasil disimpan. Silakan coba kembali.',
        $kembaliDetail
      );
    }
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

  private function kodeValid(string $kode): bool
  {
    return $kode !== ''
      && strlen($kode) <= 30
      && preg_match('/\A[\x21-\x7E]+\z/', $kode) === 1;
  }

  // Flash admin memakai flash_helper: tipe 'success' atau 'danger'.
  private function redirectDenganPesan(
    string $tipe,
    string $pesan,
    string $uri
  ): never {
    set_flash_message($tipe, $pesan);
    header('Location: ' . site_url($uri));
    exit;
  }
}
