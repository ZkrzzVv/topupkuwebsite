<?php
require_once "../koneksi.php";
requireAdmin();

$productTotal = productCount();
$userTotal = userCount();
$transactionTotal = transactionCount();
$successResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM transactions WHERE status = 'success'");
$successTotal = (int) mysqli_fetch_assoc($successResult)["total"];
$balanceResult = mysqli_query($conn, "SELECT COALESCE(SUM(saldo), 0) AS total FROM users");
$totalBalance = (int) mysqli_fetch_assoc($balanceResult)["total"];
$recent = mysqli_query($conn, "SELECT t.*, u.name AS user_name, p.name AS product_name FROM transactions t JOIN users u ON u.id = t.user_id JOIN products p ON p.id = t.product_id ORDER BY t.created_at DESC, t.id DESC LIMIT 8");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TopupKu</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="admin-shell">
    <aside class="admin-sidebar">
        <a href="index.php" class="brand"><span class="brand-mark">T</span><span>TopupKu</span></a>
        <div class="admin-profile"><span>👑</span><div><strong>Admin</strong><small><?= e($_SESSION["admin_username"] ?? "admin") ?></small></div></div>
        <nav>
            <a class="active" href="index.php">Dashboard</a>
            <a href="produk.php">Produk</a>
            <a href="users.php">User</a>
            <a href="saldo.php">Saldo</a>
            <a href="transaksi.php">Transaksi</a>
            <a href="../index.php">Website</a>
            <a href="logout.php">Logout</a>
        </nav>
    </aside>
    <main class="admin-main">
        <div class="admin-top"><div><span class="eyebrow">ADMIN PANEL</span><h1>Dashboard</h1></div><a href="tambah.php" class="btn btn-primary">+ Tambah Produk</a></div>
        <div class="stats-grid admin-stats">
            <div class="stat-card"><span>Produk</span><strong><?= $productTotal ?></strong><small>Total katalog</small></div>
            <div class="stat-card"><span>User</span><strong><?= $userTotal ?></strong><small>Akun terdaftar</small></div>
            <div class="stat-card"><span>Transaksi</span><strong><?= $transactionTotal ?></strong><small>Semua transaksi</small></div>
            <div class="stat-card"><span>Success</span><strong><?= $successTotal ?></strong><small>Transaksi berhasil</small></div>
            <div class="stat-card"><span>Total saldo user</span><strong><?= rupiah($totalBalance) ?></strong><small>Saldo tersimpan</small></div>
        </div>
        <section class="panel">
            <div class="panel-head"><h2>Transaksi terbaru</h2><a href="transaksi.php">Kelola →</a></div>
            <div class="table-wrap">
                <table><thead><tr><th>ID</th><th>User</th><th>Produk</th><th>Harga</th><th>Status</th></tr></thead><tbody>
                <?php while ($trx = mysqli_fetch_assoc($recent)): ?>
                    <tr><td>#<?= (int) $trx["id"] ?></td><td><?= e($trx["user_name"]) ?></td><td><?= e($trx["product_name"]) ?></td><td><?= rupiah($trx["price"]) ?></td><td><span class="status status-<?= e($trx["status"]) ?>"><?= e(ucfirst($trx["status"])) ?></span></td></tr>
                <?php endwhile; ?>
                </tbody></table>
            </div>
        </section>
    </main>
</div>
<script src="../assets/js/app.js"></script>
</body>
</html>
