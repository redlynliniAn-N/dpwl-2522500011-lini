<?php
require APPPATH . 'views/templates/header.php';
require APPPATH . 'views/templates/sidebar.php';

$old = $old ?? [];
$errors = $errors ?? [];
$penyewa = $penyewa ?? [];
$mobil = $mobil ?? [];
$token = $token ?? '';
$dp_persen = $dp_persen ?? 30;
$maks_unit = $maks_unit ?? 10;
$e = static fn($v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');

$platTerpilih = array_values(array_filter(
    (array) ($old['no_plat'] ?? []),
    static fn($p) => is_string($p)
));
$jsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>Transaksi Sewa Langsung Baru <small>Formulir header &amp; detail</small></h1>
    <ol class="breadcrumb">
      <li><a href="<?= $e(site_url('admin')) ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="<?= $e(site_url('admin/sewa-langsung')) ?>">Sewa Langsung</a></li>
      <li class="active">Baru</li>
    </ol>
  </section>

  <section class="content">
    <?php if ($errors): ?>
      <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="fa fa-warning"></i> Transaksi belum disimpan:
        <ul style="margin:6px 0 0 18px;">
          <?php foreach ($errors as $msg): ?>
            <li><?= $e($msg) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" id="formSewa" autocomplete="off"
          action="<?= $e(site_url('admin/sewa-langsung/tambah')) ?>">
      <input type="hidden" name="csrf_token" value="<?= $e($token) ?>">

      <!-- ===================== HEADER ===================== -->
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">1. Data Transaksi (Header)</h3>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="user_plg">Penyewa</label>
                <select class="form-control" id="user_plg" name="user_plg" required>
                  <option value="">-- pilih penyewa --</option>
                  <?php foreach ($penyewa as $p): ?>
                    <option value="<?= $e($p['user_plg']) ?>"
                      <?= ($old['user_plg'] ?? '') === $p['user_plg'] ? 'selected' : '' ?>>
                      <?= $e($p['nm_lngkp']) ?> (<?= $e($p['user_plg']) ?>) &ndash; <?= $e($p['no_hp']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <p class="help-block">Penyewa harus sudah terdaftar.</p>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="tgl_mulai">Tanggal Mulai</label>
                <input type="date" class="form-control" id="tgl_mulai" name="tgl_mulai"
                       value="<?= $e($old['tgl_mulai'] ?? '') ?>" required>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="tgl_selesai">Tanggal Selesai</label>
                <input type="date" class="form-control" id="tgl_selesai" name="tgl_selesai"
                       value="<?= $e($old['tgl_selesai'] ?? '') ?>" required>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="wkt_ambil">Waktu Ambil</label>
                <input type="time" class="form-control" id="wkt_ambil" name="wkt_ambil"
                       value="<?= $e(substr((string) ($old['wkt_ambil'] ?? ''), 0, 5)) ?>" required>
              </div>
            </div>
          </div>
          <p class="text-muted" style="margin:0;">
            Sewa langsung dimulai <strong>hari ini</strong>. Lama sewa dihitung dari selisih tanggal
            (waktu kembali = waktu ambil pada tanggal selesai).
          </p>
        </div>
      </div>

      <!-- ===================== DETAIL ===================== -->
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">2. Kendaraan (Detail)</h3>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-md-8">
              <div class="input-group">
                <select class="form-control" id="pilihMobil">
                  <option value="">-- pilih kendaraan tersedia --</option>
                  <?php foreach ($mobil as $m): ?>
                    <option value="<?= $e($m['no_plat']) ?>">
                      <?= $e($m['nm_mobil']) ?> &ndash; <?= $e($m['no_plat']) ?>
                      (Rp <?= $e(number_format((int) $m['hrg_hari'], 0, ',', '.')) ?>/hari)
                    </option>
                  <?php endforeach; ?>
                </select>
                <span class="input-group-btn">
                  <button type="button" class="btn btn-success btn-flat" id="btnTambahMobil">
                    <i class="fa fa-plus"></i> Tambah
                  </button>
                </span>
              </div>
              <p class="help-block">Maksimal <?= (int) $maks_unit ?> kendaraan. Hanya unit berstatus tersedia yang tampil.</p>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered" id="tabelDetail">
              <thead>
                <tr>
                  <th style="width:50px;">No</th>
                  <th>Kendaraan</th>
                  <th>No. Plat</th>
                  <th class="text-right">Harga/Hari</th>
                  <th class="text-right">Hari</th>
                  <th class="text-right">Subtotal</th>
                  <th style="width:60px;">Aksi</th>
                </tr>
              </thead>
              <tbody></tbody>
              <tfoot>
                <tr>
                  <th colspan="5" class="text-right">Total Sewa</th>
                  <th class="text-right" id="lblTotal">Rp 0</th>
                  <th></th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

      <!-- ===================== PEMBAYARAN ===================== -->
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">3. Pembayaran Tunai</h3>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="nominal">Nominal Dibayar (Rp)</label>
                <input type="number" class="form-control" id="nominal" name="nominal"
                       min="1" step="1" inputmode="numeric"
                       value="<?= $e($old['nominal'] ?? '') ?>" required>
              </div>
            </div>
            <div class="col-md-8">
              <p style="margin-top:25px;">
                DP minimal <?= (int) $dp_persen ?>%:
                <strong id="lblDpMin">Rp 0</strong>
                <button type="button" class="btn btn-default btn-xs" id="btnDp">Isi DP minimal</button>
                <button type="button" class="btn btn-default btn-xs" id="btnLunas">Isi lunas</button>
                <br>
                <span class="text-muted">Dibayar kurang dari total = DP; sama dengan total = pelunasan.</span>
              </p>
            </div>
          </div>
        </div>
        <div class="box-footer">
          <button type="submit" class="btn btn-primary" id="btnSimpan">
            <i class="fa fa-save"></i> Simpan Transaksi
          </button>
          <a href="<?= $e(site_url('admin/sewa-langsung')) ?>" class="btn btn-default">
            <i class="fa fa-times"></i> Batal
          </a>
          <span class="text-muted" style="margin-left:10px;">
            Harga &amp; total dihitung ulang oleh server saat disimpan.
          </span>
        </div>
      </div>
    </form>
  </section>
</div>

<script>
// Vanilla JS (jQuery dimuat di footer). Hanya untuk kenyamanan tampilan;
// seluruh validasi dan perhitungan resmi dilakukan server.
document.addEventListener('DOMContentLoaded', function () {
  var MOBIL = <?= json_encode($mobil, $jsonFlags) ?>;
  var TERPILIH = <?= json_encode($platTerpilih, $jsonFlags) ?>;
  var DP_PERSEN = <?= (int) $dp_persen ?>;
  var MAKS = <?= (int) $maks_unit ?>;

  var peta = {};
  MOBIL.forEach(function (m) { peta[m.no_plat] = m; });

  var tbody = document.querySelector('#tabelDetail tbody');
  var selMobil = document.getElementById('pilihMobil');
  var tglMulai = document.getElementById('tgl_mulai');
  var tglSelesai = document.getElementById('tgl_selesai');
  var nominal = document.getElementById('nominal');
  var dipilih = [];

  function rp(n) {
    return 'Rp ' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }

  function hitungHari() {
    if (!tglMulai.value || !tglSelesai.value) { return 0; }
    var a = Date.parse(tglMulai.value + 'T00:00:00Z');
    var b = Date.parse(tglSelesai.value + 'T00:00:00Z');
    if (isNaN(a) || isNaN(b)) { return 0; }
    var d = Math.round((b - a) / 86400000);
    return d > 0 ? d : 0;
  }

  function sel(teks, kelas) {
    var td = document.createElement('td');
    td.textContent = teks;
    if (kelas) { td.className = kelas; }
    return td;
  }

  function render() {
    var hari = hitungHari();
    var total = 0;
    tbody.innerHTML = '';

    if (dipilih.length === 0) {
      var tr0 = document.createElement('tr');
      var td0 = sel('Belum ada kendaraan dipilih.', 'text-center text-muted');
      td0.colSpan = 7;
      tr0.appendChild(td0);
      tbody.appendChild(tr0);
    }

    dipilih.forEach(function (plat, i) {
      var m = peta[plat];
      var sub = Number(m.hrg_hari) * hari;
      total += sub;

      var tr = document.createElement('tr');
      tr.appendChild(sel(String(i + 1)));
      tr.appendChild(sel(m.nm_mobil));
      tr.appendChild(sel(m.no_plat));
      tr.appendChild(sel(rp(Number(m.hrg_hari)), 'text-right'));
      tr.appendChild(sel(String(hari), 'text-right'));
      tr.appendChild(sel(rp(sub), 'text-right'));

      var tdAksi = document.createElement('td');
      var hid = document.createElement('input');
      hid.type = 'hidden'; hid.name = 'no_plat[]'; hid.value = m.no_plat;
      var btn = document.createElement('button');
      btn.type = 'button'; btn.className = 'btn btn-danger btn-xs';
      btn.title = 'Hapus';
      btn.innerHTML = '<i class="fa fa-trash"></i>';
      btn.addEventListener('click', function () {
        dipilih.splice(dipilih.indexOf(plat), 1);
        render();
      });
      tdAksi.appendChild(hid);
      tdAksi.appendChild(btn);
      tr.appendChild(tdAksi);
      tbody.appendChild(tr);
    });

    document.getElementById('lblTotal').textContent = rp(total);
    document.getElementById('lblDpMin').textContent =
      rp(Math.round(total * DP_PERSEN / 100));
    render.total = total;

    // sembunyikan unit yang sudah dipilih dari dropdown
    Array.prototype.forEach.call(selMobil.options, function (o) {
      if (o.value === '') { return; }
      var sudah = dipilih.indexOf(o.value) !== -1;
      o.disabled = sudah;
      o.hidden = sudah;
    });
  }

  document.getElementById('btnTambahMobil').addEventListener('click', function () {
    var v = selMobil.value;
    if (!v || !peta[v]) { return; }
    if (dipilih.indexOf(v) !== -1) { return; }
    if (dipilih.length >= MAKS) {
      alert('Maksimal ' + MAKS + ' kendaraan per transaksi.');
      return;
    }
    dipilih.push(v);
    selMobil.value = '';
    render();
  });

  document.getElementById('btnDp').addEventListener('click', function () {
    nominal.value = Math.round((render.total || 0) * DP_PERSEN / 100);
  });
  document.getElementById('btnLunas').addEventListener('click', function () {
    nominal.value = Math.round(render.total || 0);
  });

  tglMulai.addEventListener('change', render);
  tglSelesai.addEventListener('change', render);

  document.getElementById('formSewa').addEventListener('submit', function (ev) {
    if (dipilih.length === 0) {
      ev.preventDefault();
      alert('Pilih minimal satu kendaraan.');
      return;
    }
    // cegah kirim ganda
    var b = document.getElementById('btnSimpan');
    b.disabled = true;
    b.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyimpan...';
  });

  // isi awal dari input sebelumnya (setelah validasi gagal)
  TERPILIH.forEach(function (p) {
    if (peta[p] && dipilih.indexOf(p) === -1 && dipilih.length < MAKS) {
      dipilih.push(p);
    }
  });
  render();
});
</script>
<?php require APPPATH . 'views/templates/footer.php'; ?>
