<?php
require_once APPPATH . 'models/Penyewa_model.php';
require_once APPPATH . 'models/Transaksi_model.php';

class Penyewa_controller extends Controller
{
  private Penyewa_model $model;
  private Transaksi_model $transaksiModel;

  public function __construct()
  {
    // Dibutuhkan oleh Manajemen Penyewa (sisi admin).
    require_once APPPATH . 'helpers/auth_helper.php';
    require_once APPPATH . 'helpers/flash_helper.php';

    $this->model = new Penyewa_model();
    $this->transaksiModel = new Transaksi_model();
  }

  public function profil(): void
  {
    if (empty($_SESSION['penyewa_user_plg'])) {
      $_SESSION['flash_penyewa_nav'] =
        'Silakan login terlebih dahulu untuk mengakses profil.';

      header('Location: ' . site_url('auth/login-penyewa'));
      exit;
    }

    $userPlg = (string) $_SESSION['penyewa_user_plg'];

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      try {
        $profil = $this->model->getProfil($userPlg);

        if ($profil === null) {
          unset($_SESSION['penyewa_user_plg']);

          $_SESSION['flash_penyewa_nav'] =
            'Data akun tidak ditemukan. Silakan login kembali.';

          header('Location: ' . site_url('auth/login-penyewa'));
          exit;
        }

        $flash = $_SESSION['flash_profil_penyewa'] ?? null;
        unset($_SESSION['flash_profil_penyewa']);

        $jumlahKeranjang = $this->transaksiModel->getJumlahMobilKeranjang($userPlg);

        $this->view('penyewa/profil', [
          'profil' => $profil,
          'flash' => $flash,
          'error' => null,
          'old' => [],
          'jumlahKeranjang' => $jumlahKeranjang
        ]);

        return;
      } catch (Throwable $e) {
        error_log('Pengambilan profil penyewa gagal: ' . $e->getMessage());

        http_response_code(500);
        exit('Profil belum dapat ditampilkan. Silakan coba kembali.');
      }
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      header('Allow: GET, POST');
      exit('Method Not Allowed');
    }

    $nik = trim((string) ($_POST['nik'] ?? ''));
    $nama = trim((string) ($_POST['nm_lngkp'] ?? ''));
    $noHp = trim((string) ($_POST['no_hp'] ?? ''));
    $alamat = trim((string) ($_POST['almt_lngkp'] ?? ''));
    $passwordSaatIni = (string) ($_POST['password_saat_ini'] ?? '');
    $passwordBaru = (string) ($_POST['password_baru'] ?? '');
    $konfirmasiPassword = (string) (
      $_POST['konfirmasi_password'] ?? ''
    );

    $old = [
      'nik' => $nik,
      'nm_lngkp' => $nama,
      'no_hp' => $noHp,
      'almt_lngkp' => $alamat
    ];

    $error = $this->validateProfilInput($nik, $nama, $noHp, $alamat);

