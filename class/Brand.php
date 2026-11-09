<?php
// Memuat konfigurasi koneksi basis data
require_once 'config/db.php';

// Class Brand: Model OOP untuk mengontrol seluruh operasi data pada tabel 'brands'
class Brand {
    // Variabel privat penampung objek koneksi PDO
    private $db;

    // Konstruktor: Menginisialisasi koneksi basis data
    public function __construct() {
        // Mengambil objek koneksi PDO aktif dari instance Database
        $this->db = (new Database())->conn;
    }

    // Mengambil seluruh data merek/produsen dari tabel brands
    public function getAllBrands() {
        // Menyiapkan prepared statement query SELECT dengan urutan id menaik (ASC)
        $stmt = $this->db->prepare("SELECT * FROM brands ORDER BY id ASC");
        // Mengeksekusi query database
        $stmt->execute();
        // Mengembalikan kumpulan seluruh baris data sebagai array asosiatif
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mengambil satu data merek berdasarkan parameter ID
    public function getBrandById($id) {
        // Menyiapkan query berparameter dengan placeholder positional (?) untuk mencegah SQL Injection
        $stmt = $this->db->prepare("SELECT * FROM brands WHERE id = ?");
        // Mengeksekusi query dengan parameter $id
        $stmt->execute([$id]);
        // Mengembalikan satu baris data merek yang ditemukan
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Menambahkan data merek baru ke dalam tabel brands
    public function addBrand($name, $country) {
        // Menyiapkan statement INSERT untuk kolom name dan country
        $stmt = $this->db->prepare("INSERT INTO brands (name, country) VALUES (?, ?)");
        // Mengeksekusi statement dengan mengikat nilai $name dan $country secara aman
        return $stmt->execute([$name, $country]);
    }

    // Memperbarui informasi nama merek dan negara asal berdasarkan ID
    public function updateBrand($id, $name, $country) {
        // Menyiapkan statement UPDATE dengan filter kondisi WHERE id = ?
        $stmt = $this->db->prepare("UPDATE brands SET name = ?, country = ? WHERE id = ?");
        // Mengeksekusi perintah pembaruan data
        return $stmt->execute([$name, $country, $id]);
    }

    // Menghapus data merek dari tabel brands berdasarkan ID
    public function deleteBrand($id) {
        // Menyiapkan statement DELETE berdasarkan ID
        $stmt = $this->db->prepare("DELETE FROM brands WHERE id = ?");
        // Mengeksekusi perintah penghapusan data
        return $stmt->execute([$id]);
    }
}
?>
