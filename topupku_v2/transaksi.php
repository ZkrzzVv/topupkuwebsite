<?php
require_once "koneksi.php";
requireLogin();

$userId = (int) $_SESSION["user_id"];
$statusFilter = $_GET["status"] ?? "";
$allowed = ["pending", "success", "failed"];

$sql = "SELECT t.*, p.name AS product_name, p.category FROM transactions t JOIN products p ON p.id = t.product_id WHERE t.user_id = ?";
$params = [$userId];
$types = "i";
if (in_array($statusFilter, $allowed, true)) {
    $sql .= " AND t.status = ?";
    $params[] = $statusFilter;
    $types .= "s";
}
$sql .= " ORDER BY t.created_at DESC, t.id DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$transactions = mysqli_stmt_get_result($stmt);

$pageTitle = "Transaksi";
require "header.php";
?>

<section class="page-heading">
    <span class="eyebrow">RIWAYAT</span>
    <h1>Transaksi</h1>
    <p>Semua aktivitas top up akun kamu.</p>
</section>

<?php if (isset($_GET["success"])): ?>
    <div class="alert alert-success">Transaksi berhasil dibuat. ID #<?= (int) ($_GET["id"] ?? 0) ?>.</div>
<?php endif; ?>

<div class="filter-tabs">
    <a class="<?= $statusFilter === "" ? "active" : "" ?>" href="transaksi.php">Semua</a>
    <a class="<?= $statusFilter === "success" ? "active" : "" ?>" href="transaksi.php?status=success">Success</a>
    <a class="<?= $statusFilter === "pending" ? "active" : "" ?>" href="transaksi.php?status=pending">Pending</a>
    <a class="<?= $statusFilter === "failed" ? "active" : "" ?>" href="transaksi.php?status=failed">Failed</a>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr><th>ID</th><th>Produk</th><th>Target</th><th>Harga</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        <?php while ($trx = mysqli_fetch_assoc($transactions)): ?>
            <tr>
                <td>#<?= (int) $trx["id"] ?></td>
                <td><strong><?= e($trx["product_name"]) ?></strong><small><?= e($trx["category"]) ?></small></td>
                <td><?= e($trx["target_number"]) ?></td>
                <td><?= rupiah($trx["price"]) ?></td>
                <td><span class="status status-<?= e($trx["status"]) ?>"><?= e(ucfirst($trx["status"])) ?></span></td>
                <td><?= e(date("d/m/Y H:i", strtotime($trx["created_at"]))) ?></td>
                <td><a class="btn btn-small btn-soft" href="invoice.php?id=<?= (int) $trx["id"] ?>">Invoice</a></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php if (mysqli_num_rows($transactions) === 0): ?>
    <div class="empty">Belum ada transaksi pada filter ini.</div>
<?php endif; ?>

<?php require "footer.php"; ?>
