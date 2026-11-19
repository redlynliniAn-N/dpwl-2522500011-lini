<?php
// P9 BARU - application/views/admin/pembayaran_view.php
require APPPATH . '/views/templates/header.php';
require APPPATH . '/views/templates/sidebar.php';

$e = static function ($value): string {
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

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

$flash = $flash ?? null;
$flashClass = (($flash['type'] ?? '') === 'success') ? 'alert-success' : 'alert-danger';
$mode = $mode ?? null;
$daftar = is_array($daftar ?? null) ? $daftar : [];
$detail = is_array($detail ?? null) ? $detail : null;

$jumlahMenunggu = 0;

foreach ($daftar as $baris) {
  if (
    Pembayaran_model::kunciStatus($baris['sttus_byr'] ?? null, $baris['ft_byr'] ?? null)
    === 'menunggu_verifikasi'
  ) {
    $jumlahMenunggu++;
  }
}
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Verifikasi Pembayaran
      <small>Pembayaran awal (DP) penyewa</small>
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="<?= $e(site_url('admin')) ?>">
          <i class="fa fa-dashboard"></i> Dashboard
        </a>
      </li>
      <?php if ($mode === 'detail'): ?>
        <li><a href="<?= $e(site_url('admin/pembayaran')) ?>">Verifikasi Pembayaran</a></li>
        <li class="active">Detail</li>
      <?php else: ?>
        <li class="active">Verifikasi Pembayaran</li>
      <?php endif; ?>
    </ol>
  </section>

  <section class="content">

    <?php if (!empty($flash)): ?>
      <div class="alert <?= $flashClass ?> alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="fa <?= (($flash['type'] ?? '') === 'success') ? 'fa-check' : 'fa-warning' ?>"></i>
        <?= $e($flash['message'] ?? '') ?>
      </div>
    <?php endif; ?>

    <?php if ($mode === 'detail' && $detail !== null): ?>
      <?php
      $kunci = Pembayaran_model::kunciStatus($detail['sttus_byr'] ?? null, $detail['ft_byr'] ?? null);
      $tampil = Pembayaran_model::tampilStatus($kunci);
      $kodeBayar = (string) ($detail['kode_bayar'] ?? '');
      $adaBukti = ($detail['ft_byr'] ?? '') !== '' && $detail['ft_byr'] !== null;
      $urlBukti = site_url('admin/pembayaran/bukti?kode_bayar=' . rawurlencode($kodeBayar));
      $bolehPutus = Pembayaran_model::bolehDiputuskan(
        $detail['sttus_byr'] ?? null,
        $detail['ft_byr'] ?? null,
        $detail['status_sewa'] ?? null
      );
      $rincian = is_array($detail['detail'] ?? null) ? $detail['detail'] : [];
      ?>

      <div class="row">
        <div class="col-md-6">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Data Pembayaran</h3>
            </div>
            <div class="box-body">
              <table class="table table-condensed">
                <tr>
                  <th style="width: 40%">Kode pembayaran</th>
                  <td><?= $e($kodeBayar) ?></td>
                </tr>
                <tr>
                  <th>Kode transaksi</th>
                  <td><?= $e($detail['kode_sewa'] ?? '-') ?></td>
                </tr>
                <tr>
                  <th>Penyewa</th>
                  <td>
                    <?= $e($detail['nm_lngkp'] ?? '-') ?>
                    (<?= $e($detail['user_plg'] ?? '-') ?>)
                    <?php if (!empty($detail['no_hp'])): ?>
                      <br><small class="text-muted"><?= $e($detail['no_hp']) ?></small>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <th>Periode sewa</th>
                  <td><?= $e($detail['tgl_mulai'] ?? '-') ?> s.d. <?= $e($detail['tgl_selesai'] ?? '-') ?></td>
                </tr>
                <tr>
                  <th>Status transaksi</th>
                  <td><?= $e($detail['status_sewa'] ?? '-') ?></td>
                </tr>
                <tr>
                  <th>Total sewa</th>
                  <td><?= $e($formatRupiah($detail['total'] ?? 0)) ?></td>
                </tr>
                <tr>
                  <th>Nominal DP (30%)</th>
                  <td><strong><?= $e($formatRupiah($detail['nominal'] ?? 0)) ?></strong></td>
                </tr>
                <tr>
                  <th>Sisa setelah DP</th>
                  <td><?= $e($formatRupiah($detail['sisa'] ?? 0)) ?></td>
                </tr>
                <tr>
                  <th>Metode</th>
                  <td><?= $e($detail['mtode_byr'] ?? '-') ?></td>
                </tr>
                <tr>
                  <th>Status pembayaran</th>
                  <td><span class="label <?= $e($tampil['lte']) ?>"><?= $e($tampil['teks']) ?></span></td>
                </tr>
                <tr>
                  <th>Waktu unggah bukti</th>
                  <td><?= $adaBukti ? $e($formatWaktu($detail['tgl_wkt_byr'] ?? null)) : '-' ?></td>
                </tr>
                <tr>
                  <th>Waktu verifikasi</th>
                  <td><?= $e($formatWaktu($detail['tgl_wkt_ver'] ?? null)) ?></td>
                </tr>
              </table>
            </div>
          </div>

          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">Kendaraan</h3>
            </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Kendaraan</th>
                    <th>No. Plat</th>
                    <th class="text-right">Harga/Hari</th>
                    <th class="text-center">Hari</th>
                    <th class="text-right">Subtotal</th>
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
                      <td class="text-right"><?= $e($formatRupiah($harga)) ?></td>
                      <td class="text-center"><?= $e($hari) ?></td>
                      <td class="text-right"><?= $e($formatRupiah($harga * $hari)) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Bukti Pembayaran</h3>
            </div>
            <div class="box-body text-center">
              <?php if ($adaBukti): ?>
                <a href="<?= $e($urlBukti) ?>" target="_blank" rel="noopener">
                  <img src="<?= $e($urlBukti) ?>" alt="Bukti pembayaran DP"
                    class="img-responsive center-block" style="max-height: 480px;">
                </a>
                <p class="text-muted" style="margin-top: 8px;">Klik gambar untuk membuka ukuran penuh.</p>
              <?php else: ?>
                <p class="text-muted">Penyewa belum mengunggah bukti pembayaran.</p>
              <?php endif; ?>
            </div>

            <?php if ($bolehPutus): ?>
              <div class="box-footer">
                <form method="post" style="display:inline"
                  action="<?= $e(site_url('admin/pembayaran/verifikasi')) ?>"
                  onsubmit="return confirm('Verifikasi pembayaran DP ini sebagai sah?');">
                  <?= csrf_field() ?>
                  <input type="hidden" name="kode_bayar" value="<?= $e($kodeBayar) ?>">
                  <button type="submit" class="btn btn-success">
                    <i class="fa fa-check"></i> Verifikasi
                  </button>
                </form>
                <form method="post" style="display:inline"
                  action="<?= $e(site_url('admin/pembayaran/tolak')) ?>"
                  onsubmit="return confirm('Tolak bukti pembayaran ini? Penyewa akan diminta mengunggah ulang.');">
                  <?= csrf_field() ?>
                  <input type="hidden" name="kode_bayar" value="<?= $e($kodeBayar) ?>">
                  <button type="submit" class="btn btn-danger">
                    <i class="fa fa-times"></i> Tolak
                  </button>
                </form>
              </div>
            <?php else: ?>
              <div class="box-footer">
                <span class="text-muted">
                  Pembayaran ini tidak sedang menunggu verifikasi, sehingga tidak ada tindakan yang tersedia.
                </span>
              </div>
            <?php endif; ?>
          </div>

          <a href="<?= $e(site_url('admin/pembayaran')) ?>" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Kembali ke Daftar
          </a>
        </div>
      </div>

    <?php else: ?>

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Pembayaran DP</h3>
          <div class="box-tools">
            <span class="label label-warning">
              Menunggu verifikasi: <?= (int) $jumlahMenunggu ?>
            </span>
          </div>
        </div>

        <div class="box-body">
          <div class="table-responsive">
            <table id="pembayaranTable" class="table table-bordered table-striped js-data-table">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Kode Pembayaran</th>
                  <th>Kode Transaksi</th>
                  <th>Penyewa</th>
                  <th>Nominal DP</th>
                  <th>Waktu Unggah</th>
                  <th>Status</th>
                  <th class="no-sort">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($daftar as $no => $baris): ?>
                  <?php
                  $kunciBaris = Pembayaran_model::kunciStatus(
                    $baris['sttus_byr'] ?? null,
                    $baris['ft_byr'] ?? null
                  );
                  $tampilBaris = Pembayaran_model::tampilStatus($kunciBaris);
                  $adaBuktiBaris = ($baris['ft_byr'] ?? '') !== '' && $baris['ft_byr'] !== null;
                  ?>
                  <tr>
                    <td><?= (int) $no + 1 ?></td>
                    <td><?= $e($baris['kode_bayar'] ?? '') ?></td>
                    <td><?= $e($baris['kode_sewa'] ?? '') ?></td>
                    <td>
                      <?= $e($baris['nm_lngkp'] ?? '-') ?>
                      <small class="text-muted">(<?= $e($baris['user_plg'] ?? '-') ?>)</small>
                    </td>
                    <td>Rp <?= number_format((int) ($baris['nominal'] ?? 0), 0, ',', '.') ?></td>
                    <td><?= $adaBuktiBaris ? $e($formatWaktu($baris['tgl_wkt_byr'] ?? null)) : '-' ?></td>
                    <td><span class="label <?= $e($tampilBaris['lte']) ?>"><?= $e($tampilBaris['teks']) ?></span></td>
                    <td>
                      <a class="btn btn-primary btn-xs"
                        href="<?= $e(site_url('admin/pembayaran/detail?kode_bayar=' . rawurlencode((string) ($baris['kode_bayar'] ?? '')))) ?>">
                        <i class="fa fa-search"></i> Detail
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    <?php endif; ?>

  </section>
</div>

<?php require APPPATH . '/views/templates/footer.php'; ?>
