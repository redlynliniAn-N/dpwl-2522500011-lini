<?php
// P9 BARU - application/helpers/csrf_helper.php
// Token CSRF sederhana berbasis session untuk form POST yang mengubah data
// pembayaran. Mengikuti pola helper fungsi seperti auth_helper dan flash_helper.

function csrf_token(): string
{
  if (
    !isset($_SESSION['_csrf']) ||
    !is_string($_SESSION['_csrf']) ||
    $_SESSION['_csrf'] === ''
  ) {
    $_SESSION['_csrf'] = bin2hex(random_bytes(32));
  }

  return $_SESSION['_csrf'];
}

function csrf_field(): string
{
  return '<input type="hidden" name="_csrf" value="'
    . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8')
    . '">';
}

function csrf_valid(): bool
{
  $dikirim = $_POST['_csrf'] ?? '';

  return is_string($dikirim)
    && $dikirim !== ''
    && hash_equals(csrf_token(), $dikirim);
}
