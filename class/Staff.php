<?php
// Memuat konfigurasi koneksi basis data
require_once 'config/db.php';

// Class Staff: Model OOP untuk mengelola data petugas/operator pada tabel 'staff'
class Staff {
    // Variabel privat penyimpan koneksi aktif PDO
    private $db;

    // Konstruktor: Menginisialisasi koneksi basis data
    public function __construct() {
        // Mengambil objek koneksi PDO aktif dari instance Database
        $this->db = (new Database())->conn;
    }

    // Mengambil seluruh data staf rental, diurutkan berdasarkan ID secara menaik
    public function getAllStaff() {
        // Menyiapkan prepared statement query SELECT seluruh staf
        $stmt = $this->db->prepare("SELECT * FROM staff ORDER BY id ASC");
        // Mengeksekusi query database
        $stmt->execute();
        // Mengembalikan seluruh baris data dalam format associative array
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mengambil satu data staf spesifik berdasarkan ID
    public function getStaffById($id) {
        // Menyiapkan query berparameter dengan placeholder positional (?) untuk proteksi SQL Injection
        $stmt = $this->db->prepare("SELECT * FROM staff WHERE id = ?");
        // Mengeksekusi query dengan parameter $id
        $stmt->execute([$id]);
        // Mengembalikan satu baris data staf yang sesuai
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Menambahkan data staf atau petugas baru ke dalam basis data
    public function addStaff($name, $employee_code, $phone, $role) {
        // Menyiapkan statement INSERT untuk data staf baru
        $stmt = $this->db->prepare("INSERT INTO staff (name, employee_code, phone, role) VALUES (?, ?, ?, ?)");
        // Mengeksekusi query dengan mengikat nilai parameter secara aman
        return $stmt->execute([$name, $employee_code, $phone, $role]);
    }

    // Memperbarui informasi profil atau jabatan staf berdasarkan ID
    public function updateStaff($id, $name, $employee_code, $phone, $role) {
        // Menyiapkan statement UPDATE pada tabel staff berdasarkan WHERE id = ?
        $stmt = $this->db->prepare("UPDATE staff SET name = ?, employee_code = ?, phone = ?, role = ? WHERE id = ?");
        // Mengeksekusi query dengan parameter data baru beserta ID target
        return $stmt->execute([$name, $employee_code, $phone, $role, $id]);
    }

    // Menghapus data staf dari basis data berdasarkan ID
    public function deleteStaff($id) {
        // Menyiapkan statement DELETE berdasarkan parameter ID
        $stmt = $this->db->prepare("DELETE FROM staff WHERE id = ?");
        // Mengeksekusi perintah penghapusan data
        return $stmt->execute([$id]);
    }
}
?>
