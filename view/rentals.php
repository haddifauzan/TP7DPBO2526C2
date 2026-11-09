<!-- Header Halaman: Judul, Deskripsi, dan Tombol Tambah Rental Baru -->
<div class="page-header">
    <div class="page-title">
        <h1>Transaksi Peminjaman & Rental</h1>
        <p>Kelola peminjaman unit alat, pengembalian, status pembayaran, dan riwayat transaksi.</p>
    </div>
    <div>
        <button class="btn btn-primary" onclick="openModal('modalAddRental')">
            Buat Rental Baru
        </button>
    </div>
</div>

<!-- Statistik Ringkas Rental: Menghitung total transaksi, status sewa aktif, kembali, dan omset -->
<?php
    $activeRentals = 0;   // Penampung jumlah transaksi yang belum dikembalikan
    $returnedRentals = 0; // Penampung jumlah transaksi yang sudah selesai kembali
    $totalRevenue = 0;    // Penampung akumulasi total nilai transaksi rental

    // Perulangan untuk mengagregasi data statistik transaksi rental
    foreach ($rentalsList as $r) {
        // Cek jika status sewa sudah dikembalikan (returned)
        if ($r['payment_status'] === 'returned') {
            $returnedRentals++; // Tambah counter unit kembali
        } else {
            $activeRentals++;   // Tambah counter rental aktif
        }
        $totalRevenue += (int)$r['total_cost']; // Tambahkan biaya rental ke total pendapatan
    }
?>
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Total Transaksi</div>
            <!-- Menampilkan total jumlah transaksi rental dari count array -->
            <div class="stat-value"><?= count($rentalsList) ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Sedang Disewa</div>
            <!-- Menampilkan jumlah unit yang sedang berada di tangan pelanggan -->
            <div class="stat-value" style="color: var(--color-warning-text);"><?= $activeRentals ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Unit Kembali</div>
            <!-- Menampilkan jumlah transaksi yang sudah berstatus selesai kembali -->
            <div class="stat-value" style="color: var(--color-success-text);"><?= $returnedRentals ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Nilai Transaksi</div>
            <!-- Menampilkan format rupiah dari total nilai pendapatan rental -->
            <div class="stat-value" style="color: var(--color-primary); font-size: 1.25rem;">Rp <?= number_format($totalRevenue, 0, ',', '.') ?></div>
        </div>
    </div>
</div>

