<!-- Header Halaman: Judul, Deskripsi, dan Tombol Tambah Staf -->
<div class="page-header">
    <div class="page-title">
        <h1>Manajemen Petugas & Staf</h1>
        <p>Kelola data pegawai, operator rental, dan penanggung jawab inventaris.</p>
    </div>
    <div>
        <button class="btn btn-primary" onclick="openModal('modalAddStaff')">
            Tambah Staf
        </button>
    </div>
</div>

<!-- Statistik Ringkas: Menghitung total staf aktif -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Total Staf Aktif</div>
            <!-- Menghitung jumlah elemen array staf -->
            <div class="stat-value"><?= count($staffList) ?></div>
        </div>
    </div>
</div>

<!-- Tabel Data Staf -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Kode Karyawan</th>
                        <th>Nama Staf</th>
                        <th>Peran / Jabatan</th>
                        <th>No. Telepon</th>
                        <th style="width: 160px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Kondisi jika data staf masih kosong -->
                    <?php if (empty($staffList)): ?>
                        <tr>
                            <td colspan="6" class="table-empty">
                                <strong>Belum ada data staf</strong>
                                <p>Klik tombol "Tambah Staf" di atas untuk menambahkan anggota tim baru.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <!-- Perulangan menampilkan data staf dengan nomor urut dinamis -->
                        <?php $no = 1; foreach ($staffList as $stf): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><span class="badge badge-tag"><code><?= htmlspecialchars($stf['employee_code']) ?></code></span></td>
                                <td><strong><?= htmlspecialchars($stf['name']) ?></strong></td>
                                <td><span class="badge badge-available"><?= htmlspecialchars($stf['role']) ?></span></td>
                                <td><?= htmlspecialchars($stf['phone']) ?></td>
                                <td style="text-align: center;">
                                    <div class="action-buttons" style="justify-content: center;">
                                        <!-- Tombol edit: memicu URL dengan parameter edit_id -->
                                        <a href="index.php?page=staff&edit_id=<?= $stf['id'] ?>" class="btn btn-warning btn-sm">
                                            Edit
                                        </a>
                                        <!-- Tombol hapus: memicu fungsi konfirmasi JavaScript -->
                                        <button onclick="confirmDelete('Hapus staf <?= addslashes($stf['name']) ?>?', 'index.php?page=staff&action=delete_staff&id=<?= $stf['id'] ?>')" class="btn btn-danger btn-sm">
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

<!-- Modal Dialog Tambah Staf -->
<div id="modalAddStaff" class="modal-backdrop">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>Tambah Staf Baru</h3>
            <button class="modal-close" onclick="closeModal('modalAddStaff')">&times;</button>
        </div>
        <form action="index.php?page=staff" method="POST">
            <!-- Hidden input menentukan aksi add_staff pada controller -->
            <input type="hidden" name="action" value="add_staff">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="stf_code">Kode Pegawai / NIP *</label>
                        <input type="text" id="stf_code" name="employee_code" class="form-control" placeholder="Contoh: STF-005" required>
                    </div>
                    <div class="form-group">
                        <label for="stf_role">Peran / Jabatan *</label>
                        <input type="text" id="stf_role" name="role" class="form-control" placeholder="Contoh: Kasir, Teknisi, Admin" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="stf_name">Nama Lengkap *</label>
                    <input type="text" id="stf_name" name="name" class="form-control" placeholder="Nama staf" required>
                </div>
                <div class="form-group">
                    <label for="stf_phone">No. Telepon / WhatsApp *</label>
                    <input type="text" id="stf_phone" name="phone" class="form-control" placeholder="08xxxxxxxxxx" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalAddStaff')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Staf</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Dialog Edit Staf (Aktif otomatis jika $editStaff terisi) -->
<?php if ($editStaff): ?>
<div id="modalEditStaff" class="modal-backdrop show">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>Edit Staf</h3>
            <a href="index.php?page=staff" class="modal-close">&times;</a>
        </div>
        <form action="index.php?page=staff" method="POST">
            <!-- Menentukan aksi update_staff dan ID staf target -->
            <input type="hidden" name="action" value="update_staff">
            <input type="hidden" name="id" value="<?= $editStaff['id'] ?>">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_stf_code">Kode Pegawai / NIP *</label>
                        <input type="text" id="edit_stf_code" name="employee_code" class="form-control" value="<?= htmlspecialchars($editStaff['employee_code']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_stf_role">Peran / Jabatan *</label>
                        <input type="text" id="edit_stf_role" name="role" class="form-control" value="<?= htmlspecialchars($editStaff['role']) ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_stf_name">Nama Lengkap *</label>
                    <input type="text" id="edit_stf_name" name="name" class="form-control" value="<?= htmlspecialchars($editStaff['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="edit_stf_phone">No. Telepon / WhatsApp *</label>
                    <input type="text" id="edit_stf_phone" name="phone" class="form-control" value="<?= htmlspecialchars($editStaff['phone']) ?>" required>
                </div>
            </div>
            <div class="modal-footer">
                <a href="index.php?page=staff" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Perbarui Staf</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
