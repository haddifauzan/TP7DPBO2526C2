<?php
// Memulai sesi PHP jika belum ada sesi yang aktif, digunakan untuk flash messages (pesan notifikasi)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Memuat konfigurasi basis data dan seluruh file class model OOP
require_once 'config/db.php';
require_once 'class/Category.php';
require_once 'class/Brand.php';
require_once 'class/Customer.php';
require_once 'class/Staff.php';
require_once 'class/Equipment.php';
require_once 'class/Rental.php';

// Inisialisasi instance objek untuk masing-masing class model
$categoryModel  = new Category();   // Objek pengelola tabel categories
$brandModel     = new Brand();      // Objek pengelola tabel brands
$customerModel  = new Customer();   // Objek pengelola tabel customers
$staffModel     = new Staff();      // Objek pengelola tabel staff
$equipmentModel = new Equipment();  // Objek pengelola tabel equipments
$rentalModel    = new Rental();     // Objek pengelola tabel rentals

// Menentukan daftar halaman yang valid (whitelist) untuk mencegah Local File Inclusion (LFI)
$allowedPages = ['rentals', 'equipments', 'categories', 'brands', 'customers', 'staff'];
// Menetapkan halaman aktif berdasarkan parameter URL ?page=..., jika tidak ada maka default ke 'rentals'
$page = isset($_GET['page']) && in_array($_GET['page'], $allowedPages) ? $_GET['page'] : 'rentals';

