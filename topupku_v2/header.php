<?php
require_once __DIR__ . "/koneksi.php";
$flash = getFlash();
$pageTitle = $pageTitle ?? "TopupKu";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> - TopupKu</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="site-shell">
    <header class="navbar">
        <a href="index.php" class="brand">
            <span class="brand-mark">T</span>
            <span>TopupKu</span>
        </a>
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="produk.php">Produk</a>
            <?php if (isAdmin()): ?>
                <a href="admin/index.php" class="nav-admin">👑 Admin Panel</a>
                <a href="admin/logout.php">Logout Admin</a>
            <?php elseif (isLoggedIn()): ?>
                <a href="transaksi.php">Transaksi</a>
                <a href="topup.php">Top Up Saldo</a>
                <a href="akun.php">Akun</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php" class="nav-cta">Daftar</a>
            <?php endif; ?>
        </nav>
        <button class="theme-btn" id="themeToggle" type="button" aria-label="Dark mode">☾</button>
    </header>

    <main class="container">
        <?php if ($flash): ?>
            <div class="toast toast-<?= e($flash["type"]) ?>" data-toast>
                <?= e($flash["message"]) ?>
            </div>
        <?php endif; ?>
