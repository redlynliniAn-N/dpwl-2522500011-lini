<?php
require APPPATH . '/views/templates/header.php';
require APPPATH . '/views/templates/sidebar.php';
$penyewa = $penyewa ?? [];
$mode = $mode ?? null;
$penyewa_dipilih = $penyewa_dipilih ?? null;
$flash = $flash ?? null;
$error = $error ?? null;
$isForm = in_array($mode, ['tambah','ubah'], true);
$isEdit = $mode === 'ubah';
$userDipilih = $penyewa_dipilih['user_plg'] ?? '';
$formAction = $isEdit ? site_url('admin/penyewa/ubah/'.rawurlencode($userDipilih)) : site_url('admin/penyewa/tambah');
$e = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
?>
<div class="content-wrapper">
<section class="content-header">
<h1>Manajemen Penyewa <small>Kelola seluruh data penyewa</small></h1>
<ol class="breadcrumb">
<li><a href="<?= $e(site_url('admin')) ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
<li class="active">Manajemen Penyewa</li>
</ol>
</section>
<section class="content">
<?php if (!empty($flash)): ?>
<div class="alert <?= ($flash['type'] ?? '') === 'success' ? 'alert-success' : 'alert-danger' ?> alert-dismissible">
<button type="button" class="close" data-dismiss="alert">&times;</button>
<?= $e($flash['message'] ?? '') ?>
</div>
<?php endif; ?>
<div class="box">
<div class="box-header">
<h3 class="box-title">Data Penyewa</h3>
<div class="box-tools">
<a href="<?= $e(site_url('admin/penyewa/tambah')) ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Tambah Penyewa</a>
</div>
</div>
<div class="box-body">
<div class="table-responsive">
<table id="penyewaTable" class="table table-bordered table-striped js-data-table">
<thead><tr>
<th>No</th><th>Username</th><th>NIK</th><th>Nama Lengkap</th><th>No. HP</th><th>Alamat</th><th>Foto KTP</th><th>Foto SIM A</th><th>Aksi</th>
</tr></thead>
<tbody>
<?php foreach ($penyewa as $no=>$item): ?>
<tr>
<td><?= (int)$no+1 ?></td>
<td><?= $e($item['user_plg'] ?? '') ?></td>
<td><?= $e($item['nik'] ?? '') ?></td>
<td><?= $e($item['nm_lngkp'] ?? '') ?></td>
<td><?= $e($item['no_hp'] ?? '') ?></td>
<td><?= $e($item['almt_lngkp'] ?? '') ?></td>
<?php foreach (['ft_ktp'=>'KTP','ft_sim_a'=>'SIM A'] as $field=>$label): ?>
<td>
<?php if (!empty($item[$field])): ?>
<a href="<?= $e(base_url('uploads/penyewa/'.rawurlencode($item[$field]))) ?>" target="_blank" rel="noopener">
<img src="<?= $e(base_url('uploads/penyewa/'.rawurlencode($item[$field]))) ?>" alt="<?= $e($label) ?>" style="width:75px;height:55px;object-fit:cover;">
</a>
<?php else: ?><span class="text-muted">Belum ada foto</span><?php endif; ?>
</td>
<?php endforeach; ?>
<td style="white-space:nowrap">
<a class="btn btn-warning btn-xs" href="<?= $e(site_url('admin/penyewa/ubah/'.rawurlencode($item['user_plg']))) ?>"><i class="fa fa-edit"></i> Ubah</a>
<form method="post" style="display:inline" action="<?= $e(site_url('admin/penyewa/hapus/'.rawurlencode($item['user_plg']))) ?>" onsubmit="return confirm('Hapus data penyewa <?= $e($item['user_plg']) ?>?');">
<input type="hidden" name="_csrf" value="<?= $e($_SESSION['csrf_penyewa'] ?? '') ?>">
<button type="submit" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Hapus</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</tbody>
<tfoot><tr><th>No</th><th>Username</th><th>NIK</th><th>Nama Lengkap</th><th>No. HP</th><th>Alamat</th><th>Foto KTP</th><th>Foto SIM A</th><th>Aksi</th></tr></tfoot>
</table>
</div>
</div>
</div>