// ============================================================
// 1. PENANGANAN HTTP POST (OPERASI CREATE & UPDATE) - PRG PATTERN
// ============================================================
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action']; // Mengambil identifier aksi form yang dikirimkan

    try {
        switch ($action) {
            // --- KELOMPOK AKSI KATEGORI ---
            case 'add_category':
                // Memanggil method penambahan kategori dengan data nama dan deskripsi
                $categoryModel->addCategory($_POST['name'], $_POST['description']);
                // Menyiapkan pesan notifikasi sukses pada sesi
                $_SESSION['msg'] = "Kategori '{$_POST['name']}' berhasil ditambahkan!";
                $_SESSION['msg_type'] = "success";
                // Redirect ke halaman kategori untuk mencegah resubmisi ganda (Pola PRG)
                header("Location: index.php?page=categories");
                exit;

            case 'update_category':
                // Memanggil method pembaruan kategori berdasarkan ID
                $categoryModel->updateCategory($_POST['id'], $_POST['name'], $_POST['description']);
                $_SESSION['msg'] = "Kategori berhasil diperbarui!";
                $_SESSION['msg_type'] = "success";
                header("Location: index.php?page=categories");
                exit;

            // --- KELOMPOK AKSI BRAND / MEREK ---
            case 'add_brand':
                // Memanggil method penambahan brand dengan nama dan negara asal
                $brandModel->addBrand($_POST['name'], $_POST['country']);
                $_SESSION['msg'] = "Brand '{$_POST['name']}' berhasil ditambahkan!";
                $_SESSION['msg_type'] = "success";
                header("Location: index.php?page=brands");
                exit;

            case 'update_brand':
                // Memanggil method pembaruan data brand berdasarkan ID
                $brandModel->updateBrand($_POST['id'], $_POST['name'], $_POST['country']);
                $_SESSION['msg'] = "Data brand berhasil diperbarui!";
                $_SESSION['msg_type'] = "success";
                header("Location: index.php?page=brands");
                exit;

            // --- KELOMPOK AKSI PELANGGAN ---
            case 'add_customer':
                // Memanggil method penambahan pelanggan baru dengan validasi parameter
                $customerModel->addCustomer(
                    $_POST['name'],
                    $_POST['identity_number'],
                    $_POST['phone'],
                    $_POST['email'],
                    $_POST['address']
                );
                $_SESSION['msg'] = "Data pelanggan '{$_POST['name']}' berhasil disimpan!";
                $_SESSION['msg_type'] = "success";
                header("Location: index.php?page=customers");
                exit;

            case 'update_customer':
                // Memanggil method pembaruan data profil pelanggan berdasarkan ID
                $customerModel->updateCustomer(
                    $_POST['id'],
                    $_POST['name'],
                    $_POST['identity_number'],
                    $_POST['phone'],
                    $_POST['email'],
                    $_POST['address']
                );
                $_SESSION['msg'] = "Data pelanggan berhasil diperbarui!";
                $_SESSION['msg_type'] = "success";
                header("Location: index.php?page=customers");
                exit;

            // --- KELOMPOK AKSI STAF / PETUGAS ---
            case 'add_staff':
                // Memanggil method penambahan staf baru
                $staffModel->addStaff(
                    $_POST['name'],
                    $_POST['employee_code'],
                    $_POST['phone'],
                    $_POST['role']
                );
                $_SESSION['msg'] = "Data staf '{$_POST['name']}' berhasil ditambahkan!";
                $_SESSION['msg_type'] = "success";
                header("Location: index.php?page=staff");
                exit;

            case 'update_staff':
                // Memanggil method pembaruan data staf berdasarkan ID
                $staffModel->updateStaff(
                    $_POST['id'],
                    $_POST['name'],
                    $_POST['employee_code'],
                    $_POST['phone'],
                    $_POST['role']
                );
                $_SESSION['msg'] = "Data staf berhasil diperbarui!";
                $_SESSION['msg_type'] = "success";
                header("Location: index.php?page=staff");
                exit;

            // --- KELOMPOK AKSI PERALATAN / ALAT ---
            case 'add_equipment':
                // Memanggil method penambahan alat baru ke inventaris dengan relasi FK Kategori & Brand
                $equipmentModel->addEquipment(
                    $_POST['category_id'],
                    $_POST['brand_id'],
                    $_POST['name'],
                    $_POST['serial_number'],
                    $_POST['daily_rate'],
                    $_POST['stock'],
                    $_POST['status'] ?? 'available'
                );
                $_SESSION['msg'] = "Alat baru '{$_POST['name']}' berhasil ditambahkan ke inventaris!";
                $_SESSION['msg_type'] = "success";
                header("Location: index.php?page=equipments");
                exit;

            case 'update_equipment':
                // Memanggil method pembaruan data peralatan inventaris
                $equipmentModel->updateEquipment(
                    $_POST['id'],
                    $_POST['category_id'],
                    $_POST['brand_id'],
                    $_POST['name'],
                    $_POST['serial_number'],
                    $_POST['daily_rate'],
                    $_POST['stock'],
                    $_POST['status']
                );
                $_SESSION['msg'] = "Data peralatan berhasil diperbarui!";
                $_SESSION['msg_type'] = "success";
                header("Location: index.php?page=equipments");
                exit;

            // --- KELOMPOK AKSI TRANSAKSI RENTAL ---
            case 'add_rental':
                // Memproses transaksi sewa baru sekaligus memotong stok unit alat
                $success = $rentalModel->addRental(
                    $_POST['equipment_id'],
                    $_POST['customer_id'],
                    $_POST['staff_id'],
                    $_POST['rental_date'],
                    $_POST['return_date'],
                    $_POST['total_cost'],
                    $_POST['payment_status'],
                    $_POST['notes']
                );
                
                // Menentukan notifikasi berdasarkan ketersediaan stok alat
                if ($success) {
                    $_SESSION['msg'] = "Transaksi rental berhasil dibuat! Stok alat telah disesuaikan.";
                    $_SESSION['msg_type'] = "success";
                } else {
                    $_SESSION['msg'] = "Gagal membuat rental: Stok unit alat tidak mencukupi!";
                    $_SESSION['msg_type'] = "danger";
                }
                header("Location: index.php?page=rentals");
                exit;

            case 'update_rental':
                // Memanggil method pembaruan transaksi rental dan sinkronisasi stok alat
                $rentalModel->updateRental(
                    $_POST['id'],
                    $_POST['equipment_id'],
                    $_POST['customer_id'],
                    $_POST['staff_id'],
                    $_POST['rental_date'],
                    $_POST['return_date'],
                    $_POST['total_cost'],
                    $_POST['payment_status'],
                    $_POST['notes']
                );
                $_SESSION['msg'] = "Data transaksi rental berhasil diperbarui!";
                $_SESSION['msg_type'] = "success";
                header("Location: index.php?page=rentals");
                exit;
        }
    } catch (PDOException $e) {
        // Menangkap error exception query PDO dan menampilkan pesan error ke user
        $_SESSION['msg'] = "Terjadi kesalahan basis data: " . $e->getMessage();
        $_SESSION['msg_type'] = "danger";
        header("Location: index.php?page={$page}");
        exit;
    }
}

