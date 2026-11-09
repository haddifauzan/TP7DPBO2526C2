<!-- Header Halaman: Judul, Deskripsi, dan Tombol Tambah Pelanggan -->
<div class="page-header">
    <div class="page-title">
        <h1>Manajemen Pelanggan</h1>
        <p>Kelola data profil, kontak, dan identitas penyewa.</p>
    </div>
    <div>
        <button class="btn btn-primary" onclick="openModal('modalAddCustomer')">
            Tambah Pelanggan
        </button>
    </div>
</div>

<!-- Statistik Ringkas: Menghitung total pelanggan terdaftar -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Total Pelanggan Terdaftar</div>
            <!-- Menghitung jumlah elemen array pelanggan -->
            <div class="stat-value"><?= count($customersList) ?></div>
        </div>
    </div>
</div>

<!-- Tabel Data Pelanggan -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Nama Pelanggan</th>
                        <th>No. Identitas (KTP)</th>
                        <th>No. Telepon / WhatsApp</th>
                        <th>Email</th>
                        <th>Alamat</th>
                        <th style="width: 160px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Kondisi jika data pelanggan masih kosong -->
                    <?php if (empty($customersList)): ?>
                        <tr>
                            <td colspan="7" class="table-empty">
                                <strong>Belum ada data pelanggan</strong>
                                <p>Klik tombol "Tambah Pelanggan" di atas untuk mendaftarkan pelanggan baru.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <!-- Perulangan menampilkan data pelanggan dengan nomor urut dinamis -->
                        <?php $no = 1; foreach ($customersList as $cust): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($cust['name']) ?></strong></td>
                                <td><code><?= htmlspecialchars($cust['identity_number']) ?></code></td>
                                <td><?= htmlspecialchars($cust['phone']) ?></td>
                                <td><?= htmlspecialchars($cust['email'] ?: '-') ?></td>
                                <td><?= htmlspecialchars($cust['address'] ?: '-') ?></td>
                                <td style="text-align: center;">
                                    <div class="action-buttons" style="justify-content: center;">
                                        <!-- Tombol edit: memicu URL dengan parameter edit_id -->
                                        <a href="index.php?page=customers&edit_id=<?= $cust['id'] ?>" class="btn btn-warning btn-sm">
                                            Edit
                                        </a>
                                        <!-- Tombol hapus: memicu fungsi konfirmasi JavaScript -->
                                        <button onclick="confirmDelete('Hapus pelanggan <?= addslashes($cust['name']) ?>?', 'index.php?page=customers&action=delete_customer&id=<?= $cust['id'] ?>')" class="btn btn-danger btn-sm">
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

<!-- Modal Dialog Tambah Pelanggan -->
<div id="modalAddCustomer" class="modal-backdrop">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>Tambah Pelanggan Baru</h3>
            <button class="modal-close" onclick="closeModal('modalAddCustomer')">&times;</button>
        </div>
        <form action="index.php?page=customers" method="POST">
            <!-- Hidden input menentukan aksi add_customer pada controller -->
            <input type="hidden" name="action" value="add_customer">
            <div class="modal-body">
                <div class="form-group">
                    <label for="cust_name">Nama Lengkap *</label>
                    <input type="text" id="cust_name" name="name" class="form-control" placeholder="Nama lengkap sesuai KTP" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="cust_id_num">No. Identitas / KTP *</label>
                        <input type="text" id="cust_id_num" name="identity_number" class="form-control" placeholder="16 digit NIK" required>
                    </div>
                    <div class="form-group">
                        <label for="cust_phone">No. Telepon / WA *</label>
                        <input type="text" id="cust_phone" name="phone" class="form-control" placeholder="08xxxxxxxxxx" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="cust_email">Alamat Email</label>
                    <input type="email" id="cust_email" name="email" class="form-control" placeholder="nama@email.com">
                </div>
                <div class="form-group">
                    <label for="cust_address">Alamat Domisili</label>
                    <textarea id="cust_address" name="address" class="form-control" rows="2" placeholder="Alamat lengkap tempat tinggal"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalAddCustomer')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Pelanggan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Dialog Edit Pelanggan (Aktif otomatis jika $editCustomer terisi) -->
<?php if ($editCustomer): ?>
<div id="modalEditCustomer" class="modal-backdrop show">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>Edit Pelanggan</h3>
            <a href="index.php?page=customers" class="modal-close">&times;</a>
        </div>
        <form action="index.php?page=customers" method="POST">
            <!-- Menentukan aksi update_customer dan ID pelanggan target -->
            <input type="hidden" name="action" value="update_customer">
            <input type="hidden" name="id" value="<?= $editCustomer['id'] ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_cust_name">Nama Lengkap *</label>
                    <input type="text" id="edit_cust_name" name="name" class="form-control" value="<?= htmlspecialchars($editCustomer['name']) ?>" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_cust_id_num">No. Identitas / KTP *</label>
                        <input type="text" id="edit_cust_id_num" name="identity_number" class="form-control" value="<?= htmlspecialchars($editCustomer['identity_number']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_cust_phone">No. Telepon / WA *</label>
                        <input type="text" id="edit_cust_phone" name="phone" class="form-control" value="<?= htmlspecialchars($editCustomer['phone']) ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_cust_email">Alamat Email</label>
                    <input type="email" id="edit_cust_email" name="email" class="form-control" value="<?= htmlspecialchars($editCustomer['email']) ?>">
                </div>
                <div class="form-group">
                    <label for="edit_cust_address">Alamat Domisili</label>
                    <textarea id="edit_cust_address" name="address" class="form-control" rows="2"><?= htmlspecialchars($editCustomer['address']) ?></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <a href="index.php?page=customers" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Perbarui Data</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