<?php if ($error !== null): ?>
<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fa fa-warning"></i> <?= $e($error) ?></div>
<?php endif; ?>

<?php if ($isForm): ?>
<div class="box box-primary">
<div class="box-header with-border"><h3 class="box-title"><?= $isEdit ? 'Ubah Data Penyewa' : 'Tambah Penyewa' ?></h3></div>
<form role="form" method="post" enctype="multipart/form-data" action="<?= $e($formAction) ?>">
<input type="hidden" name="_csrf" value="<?= $e($_SESSION['csrf_penyewa'] ?? '') ?>">
<div class="box-body">
<div class="form-group">
<label for="user_plg">Username Penyewa</label>
<input type="text" class="form-control" id="user_plg" name="user_plg" maxlength="30" value="<?= $e($penyewa_dipilih['user_plg'] ?? '') ?>" <?= $isEdit ? 'readonly' : '' ?> required>
</div>
<div class="form-group">
<label for="sandi">Password <?= $isEdit ? '(opsional)' : '' ?></label>
<input type="password" class="form-control" id="sandi" name="sandi" <?= $isEdit ? '' : 'required' ?> minlength="8" maxlength="72" autocomplete="new-password">
<?php if ($isEdit): ?><p class="help-block">Kosongkan jika password tidak ingin diubah.</p><?php endif; ?>
</div>
<div class="form-group">
<label for="nik">NIK</label>
<input type="text" class="form-control" id="nik" name="nik" maxlength="16" minlength="16" pattern="[0-9]{16}" value="<?= $e($penyewa_dipilih['nik'] ?? '') ?>" required>
</div>
<div class="form-group">
<label for="nm_lngkp">Nama Lengkap</label>
<input type="text" class="form-control" id="nm_lngkp" name="nm_lngkp" maxlength="100" value="<?= $e($penyewa_dipilih['nm_lngkp'] ?? '') ?>" required>
</div>
<div class="form-group">
<label for="no_hp">Nomor HP</label>
<input type="text" class="form-control" id="no_hp" name="no_hp" maxlength="15" value="<?= $e($penyewa_dipilih['no_hp'] ?? '') ?>" required>
</div>
<div class="form-group">
<label for="almt_lngkp">Alamat Lengkap</label>
<textarea class="form-control" id="almt_lngkp" name="almt_lngkp" maxlength="300" rows="3" required><?= $e($penyewa_dipilih['almt_lngkp'] ?? '') ?></textarea>
</div>
<div class="form-group">
<label for="ft_ktp">Foto KTP (JPEG/PNG/WEBP, maksimal 5 MB)</label>
<input type="file" class="form-control" id="ft_ktp" name="ft_ktp" accept="image/jpeg,image/png,image/webp">
<?php if (!empty($penyewa_dipilih['ft_ktp'])): ?><p class="help-block">Foto KTP saat ini: <?= $e($penyewa_dipilih['ft_ktp']) ?>. Pilih file baru jika ingin mengganti.</p><?php endif; ?>
</div>
<div class="form-group">
<label for="ft_sim_a">Foto SIM A (JPEG/PNG/WEBP, maksimal 5 MB)</label>
<input type="file" class="form-control" id="ft_sim_a" name="ft_sim_a" accept="image/jpeg,image/png,image/webp">
<?php if (!empty($penyewa_dipilih['ft_sim_a'])): ?><p class="help-block">Foto SIM A saat ini: <?= $e($penyewa_dipilih['ft_sim_a']) ?>. Pilih file baru jika ingin mengganti.</p><?php endif; ?>
</div>
</div>
<div class="box-footer">
<button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
<a href="<?= $e(site_url('admin/penyewa')) ?>" class="btn btn-default"><i class="fa fa-times"></i> Batal</a>
</div>
</form>
</div>
<?php endif; ?>
</section>
</div>
<?php require APPPATH . '/views/templates/footer.php'; ?>
