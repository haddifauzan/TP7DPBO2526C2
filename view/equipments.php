<!-- Header Halaman: Judul, Deskripsi, dan Tombol Tambah Peralatan -->
<div class="page-header">
    <div class="page-title">
        <h1>Inventaris Alat & Kamera</h1>
        <p>Manajemen unit peralatan, status kesiapan, stok, dan tarif rental harian.</p>
    </div>
    <div>
        <button class="btn btn-primary" onclick="openModal('modalAddEquipment')">
            Tambah Peralatan
        </button>
    </div>
</div>

<!-- Statistik Ringkas: Menghitung total stok fisik, alat siap sewa, dan alat dalam perawatan -->
<?php
    $totalStock = 0;        // Penampung akumulasi total unit fisik
    $availableCount = 0;    // Penampung jumlah model alat yang siap disewa
    $maintenanceCount = 0;  // Penampung jumlah model alat yang sedang diservis

    // Perulangan untuk mengagregasi data statistik inventaris
    foreach ($equipmentsList as $e) {
        $totalStock += (int)$e['stock'];
        if ($e['status'] === 'available') $availableCount++;
        elseif ($e['status'] === 'maintenance') $maintenanceCount++;
    }
?>
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Total Model Alat</div>
            <div class="stat-value"><?= count($equipmentsList) ?> Jenis</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Total Unit Fisik</div>
            <div class="stat-value"><?= $totalStock ?> Unit</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Siap Disewa</div>
            <div class="stat-value" style="color: var(--color-success-text);"><?= $availableCount ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Dalam Perawatan</div>
            <div class="stat-value" style="color: var(--color-warning-text);"><?= $maintenanceCount ?></div>
        </div>
    </div>
</div>

<!-- Tabel Inventaris Peralatan -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Nama Alat & Serial</th>
                        <th>Kategori</th>
                        <th>Brand</th>
                        <th>Tarif Sewa / Hari</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th style="width: 160px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Kondisi jika data inventaris masih kosong -->
                    <?php if (empty($equipmentsList)): ?>
                        <tr>
                            <td colspan="8" class="table-empty">
                                <strong>Belum ada data peralatan</strong>
                                <p>Klik tombol "Tambah Peralatan" di atas untuk menambahkan unit ke inventaris.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <!-- Perulangan menampilkan data peralatan dengan nomor urut dinamis -->
                        <?php $no = 1; foreach ($equipmentsList as $eq): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($eq['name']) ?></strong><br>
                                    <small style="color: var(--color-text-muted);">SN: <code><?= htmlspecialchars($eq['serial_number']) ?></code></small>
                                </td>
                                <!-- Menampilkan nama kategori hasil query JOIN (Foreign Key) -->
                                <td><span class="badge badge-tag"><?= htmlspecialchars($eq['category_name']) ?></span></td>
                                <!-- Menampilkan nama brand hasil query JOIN (Foreign Key) -->
                                <td><?= htmlspecialchars($eq['brand_name']) ?></td>
                                <!-- Format angka ke Rupiah Indonesia -->
                                <td><strong>Rp <?= number_format($eq['daily_rate'], 0, ',', '.') ?></strong></td>
                                <td>
                                    <!-- Warna stok: teks normal jika > 0, merah jika habis -->
                                    <span style="font-weight: 700; font-size: 1rem; color: <?= $eq['stock'] > 0 ? 'var(--color-text-main)' : 'var(--color-danger)' ?>">
                                        <?= $eq['stock'] ?> unit
                                    </span>
                                </td>
                                <td>
                                    <!-- Logika penentuan warna badge status kesiapan alat -->
                                    <?php 
                                        $badgeClass = 'badge-available';
                                        $statusLabel = 'Tersedia';
                                        if ($eq['status'] === 'maintenance') {
                                            $badgeClass = 'badge-maintenance';
                                            $statusLabel = 'Perawatan';
                                        } elseif ($eq['status'] === 'out_of_stock' || $eq['stock'] <= 0) {
                                            $badgeClass = 'badge-out_of_stock';
                                            $statusLabel = 'Stok Habis';
                                        }
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= $statusLabel ?></span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="action-buttons" style="justify-content: center;">
                                        <!-- Tombol edit: memicu URL dengan parameter edit_id -->
                                        <a href="index.php?page=equipments&edit_id=<?= $eq['id'] ?>" class="btn btn-warning btn-sm">
                                            Edit
                                        </a>
                                        <!-- Tombol hapus: memicu fungsi konfirmasi JavaScript -->
                                        <button onclick="confirmDelete('Hapus alat <?= addslashes($eq['name']) ?> dari inventaris?', 'index.php?page=equipments&action=delete_equipment&id=<?= $eq['id'] ?>')" class="btn btn-danger btn-sm">
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