<!-- Tabel Transaksi Rental -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Alat Disewa</th>
                        <th>Pelanggan</th>
                        <th>Operator</th>
                        <th>Periode Sewa</th>
                        <th>Total Biaya</th>
                        <th>Status</th>
                        <th style="min-width: 180px;">Catatan</th>
                        <th style="width: 200px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Kondisi jika data transaksi rental masih kosong -->
                    <?php if (empty($rentalsList)): ?>
                        <tr>
                            <td colspan="9" class="table-empty">
                                <strong>Belum ada transaksi penyewaan</strong>
                                <p>Klik tombol "Buat Rental Baru" di atas untuk mencatat transaksi rental pertama.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <!-- Perulangan menampilkan daftar transaksi rental dengan penomoran urut -->
                        <?php $no = 1; foreach ($rentalsList as $rent): ?>
                            <tr>
                                <!-- Nomor urut dinamis per baris -->
                                <td><?= $no++ ?></td>
                                <td>
                                    <!-- Nama peralatan dan nomor seri hasil relasi JOIN -->
                                    <strong><?= htmlspecialchars($rent['equipment_name']) ?></strong><br>
                                    <small style="color: var(--color-text-muted);">SN: <code><?= htmlspecialchars($rent['serial_number']) ?></code></small>
                                </td>
                                <td>
                                    <!-- Nama pelanggan dan nomor telepon hasil relasi JOIN -->
                                    <strong><?= htmlspecialchars($rent['customer_name']) ?></strong><br>
                                    <small style="color: var(--color-text-muted);"><?= htmlspecialchars($rent['customer_phone']) ?></small>
                                </td>
                                <!-- Nama staf petugas yang melayani transaksi -->
                                <td><?= htmlspecialchars($rent['staff_name']) ?></td>
                                <td>
                                    <!-- Format tanggal pinjam dan rencana pengembalian -->
                                    <span style="font-size: 0.84rem;"><?= date('d M Y', strtotime($rent['rental_date'])) ?></span><br>
                                    <span style="font-size: 0.84rem; color: var(--color-text-muted);">s/d <?= date('d M Y', strtotime($rent['return_date'])) ?></span>
                                </td>
                                <!-- Total biaya sewa dalam format mata uang Rupiah -->
                                <td><strong>Rp <?= number_format($rent['total_cost'], 0, ',', '.') ?></strong></td>
                                <td>
                                    <!-- Penentuan class badge dan label teks status pembayaran/peminjaman -->
                                    <?php 
                                        $statusClass = 'badge-unpaid';   // Default status: belum lunas
                                        $labelStatus = 'Belum Lunas';
                                        if ($rent['payment_status'] === 'paid') {
                                            $statusClass = 'badge-paid';       // Status lunas / sedang aktif
                                            $labelStatus = 'Lunas / Aktif';
                                        } elseif ($rent['payment_status'] === 'returned') {
                                            $statusClass = 'badge-returned';   // Status unit telah dikembalikan
                                            $labelStatus = 'Selesai Kembali';
                                        } elseif ($rent['payment_status'] === 'late') {
                                            $statusClass = 'badge-late';       // Status sewa terlambat
                                            $labelStatus = 'Terlambat';
                                        }
                                    ?>
                                    <span class="badge <?= $statusClass ?>">
                                        <?= $labelStatus ?>
                                    </span>
                                </td>
                                <td>
                                    <!-- Menampilkan rincian catatan transaksi dengan konversi baris baru -->
                                    <?php if (!empty($rent['notes'])): ?>
                                        <div style="font-size: 0.84rem; color: var(--color-text-main); line-height: 1.4;">
                                            <?= nl2br(htmlspecialchars($rent['notes'])) ?>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: var(--color-text-muted); font-size: 0.84rem;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <div class="action-buttons" style="justify-content: center; flex-wrap: wrap;">
                                        <!-- Tombol pengembalian alat: hanya tampil jika status belum returned -->
                                        <?php if ($rent['payment_status'] !== 'returned'): ?>
                                            <a href="index.php?page=rentals&action=return_rental&id=<?= $rent['id'] ?>" class="btn btn-success btn-sm" title="Tandai alat telah dikembalikan dan pulihkan stok">
                                                Selesai
                                            </a>
                                        <?php endif; ?>
                                        <!-- Tombol untuk memicu modal edit transaksi -->
                                        <a href="index.php?page=rentals&edit_id=<?= $rent['id'] ?>" class="btn btn-warning btn-sm">
                                            Edit
                                        </a>
                                        <!-- Tombol hapus data transaksi dengan konfirmasi dialog -->
                                        <button onclick="confirmDelete('Hapus transaksi rental #<?= $rent['id'] ?>?', 'index.php?page=rentals&action=delete_rental&id=<?= $rent['id'] ?>')" class="btn btn-danger btn-sm">
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

