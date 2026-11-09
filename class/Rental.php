<?php
// Memuat konfigurasi koneksi basis data
require_once 'config/db.php';
// Memuat class model Equipment untuk sinkronisasi data stok unit
require_once 'class/Equipment.php';

// Class Rental: Model OOP untuk mengelola seluruh transaksi penyewaan pada tabel 'rentals'
class Rental {
    // Variabel privat penyimpan koneksi aktif PDO
    private $db;

    // Konstruktor: Menginisialisasi koneksi basis data
    public function __construct() {
        // Mengambil objek koneksi PDO aktif dari instance Database
        $this->db = (new Database())->conn;
    }

    // Mengambil seluruh transaksi rental dengan menggabungkan tabel equipments, customers, dan staff
    public function getAllRentals() {
        // Query SELECT dengan relasi INNER JOIN multi-tabel untuk mengambil nama alat, penyewa, dan staf
        $stmt = $this->db->prepare("SELECT rentals.*, 
                                           equipments.name AS equipment_name, 
                                           equipments.serial_number, 
                                           customers.name AS customer_name, 
                                           customers.phone AS customer_phone, 
                                           staff.name AS staff_name 
                                    FROM rentals 
                                    JOIN equipments ON rentals.equipment_id = equipments.id 
                                    JOIN customers ON rentals.customer_id = customers.id 
                                    JOIN staff ON rentals.staff_id = staff.id 
                                    ORDER BY rentals.id DESC");
        // Mengeksekusi query database
        $stmt->execute();
        // Mengembalikan seluruh baris data transaksi sebagai array asosiatif
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mengambil satu data transaksi rental spesifik berdasarkan ID
    public function getRentalById($id) {
        // Menyiapkan query berparameter dengan placeholder positional (?) untuk keamanan
        $stmt = $this->db->prepare("SELECT * FROM rentals WHERE id = ?");
        // Mengeksekusi query dengan parameter $id
        $stmt->execute([$id]);
        // Mengembalikan satu baris data transaksi hasil pencarian
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Membuat transaksi rental baru sekaligus memotong stok unit alat secara otomatis
    public function addRental($equipment_id, $customer_id, $staff_id, $rental_date, $return_date, $total_cost, $payment_status, $notes) {
        // Memeriksa ketersediaan stok alat terlebih dahulu sebelum memproses sewa
        $stmtStock = $this->db->prepare("SELECT stock FROM equipments WHERE id = ?");
        // Mengeksekusi pengecekan stok alat bersangkutan
        $stmtStock->execute([$equipment_id]);
        // Mengambil baris data stok alat
        $eqData = $stmtStock->fetch(PDO::FETCH_ASSOC);

        // Validasi: Pastikan alat ditemukan dan stoknya mencukupi (> 0)
        if ($eqData && (int)$eqData['stock'] > 0) {
            // Menyiapkan query INSERT transaksi peminjaman baru
            $stmt = $this->db->prepare("INSERT INTO rentals (equipment_id, customer_id, staff_id, rental_date, return_date, total_cost, payment_status, notes) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            // Mengeksekusi penyimpanan transaksi rental
            $success = $stmt->execute([$equipment_id, $customer_id, $staff_id, $rental_date, $return_date, (int)$total_cost, $payment_status, $notes]);
            
            // Jika pencatatan transaksi berhasil, kurangi stok unit alat sebanyak 1
            if ($success) {
                // Membuat objek Equipment untuk memanggil logika pembaruan stok
                $equipment = new Equipment();
                // Mengurangi stok fisik alat sebesar 1 unit
                $equipment->updateStock($equipment_id, (int)$eqData['stock'] - 1);
                // Mengembalikan true menandakan proses berhasil
                return true;
            }
        }
        // Mengembalikan false jika stok alat habis atau data tidak valid
        return false;
    }

    // Memperbarui data transaksi rental dan menyinkronkan stok jika unit alat ditukar
    public function updateRental($id, $equipment_id, $customer_id, $staff_id, $rental_date, $return_date, $total_cost, $payment_status, $notes) {
        // Mengambil data rental lama untuk mengecek apakah ada pergantian alat yang disewa
        $stmtOld = $this->db->prepare("SELECT equipment_id, payment_status FROM rentals WHERE id = ?");
        $stmtOld->execute([$id]);
        $oldRental = $stmtOld->fetch(PDO::FETCH_ASSOC);

        // Jika alat diganti dan transaksi belum selesai (belum dikembalikan), sinkronkan stok kedua alat
        if ($oldRental && $oldRental['equipment_id'] != $equipment_id && $oldRental['payment_status'] !== 'returned') {
            $equipment = new Equipment();
            
            // 1. Mengembalikan stok alat yang lama (+1)
            $stmtOldStock = $this->db->prepare("SELECT stock FROM equipments WHERE id = ?");
            $stmtOldStock->execute([$oldRental['equipment_id']]);
            $oldEq = $stmtOldStock->fetch(PDO::FETCH_ASSOC);
            if ($oldEq) {
                $equipment->updateStock($oldRental['equipment_id'], (int)$oldEq['stock'] + 1);
            }

            // 2. Mengurangi stok alat pengganti yang baru (-1)
            $stmtNewStock = $this->db->prepare("SELECT stock FROM equipments WHERE id = ?");
            $stmtNewStock->execute([$equipment_id]);
            $newEq = $stmtNewStock->fetch(PDO::FETCH_ASSOC);
            if ($newEq) {
                $equipment->updateStock($equipment_id, (int)$newEq['stock'] - 1);
            }
        }

        // Menyiapkan statement UPDATE data transaksi rental
        $stmt = $this->db->prepare("UPDATE rentals 
                                    SET equipment_id = ?, customer_id = ?, staff_id = ?, rental_date = ?, return_date = ?, total_cost = ?, payment_status = ?, notes = ? 
                                    WHERE id = ?");
        // Mengeksekusi query dengan parameter data transaksi yang telah diperbarui
        return $stmt->execute([$equipment_id, $customer_id, $staff_id, $rental_date, $return_date, (int)$total_cost, $payment_status, $notes, $id]);
    }

    // Memproses pengembalian unit alat (Return): Menandai status 'returned' dan memulihkan stok alat
    public function returnRental($id) {
        // Mengambil informasi transaksi untuk mengetahui alat yang disewa dan status saat ini
        $stmtRental = $this->db->prepare("SELECT equipment_id, payment_status FROM rentals WHERE id = ?");
        $stmtRental->execute([$id]);
        $rental = $stmtRental->fetch(PDO::FETCH_ASSOC);

        // Validasi: Pastikan transaksi ada dan statusnya belum pernah dikembalikan sebelumnya
        if ($rental && $rental['payment_status'] !== 'returned') {
            // Mengubah status transaksi rental menjadi 'returned'
            $stmt = $this->db->prepare("UPDATE rentals SET payment_status = 'returned' WHERE id = ?");
            $success = $stmt->execute([$id]);
            
            // Jika status berhasil diubah, pulihkan stok fisik unit alat (+1)
            if ($success) {
                // Mengambil jumlah stok terkini dari alat yang bersangkutan
                $stmtStock = $this->db->prepare("SELECT stock FROM equipments WHERE id = ?");
                $stmtStock->execute([$rental['equipment_id']]);
                $eqData = $stmtStock->fetch(PDO::FETCH_ASSOC);
                
                // Jika data alat valid, tambahkan stoknya kembali
                if ($eqData) {
                    $equipment = new Equipment();
                    // Menambah stok fisik alat sebanyak 1 unit
                    $equipment->updateStock($rental['equipment_id'], (int)$eqData['stock'] + 1);
                }
                // Mengembalikan true menandakan pengembalian berhasil diproses
                return true;
            }
        }
        // Mengembalikan false jika transaksi tidak memenuhi syarat pengembalian
        return false;
    }

    // Menghapus data transaksi rental dari basis data
    public function deleteRental($id) {
        // Memeriksa transaksi untuk memastikan apakah stok perlu dikembalikan sebelum dihapus
        $stmtCheck = $this->db->prepare("SELECT equipment_id, payment_status FROM rentals WHERE id = ?");
        $stmtCheck->execute([$id]);
        $rental = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        // Jika transaksi dihapus saat statusnya belum dikembalikan, kembalikan stok unit alat (+1)
        if ($rental && $rental['payment_status'] !== 'returned') {
            $stmtStock = $this->db->prepare("SELECT stock FROM equipments WHERE id = ?");
            $stmtStock->execute([$rental['equipment_id']]);
            $eqData = $stmtStock->fetch(PDO::FETCH_ASSOC);
            if ($eqData) {
                $equipment = new Equipment();
                // Memulihkan stok fisik alat
                $equipment->updateStock($rental['equipment_id'], (int)$eqData['stock'] + 1);
            }
        }

        // Menyiapkan statement DELETE transaksi rental berdasarkan ID
        $stmt = $this->db->prepare("DELETE FROM rentals WHERE id = ?");
        // Mengeksekusi penghapusan data transaksi dari database
        return $stmt->execute([$id]);
    }
}
?>