<!-- Modal Dialog Tambah Peralatan -->
<div id="modalAddEquipment" class="modal-backdrop">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>Tambah Peralatan Baru</h3>
            <button class="modal-close" onclick="closeModal('modalAddEquipment')">&times;</button>
        </div>
        <form action="index.php?page=equipments" method="POST">
            <!-- Hidden input menentukan aksi add_equipment pada controller -->
            <input type="hidden" name="action" value="add_equipment">
            <div class="modal-body">
                <div class="form-group">
                    <label for="eq_name">Nama Peralatan / Model *</label>
                    <input type="text" id="eq_name" name="name" class="form-control" placeholder="Contoh: Sony Alpha 7 IV, DJI RS 3 Pro" required>
                </div>
                <div class="form-row">
                    <!-- Dropdown Relasi FK Kategori: Diambil dinamis dari tabel categories -->
                    <div class="form-group">
                        <label for="eq_category">Kategori (Relasi FK) *</label>
                        <select id="eq_category" name="category_id" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($categoriesList as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- Dropdown Relasi FK Merek: Diambil dinamis dari tabel brands -->
                    <div class="form-group">
                        <label for="eq_brand">Merek / Brand (Relasi FK) *</label>
                        <select id="eq_brand" name="brand_id" class="form-control" required>
                            <option value="">-- Pilih Brand --</option>
                            <?php foreach ($brandsList as $b): ?>
                                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="eq_sn">Nomor Seri (Serial Number) *</label>
                    <input type="text" id="eq_sn" name="serial_number" class="form-control" placeholder="Nomor seri unik perangkat" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="eq_rate">Tarif Sewa per Hari (Rp) *</label>
                        <input type="number" id="eq_rate" name="daily_rate" class="form-control" placeholder="Contoh: 250000" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="eq_stock">Jumlah Stok Unit *</label>
                        <input type="number" id="eq_stock" name="stock" class="form-control" value="1" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="eq_status">Status Kesiapan *</label>
                        <select id="eq_status" name="status" class="form-control" required>
                            <option value="available">Tersedia (Available)</option>
                            <option value="maintenance">Perawatan (Maintenance)</option>
                            <option value="out_of_stock">Stok Habis (Out of Stock)</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalAddEquipment')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Peralatan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Dialog Edit Peralatan (Aktif otomatis jika $editEquipment terisi) -->
<?php if ($editEquipment): ?>
<div id="modalEditEquipment" class="modal-backdrop show">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>Edit Peralatan</h3>
            <a href="index.php?page=equipments" class="modal-close">&times;</a>
        </div>
        <form action="index.php?page=equipments" method="POST">
            <!-- Menentukan aksi update_equipment dan ID alat target -->
            <input type="hidden" name="action" value="update_equipment">
            <input type="hidden" name="id" value="<?= $editEquipment['id'] ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_eq_name">Nama Peralatan / Model *</label>
                    <input type="text" id="edit_eq_name" name="name" class="form-control" value="<?= htmlspecialchars($editEquipment['name']) ?>" required>
                </div>
                <div class="form-row">
                    <!-- Dropdown FK Kategori dengan opsi yang otomatis terpilih (selected) -->
                    <div class="form-group">
                        <label for="edit_eq_category">Kategori (Relasi FK) *</label>
                        <select id="edit_eq_category" name="category_id" class="form-control" required>
                            <?php foreach ($categoriesList as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $editEquipment['category_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- Dropdown FK Brand dengan opsi yang otomatis terpilih (selected) -->
                    <div class="form-group">
                        <label for="edit_eq_brand">Merek / Brand (Relasi FK) *</label>
                        <select id="edit_eq_brand" name="brand_id" class="form-control" required>
                            <?php foreach ($brandsList as $b): ?>
                                <option value="<?= $b['id'] ?>" <?= ($b['id'] == $editEquipment['brand_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($b['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_eq_sn">Nomor Seri (Serial Number) *</label>
                    <input type="text" id="edit_eq_sn" name="serial_number" class="form-control" value="<?= htmlspecialchars($editEquipment['serial_number']) ?>" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_eq_rate">Tarif Sewa per Hari (Rp) *</label>
                        <input type="number" id="edit_eq_rate" name="daily_rate" class="form-control" value="<?= $editEquipment['daily_rate'] ?>" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_eq_stock">Jumlah Stok Unit *</label>
                        <input type="number" id="edit_eq_stock" name="stock" class="form-control" value="<?= $editEquipment['stock'] ?>" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_eq_status">Status Kesiapan *</label>
                        <select id="edit_eq_status" name="status" class="form-control" required>
                            <option value="available" <?= ($editEquipment['status'] === 'available') ? 'selected' : '' ?>>Tersedia (Available)</option>
                            <option value="maintenance" <?= ($editEquipment['status'] === 'maintenance') ? 'selected' : '' ?>>Perawatan (Maintenance)</option>
                            <option value="out_of_stock" <?= ($editEquipment['status'] === 'out_of_stock') ? 'selected' : '' ?>>Stok Habis (Out of Stock)</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="index.php?page=equipments" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Perbarui Peralatan</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