    try {
      $profil = $this->model->getProfil($userPlg);

      if ($profil === null) {
        unset($_SESSION['penyewa_user_plg']);

        $_SESSION['flash_penyewa_nav'] =
          'Data akun tidak ditemukan. Silakan login kembali.';

        header('Location: ' . site_url('auth/login-penyewa'));
        exit;
      }

      $passwordHashBaru = null;

      $inginGantiPassword =
        $passwordSaatIni !== '' ||
        $passwordBaru !== '' ||
        $konfirmasiPassword !== '';

      if ($inginGantiPassword) {
        if (
          $passwordSaatIni === '' ||
          $passwordBaru === '' ||
          $konfirmasiPassword === ''
        ) {
          $error = 'Untuk mengganti password, ketiga kolom password wajib diisi.';
        } elseif (strlen($passwordBaru) < 8) {
          $error = 'Password baru minimal 8 karakter.';
        } elseif (strlen($passwordBaru) > 72) {
          $error = 'Password baru maksimal 72 byte.';
        } elseif ($passwordBaru !== $konfirmasiPassword) {
          $error = 'Konfirmasi password baru tidak sama.';
        } elseif (!$this->model->verifikasiPassword(
          $userPlg,
          $passwordSaatIni
        )) {
          $error = 'Password saat ini tidak sesuai.';
        } else {
          $passwordHashBaru = password_hash(
            $passwordBaru,
            PASSWORD_DEFAULT
          );

          if ($passwordHashBaru === false) {
            throw new RuntimeException(
              'Pembuatan hash password baru gagal.'
            );
          }
        }
      }

      $jumlahKeranjang = $this->transaksiModel->getJumlahMobilKeranjang($userPlg);

      if ($error !== null) {
        $this->view('penyewa/profil', [
          'profil' => $profil,
          'flash' => null,
          'error' => $error,
          'old' => $old,
          'jumlahKeranjang' => $jumlahKeranjang
        ]);

        return;
      }

      $uploadedFiles = [];

      $uploadKtp = $this->uploadFotoPenyewa('ft_ktp');

      if (!$uploadKtp['success']) {
        $this->renderProfilError(
          $profil,
          $old,
          $uploadKtp['error']
        );
        return;
      }

      if ($uploadKtp['filename'] !== null) {
        $uploadedFiles[] = $uploadKtp['filename'];
      }

      $uploadSim = $this->uploadFotoPenyewa('ft_sim_a');

      if (!$uploadSim['success']) {
        $this->hapusFotoPenyewa($uploadedFiles);

        $this->renderProfilError(
          $profil,
          $old,
          $uploadSim['error']
        );
        return;
      }

      if ($uploadSim['filename'] !== null) {
        $uploadedFiles[] = $uploadSim['filename'];
      }

      // Foto lama dipertahankan jika tidak ada file baru.
      $ftKtpBaru = $uploadKtp['filename']
        ?? ($profil['ft_ktp'] ?? null);

      $ftSimBaru = $uploadSim['filename']
        ?? ($profil['ft_sim_a'] ?? null);

      $success = $this->model->updateProfil(
        $userPlg,
        $nik,
        $nama,
        $noHp,
        $alamat,
        $ftKtpBaru,
        $ftSimBaru,
        $passwordHashBaru
      );

      if (!$success) {
        $this->hapusFotoPenyewa($uploadedFiles);

        $this->renderProfilError(
          $profil,
          $old,
          'Profil gagal diperbarui. Periksa kembali data Anda.'
        );
        return;
      }

      // Hapus foto lama hanya setelah database berhasil diperbarui.
      $fotoLama = [];

      if (
        $uploadKtp['filename'] !== null &&
        !empty($profil['ft_ktp'])
      ) {
        $fotoLama[] = [
          'ft_ktp',
          $profil['ft_ktp']
        ];
      }

      if (
        $uploadSim['filename'] !== null &&
        !empty($profil['ft_sim_a'])
      ) {
        $fotoLama[] = [
          'ft_sim_a',
          $profil['ft_sim_a']
        ];
      }

      $this->hapusFotoPenyewa($fotoLama);

      $_SESSION['flash_profil_penyewa'] =
        'Profil berhasil diperbarui.';

      header('Location: ' . site_url('penyewa/profil'));
      exit;
    } catch (Throwable $e) {
      error_log('Update profil penyewa gagal: ' . $e->getMessage());

      if (!empty($uploadedFiles)) {
        $this->hapusFotoPenyewa($uploadedFiles);
      }

      $this->renderProfilError(
        $profil ?? [],
        $old,
        'Profil belum berhasil diperbarui. Silakan coba kembali.'
      );
    }
  }

  // =====================================================================
  // MANAJEMEN PENYEWA (SISI ADMIN) - CRUD, pola sama dengan Mobil_controller
  // =====================================================================

  public function index(): void
  {
    // Semua halaman/proses Manajemen Penyewa wajib login admin.
    require_admin_login();

    // Tabel dan form berada pada satu View: admin/penyewa_view.php.
    $this->view('admin/penyewa_view', [
      'username' => $_SESSION['admin_username'],
      'penyewa' => $this->model->getAll(),
      'flash' => get_flash_message(),
      'mode' => null,
      'penyewa_dipilih' => null,
      'error' => null,
    ]);
  }

  private function renderPenyewa(
    string $mode,
    ?array $penyewaDipilih = null,
    ?string $error = null
  ): void {
    // Ambil ulang daftar agar tabel tetap tampil saat form gagal divalidasi.
    $this->view('admin/penyewa_view', [
      'username' => $_SESSION['admin_username'],
      'penyewa' => $this->model->getAll(),
      'flash' => null,
      'mode' => $mode,
      'penyewa_dipilih' => $penyewaDipilih,
      'error' => $error,
    ]);
  }

  public function tambah(): void
  {
    require_admin_login();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      $this->renderPenyewa('tambah', []);
      return;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      exit('Metode request tidak diizinkan.');
    }

    $userPlg = trim((string) ($_POST['user_plg'] ?? ''));
    $sandi = (string) ($_POST['sandi'] ?? '');
    $nik = trim((string) ($_POST['nik'] ?? ''));
    $nama = trim((string) ($_POST['nm_lngkp'] ?? ''));
    $noHp = trim((string) ($_POST['no_hp'] ?? ''));
    $alamat = trim((string) ($_POST['almt_lngkp'] ?? ''));

    // Data untuk mengisi ulang form. Password tidak pernah dikembalikan ke View.
    $old = [
      'user_plg' => $userPlg,
      'nik' => $nik,
      'nm_lngkp' => $nama,
      'no_hp' => $noHp,
      'almt_lngkp' => $alamat,
      'ft_ktp' => null,
      'ft_sim_a' => null,
    ];

    $error = $this->validatePenyewaInput(
      $userPlg,
      $sandi,
      $nik,
      $nama,
      $noHp,
      $alamat,
      false
    );

    if ($error !== null) {
      $this->renderPenyewa('tambah', $old, $error);
      return;
    }

    try {
      // user_plg adalah PRIMARY KEY, cek lebih dulu sebelum unggah foto.
      if ($this->model->findByUserPlg($userPlg) !== null) {
        $this->renderPenyewa('tambah', $old, 'Username sudah digunakan.');
        return;
      }
    } catch (Throwable $e) {
      error_log('Cek username penyewa gagal: ' . $e->getMessage());
      $this->renderPenyewa(
        'tambah',
        $old,
        'Data penyewa belum dapat diproses. Silakan coba kembali.'
      );
      return;
    }

    $upload = $this->uploadDokumenPenyewa();

    if (!$upload['success']) {
      $this->renderPenyewa('tambah', $old, $upload['error']);
      return;
    }

    // Password tidak disimpan sebagai plainteks.
    $passwordHash = password_hash($sandi, PASSWORD_DEFAULT);

    try {
      if ($passwordHash === false) {
        throw new RuntimeException('Pembuatan hash password gagal.');
      }

      $success = $this->model->createByAdmin([
        'user_plg' => $userPlg,
        'sandi' => $passwordHash,
        'nik' => $nik,
        'nm_lngkp' => $nama,
        'no_hp' => $noHp,
        'almt_lngkp' => $alamat,
        'ft_ktp' => $upload['foto']['ft_ktp'],
        'ft_sim_a' => $upload['foto']['ft_sim_a'],
      ]);
    } catch (Throwable $e) {
      error_log('Tambah penyewa gagal: ' . $e->getMessage());
      $success = false;
    }

    if (!$success) {
      // Foto yang sudah terlanjur diunggah dibersihkan agar tidak jadi sampah.
      $this->hapusFotoPenyewa($upload['uploaded']);

      $this->renderPenyewa(
        'tambah',
        $old,
        'Data penyewa gagal disimpan. Periksa kembali data yang dimasukkan.'
      );
      return;
    }

    // PRG: POST -> flash -> redirect -> GET /admin/penyewa.
    set_flash_message('success', 'Data penyewa berhasil ditambahkan.');
    header('Location: ' . site_url('admin/penyewa'));
    exit;
  }

  public function ubah(string $userPlg): void
  {
    require_admin_login();

    $lama = $this->model->getProfil($userPlg);

    if ($lama === null) {
      set_flash_message('danger', 'Data penyewa tidak ditemukan.');
      header('Location: ' . site_url('admin/penyewa'));
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      $this->renderPenyewa('ubah', $lama);
      return;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      exit('Metode request tidak diizinkan.');
    }

    // user_plg tetap dari parameter route; tidak bisa diubah dari form.
    $sandi = (string) ($_POST['sandi'] ?? '');
    $nik = trim((string) ($_POST['nik'] ?? ''));
    $nama = trim((string) ($_POST['nm_lngkp'] ?? ''));
    $noHp = trim((string) ($_POST['no_hp'] ?? ''));
    $alamat = trim((string) ($_POST['almt_lngkp'] ?? ''));

    $tampil = array_merge($lama, [
      'nik' => $nik,
      'nm_lngkp' => $nama,
      'no_hp' => $noHp,
      'almt_lngkp' => $alamat,
    ]);

    $error = $this->validatePenyewaInput(
      $userPlg,
      $sandi,
      $nik,
      $nama,
      $noHp,
      $alamat,
      true
    );

    if ($error !== null) {
      $this->renderPenyewa('ubah', $tampil, $error);
      return;
    }

    $upload = $this->uploadDokumenPenyewa();

    if (!$upload['success']) {
      $this->renderPenyewa('ubah', $tampil, $upload['error']);
      return;
    }

    // Foto lama dipertahankan bila tidak ada file baru.
    $ftKtp = $upload['foto']['ft_ktp'] ?? ($lama['ft_ktp'] ?? null);
    $ftSimA = $upload['foto']['ft_sim_a'] ?? ($lama['ft_sim_a'] ?? null);

    try {
      // Password kosong = hash lama dipertahankan.
      $passwordHash = null;

      if ($sandi !== '') {
        $passwordHash = password_hash($sandi, PASSWORD_DEFAULT);

        if ($passwordHash === false) {
          throw new RuntimeException('Pembuatan hash password gagal.');
        }
      }

      $success = $this->model->updateProfil(
        $userPlg,
        $nik,
        $nama,
        $noHp,
        $alamat,
        $ftKtp,
        $ftSimA,
        $passwordHash
      );
    } catch (Throwable $e) {
      error_log('Ubah penyewa gagal: ' . $e->getMessage());
      $success = false;
    }

    if (!$success) {
      $this->hapusFotoPenyewa($upload['uploaded']);

      $this->renderPenyewa(
        'ubah',
        $tampil,
        'Data penyewa gagal diperbarui. Periksa kembali data yang dimasukkan.'
      );
      return;
    }

    // Foto lama baru dihapus SETELAH database berhasil diperbarui.
    $fotoLama = [];

    if ($upload['foto']['ft_ktp'] !== null && !empty($lama['ft_ktp'])) {
      $fotoLama[] = ['ft_ktp', $lama['ft_ktp']];
    }

    if ($upload['foto']['ft_sim_a'] !== null && !empty($lama['ft_sim_a'])) {
      $fotoLama[] = ['ft_sim_a', $lama['ft_sim_a']];
    }

    $this->hapusFotoPenyewa($fotoLama);

    set_flash_message('success', 'Data penyewa berhasil diperbarui.');
    header('Location: ' . site_url('admin/penyewa'));
    exit;
  }

  public function hapus(string $userPlg): void
  {
    require_admin_login();

    // DELETE mengubah data, jadi wajib POST.
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      exit('Metode request tidak diizinkan.');
    }

    $penyewa = $this->model->getProfil($userPlg);

    if ($penyewa === null) {
      set_flash_message('danger', 'Data penyewa tidak ditemukan.');
      header('Location: ' . site_url('admin/penyewa'));
      exit;
    }

    $pesanGagal = 'Data penyewa gagal dihapus.';

    try {
      $success = $this->model->delete($userPlg);
    } catch (Throwable $e) {
      error_log('Hapus penyewa gagal: ' . $e->getMessage());
      $success = false;

      // 1451 = baris masih dirujuk tabel lain (foreign key), mis. riwayat sewa.
      if ($e instanceof mysqli_sql_exception && (int) $e->getCode() === 1451) {
        $pesanGagal = 'Penyewa tidak dapat dihapus karena masih memiliki data transaksi sewa.';
      }
    }

    if (!$success) {
      set_flash_message('danger', $pesanGagal);
      header('Location: ' . site_url('admin/penyewa'));
      exit;
    }

    // Berkas KTP/SIM dihapus hanya setelah baris database berhasil dihapus.
    $fotoUntukDihapus = [];

    if (!empty($penyewa['ft_ktp'])) {
      $fotoUntukDihapus[] = ['ft_ktp', $penyewa['ft_ktp']];
    }

    if (!empty($penyewa['ft_sim_a'])) {
      $fotoUntukDihapus[] = ['ft_sim_a', $penyewa['ft_sim_a']];
    }

    $this->hapusFotoPenyewa($fotoUntukDihapus);

    set_flash_message('success', 'Data penyewa berhasil dihapus.');
    header('Location: ' . site_url('admin/penyewa'));
    exit;
  }

  // Validasi sisi peladen untuk tambah (isEdit=false) dan ubah (isEdit=true).
  private function validatePenyewaInput(
    string $userPlg,
    string $sandi,
    string $nik,
    string $nama,
    string $noHp,
    string $alamat,
    bool $isEdit
  ): ?string {
    // Username hanya divalidasi saat tambah; saat ubah ia kunci record.
    if (
      !$isEdit &&
      !preg_match('/\A[A-Za-z0-9._-]{1,15}\z/', $userPlg)
    ) {
      return 'Username harus 1–15 karakter: huruf, angka, titik, garis bawah, atau tanda hubung.';
    }

    // Saat tambah password wajib; saat ubah boleh kosong (tidak diganti).
    if (!$isEdit || $sandi !== '') {
      if (strlen($sandi) < 8) {
        return 'Password minimal 8 karakter.';
      }

      if (strlen($sandi) > 72) {
        return 'Password maksimal 72 byte.';
      }
    }

    // NIK, nama, no HP, alamat memakai aturan yang sama dengan profil.
    return $this->validateProfilInput($nik, $nama, $noHp, $alamat);
  }

  // Mengunggah foto KTP dan SIM A sekaligus. Bila salah satu gagal,
  // file yang sudah terlanjur terunggah langsung dihapus kembali.
  private function uploadDokumenPenyewa(): array
  {
    $foto = ['ft_ktp' => null, 'ft_sim_a' => null];
    $uploaded = [];

    foreach (['ft_ktp', 'ft_sim_a'] as $field) {
      $upload = $this->uploadFotoPenyewa($field);

      if (!$upload['success']) {
        $this->hapusFotoPenyewa($uploaded);

        return [
          'success' => false,
          'foto' => $foto,
          'uploaded' => [],
          'error' => $upload['error'],
        ];
      }

      if ($upload['filename'] !== null) {
        $foto[$field] = $upload['filename'];
        $uploaded[] = $upload['filename'];
      }
    }

    return [
      'success' => true,
      'foto' => $foto,
      'uploaded' => $uploaded,
      'error' => null,
    ];
  }

  private function validateProfilInput(
    string $nik,
    string $nama,
    string $noHp,
    string $alamat
  ): ?string {
    if (!preg_match('/\A[0-9]{16}\z/', $nik)) {
      return 'NIK wajib terdiri dari tepat 16 digit angka.';
    }

    if (
      $nama === '' ||
      mb_strlen($nama) > 100 ||
      !preg_match(
        "/\A[\p{L}\p{M}]+(?:[ .'-][\p{L}\p{M}]+)*\z/u",
        $nama
      )
    ) {
      return 'Nama lengkap wajib diisi, maksimal 100 karakter, dan hanya berisi huruf serta pemisah nama yang wajar.';
    }

    if (
      $noHp === '' ||
      strlen($noHp) > 15 ||
      !preg_match('/\A[0-9+(). -]+\z/', $noHp) ||
      !preg_match('/[0-9]/', $noHp)
    ) {
      return 'Nomor HP wajib diisi, maksimal 15 karakter, dan hanya boleh berisi angka serta tanda +, titik, kurung, spasi, atau tanda hubung.';
    }

    if (
      $alamat === '' ||
      mb_strlen($alamat) > 300 ||
      preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $alamat)
    ) {
      return 'Alamat wajib diisi, maksimal 300 karakter, dan tidak boleh mengandung karakter kontrol.';
    }

    return null;
  }

  private function renderProfilError(
    array $profil,
    array $old,
    string $error
  ): void {
    $userPlg = trim((string) ($_SESSION['penyewa_user_plg'] ?? ''));
    $jumlahKeranjang = $userPlg === ''
      ? 0
      : $this->transaksiModel->getJumlahMobilKeranjang($userPlg);

    $this->view('penyewa/profil', [
      'profil' => $profil,
      'flash' => null,
      'error' => $error,
      'old' => $old,
      'jumlahKeranjang' => $jumlahKeranjang
    ]);
  }

  private function uploadFotoPenyewa(string $field): array
  {
    if (!in_array($field, ['ft_ktp', 'ft_sim_a'], true)) {
      return [
        'success' => false,
        'filename' => null,
        'error' => 'Field foto tidak valid.'
      ];
    }

    if (
      !isset($_FILES[$field]) ||
      ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE)
      === UPLOAD_ERR_NO_FILE
    ) {
      return [
        'success' => true,
        'filename' => null,
        'error' => null
      ];
    }

    $file = $_FILES[$field];

    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
      return [
        'success' => false,
        'filename' => null,
        'error' => 'Unggah foto gagal. Periksa ukuran file dan konfigurasi PHP.'
      ];
    }

    if (
      !isset($file['tmp_name'], $file['size']) ||
      !is_uploaded_file($file['tmp_name']) ||
      (int) $file['size'] < 1 ||
      (int) $file['size'] > 5 * 1024 * 1024
    ) {
      return [
        'success' => false,
        'filename' => null,
        'error' => 'Foto harus berukuran lebih dari 0 byte dan maksimal 5 MB.'
      ];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    $allowed = [
      'image/jpeg' => 'jpg',
      'image/png' => 'png',
      'image/webp' => 'webp'
    ];

    if (
      !isset($allowed[$mime]) ||
      @getimagesize($file['tmp_name']) === false
    ) {
      return [
        'success' => false,
        'filename' => null,
        'error' => 'Format foto harus JPEG, PNG, atau WEBP yang valid.'
      ];
    }

    $projectRoot = dirname(APPPATH);
    $uploadDir = $projectRoot . DIRECTORY_SEPARATOR . 'uploads'
      . DIRECTORY_SEPARATOR . 'penyewa' . DIRECTORY_SEPARATOR;

    if (
      !is_dir($uploadDir) &&
      !mkdir($uploadDir, 0755, true) &&
      !is_dir($uploadDir)
    ) {
      return [
        'success' => false,
        'filename' => null,
        'error' => 'Folder penyimpanan foto penyewa tidak dapat dibuat.'
      ];
    }

    if (!is_writable($uploadDir)) {
      return [
        'success' => false,
        'filename' => null,
        'error' => 'Folder penyimpanan foto penyewa tidak dapat ditulis.'
      ];
    }

    try {
      $filename = $field . '_' . date('YmdHis') . '_'
        . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
    } catch (Throwable $e) {
      return [
        'success' => false,
        'filename' => null,
        'error' => 'Nama file foto gagal dibuat.'
      ];
    }

    if (!move_uploaded_file(
      $file['tmp_name'],
      $uploadDir . $filename
    )) {
      return [
        'success' => false,
        'filename' => null,
        'error' => 'Foto gagal dipindahkan ke folder penyimpanan.'
      ];
    }

    return [
      'success' => true,
      'filename' => $filename,
      'error' => null
    ];
  }

  private function hapusFotoPenyewa(array $files): void
  {
    $uploadDir = dirname(APPPATH) . DIRECTORY_SEPARATOR . 'uploads'
      . DIRECTORY_SEPARATOR . 'penyewa' . DIRECTORY_SEPARATOR;

    foreach ($files as $item) {
      if (is_array($item)) {
        [$field, $filename] = $item;
      } else {
        // File baru yang diunggah pada request ini.
        $filename = $item;
        $field = is_string($filename)
          ? (str_starts_with($filename, 'ft_ktp_') ? 'ft_ktp' : 'ft_sim_a')
          : '';
      }

      if (
        !in_array($field, ['ft_ktp', 'ft_sim_a'], true) ||
        !is_string($filename)
      ) {
        continue;
      }

      // Nama file baru dan pola file lama yang telah ada di data.
      $pattern = '/^' . preg_quote($field, '/') .
        '_\d{10,14}_[a-f0-9]{12,16}\.(jpg|jpeg|png|webp)$/';

      if (!preg_match($pattern, $filename)) {
        continue;
      }

      $path = $uploadDir . $filename;

      if (is_file($path)) {
        @unlink($path);
      }
    }
  }
}