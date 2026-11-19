<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Manajemen Data Penyewa</h1>

    <!-- Modal / Form Tambah Penyewa -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tambah Data Penyewa</h6>
        </div>
        <div class="card-body">
            <form action="index.php?page=penyewa&action=store" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Nama Penyewa</label>
                        <input type="text" name="nama_penyewa" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>No. HP</label>
                        <input type="text" name="no_hp" class="form-control" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control" required></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Unggah Foto KTP (JPG/PNG/PDF, maks 2MB)</label>
                        <input type="file" name="foto_ktp" class="form-control-file" accept=".jpg,.jpeg,.png,.pdf" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Unggah Foto SIM A (JPG/PNG/PDF, maks 2MB)</label>
                        <input type="file" name="foto_sim" class="form-control-file" accept=".jpg,.jpeg,.png,.pdf" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </form>
        </div>
    </div>

    <!-- Tabel Data Penyewa -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Penyewa</h6>
        </div>
        <div class="card-body">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No HP</th>
                        <th>KTP</th>
                        <th>SIM A</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($penyewa as $row): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $row['nama_penyewa']; ?></td>
                        <td><?= $row['email']; ?></td>
                        <td><?= $row['no_hp']; ?></td>
                        <td><a href="uploads/<?= $row['foto_ktp']; ?>" target="_blank" class="btn btn-sm btn-info">Lihat KTP</a></td>
                        <td><a href="uploads/<?= $row['foto_sim']; ?>" target="_blank" class="btn btn-sm btn-info">Lihat SIM</a></td>
                        <td>
                            <a href="index.php?page=penyewa&action=delete&id=<?= $row['id_penyewa']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>