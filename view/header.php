<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LensGear Hub - Sistem Rental Multimedia</title>
    <!-- Berkas stylesheet utama antarmuka web -->
    <link rel="stylesheet" href="style.css">
    <!-- Google Fonts Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navbar Navigasi Utama Aplikasi -->
    <header class="navbar">
        <div class="nav-container">
            <!-- Brand Logo -->
            <a href="index.php" class="brand-logo">
                <span class="logo-text">LensGear <strong>Hub</strong></span>
            </a>
            <!-- Tautan Navigasi (Aktif berdasarkan parameter $page) -->
            <nav class="nav-links">
                <a href="index.php?page=rentals" class="nav-item <?= ($page == 'rentals') ? 'active' : '' ?>">Rental</a>
                <a href="index.php?page=equipments" class="nav-item <?= ($page == 'equipments') ? 'active' : '' ?>">Alat & Kamera</a>
                <a href="index.php?page=categories" class="nav-item <?= ($page == 'categories') ? 'active' : '' ?>">Kategori</a>
                <a href="index.php?page=brands" class="nav-item <?= ($page == 'brands') ? 'active' : '' ?>">Merek</a>
                <a href="index.php?page=customers" class="nav-item <?= ($page == 'customers') ? 'active' : '' ?>">Pelanggan</a>
                <a href="index.php?page=staff" class="nav-item <?= ($page == 'staff') ? 'active' : '' ?>">Staf</a>
            </nav>
        </div>
    </header>

    <!-- Kontainer Konten Utama -->
    <main class="main-content">
        <div class="container">
            <!-- Menampilkan pesan notifikasi (flash message) dari sesi jika tersedia -->
            <?php if (isset($_SESSION['msg'])): ?>
                <div class="alert alert-<?= htmlspecialchars($_SESSION['msg_type'] ?? 'info') ?>">
                    <span><?= htmlspecialchars($_SESSION['msg']) ?></span>
                    <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
                </div>
                <?php 
                    // Menghapus notifikasi dari sesi setelah ditampilkan agar tidak muncul berulang
                    unset($_SESSION['msg']); 
                    unset($_SESSION['msg_type']); 
                ?>
            <?php endif; ?>
