<?php
// P9 BARU - application/views/transaksi/pembayaran.php
require APPPATH . 'views/katalog/header.php';

$e = static function ($value): string {
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

require APPPATH . 'views/katalog/navbar.php';

$formatRupiah = static function ($nilai): string {
  return 'Rp ' . number_format((float) $nilai, 0, ',', '.');
};

$formatWaktu = static function ($nilai): string {
  if ($nilai === null || $nilai === '') {
    return '-';
  }

  $stempel = strtotime((string) $nilai);

  return $stempel === false ? (string) $nilai : date('d/m/Y H:i', $stempel);
};

$detail = is_array($detail ?? null) ? $detail : [];
$rincian = is_array($detail['detail'] ?? null) ? $detail['detail'] : [];
$kodeSewa = (string) ($detail['kode_sewa'] ?? '');
$sttus = $detail['sttus_byr'] ?? null;
$ftByr = $detail['ft_byr'] ?? null;
$statusSewa = $detail['status_sewa'] ?? null;

$kunci = Pembayaran_model::kunciStatus($sttus, $ftByr);
$tampil = Pembayaran_model::tampilStatus($kunci);
$bolehUnggah = Pembayaran_model::bolehUnggah($sttus, $ftByr, $statusSewa);
$adaBukti = $ftByr !== null && $ftByr !== '';
$urlBukti = site_url('transaksi/bukti?kode_sewa=' . rawurlencode($kodeSewa));
?>

<section class="bg-success py-4">
  <div class="container">
    <h1 class="h2 text-white mt-3">Pembayaran DP</h1>
    <p class="text-white mb-0">Unggah bukti transfer uang muka agar transaksi dapat diverifikasi admin.</p>
  </div>
</section>

<section class="py-5">
  <div class="container">

    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-light">
        <h2 class="h5 mb-0">Ringkasan Sewa</h2>
      </div>
      <div class="card-body">
        <dl class="row mb-0">
          <dt class="col-sm-4">Kode transaksi</dt>
          <dd class="col-sm-8"><?= $e($kodeSewa) ?></dd>
          <dt class="col-sm-4">Periode sewa</dt>
          <dd class="col-sm-8"><?= $e($detail['tgl_mulai'] ?? '-') ?> s.d. <?= $e($detail['tgl_selesai'] ?? '-') ?></dd>
          <dt class="col-sm-4">Waktu pengambilan</dt>
          <dd class="col-sm-8"><?= $e(substr((string) ($detail['wkt_ambil'] ?? '-'), 0, 5)) ?> WIB</dd>
          <dt class="col-sm-4">Status transaksi</dt>
          <dd class="col-sm-8"><span class="badge bg-secondary"><?= $e($statusSewa ?? '-') ?></span></dd>
        </dl>
      </div>
    </div>

    <div class="table-responsive mb-4">
      <table class="table table-bordered align-middle bg-white">
        <thead class="table-light">
          <tr>
            <th scope="col">Kendaraan</th>
            <th scope="col">Nomor Polisi</th>
            <th scope="col" class="text-end">Harga per Hari</th>
            <th scope="col" class="text-center">Durasi</th>
            <th scope="col" class="text-end">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rincian as $item): ?>
            <?php
            $harga = (int) ($item['hrg_sewa'] ?? 0);
            $hari = (int) ($item['n_hari'] ?? 0);
            ?>
            <tr>
              <td><?= $e($item['nm_mobil'] ?? '-') ?></td>
              <td><?= $e($item['no_plat'] ?? '-') ?></td>
              <td class="text-end text-nowrap"><?= $e($formatRupiah($harga)) ?></td>
              <td class="text-center"><?= $e($hari) ?> hari</td>
              <td class="text-end text-nowrap"><?= $e($formatRupiah($harga * $hari)) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="row g-4 mb-4">
      <div class="col-lg-6">
        <div class="card border-success h-100">
          <div class="card-header bg-light">
            <h2 class="h5 mb-0">Pembayaran DP</h2>
          </div>
          <div class="card-body">
            <?php if ($kunci === 'tidak_ada'): ?>
              <div class="alert alert-warning mb-0" role="alert">
                Record pembayaran DP untuk transaksi ini tidak ditemukan. Hubungi admin.
              </div>
            <?php else: ?>
              <dl class="row mb-0">
                <dt class="col-sm-5">Kode pembayaran</dt>
                <dd class="col-sm-7"><?= $e($detail['kode_bayar'] ?? '-') ?></dd>
                <dt class="col-sm-5">Total sewa</dt>
                <dd class="col-sm-7"><?= $e($formatRupiah($detail['total'] ?? 0)) ?></dd>
                <dt class="col-sm-5">Nominal DP (30%)</dt>
                <dd class="col-sm-7"><strong><?= $e($formatRupiah($detail['nominal'] ?? 0)) ?></strong></dd>
                <dt class="col-sm-5">Sisa setelah DP</dt>
                <dd class="col-sm-7"><?= $e($formatRupiah($detail['sisa'] ?? 0)) ?></dd>
                <dt class="col-sm-5">Metode</dt>
                <dd class="col-sm-7"><?= $e($detail['mtode_byr'] ?? '-') ?></dd>
                <dt class="col-sm-5">Status</dt>
                <dd class="col-sm-7"><span class="badge <?= $e($tampil['bs']) ?>"><?= $e($tampil['teks']) ?></span></dd>
                <dt class="col-sm-5">Waktu unggah bukti</dt>
                <dd class="col-sm-7"><?= $adaBukti ? $e($formatWaktu($detail['tgl_wkt_byr'] ?? null)) : '-' ?></dd>
                <dt class="col-sm-5">Waktu verifikasi</dt>
                <dd class="col-sm-7"><?= $e($formatWaktu($detail['tgl_wkt_ver'] ?? null)) ?></dd>
              </dl>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-light">
            <h2 class="h5 mb-0">Bukti Pembayaran</h2>
          </div>
          <div class="card-body">
            <?php if ($kunci === 'menunggu_verifikasi'): ?>
              <div class="alert alert-warning" role="alert">
                Bukti pembayaran sudah diterima dan sedang menunggu verifikasi admin.
              </div>
            <?php elseif ($kunci === 'terverifikasi'): ?>
              <div class="alert alert-success" role="alert">
                Pembayaran DP Anda sudah terverifikasi.
              </div>
            <?php elseif ($kunci === 'ditolak'): ?>
              <div class="alert alert-danger" role="alert">
                Bukti pembayaran ditolak admin. Silakan unggah ulang bukti yang jelas dan sesuai nominal DP.
              </div>
            <?php elseif ($kunci === 'belum_bayar'): ?>
              <div class="alert alert-info" role="alert">
                Transfer sesuai nominal DP, lalu unggah bukti transfernya di bawah ini.
              </div>
            <?php endif; ?>

            <?php if ($adaBukti): ?>
              <p class="small text-muted mb-1">Bukti yang terunggah saat ini:</p>
              <a href="<?= $e($urlBukti) ?>" target="_blank" rel="noopener">
                <img src="<?= $e($urlBukti) ?>" alt="Bukti pembayaran DP"
                  class="img-thumbnail mb-3" style="max-height: 220px;">
              </a>
            <?php endif; ?>

            <?php if ($bolehUnggah): ?>
              <form method="post" enctype="multipart/form-data"
                action="<?= $e(site_url('transaksi/pembayaran/unggah')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="kode_sewa" value="<?= $e($kodeSewa) ?>">
                <div class="mb-3">
                  <label for="ft_byr" class="form-label">
                    <?= $kunci === 'ditolak' ? 'Unggah ulang bukti transfer' : 'Bukti transfer' ?>
                  </label>
                  <input type="file" class="form-control" id="ft_byr" name="ft_byr"
                    accept="image/jpeg,image/png,image/webp" required>
                  <div class="form-text">Format JPEG, PNG, atau WEBP. Maksimal 5 MB.</div>
                </div>
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-upload me-1" aria-hidden="true"></i> Unggah Bukti
                </button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <a class="btn btn-outline-secondary" href="<?= $e(site_url('transaksi/riwayat')) ?>">
      <i class="fas fa-arrow-left me-1" aria-hidden="true"></i> Kembali ke Transaksi Saya
    </a>
  </div>
</section>

<?php require APPPATH . 'views/katalog/footer.php'; ?>
