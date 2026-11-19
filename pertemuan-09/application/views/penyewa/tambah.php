<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Tambah Data Penyewa</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="<?= base_url('penyewa_controller/tambah'); ?>" method="post">
                <div class="form-group">
                    <label>Nama Penyewa</label>
                    <input type="text" name="nama_penyewa" class="form-control" value="<?= set_value('nama_penyewa'); ?>">
                    <?= form_error('nama_penyewa', '<small class="text-danger pl-3">', '</small>'); ?>
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" class="form-control"><?= set_value('alamat'); ?></textarea>
                    <?= form_error('alamat', '<small class="text-danger pl-3">', '</small>'); ?>
                </div>
                <div class="form-group">
                    <label>No. HP</label>
                    <input type="text" name="no_hp" class="form-control" value="<?= set_value('no_hp'); ?>">
                    <?= form_error('no_hp', '<small class="text-danger pl-3">', '</small>'); ?>
                </div>
                <div class="form-group">
                    <label>No. KTP</label>
                    <input type="text" name="ktp" class="form-control" value="<?= set_value('ktp'); ?>">
                    <?= form_error('ktp', '<small class="text-danger pl-3">', '</small>'); ?>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?= base_url('penyewa_controller'); ?>" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>