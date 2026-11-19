<?php
require APPPATH . '/views/templates/header.php';
require APPPATH . '/views/templates/sidebar.php';

$penyewa = $penyewa ?? [];
$flash = $flash ?? null;
$flashClass = (($flash['type'] ?? '') === 'success') ? 'alert-success' : 'alert-danger';
$error = $error ?? null;

$mode = $mode ?? null;
$penyewa_dipilih = $penyewa_dipilih ?? null;

$isForm = in_array($mode, ['tambah', 'ubah'], true);
$isEdit = $mode === 'ubah';
$userPlgDipilih = $penyewa_dipilih['user_plg'] ?? '';

$formAction = $isEdit
  ? site_url('admin/penyewa/ubah/' . rawurlencode((string) $userPlgDipilih))
  : site_url('admin/penyewa/tambah');
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Manajemen Penyewa
      <small>Kelola data penyewa / pelanggan</small>
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="<?= htmlspecialchars(site_url('admin'), ENT_QUOTES, 'UTF-8') ?>">
          <i class="fa fa-dashboard"></i> Dashboard
        </a>
      </li>
      <li class="active">Manajemen Penyewa</li>
    </ol>
  </section>

  <section class="content">

    <!-- Flash message hasil PRG -->
    <?php if (!empty($flash)): ?>
      <div class="alert <?= $flashClass ?> alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="fa <?= (($flash['type'] ?? '') === 'success') ? 'fa-check' : 'fa-warning' ?>"></i>
        <?= htmlspecialchars($flash['message'] ?? '', ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <!-- Data Table -->
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Data Penyewa</h3>
        <div class="box-tools">
          <a href="<?= htmlspecialchars(site_url('admin/penyewa/tambah'), ENT_QUOTES, 'UTF-8') ?>"
            class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Tambah Penyewa
          </a>
        </div>
      </div>

      <div class="box-body">
        <div class="table-responsive">
          <table id="penyewaTable" class="table table-bordered table-striped js-data-table">
            <thead>
              <tr>
                <th>No</th>
                <th>Username</th>
                <th>NIK</th>
                <th>Nama Lengkap</th>
                <th>No. HP</th>
                <th>Alamat</th>
                <th>Foto KTP</th>
                <th>Foto SIM A</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($penyewa as $no => $item): ?>
                <tr>
                  <td><?= (int) $no + 1 ?></td>
                  <td><?= htmlspecialchars($item['user_plg'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($item['nik'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($item['nm_lngkp'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($item['no_hp'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($item['almt_lngkp'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td>
                    <?php if (!empty($item['ft_ktp'])): ?>
                      <img
                        src="<?= htmlspecialchars(
                          base_url('uploads/penyewa/' . rawurlencode($item['ft_ktp'])),
                          ENT_QUOTES,
                          'UTF-8'
                        ) ?>"
                        alt="Foto KTP <?= htmlspecialchars($item['nm_lngkp'], ENT_QUOTES, 'UTF-8') ?>"
                        style="width: 80px; height: 55px; object-fit: cover;">
                    <?php else: ?>
                      <span class="text-muted">Tidak ada</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if (!empty($item['ft_sim_a'])): ?>
                      <img
                        src="<?= htmlspecialchars(
                          base_url('uploads/penyewa/' . rawurlencode($item['ft_sim_a'])),
                          ENT_QUOTES,
                          'UTF-8'
                        ) ?>"
                        alt="Foto SIM A <?= htmlspecialchars($item['nm_lngkp'], ENT_QUOTES, 'UTF-8') ?>"
                        style="width: 80px; height: 55px; object-fit: cover;">
                    <?php else: ?>
                      <span class="text-muted">Tidak ada</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <a class="btn btn-warning btn-xs"
                      href="<?= htmlspecialchars(site_url('admin/penyewa/ubah/' . rawurlencode((string) $item['user_plg'])), ENT_QUOTES, 'UTF-8') ?>">
                      <i class="fa fa-edit"></i> Ubah
                    </a>
                    <form method="post" style="display:inline"
                      action="<?= htmlspecialchars(site_url('admin/penyewa/hapus/' . rawurlencode((string) $item['user_plg'])), ENT_QUOTES, 'UTF-8') ?>"
                      onsubmit="return confirm('Hapus penyewa <?= htmlspecialchars($item['user_plg'], ENT_QUOTES, 'UTF-8') ?>? Data dan berkas terkait akan dihapus.');">
                      <button type="submit" class="btn btn-danger btn-xs">
                        <i class="fa fa-trash"></i> Hapus
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr>
                <th>No</th>
                <th>Username</th>
                <th>NIK</th>
                <th>Nama Lengkap</th>
                <th>No. HP</th>
                <th>Alamat</th>
                <th>Foto KTP</th>
                <th>Foto SIM A</th>
                <th>Aksi</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

    <?php if ($error !== null): ?>
      <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="fa fa-warning"></i>
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <?php if ($isForm): ?>
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $isEdit ? 'Ubah Data Penyewa' : 'Tambah Penyewa Baru' ?></h3>
        </div>

        <form role="form" method="post" enctype="multipart/form-data"
          action="<?= htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8') ?>">
          <div class="box-body">
            
            <div class="form-group">
              <label for="user_plg">Username Penyewa (user_plg)</label>
              <input type="text" class="form-control" id="user_plg" name="user_plg"
                maxlength="15" value="<?= htmlspecialchars($penyewa_dipilih['user_plg'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                <?= $isEdit ? 'readonly' : '' ?> required>
            </div>

            <div class="form-group">
              <label for="sandi">Kata Sandi <?= $isEdit ? '<small class="text-muted">(Kosongkan jika tidak ingin mengubah)</small>' : '' ?></label>
              <input type="password" class="form-control" id="sandi" name="sandi"
                minlength="8" maxlength="72" autocomplete="new-password"
                <?= $isEdit ? '' : 'required' ?>>
            </div>

            <div class="form-group">
              <label for="nik">NIK</label>
              <input type="text" class="form-control" id="nik" name="nik"
                maxlength="16" minlength="16" value="<?= htmlspecialchars($penyewa_dipilih['nik'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>

            <div class="form-group">
              <label for="nm_lngkp">Nama Lengkap</label>
              <input type="text" class="form-control" id="nm_lngkp" name="nm_lngkp"
                maxlength="100" value="<?= htmlspecialchars($penyewa_dipilih['nm_lngkp'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>

            <div class="form-group">
              <label for="no_hp">Nomor Handphone</label>
              <input type="text" class="form-control" id="no_hp" name="no_hp"
                maxlength="15" value="<?= htmlspecialchars($penyewa_dipilih['no_hp'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>

            <div class="form-group">
              <label for="almt_lngkp">Alamat Lengkap</label>
              <textarea class="form-control" id="almt_lngkp" name="almt_lngkp"
                maxlength="300" rows="3" required><?= htmlspecialchars($penyewa_dipilih['almt_lngkp'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <hr>
            <h4>Dokumen Penyewa</h4>
            <?php foreach (['ft_ktp' => 'Foto KTP', 'ft_sim_a' => 'Foto SIM A'] as $field => $label): ?>
              <div class="form-group">
                <label for="<?= htmlspecialchars($field, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></label>
                <input type="file" class="form-control" id="<?= htmlspecialchars($field, ENT_QUOTES, 'UTF-8') ?>" name="<?= htmlspecialchars($field, ENT_QUOTES, 'UTF-8') ?>"
                  accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                <p class="help-block">JPG, JPEG, PNG, atau WEBP; maksimal 5 MB per foto.</p>
                <div class="photo-preview" style="margin-top: 10px;">
                  <?php if (!empty($penyewa_dipilih[$field]) && is_string($penyewa_dipilih[$field])): ?>
                    <p class="help-block current-photo-label">Berkas saat ini:</p>
                    <img
                      class="img-thumbnail current-photo"
                      src="<?= htmlspecialchars(base_url('uploads/penyewa/' . rawurlencode($penyewa_dipilih[$field])), ENT_QUOTES, 'UTF-8') ?>"
                      alt="<?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?> saat ini"
                      style="width: 180px; height: 120px; object-fit: cover;">
                    <p class="help-block current-photo-name"><?= htmlspecialchars($penyewa_dipilih[$field], ENT_QUOTES, 'UTF-8') ?></p>
                  <?php else: ?>
                    <p class="help-block current-photo-label">Belum ada berkas tersimpan.</p>
                  <?php endif; ?>
                  <div class="new-photo-preview" style="display: none; margin-top: 8px;">
                    <p class="help-block">Pratinjau berkas baru yang dipilih:</p>
                    <img
                      class="img-thumbnail new-photo-image"
                      alt="Pratinjau <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?> yang dipilih"
                      style="width: 180px; height: 120px; object-fit: cover;">
                  </div>
                </div>
              </div>
            <?php endforeach; ?>

          </div>

          <div class="box-footer">
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-save"></i> Simpan Data Penyewa
            </button>
            <a href="<?= htmlspecialchars(site_url('admin/penyewa'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-default">
              <i class="fa fa-times"></i> Batal
            </a>
          </div>
        </form>
      </div>
    <?php endif; ?>

  </section>
</div>

<?php
require APPPATH . '/views/templates/footer.php';
?>

<?php if ($isForm): ?>
  <script>
    (function() {
      'use strict';

      document.querySelectorAll('.form-group input[type="file"][name^="ft_"]').forEach(function(input) {
        var container = input.closest('.form-group');
        var preview = container ? container.querySelector('.new-photo-preview') : null;
        var image = preview ? preview.querySelector('.new-photo-image') : null;
        var previousUrl = null;

        if (!preview || !image) {
          return;
        }

        input.addEventListener('change', function() {
          if (previousUrl) {
            URL.revokeObjectURL(previousUrl);
            previousUrl = null;
          }

          var file = input.files && input.files.length ? input.files[0] : null;
          if (!file) {
            preview.style.display = 'none';
            image.removeAttribute('src');
            return;
          }

          if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size < 1 || file.size > 5 * 1024 * 1024) {
            preview.style.display = 'none';
            image.removeAttribute('src');
            return;
          }

          previousUrl = URL.createObjectURL(file);
          image.src = previousUrl;
          preview.style.display = 'block';
        });
      });
    })();
  </script>
<?php endif; ?>