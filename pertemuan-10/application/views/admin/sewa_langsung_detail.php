<?php
require APPPATH . 'views/templates/header.php';
require APPPATH . 'views/templates/sidebar.php';

$trx = $trx ?? [];
$flash = $flash ?? null;
$token = $token ?? '';
$e = static fn($v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$rp = static fn($n): string => 'Rp ' . number_format((int) $n, 0, ',', '.');
$tgl = static fn($d): string => date('d/m/Y', strtotime((string) $d));
$labelStatus = [
    'aktif' => 'label-primary',
    'selesai' => 'label-success',
    'batal' => 'label-default',
    'disetujui' => 'label-info',
    'diajukan' => 'label-warning',
    'ditolak' => 'label-danger',
];
$kodeUrl = rawurlencode(str_replace('/', '~', (string) $trx['kode_sewa']));
$bisaBatal = ($trx['status_sewa'] ?? '') === 'aktif';
?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>Detail Sewa Langsung <small><?= $e($trx['kode_sewa']) ?></small></h1>
    <ol class="breadcrumb">
      <li><a href="<?= $e(site_url('admin')) ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="<?= $e(site_url('admin/sewa-langsung')) ?>">Sewa Langsung</a></li>
      <li class="active">Detail</li>
    </ol>
  </section>

  <section class="content">
    <?php if (!empty($flash)): ?>
      <div class="alert alert-<?= (($flash['type'] ?? '') === 'success') ? 'success' : 'danger' ?> alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="fa <?= (($flash['type'] ?? '') === 'success') ? 'fa-check' : 'fa-warning' ?>"></i>
        <?= $e($flash['message'] ?? '') ?>
      </div>
    <?php endif; ?>

    <!-- HEADER -->
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Header Transaksi</h3>
      </div>
      <div class="box-body">
        <div class="row">
          <div class="col-md-6">
            <table class="table table-condensed">
              <tr><th style="width:150px;">Kode Sewa</th><td><?= $e($trx['kode_sewa']) ?></td></tr>
              <tr>
                <th>Status</th>
                <td><span class="label <?= $e($labelStatus[$trx['status_sewa']] ?? 'label-default') ?>">
                  <?= $e(ucfirst($trx['status_sewa'])) ?></span></td>
              </tr>
              <tr><th>Periode</th><td><?= $e($tgl($trx['tgl_mulai'])) ?> &ndash; <?= $e($tgl($trx['tgl_selesai'])) ?></td></tr>
              <tr><th>Waktu Ambil</th><td><?= $e(substr($trx['wkt_ambil'], 0, 5)) ?></td></tr>
            </table>
          </div>
          <div class="col-md-6">
            <table class="table table-condensed">
              <tr><th style="width:150px;">Penyewa</th><td><?= $e($trx['nm_lngkp']) ?> (<?= $e($trx['user_plg']) ?>)</td></tr>
              <tr><th>NIK</th><td><?= $e($trx['nik']) ?></td></tr>
              <tr><th>No. HP</th><td><?= $e($trx['no_hp']) ?></td></tr>
              <tr><th>Alamat</th><td><?= $e($trx['almt_lngkp']) ?></td></tr>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- DETAIL -->
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Kendaraan (Detail)</h3>
      </div>
      <div class="box-body table-responsive">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>No</th><th>Kendaraan</th><th>No. Plat</th>
              <th class="text-right">Harga/Hari</th>
              <th class="text-right">Hari</th>
              <th class="text-right">Subtotal</th>
              <th>Status Unit</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($trx['detail'] as $i => $d): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td><?= $e($d['nm_mobil']) ?> <small class="text-muted"><?= $e($d['jns_mobil']) ?> / <?= $e($d['transmisi']) ?></small></td>
                <td><?= $e($d['no_plat']) ?></td>
                <td class="text-right"><?= $e($rp($d['hrg_sewa'])) ?></td>
                <td class="text-right"><?= (int) $d['n_hari'] ?></td>
                <td class="text-right"><?= $e($rp((int) $d['hrg_sewa'] * (int) $d['n_hari'])) ?></td>
                <td><?= $e(ucfirst($d['status_unit'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr><th colspan="5" class="text-right">Total Sewa</th><th class="text-right"><?= $e($rp($trx['total'])) ?></th><th></th></tr>
            <tr><th colspan="5" class="text-right">Total Dibayar (valid)</th><th class="text-right"><?= $e($rp($trx['dibayar'])) ?></th><th></th></tr>
            <tr><th colspan="5" class="text-right">Sisa Pembayaran</th><th class="text-right"><?= $e($rp($trx['sisa'])) ?></th><th></th></tr>
          </tfoot>
        </table>
      </div>
    </div>

    <!-- PEMBAYARAN -->
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Riwayat Pembayaran</h3>
      </div>
      <div class="box-body table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr><th>Kode Bayar</th><th>Waktu</th><th>Metode</th><th>Jenis</th><th class="text-right">Nominal</th><th>Status</th></tr>
          </thead>
          <tbody>
            <?php foreach ($trx['bayar'] as $b): ?>
              <tr>
                <td><?= $e($b['kode_bayar']) ?></td>
                <td><?= $e(date('d/m/Y H:i', strtotime($b['tgl_wkt_byr']))) ?></td>
                <td><?= $e(ucfirst($b['mtode_byr'])) ?></td>
                <td><?= $e(strtoupper((string) $b['jns_byr'])) ?></td>
                <td class="text-right"><?= $e($rp($b['nominal'])) ?></td>
                <td><?= $e(ucfirst($b['sttus_byr'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="box-footer">
        <a href="<?= $e(site_url('admin/sewa-langsung')) ?>" class="btn btn-default">
          <i class="fa fa-arrow-left"></i> Kembali
        </a>
        <?php if ($bisaBatal): ?>
          <form method="post" style="display:inline;"
                action="<?= $e(site_url('admin/sewa-langsung/batalkan/' . $kodeUrl)) ?>">
            <input type="hidden" name="csrf_token" value="<?= $e($token) ?>">
            <button type="submit" class="btn btn-danger"
                    onclick="return confirm('Batalkan transaksi ini? Kendaraan akan dikembalikan ke status tersedia.');">
              <i class="fa fa-ban"></i> Batalkan Transaksi
            </button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </section>
</div>
<?php require APPPATH . 'views/templates/footer.php'; ?>
