<?php
// Memuat konfigurasi koneksi basis data
require_once 'config/db.php';

// Class Customer: Model OOP untuk mengelola seluruh data penyewa/pelanggan pada tabel 'customers'
class Customer {
    // Variabel privat penyimpan koneksi aktif PDO
    private $db;

    // Konstruktor: Menginisialisasi koneksi basis data
    public function __construct() {
        // Mengambil objek koneksi PDO aktif dari instance Database
        $this->db = (new Database())->conn;
    }

    // Mengambil seluruh data pelanggan, diurutkan dari yang terbaru (DESC)
    public function getAllCustomers() {
        // Menyiapkan prepared statement query SELECT seluruh pelanggan
        $stmt = $this->db->prepare("SELECT * FROM customers ORDER BY id DESC");
        // Mengeksekusi query database
        $stmt->execute();
        // Mengembalikan seluruh baris data dalam format associative array
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mengambil data spesifik satu pelanggan berdasarkan ID
    public function getCustomerById($id) {
        // Menyiapkan query berparameter dengan placeholder (?) untuk keamanan
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE id = ?");
        // Mengeksekusi query dengan mengikat nilai parameter $id
        $stmt->execute([$id]);
        // Mengembalikan baris data pelanggan yang sesuai
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Menambahkan data pelanggan baru ke basis data
    public function addCustomer($name, $identity_number, $phone, $email, $address) {
        // Menyiapkan statement INSERT untuk 5 atribut pelanggan
        $stmt = $this->db->prepare("INSERT INTO customers (name, identity_number, phone, email, address) VALUES (?, ?, ?, ?, ?)");
        // Mengeksekusi query dengan parameter terikat secara aman
        return $stmt->execute([$name, $identity_number, $phone, $email, $address]);
    }

    // Memperbarui data profil pelanggan yang sudah ada berdasarkan ID
    public function updateCustomer($id, $name, $identity_number, $phone, $email, $address) {
        // Menyiapkan statement UPDATE untuk mengubah informasi pelanggan berdasarkan ID
        $stmt = $this->db->prepare("UPDATE customers SET name = ?, identity_number = ?, phone = ?, email = ?, address = ? WHERE id = ?");
        // Mengeksekusi query dengan parameter data baru beserta ID target
        return $stmt->execute([$name, $identity_number, $phone, $email, $address, $id]);
    }

    // Menghapus data pelanggan dari basis data berdasarkan ID
    public function deleteCustomer($id) {
        // Menyiapkan statement DELETE berdasarkan ID
        $stmt = $this->db->prepare("DELETE FROM customers WHERE id = ?");
        // Mengeksekusi perintah penghapusan data
        return $stmt->execute([$id]);
    }
}
?>