// ============================================================
// 2. PENANGANAN HTTP GET (OPERASI DELETE & RETURN TRANSAKSI)
// ============================================================
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET' && isset($_GET['action'])) {
    $action = $_GET['action'];                      // Mengambil nama aksi GET
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0; // Mengonversi ID ke tipe integer untuk keamanan

    try {
        switch ($action) {
            case 'delete_category':
                // Menghapus data kategori berdasarkan ID
                $categoryModel->deleteCategory($id);
                $_SESSION['msg'] = "Kategori berhasil dihapus!";
                $_SESSION['msg_type'] = "success";
                header("Location: index.php?page=categories");
                exit;

            case 'delete_brand':
                // Menghapus data brand berdasarkan ID
                $brandModel->deleteBrand($id);
                $_SESSION['msg'] = "Brand berhasil dihapus!";
                $_SESSION['msg_type'] = "success";
                header("Location: index.php?page=brands");
                exit;

            case 'delete_customer':
                // Menghapus data pelanggan berdasarkan ID
                $customerModel->deleteCustomer($id);
                $_SESSION['msg'] = "Data pelanggan berhasil dihapus!";
                $_SESSION['msg_type'] = "success";
                header("Location: index.php?page=customers");
                exit;

            case 'delete_staff':
                // Menghapus data staf berdasarkan ID
                $staffModel->deleteStaff($id);
                $_SESSION['msg'] = "Data staf berhasil dihapus!";
                $_SESSION['msg_type'] = "success";
                header("Location: index.php?page=staff");
                exit;

            case 'delete_equipment':
                // Menghapus data alat dari inventaris
                $equipmentModel->deleteEquipment($id);
                $_SESSION['msg'] = "Peralatan berhasil dihapus dari inventaris!";
                $_SESSION['msg_type'] = "success";
                header("Location: index.php?page=equipments");
                exit;

            case 'delete_rental':
                // Menghapus transaksi rental dan memulihkan stok jika belum dikembalikan
                $rentalModel->deleteRental($id);
                $_SESSION['msg'] = "Data transaksi rental berhasil dihapus!";
                $_SESSION['msg_type'] = "success";
                header("Location: index.php?page=rentals");
                exit;

            case 'return_rental':
                // Memproses pengembalian unit alat (mengubah status jadi 'returned' & menambah stok +1)
                $success = $rentalModel->returnRental($id);
                if ($success) {
                    $_SESSION['msg'] = "Pengembalian alat berhasil dicatat! Stok alat otomatis bertambah.";
                    $_SESSION['msg_type'] = "success";
                } else {
                    $_SESSION['msg'] = "Gagal memproses pengembalian: Unit alat mungkin sudah berstatus returned.";
                    $_SESSION['msg_type'] = "warning";
                }
                header("Location: index.php?page=rentals");
                exit;
        }
    } catch (PDOException $e) {
        // Menangani Foreign Key Constraint Violation (Kode Error SQLSTATE 23000)
        if ($e->getCode() == '23000') {
            $_SESSION['msg'] = "Data tidak dapat dihapus karena masih digunakan sebagai referensi oleh tabel lain (Foreign Key Constraint).";
        } else {
            $_SESSION['msg'] = "Terjadi kesalahan: " . $e->getMessage();
        }
        $_SESSION['msg_type'] = "danger";
        header("Location: index.php?page={$page}");
        exit;
    }
}

// ============================================================
// 3. PERSIAPAN DATA UNTUK DITAMPILKAN PADA VIEW
// ============================================================
// Memeriksa apakah ada parameter edit_id di URL untuk membuka modal edit
$editId = isset($_GET['edit_id']) ? (int)$_GET['edit_id'] : null;

// Mengambil kumpulan data yang dibutuhkan oleh masing-masing halaman view
switch ($page) {
    case 'categories':
        $categoriesList = $categoryModel->getAllCategories();                        // Daftar seluruh kategori
        $editCategory   = $editId ? $categoryModel->getCategoryById($editId) : null; // Data spesifik kategori yang diedit
        break;

    case 'brands':
        $brandsList = $brandModel->getAllBrands();                        // Daftar seluruh merek
        $editBrand  = $editId ? $brandModel->getBrandById($editId) : null; // Data spesifik brand yang diedit
        break;

    case 'customers':
        $customersList = $customerModel->getAllCustomers();                            // Daftar seluruh pelanggan
        $editCustomer  = $editId ? $customerModel->getCustomerById($editId) : null;    // Data pelanggan yang diedit
        break;

    case 'staff':
        $staffList = $staffModel->getAllStaff();                        // Daftar seluruh staf
        $editStaff = $editId ? $staffModel->getStaffById($editId) : null; // Data staf yang diedit
        break;

    case 'equipments':
        $equipmentsList = $equipmentModel->getAllEquipments();                        // Daftar alat beserta nama kategori & brand
        $categoriesList = $categoryModel->getAllCategories();                         // Data kategori untuk dropdown FK
        $brandsList     = $brandModel->getAllBrands();                                // Data brand untuk dropdown FK
        $editEquipment  = $editId ? $equipmentModel->getEquipmentById($editId) : null; // Data alat yang diedit
        break;

    case 'rentals':
    default:
        $rentalsList    = $rentalModel->getAllRentals();                        // Daftar riwayat rental (JOIN 3 tabel)
        $equipmentsList = $equipmentModel->getAllEquipments();                  // Data alat untuk dropdown FK transaksi
        $customersList  = $customerModel->getAllCustomers();                    // Data pelanggan untuk dropdown FK transaksi
        $staffList      = $staffModel->getAllStaff();                            // Data staf untuk dropdown FK transaksi
        $editRental     = $editId ? $rentalModel->getRentalById($editId) : null; // Data rental yang diedit
        break;
}

// ============================================================
// 4. MERENDER TAMPILAN MODULAR ANTARMUKA PENGGUNA (GUI)
// ============================================================
include 'view/header.php';      // Memuat bagian atas halaman (navbar, CSS link, dan flash message)
include "view/{$page}.php";     // Memuat konten view yang sesuai dengan modul halaman aktif
include 'view/footer.php';      // Memuat bagian bawah halaman (footer info dan script modal/kalkulasi)
?>
