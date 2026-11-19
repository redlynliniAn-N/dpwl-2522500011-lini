<?php
// P9 BARU - application/helpers/bukti_helper.php
// Penanganan berkas bukti pembayaran. Validasi unggahan mengikuti pola
// uploadFoto() pada Mobil_controller dan uploadFotoPenyewa() pada
// Penyewa_controller: finfo + getimagesize, maksimal 5 MB, nama acak.
// Penyimpanan: uploads/bayar/ . Berkas disajikan melalui controller
// (setelah pemeriksaan akses), bukan lewat URL langsung.

function bukti_bayar_dir(): string
{
  return dirname(APPPATH) . DIRECTORY_SEPARATOR . 'uploads'
    . DIRECTORY_SEPARATOR . 'bayar' . DIRECTORY_SEPARATOR;
}

function bukti_bayar_nama_valid(string $filename): bool
{
  return preg_match(
    '/\Aft_byr_\d{14}_[a-f0-9]{16}\.(jpg|png|webp)\z/',
    $filename
  ) === 1;
}

function bukti_bayar_unggah(string $field = 'ft_byr'): array
{
  $gagal = static fn(string $pesan): array => [
    'success' => false,
    'filename' => null,
    'error' => $pesan,
  ];

  if ($field !== 'ft_byr') {
    return $gagal('Field bukti pembayaran tidak valid.');
  }

  if (
    !isset($_FILES[$field]) ||
    !is_array($_FILES[$field]) ||
    ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE
  ) {
    return $gagal('Bukti pembayaran wajib dipilih.');
  }

  $file = $_FILES[$field];

  if (is_array($file['error'] ?? null) || is_array($file['tmp_name'] ?? null)) {
    return $gagal('Unggahan bukti pembayaran tidak valid.');
  }

  $kode = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);

  if ($kode === UPLOAD_ERR_INI_SIZE || $kode === UPLOAD_ERR_FORM_SIZE) {
    return $gagal('Ukuran bukti pembayaran melebihi batas. Maksimal 5 MB.');
  }

  if ($kode !== UPLOAD_ERR_OK) {
    return $gagal('Unggah bukti pembayaran gagal. Silakan coba kembali.');
  }

  if (
    !isset($file['tmp_name'], $file['size']) ||
    !is_uploaded_file($file['tmp_name']) ||
    (int) $file['size'] < 1 ||
    (int) $file['size'] > 5 * 1024 * 1024
  ) {
    return $gagal(
      'Bukti pembayaran harus berukuran lebih dari 0 byte dan maksimal 5 MB.'
    );
  }

  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mime = $finfo->file($file['tmp_name']);

  $diizinkan = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
  ];

  if (
    !is_string($mime) ||
    !isset($diizinkan[$mime]) ||
    @getimagesize($file['tmp_name']) === false
  ) {
    return $gagal('Format bukti pembayaran harus JPEG, PNG, atau WEBP yang valid.');
  }

  $dir = bukti_bayar_dir();

  if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
    return $gagal('Folder penyimpanan bukti pembayaran tidak dapat dibuat.');
  }

  if (!is_writable($dir)) {
    return $gagal('Folder penyimpanan bukti pembayaran tidak dapat ditulis.');
  }

  try {
    $nama = $field . '_' . date('YmdHis') . '_'
      . bin2hex(random_bytes(8)) . '.' . $diizinkan[$mime];
  } catch (Throwable $e) {
    return $gagal('Nama berkas bukti pembayaran gagal dibuat.');
  }

  if (!move_uploaded_file($file['tmp_name'], $dir . $nama)) {
    return $gagal('Bukti pembayaran gagal dipindahkan ke folder penyimpanan.');
  }

  return ['success' => true, 'filename' => $nama, 'error' => null];
}

// Hanya menghapus nama berkas yang dibuat oleh bukti_bayar_unggah().
function bukti_bayar_hapus(?string $filename): void
{
  if (
    !is_string($filename) ||
    $filename === '' ||
    !bukti_bayar_nama_valid($filename)
  ) {
    return;
  }

  $path = bukti_bayar_dir() . $filename;

  if (is_file($path)) {
    @unlink($path);
  }
}

// Menyajikan berkas ke browser. Pemanggil WAJIB sudah memeriksa hak akses.
function bukti_bayar_kirim(?string $filename): never
{
  $tidakAda = static function (): never {
    http_response_code(404);
    exit('Bukti pembayaran tidak ditemukan.');
  };

  if (
    !is_string($filename) ||
    $filename === '' ||
    !bukti_bayar_nama_valid($filename)
  ) {
    $tidakAda();
  }

  $path = bukti_bayar_dir() . $filename;

  if (!is_file($path) || !is_readable($path)) {
    $tidakAda();
  }

  $peta = [
    'jpg' => 'image/jpeg',
    'png' => 'image/png',
    'webp' => 'image/webp',
  ];
  $ekstensi = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mime = $finfo->file($path);

  if (!isset($peta[$ekstensi]) || $mime !== $peta[$ekstensi]) {
    $tidakAda();
  }

  $ukuran = filesize($path);

  // Lepas kunci session agar permintaan lain tidak tertahan saat streaming.
  session_write_close();

  header('Content-Type: ' . $mime);
  header('X-Content-Type-Options: nosniff');
  header('Cache-Control: private, no-store');
  header('Content-Disposition: inline; filename="bukti-pembayaran.' . $ekstensi . '"');

  if ($ukuran !== false) {
    header('Content-Length: ' . $ukuran);
  }

  readfile($path);
  exit;
}