<!-- Modal Tambah Rental Baru -->
<div id="modalAddRental" class="modal-backdrop">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>Buat Transaksi Rental Baru</h3>
            <button class="modal-close" onclick="closeModal('modalAddRental')">&times;</button>
        </div>
        <form action="index.php?page=rentals" method="POST">
            <!-- Parameter aksi controller: tambah rental -->
            <input type="hidden" name="action" value="add_rental">
            <div class="modal-body">
                <div class="form-group">
                    <label for="rental_equipment_id">Pilih Alat / Kamera (Relasi FK) *</label>
                    <select id="rental_equipment_id" name="equipment_id" class="form-control" onchange="calculateRentalCost()" required>
                        <option value="">-- Pilih Unit Peralatan --</option>
                        <!-- Perulangan memuat daftar peralatan yang tersedia di database -->
                        <?php foreach ($equipmentsList as $eq): ?>
                            <!-- Cek apakah stok fisik masih tersedia dan status alat available -->
                            <?php if ($eq['stock'] > 0 && $eq['status'] === 'available'): ?>
                                <option value="<?= $eq['id'] ?>" data-rate="<?= $eq['daily_rate'] ?>">
                                    <?= htmlspecialchars($eq['name']) ?> (Stok: <?= $eq['stock'] ?>) — Rp <?= number_format($eq['daily_rate'], 0, ',', '.') ?>/hari
                                </option>
                            <?php else: ?>
                                <!-- Unit yang habis atau dalam perbaikan dinonaktifkan -->
                                <option value="<?= $eq['id'] ?>" disabled style="color: #94a3b8;">
                                    <?= htmlspecialchars($eq['name']) ?> (Tidak Tersedia / Habis)
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="rental_customer_id">Pilih Pelanggan (Relasi FK) *</label>
                        <select id="rental_customer_id" name="customer_id" class="form-control" required>
                            <option value="">-- Pilih Pelanggan --</option>
                            <!-- Perulangan memuat seluruh data pelanggan terdaftar -->
                            <?php foreach ($customersList as $cust): ?>
                                <option value="<?= $cust['id'] ?>"><?= htmlspecialchars($cust['name']) ?> (<?= htmlspecialchars($cust['phone']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="rental_staff_id">Petugas / Operator (Relasi FK) *</label>
                        <select id="rental_staff_id" name="staff_id" class="form-control" required>
                            <option value="">-- Pilih Petugas --</option>
                            <!-- Perulangan memuat seluruh staf operator aktif -->
                            <?php foreach ($staffList as $stf): ?>
                                <option value="<?= $stf['id'] ?>"><?= htmlspecialchars($stf['name']) ?> (<?= htmlspecialchars($stf['role']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="rental_date">Tanggal Pinjam *</label>
                        <!-- Tanggal default: hari ini -->
                        <input type="date" id="rental_date" name="rental_date" class="form-control" value="<?= date('Y-m-d') ?>" onchange="calculateRentalCost()" required>
                    </div>
                    <div class="form-group">
                        <label for="return_date">Tanggal Kembali (Rencana) *</label>
                        <!-- Tanggal default: besok (+1 hari) -->
                        <input type="date" id="return_date" name="return_date" class="form-control" value="<?= date('Y-m-d', strtotime('+1 day')) ?>" onchange="calculateRentalCost()" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="total_cost">Total Biaya Rental (Rp) *</label>
                        <input type="number" id="total_cost" name="total_cost" class="form-control" placeholder="Terhitung otomatis" min="0" required>
                        <div class="form-hint">Otomatis dihitung berdasarkan durasi hari & tarif sewa alat.</div>
                    </div>
                    <div class="form-group">
                        <label for="payment_status">Status Pembayaran / Sewa *</label>
                        <select id="payment_status" name="payment_status" class="form-control" required>
                            <option value="unpaid">Belum Lunas (Unpaid)</option>
                            <option value="paid" selected>Lunas (Paid)</option>
                            <option value="returned">Sudah Dikembalikan (Returned)</option>
                            <option value="late">Terlambat (Late)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="rental_notes">Catatan Transaksi</label>
                    <textarea id="rental_notes" name="notes" class="form-control" rows="2" placeholder="Tujuan sewa, kelengkapan aksesoris tambahan, dll."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalAddRental')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Transaksi Rental</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Rental: Ditampilkan hanya jika ada data sewa yang sedang diedit -->
<?php if ($editRental): ?>
<div id="modalEditRental" class="modal-backdrop show">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>Edit Transaksi Rental</h3>
            <a href="index.php?page=rentals" class="modal-close">&times;</a>
        </div>
        <form action="index.php?page=rentals" method="POST">
            <!-- Parameter aksi controller: perbarui data rental -->
            <input type="hidden" name="action" value="update_rental">
            <!-- ID record rental yang sedang diperbarui -->
            <input type="hidden" name="id" value="<?= $editRental['id'] ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_rental_eq_id">Alat / Kamera (Relasi FK) *</label>
                    <select id="edit_rental_eq_id" name="equipment_id" class="form-control" required>
                        <!-- Pilihan alat saat edit (otomatis terpilih sesuai data relasi rental) -->
                        <?php foreach ($equipmentsList as $eq): ?>
                            <option value="<?= $eq['id'] ?>" <?= ($eq['id'] == $editRental['equipment_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($eq['name']) ?> (SN: <?= htmlspecialchars($eq['serial_number']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_rental_cust_id">Pelanggan (Relasi FK) *</label>
                        <select id="edit_rental_cust_id" name="customer_id" class="form-control" required>
                            <!-- Pilihan pelanggan (otomatis terpilih sesuai data relasi customer_id) -->
                            <?php foreach ($customersList as $cust): ?>
                                <option value="<?= $cust['id'] ?>" <?= ($cust['id'] == $editRental['customer_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cust['name']) ?> (<?= htmlspecialchars($cust['phone']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_rental_staff_id">Petugas / Operator (Relasi FK) *</label>
                        <select id="edit_rental_staff_id" name="staff_id" class="form-control" required>
                            <!-- Pilihan petugas (otomatis terpilih sesuai data relasi staff_id) -->
                            <?php foreach ($staffList as $stf): ?>
                                <option value="<?= $stf['id'] ?>" <?= ($stf['id'] == $editRental['staff_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($stf['name']) ?> (<?= htmlspecialchars($stf['role']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_rental_date">Tanggal Pinjam *</label>
                        <!-- Mengisi input tanggal pinjam yang tersimpan sebelumnya -->
                        <input type="date" id="edit_rental_date" name="rental_date" class="form-control" value="<?= $editRental['rental_date'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_return_date">Tanggal Kembali *</label>
                        <!-- Mengisi input tanggal rencana kembali yang tersimpan sebelumnya -->
                        <input type="date" id="edit_return_date" name="return_date" class="form-control" value="<?= $editRental['return_date'] ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_total_cost">Total Biaya Rental (Rp) *</label>
                        <!-- Mengisi input total biaya sewa yang tersimpan sebelumnya -->
                        <input type="number" id="edit_total_cost" name="total_cost" class="form-control" value="<?= $editRental['total_cost'] ?>" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_payment_status">Status Pembayaran / Sewa *</label>
                        <!-- Pilihan status pembayaran sewa dengan kondisi selected -->
                        <select id="edit_payment_status" name="payment_status" class="form-control" required>
                            <option value="unpaid" <?= ($editRental['payment_status'] === 'unpaid') ? 'selected' : '' ?>>Belum Lunas (Unpaid)</option>
                            <option value="paid" <?= ($editRental['payment_status'] === 'paid') ? 'selected' : '' ?>>Lunas (Paid)</option>
                            <option value="returned" <?= ($editRental['payment_status'] === 'returned') ? 'selected' : '' ?>>Sudah Dikembalikan (Returned)</option>
                            <option value="late" <?= ($editRental['payment_status'] === 'late') ? 'selected' : '' ?>>Terlambat (Late)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_rental_notes">Catatan Transaksi</label>
                    <!-- Menampilkan catatan khusus sewa yang tersimpan sebelumnya -->
                    <textarea id="edit_rental_notes" name="notes" class="form-control" rows="2"><?= htmlspecialchars($editRental['notes']) ?></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <a href="index.php?page=rentals" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Perbarui Transaksi</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
