<?php
// Memuat konfigurasi koneksi basis data
require_once 'config/db.php';

// Class Equipment: Model OOP untuk mengelola inventaris peralatan kamera dan multimedia pada tabel 'equipments'
class Equipment {
    // Variabel privat penyimpan koneksi aktif PDO
    private $db;

    // Konstruktor: Menginisialisasi koneksi basis data
    public function __construct() {
        // Mengambil objek koneksi PDO aktif dari instance Database
        $this->db = (new Database())->conn;
    }

    // Mengambil seluruh data peralatan dengan menggabungkan (JOIN) tabel categories dan brands
    public function getAllEquipments() {
        // Menyiapkan query SELECT dengan relasi INNER JOIN untuk mendapatkan nama kategori dan merek
        $stmt = $this->db->prepare("SELECT equipments.*, 
                                           categories.name AS category_name, 
                                           brands.name AS brand_name 
                                    FROM equipments 
                                    JOIN categories ON equipments.category_id = categories.id 
                                    JOIN brands ON equipments.brand_id = brands.id 
                                    ORDER BY equipments.id DESC");
        // Mengeksekusi query database
        $stmt->execute();
        // Mengembalikan seluruh baris data inventaris dalam bentuk associative array
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mengambil data spesifik satu peralatan berdasarkan ID
    public function getEquipmentById($id) {
        // Menyiapkan query berparameter dengan placeholder positional (?) untuk proteksi SQL Injection
        $stmt = $this->db->prepare("SELECT * FROM equipments WHERE id = ?");
        // Mengeksekusi query dengan parameter $id
        $stmt->execute([$id]);
        // Mengembalikan baris data peralatan hasil pencarian
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Menambahkan peralatan baru ke inventaris basis data
    public function addEquipment($category_id, $brand_id, $name, $serial_number, $daily_rate, $stock, $status = 'available') {
        // Menyiapkan statement INSERT untuk seluruh atribut peralatan
        $stmt = $this->db->prepare("INSERT INTO equipments (category_id, brand_id, name, serial_number, daily_rate, stock, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        // Mengeksekusi query dengan parameter yang divalidasi dan dicasting tipe datanya
        return $stmt->execute([$category_id, $brand_id, $name, $serial_number, (int)$daily_rate, (int)$stock, $status]);
    }

    // Memperbarui informasi peralatan di inventaris berdasarkan ID
    public function updateEquipment($id, $category_id, $brand_id, $name, $serial_number, $daily_rate, $stock, $status) {
        // Menyiapkan statement UPDATE pada tabel equipments dengan filter WHERE id = ?
        $stmt = $this->db->prepare("UPDATE equipments SET category_id = ?, brand_id = ?, name = ?, serial_number = ?, daily_rate = ?, stock = ?, status = ? WHERE id = ?");
        // Mengeksekusi query pembaruan data peralatan
        return $stmt->execute([$category_id, $brand_id, $name, $serial_number, (int)$daily_rate, (int)$stock, $status, $id]);
    }

    // Menghapus peralatan dari inventaris berdasarkan ID
    public function deleteEquipment($id) {
        // Menyiapkan statement DELETE berdasarkan parameter ID
        $stmt = $this->db->prepare("DELETE FROM equipments WHERE id = ?");
        // Mengeksekusi perintah penghapusan data
        return $stmt->execute([$id]);
    }

    // Memperbarui stok unit fisik dan secara otomatis menyesuaikan status kesiapan alat
    public function updateStock($id, $stock) {
        // Memastikan nilai stok adalah integer
        $stock = (int)$stock;
        // Logika otomatisasi status: jika stok <= 0 maka 'out_of_stock', jika stok > 0 maka 'available'
        $status = $stock <= 0 ? 'out_of_stock' : 'available';
        // Menyiapkan query UPDATE untuk memperbarui stok dan status kesiapan alat
        $stmt = $this->db->prepare("UPDATE equipments SET stock = ?, status = ? WHERE id = ?");
        // Mengeksekusi query pembaruan stok
        return $stmt->execute([$stock, $status, $id]);
    }
}
?>
