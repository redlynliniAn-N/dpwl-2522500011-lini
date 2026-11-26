<?php
require APPPATH . 'views/templates/header.php';
require APPPATH . 'views/templates/sidebar.php';

$daftar = $daftar ?? [];
$flash = $flash ?? null;
$e = static fn($v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$rp = static fn($n): string => 'Rp ' . number_format((int) $n, 0, ',', '.');
$labelStatus = [
    'aktif' => 'label-primary',
    'selesai' => 'label-success',
    'batal' => 'label-default',
    'disetujui' => 'label-info',
    'diajukan' => 'label-warning',
    'ditolak' => 'label-danger',
];
?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>Transaksi Sewa Langsung <small>Penyewaan yang diinput admin di tempat</small></h1>
    <ol class="breadcrumb">
      <li><a href="<?= $e(site_url('admin')) ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li class="active">Sewa Langsung</li>
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

    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">Data Transaksi</h3>
        <div class="box-tools">
          <a href="<?= $e(site_url('admin/sewa-langsung/tambah')) ?>" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Transaksi Baru
          </a>
        </div>
      </div>
      <div class="box-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped js-data-table">
            <thead>
              <tr>
                <th>No</th>
                <th>Kode Sewa</th>
                <th>Penyewa</th>
                <th>Periode</th>
                <th>Unit</th>
                <th>Total</th>
                <th>Dibayar</th>
                <th>Sisa</th>
                <th>Status</th>
                <th class="no-sort">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($daftar as $i => $row): ?>
                <?php
                $sisa = max(0, (int) $row['total'] - (int) $row['dibayar']);
                $kodeUrl = rawurlencode(str_replace('/', '~', $row['kode_sewa']));
                ?>
                <tr>
                  <td><?= $i + 1 ?></td>
                  <td><?= $e($row['kode_sewa']) ?></td>
                  <td><?= $e($row['nm_lngkp']) ?><br><small class="text-muted"><?= $e($row['user_plg']) ?></small></td>
                  <td>
                    <?= $e(date('d/m/Y', strtotime($row['tgl_mulai']))) ?>
                    &ndash;
                    <?= $e(date('d/m/Y', strtotime($row['tgl_selesai']))) ?>
                    <br><small class="text-muted">ambil <?= $e(substr($row['wkt_ambil'], 0, 5)) ?></small>
                  </td>
                  <td><?= (int) $row['jml_unit'] ?></td>
                  <td><?= $e($rp($row['total'])) ?></td>
                  <td><?= $e($rp($row['dibayar'])) ?></td>
                  <td><?= $e($rp($sisa)) ?></td>
                  <td>
                    <span class="label <?= $e($labelStatus[$row['status_sewa']] ?? 'label-default') ?>">
                      <?= $e(ucfirst($row['status_sewa'])) ?>
                    </span>
                  </td>
                  <td>
                    <a href="<?= $e(site_url('admin/sewa-langsung/detail/' . $kodeUrl)) ?>"
                       class="btn btn-info btn-sm" title="Detail">
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
  </section>
</div>
<?php require APPPATH . 'views/templates/footer.php'; ?>
