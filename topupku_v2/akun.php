<?php
require_once "koneksi.php";
requireLogin();

$userId = (int) $_SESSION["user_id"];
$stmt = mysqli_prepare($conn, "SELECT id, name, email, saldo, created_at FROM users WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

$countStmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM transactions WHERE user_id = ?");
mysqli_stmt_bind_param($countStmt, "i", $userId);
mysqli_stmt_execute($countStmt);
$totalTransactions = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($countStmt))["total"];
mysqli_stmt_close($countStmt);

$lastStmt = mysqli_prepare($conn, "SELECT t.*, p.name AS product_name, p.category FROM transactions t JOIN products p ON p.id = t.product_id WHERE t.user_id = ? ORDER BY t.created_at DESC, t.id DESC LIMIT 5");
mysqli_stmt_bind_param($lastStmt, "i", $userId);
mysqli_stmt_execute($lastStmt);
$lastTransactions = mysqli_stmt_get_result($lastStmt);

$pageTitle = "Dashboard";
require "header.php";
?>

<section class="dashboard-top">
    <div>
        <span class="eyebrow">DASHBOARD USER</span>
        <h1>Halo, <?= e($user["name"]) ?> 👋</h1>
        <p>Kelola saldo dan lihat aktivitas top up kamu.</p>
    </div>
    <a href="topup.php" class="btn btn-primary">💳 Top Up Saldo</a>
            <a href="produk.php" class="btn btn-primary">+ Top Up Sekarang</a>
</section>

<div class="stats-grid">
    <div class="stat-card"><span>Saldo</span><strong><?= rupiah($user["saldo"]) ?></strong><small>Saldo akun</small></div>
    <div class="stat-card"><span>Total transaksi</span><strong><?= $totalTransactions ?></strong><small>Semua transaksi</small></div>
    <div class="stat-card"><span>Email</span><strong class="small-stat"><?= e($user["email"]) ?></strong><small>Akun aktif</small></div>
</div>

<section class="dashboard-grid">
    <div class="panel">
        <div class="panel-head"><h2>Transaksi terakhir</h2><a href="transaksi.php">Lihat semua →</a></div>
        <?php if (mysqli_num_rows($lastTransactions) > 0): ?>
            <div class="transaction-list">
                <?php while ($trx = mysqli_fetch_assoc($lastTransactions)): ?>
                    <div class="transaction-row">
                        <div><strong><?= e($trx["product_name"]) ?></strong><span><?= e($trx["target_number"]) ?></span></div>
                        <div><strong><?= rupiah($trx["price"]) ?></strong><span class="status status-<?= e($trx["status"]) ?>"><?= e(ucfirst($trx["status"])) ?></span></div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty small-empty">Belum ada transaksi.</div>
        <?php endif; ?>
    </div>
    <aside class="panel quick-panel">
        <h2>Quick action</h2>
        <a href="produk.php" class="quick-action">🛍️ <span>Belanja produk</span> →</a>
        <a href="transaksi.php" class="quick-action">🧾 <span>Cek transaksi</span> →</a>
        <a href="keranjang.php" class="quick-action">🛒 <span>Lihat keranjang</span> →</a>
        <a href="logout.php" class="quick-action danger-text">↪ <span>Logout</span> →</a>
    </aside>
</section>

<?php require "footer.php"; ?>
