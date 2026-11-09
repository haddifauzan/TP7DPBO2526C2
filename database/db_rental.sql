-- ============================================================
-- Basis Data: db_rental
-- Tema: LensGear Hub - Sistem Manajemen Rental Kamera & Multimedia
-- Praktikum DPBO - Modul 8
-- ============================================================

CREATE DATABASE IF NOT EXISTS `db_rental` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `db_rental`;

-- Nonaktifkan pengecekan foreign key sementara agar drop table bersih
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `rentals`;
DROP TABLE IF EXISTS `equipments`;
DROP TABLE IF EXISTS `staff`;
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `brands`;
DROP TABLE IF EXISTS `categories`;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- 1. Tabel: categories (Master Kategori Alat)
-- ============================================================
CREATE TABLE `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 2. Tabel: brands (Master Merek/Produsen)
-- ============================================================
CREATE TABLE `brands` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `country` VARCHAR(100) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 3. Tabel: customers (Data Pelanggan/Penyewa)
-- ============================================================
CREATE TABLE `customers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `identity_number` VARCHAR(50) NOT NULL UNIQUE,
    `phone` VARCHAR(25) NOT NULL,
    `email` VARCHAR(100) NULL,
    `address` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 4. Tabel: staff (Data Petugas/Operator Rental)
-- ============================================================
CREATE TABLE `staff` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `employee_code` VARCHAR(50) NOT NULL UNIQUE,
    `phone` VARCHAR(25) NOT NULL,
    `role` VARCHAR(50) NOT NULL DEFAULT 'Operator',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 5. Tabel: equipments (Inventaris Peralatan)
