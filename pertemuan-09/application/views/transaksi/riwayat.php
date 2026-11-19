<?php
// P9 BARU - application/views/transaksi/riwayat.php
require APPPATH . 'views/katalog/header.php';

$e = static function ($value): string {
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

require APPPATH . 'views/katalog/navbar.php';

$formatRupiah = static function ($nilai): string {
  return 'Rp ' . number_format((float) $nilai, 0, ',', '.');
};

$daftar = is_array($daftar ?? null) ? $daftar : [];
?>

<section class="bg-success py-4">
  <div class="container">
    <h1 class="h2 text-white mt-3">Transaksi Saya</h1>
    <p class="text-white mb-0">Pantau transaksi yang sudah diajukan dan status pembayaran DP.</p>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <?php if (count($daftar) === 0): ?>
      <div class="alert alert-info mb-0" role="alert">
        Belum ada transaksi yang diajukan.
        <a href="<?= $e(site_url('katalog')) ?>#katalog" class="alert-link">Pilih kendaraan</a>
        untuk memulai.
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle bg-white">
          <thead class="table-light">
            <tr>
              <th scope="col">Kode Transaksi</th>
              <th scope="col">Periode Sewa</th>
              <th scope="col" class="text-end">Total Sewa</th>
              <th scope="col" class="text-end">DP</th>
              <th scope="col">Status DP</th>
              <th scope="col" class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($daftar as $baris): ?>
              <?php
              $kunci = Pembayaran_model::kunciStatus(
                $baris['sttus_byr'] ?? null,
                $baris['ft_byr'] ?? null
              );
              $tampil = Pembayaran_model::tampilStatus($kunci);
              $kodeSewa = (string) ($baris['kode_sewa'] ?? '');
              $boleh = Pembayaran_model::bolehUnggah(
                $baris['sttus_byr'] ?? null,
                $baris['ft_byr'] ?? null,
                $baris['status_sewa'] ?? null
              );
              ?>
              <tr>
                <td class="text-nowrap"><?= $e($kodeSewa) ?></td>
                <td class="text-nowrap">
                  <?= $e($baris['tgl_mulai'] ?? '-') ?> s.d. <?= $e($baris['tgl_selesai'] ?? '-') ?>
                </td>
                <td class="text-end text-nowrap"><?= $e($formatRupiah($baris['total'] ?? 0)) ?></td>
                <td class="text-end text-nowrap">
                  <?= $baris['nominal'] === null ? '-' : $e($formatRupiah($baris['nominal'])) ?>
                </td>
                <td>
                  <span class="badge <?= $e($tampil['bs']) ?>"><?= $e($tampil['teks']) ?></span>
                </td>
                <td class="text-center text-nowrap">
                  <a class="btn btn-sm <?= $boleh ? 'btn-success' : 'btn-outline-secondary' ?>"
                    href="<?= $e(site_url('transaksi/pembayaran?kode_sewa=' . rawurlencode($kodeSewa))) ?>">
                    <?= $boleh ? 'Bayar DP' : 'Lihat' ?>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require APPPATH . 'views/katalog/footer.php'; ?>
