<?php
require_once "../koneksi.php";
requireAdmin();

$status = $_GET["status"] ?? "";
$allowed = ["pending", "success", "failed"];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int) ($_POST["id"] ?? 0);
    $newStatus = $_POST["status"] ?? "pending";
    if ($id > 0 && in_array($newStatus, $allowed, true)) {
        $stmt = mysqli_prepare($conn, "UPDATE transactions SET status = ?, updated_at = NOW() WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $newStatus, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        flash("success", "Status transaksi diperbarui.");
    }
    header("Location: transaksi.php");
    exit;
}

if (in_array($status, $allowed, true)) {
    $stmt = mysqli_prepare($conn, "SELECT t.*, u.name AS user_name, p.name AS product_name FROM transactions t JOIN users u ON u.id = t.user_id JOIN products p ON p.id = t.product_id WHERE t.status = ? ORDER BY t.created_at DESC, t.id DESC");
    mysqli_stmt_bind_param($stmt, "s", $status);
    mysqli_stmt_execute($stmt);
    $transactions = mysqli_stmt_get_result($stmt);
} else {
    $transactions = mysqli_query($conn, "SELECT t.*, u.name AS user_name, p.name AS product_name FROM transactions t JOIN users u ON u.id = t.user_id JOIN products p ON p.id = t.product_id ORDER BY t.created_at DESC, t.id DESC");
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Transaksi Admin</title><link rel="stylesheet" href="../assets/css/style.css"></head><body><div class="admin-shell"><aside class="admin-sidebar"><a href="index.php" class="brand"><span class="brand-mark">T</span><span>TopupKu</span></a><nav><a href="index.php">Dashboard</a><a href="produk.php">Produk</a><a href="users.php">User</a><a href="saldo.php">Saldo</a><a class="active" href="transaksi.php">Transaksi</a><a href="logout.php">Logout</a></nav></aside><main class="admin-main"><div class="admin-top"><div><span class="eyebrow">ORDERS</span><h1>Transaksi</h1></div></div><div class="filter-tabs"><a class="<?= $status === "" ? "active" : "" ?>" href="transaksi.php">Semua</a><a class="<?= $status === "success" ? "active" : "" ?>" href="transaksi.php?status=success">Success</a><a class="<?= $status === "pending" ? "active" : "" ?>" href="transaksi.php?status=pending">Pending</a><a class="<?= $status === "failed" ? "active" : "" ?>" href="transaksi.php?status=failed">Failed</a></div><div class="table-wrap"><table><thead><tr><th>ID</th><th>User</th><th>Produk</th><th>Target</th><th>Harga</th><th>Status</th><th>Update</th></tr></thead><tbody><?php while ($t = mysqli_fetch_assoc($transactions)): ?><tr><td>#<?= (int) $t["id"] ?></td><td><?= e($t["user_name"]) ?></td><td><?= e($t["product_name"]) ?></td><td><?= e($t["target_number"]) ?></td><td><?= rupiah($t["price"]) ?></td><td><span class="status status-<?= e($t["status"]) ?>"><?= e(ucfirst($t["status"])) ?></span></td><td><form class="status-form" method="post"><input type="hidden" name="id" value="<?= (int) $t["id"] ?>"><select name="status"><option value="pending" <?= $t["status"] === "pending" ? "selected" : "" ?>>Pending</option><option value="success" <?= $t["status"] === "success" ? "selected" : "" ?>>Success</option><option value="failed" <?= $t["status"] === "failed" ? "selected" : "" ?>>Failed</option></select><button class="btn btn-small btn-soft" type="submit">Simpan</button></form></td></tr><?php endwhile; ?></tbody></table></div></main></div><script src="../assets/js/app.js"></script></body></html>