-- Memiliki Foreign Key ke `categories` dan `brands`
-- ============================================================
CREATE TABLE `equipments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT NOT NULL,
    `brand_id` INT NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `serial_number` VARCHAR(100) NOT NULL UNIQUE,
    `daily_rate` INT NOT NULL,
    `stock` INT NOT NULL DEFAULT 1,
    `status` ENUM('available', 'maintenance', 'out_of_stock') NOT NULL DEFAULT 'available',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_equipment_category` 
        FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) 
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `fk_equipment_brand` 
        FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) 
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 6. Tabel: rentals (Transaksi Penyewaan Alat)
-- Memiliki Foreign Key ke `equipments`, `customers`, dan `staff`
-- ============================================================
CREATE TABLE `rentals` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `equipment_id` INT NOT NULL,
    `customer_id` INT NOT NULL,
    `staff_id` INT NOT NULL,
    `rental_date` DATE NOT NULL,
    `return_date` DATE NOT NULL,
    `total_cost` INT NOT NULL,
    `payment_status` ENUM('unpaid', 'paid', 'returned', 'late') NOT NULL DEFAULT 'unpaid',
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_rental_equipment` 
        FOREIGN KEY (`equipment_id`) REFERENCES `equipments` (`id`) 
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `fk_rental_customer` 
        FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) 
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `fk_rental_staff` 
        FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) 
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- DUMP DATA AWAL (SEEDING DATA DUMMY)
-- ============================================================

-- Data Categories
INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Kamera Mirrorless', 'Bodi kamera digital tanpa cermin dengan sensor Full Frame dan APS-C.'),
(2, 'Lensa Kamera', 'Lensa fix, zoom, dan makro untuk berbagai kebutuhan produksi foto dan video.'),
(3, 'Lighting & Flash', 'Lampu continuous LED video, flash strobe, dan softbox.'),
(4, 'Audio & Mikrofon', 'Mikrofon clip-on wireless, boom mic, dan audio recorder portabel.'),
(5, 'Stabilizer & Gimbal', 'Gimbal elektronik 3-axis untuk stabilisasi gerakan kamera.'),
(6, 'Drone & Aksesoris', 'Perangkat drone kamera udara dan kelengkapan baterai cadangan.');

-- Data Brands
INSERT INTO `brands` (`id`, `name`, `country`) VALUES
(1, 'Sony', 'Jepang'),
(2, 'Canon', 'Jepang'),
(3, 'Fujifilm', 'Jepang'),
(4, 'Godox', 'Tiongkok'),
(5, 'Rode', 'Australia'),
(6, 'DJI', 'Tiongkok');

-- Data Customers
INSERT INTO `customers` (`id`, `name`, `identity_number`, `phone`, `email`, `address`) VALUES
(1, 'Ahmad Fadhilah', '3273011204990001', '081234567890', 'ahmad.fadhilah@gmail.com', 'Jl. Setiabudi No. 123, Bandung'),
(2, 'Siti Nurhaliza', '3273025508010003', '085721987654', 'siti.nurhaliza@gmail.com', 'Jl. Gegerkalong Hilir No. 45, Bandung'),
(3, 'Rian Pratama', '3204121503980005', '087822334455', 'rian.pratama@yahoo.com', 'Jl. Dago Asri No. 18, Bandung'),
(4, 'Dewi Lestari', '3271046811000002', '089655443322', 'dewi.lestari@gmail.com', 'Jl. Ciumbuleuit No. 88, Bandung'),
(5, 'Budi Santoso', '3273051010970004', '082133445566', 'budi.santoso@outlook.com', 'Jl. Buah Batu No. 210, Bandung');

-- Data Staff
INSERT INTO `staff` (`id`, `name`, `employee_code`, `phone`, `role`) VALUES
(1, 'Rangga Wicaksono', 'STF-001', '081399887766', 'Manajer Operasional'),
(2, 'Nadia Maharani', 'STF-002', '081266554433', 'Kasir & Front Office'),
(3, 'Ilham Saputra', 'STF-003', '085811223344', 'Teknisi Peralatan'),
(4, 'Farhan Maulana', 'STF-004', '087799001122', 'Customer Support');

-- Data Equipments
INSERT INTO `equipments` (`id`, `category_id`, `brand_id`, `name`, `serial_number`, `daily_rate`, `stock`, `status`) VALUES
(1, 1, 1, 'Sony Alpha 7 IV (Body Only)', 'SN-SNY-A7M4-001', 350000, 3, 'available'),
(2, 1, 2, 'Canon EOS R6 Mark II', 'SN-CAN-R6M2-002', 380000, 2, 'available'),
(3, 1, 3, 'Fujifilm X-T5 Silver', 'SN-FUJ-XT5-003', 280000, 2, 'available'),
(4, 2, 1, 'Sony FE 24-70mm f/2.8 GM II', 'SN-SNY-2470-004', 200000, 4, 'available'),
(5, 2, 2, 'Canon RF 50mm f/1.2 L USM', 'SN-CAN-5012-005', 180000, 2, 'available'),
(6, 3, 4, 'Godox SL60W LED Video Light Kit', 'SN-GDX-SL60-006', 75000, 5, 'available'),
(7, 4, 5, 'Rode Wireless GO II Dual Mic', 'SN-ROD-WGO2-007', 120000, 3, 'available'),
(8, 5, 6, 'DJI RS 3 Pro Gimbal Stabilizer', 'SN-DJI-RS3P-008', 220000, 2, 'available'),
(9, 6, 6, 'DJI Mini 3 Pro Fly More Combo', 'SN-DJI-M3PR-009', 300000, 1, 'maintenance');

-- Data Rentals
INSERT INTO `rentals` (`id`, `equipment_id`, `customer_id`, `staff_id`, `rental_date`, `return_date`, `total_cost`, `payment_status`, `notes`) VALUES
(1, 1, 1, 2, '2026-11-01', '2026-11-03', 700000, 'returned', 'Sewa 2 hari untuk project dokumentasi wisuda. Unit kembali lengkap dan mulus.'),
(2, 4, 1, 2, '2026-11-01', '2026-11-03', 400000, 'returned', 'Paket lensa bersama bodi Sony A7IV.'),
(3, 2, 2, 2, '2026-11-05', '2026-11-08', 1140000, 'paid', 'Sewa 3 hari pre-wedding photoshoot di Lembang.'),
(4, 7, 3, 4, '2026-11-07', '2026-11-09', 240000, 'paid', 'Sewa mic wireless untuk wawancara podcast mahasiswa.'),
(5, 8, 4, 2, '2026-11-08', '2026-11-10', 440000, 'unpaid', 'Unit baru diambil pagi ini, pembayaran dijanjikan saat pengembalian.');

