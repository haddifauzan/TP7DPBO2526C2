<?php
// Memuat konfigurasi koneksi basis data
require_once 'config/db.php';

// Class Category: Model OOP untuk mengontrol seluruh operasi data pada tabel 'categories'
class Category {
    // Variabel privat untuk menampung koneksi PDO
    private $db;

    // Konstruktor: Menginisialisasi koneksi basis data dari class Database
    public function __construct() {
        // Mengambil objek koneksi PDO aktif dari instance Database
        $this->db = (new Database())->conn;
    }

    // Mengambil seluruh baris data kategori, diurutkan berdasarkan ID dari terkecil ke terbesar
    public function getAllCategories() {
        // Menyiapkan prepared statement query SELECT untuk mengambil semua kolom dari tabel categories
        $stmt = $this->db->prepare("SELECT * FROM categories ORDER BY id ASC");
        // Mengeksekusi query yang telah disiapkan
        $stmt->execute();
        // Mengembalikan seluruh hasil baris data dalam bentuk associative array
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mengambil data satu kategori berdasarkan ID spesifik
    public function getCategoryById($id) {
        // Menyiapkan query berparameter dengan placeholder positional (?) untuk proteksi SQL Injection
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = ?");
        // Mengeksekusi query dengan mengikat nilai parameter $id
        $stmt->execute([$id]);
        // Mengembalikan satu baris data kategori hasil pencarian
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Menambahkan data kategori baru ke basis data
    public function addCategory($name, $description) {
        // Menyiapkan statement INSERT dengan 2 parameter positional
        $stmt = $this->db->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        // Mengeksekusi statement dengan memasukkan nilai $name dan $description secara aman
        return $stmt->execute([$name, $description]);
    }

    // Memperbarui data nama dan deskripsi kategori berdasarkan ID
    public function updateCategory($id, $name, $description) {
        // Menyiapkan statement UPDATE pada tabel categories dengan filter WHERE id = ?
        $stmt = $this->db->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
        // Mengeksekusi query dengan nilai parameter yang diperbarui sesuai urutan placeholder
        return $stmt->execute([$name, $description, $id]);
    }

    // Menghapus data kategori berdasarkan ID
    public function deleteCategory($id) {
        // Menyiapkan statement DELETE berdasarkan parameter ID
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = ?");
        // Mengeksekusi perintah penghapusan data
        return $stmt->execute([$id]);
    }
}
?>
