        </div>
    </main>

    <!-- Footer Informasi Sistem -->
    <footer class="footer">
        <div class="footer-container">
            <p>&copy; <?= date('Y') ?> <strong>LensGear Hub</strong> — Tugas Praktikum 7 Desain & Pemrograman Berorientasi Objek (DPBO)</p>
        </div>
    </footer>

    <!-- JavaScript Logika Antarmuka (Modal, Konfirmasi Hapus, & Kalkulasi Rental) -->
    <script>
        // Membuka modal dialog berdasarkan ID elemen
        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden'; // Menahan scroll pada latar belakang
            }
        }

        // Menutup modal dialog berdasarkan ID elemen
        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = ''; // Mengaktifkan kembali scroll latar belakang
            }
        }

        // Menutup modal jika pengguna mengklik area backdrop di luar dialog modal
        window.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-backdrop')) {
                e.target.classList.remove('show');
                document.body.style.overflow = '';
            }
        });

        // Menampilkan dialog konfirmasi JavaScript sebelum eksekusi penghapusan data
        function confirmDelete(message, url) {
            if (confirm(message || 'Apakah Anda yakin ingin menghapus data ini?')) {
                window.location.href = url;
            }
        }

        // Menghitung total biaya rental secara otomatis berdasarkan durasi hari & tarif harian alat
        function calculateRentalCost() {
            const eqSelect = document.getElementById('rental_equipment_id');
            const rentalDateInput = document.getElementById('rental_date');
            const returnDateInput = document.getElementById('return_date');
            const totalCostInput = document.getElementById('total_cost');

            if (!eqSelect || !rentalDateInput || !returnDateInput || !totalCostInput) return;

            // Mengambil tarif harian dari atribut data-rate pada opsi alat yang dipilih
            const selectedOption = eqSelect.options[eqSelect.selectedIndex];
            const dailyRate = parseInt(selectedOption ? selectedOption.getAttribute('data-rate') || 0 : 0);

            const rDate = new Date(rentalDateInput.value);
            const retDate = new Date(returnDateInput.value);

            // Menghitung selisih hari jika tanggal pengembalian valid dan tidak mendahului tanggal pinjam
            if (!isNaN(rDate.getTime()) && !isNaN(retDate.getTime()) && retDate >= rDate) {
                const diffTime = Math.abs(retDate - rDate);
                let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                if (diffDays === 0) diffDays = 1; // Minimal sewa dihitung 1 hari
                const total = diffDays * dailyRate;
                totalCostInput.value = total;
            }
        }
    </script>
</body>
</html>
