<!-- Header Halaman: Judul, Deskripsi, dan Tombol Tambah Brand -->
<div class="page-header">
    <div class="page-title">
        <h1>Manajemen Merek / Brand</h1>
        <p>Kelola data produsen dan brand peralatan multimedia.</p>
    </div>
    <div>
        <button class="btn btn-primary" onclick="openModal('modalAddBrand')">
            Tambah Brand
        </button>
    </div>
</div>

<!-- Statistik Ringkas: Menghitung total data merek -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Total Merek Terdaftar</div>
            <!-- Menghitung jumlah elemen array brand -->
            <div class="stat-value"><?= count($brandsList) ?></div>
        </div>
    </div>
</div>

<!-- Tabel Data Merek / Brand -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th style="width: 250px;">Nama Brand</th>
                        <th>Negara Asal</th>
                        <th style="width: 160px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Kondisi jika data merek masih kosong -->
                    <?php if (empty($brandsList)): ?>
                        <tr>
                            <td colspan="4" class="table-empty">
                                <strong>Belum ada data merek</strong>
                                <p>Klik tombol "Tambah Brand" di atas untuk menambahkan merek baru.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <!-- Perulangan menampilkan data merek dengan nomor urut dinamis -->
                        <?php $no = 1; foreach ($brandsList as $b): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($b['name']) ?></strong></td>
                                <td><?= htmlspecialchars($b['country'] ?: '-') ?></td>
                                <td style="text-align: center;">
                                    <div class="action-buttons" style="justify-content: center;">
                                        <!-- Tombol edit: memicu URL dengan parameter edit_id -->
                                        <a href="index.php?page=brands&edit_id=<?= $b['id'] ?>" class="btn btn-warning btn-sm">
                                            Edit
                                        </a>
                                        <!-- Tombol hapus: memicu fungsi konfirmasi JavaScript -->
                                        <button onclick="confirmDelete('Hapus brand <?= addslashes($b['name']) ?>?', 'index.php?page=brands&action=delete_brand&id=<?= $b['id'] ?>')" class="btn btn-danger btn-sm">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Dialog Tambah Brand -->
<div id="modalAddBrand" class="modal-backdrop">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>Tambah Brand Baru</h3>
            <button class="modal-close" onclick="closeModal('modalAddBrand')">&times;</button>
        </div>
        <form action="index.php?page=brands" method="POST">
            <!-- Hidden input menentukan aksi add_brand pada controller -->
            <input type="hidden" name="action" value="add_brand">
            <div class="modal-body">
                <div class="form-group">
                    <label for="brand_name">Nama Brand *</label>
                    <input type="text" id="brand_name" name="name" class="form-control" placeholder="Contoh: Sony, Canon, DJI, Rode" required>
                </div>
                <div class="form-group">
                    <label for="brand_country">Negara Asal</label>
                    <input type="text" id="brand_country" name="country" class="form-control" placeholder="Contoh: Jepang, Tiongkok, Australia, Jerman">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalAddBrand')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Brand</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Dialog Edit Brand (Aktif otomatis jika $editBrand terisi) -->
<?php if ($editBrand): ?>
<div id="modalEditBrand" class="modal-backdrop show">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>Edit Brand</h3>
            <a href="index.php?page=brands" class="modal-close">&times;</a>
        </div>
        <form action="index.php?page=brands" method="POST">
            <!-- Menentukan aksi update_brand dan ID brand target -->
            <input type="hidden" name="action" value="update_brand">
            <input type="hidden" name="id" value="<?= $editBrand['id'] ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_brand_name">Nama Brand *</label>
                    <input type="text" id="edit_brand_name" name="name" class="form-control" value="<?= htmlspecialchars($editBrand['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="edit_brand_country">Negara Asal</label>
                    <input type="text" id="edit_brand_country" name="country" class="form-control" value="<?= htmlspecialchars($editBrand['country']) ?>">
                </div>
            </div>
            <div class="modal-footer">
                <a href="index.php?page=brands" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Perbarui Brand</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
