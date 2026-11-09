<!-- Header Halaman: Judul, Deskripsi, dan Tombol Tambah Kategori -->
<div class="page-header">
    <div class="page-title">
        <h1>Manajemen Kategori Alat</h1>
        <p>Kelola klasifikasi perlengkapan multimedia dan kamera.</p>
    </div>
    <div>
        <button class="btn btn-primary" onclick="openModal('modalAddCategory')">
            Tambah Kategori
        </button>
    </div>
</div>

<!-- Statistik Ringkas: Menghitung total data kategori -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Total Kategori</div>
            <!-- Menghitung jumlah elemen array kategori -->
            <div class="stat-value"><?= count($categoriesList) ?></div>
        </div>
    </div>
</div>

<!-- Tabel Data Kategori -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th style="width: 250px;">Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th style="width: 160px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Kondisi jika data kategori masih kosong -->
                    <?php if (empty($categoriesList)): ?>
                        <tr>
                            <td colspan="4" class="table-empty">
                                <strong>Belum ada data kategori</strong>
                                <p>Klik tombol "Tambah Kategori" di atas untuk menambahkan kategori baru.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <!-- Perulangan menampilkan data kategori dengan nomor urut dinamis -->
                        <?php $no = 1; foreach ($categoriesList as $cat): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><span class="badge badge-tag"><?= htmlspecialchars($cat['name']) ?></span></td>
                                <td><?= htmlspecialchars($cat['description'] ?: '-') ?></td>
                                <td style="text-align: center;">
                                    <div class="action-buttons" style="justify-content: center;">
                                        <!-- Tombol edit: memicu URL dengan parameter edit_id -->
                                        <a href="index.php?page=categories&edit_id=<?= $cat['id'] ?>" class="btn btn-warning btn-sm">
                                            Edit
                                        </a>
                                        <!-- Tombol hapus: memicu fungsi konfirmasi JavaScript -->
                                        <button onclick="confirmDelete('Hapus kategori <?= addslashes($cat['name']) ?>?', 'index.php?page=categories&action=delete_category&id=<?= $cat['id'] ?>')" class="btn btn-danger btn-sm">
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

<!-- Modal Dialog Tambah Kategori -->
<div id="modalAddCategory" class="modal-backdrop">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>Tambah Kategori Baru</h3>
            <button class="modal-close" onclick="closeModal('modalAddCategory')">&times;</button>
        </div>
        <form action="index.php?page=categories" method="POST">
            <!-- Hidden input menentukan aksi add_category pada controller -->
            <input type="hidden" name="action" value="add_category">
            <div class="modal-body">
                <div class="form-group">
                    <label for="cat_name">Nama Kategori *</label>
                    <input type="text" id="cat_name" name="name" class="form-control" placeholder="Contoh: Kamera Mirrorless, Lensa Tele" required>
                </div>
                <div class="form-group">
                    <label for="cat_desc">Deskripsi</label>
                    <textarea id="cat_desc" name="description" class="form-control" rows="3" placeholder="Deskripsi singkat jenis kategori alat..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalAddCategory')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Dialog Edit Kategori (Aktif secara otomatis jika variabel $editCategory terisi) -->
<?php if ($editCategory): ?>
<div id="modalEditCategory" class="modal-backdrop show">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>Edit Kategori</h3>
            <a href="index.php?page=categories" class="modal-close">&times;</a>
        </div>
        <form action="index.php?page=categories" method="POST">
            <!-- Menentukan aksi update_category dan ID data yang diubah -->
            <input type="hidden" name="action" value="update_category">
            <input type="hidden" name="id" value="<?= $editCategory['id'] ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_cat_name">Nama Kategori *</label>
                    <input type="text" id="edit_cat_name" name="name" class="form-control" value="<?= htmlspecialchars($editCategory['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="edit_cat_desc">Deskripsi</label>
                    <textarea id="edit_cat_desc" name="description" class="form-control" rows="3"><?= htmlspecialchars($editCategory['description']) ?></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <a href="index.php?page=categories" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Perbarui Kategori</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
