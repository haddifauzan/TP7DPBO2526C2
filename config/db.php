<?php
// Class Database: Mengelola koneksi basis data menggunakan ekstensi PDO (PHP Data Objects)
class Database {
    // Properti kredensial server dan basis data MySQL
    private $host = "localhost";        // Host server basis data lokal
    private $username = "root";         // Nama pengguna default MySQL
    private $password = "";             // Kata sandi default MySQL (kosong di XAMPP/Laragon)
    private $dbname = "db_rental";      // Nama basis data yang digunakan
    public $conn;                       // Menyimpan objek instance koneksi aktif PDO

    // Konstruktor: Otomatis dijalankan saat objek Database diinisialisasi
    public function __construct() {
        try {
            // Membentuk Data Source Name (DSN) dan membuat koneksi PDO baru dengan encoding UTF-8
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname;charset=utf8mb4", $this->username, $this->password);
            
            // Mengatur mode error ke PDOException agar setiap kesalahan query melempar exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Menjadikan format associative array (FETCH_ASSOC) sebagai mode pengambilan data bawaan
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Menghentikan skrip dan menampilkan pesan error jika koneksi gagal terbentuk
            die("Connection failed: " . $e->getMessage());
        }
    }

    // Mengembalikan objek koneksi PDO aktif untuk digunakan oleh class model
    public function getConnection() {
        return $this->conn;
    }
}
?>
