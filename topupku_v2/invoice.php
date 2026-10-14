<?php
require_once "koneksi.php";
requireLogin();

$id = (int) ($_GET["id"] ?? 0);
$userId = (int) $_SESSION["user_id"];

$stmt = mysqli_prepare($conn, "SELECT t.*, p.name AS product_name, p.category, p.nominal, u.name AS user_name, u.email FROM transactions t JOIN products p ON p.id = t.product_id JOIN users u ON u.id = t.user_id WHERE t.id = ? AND t.user_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "ii", $id, $userId);
mysqli_stmt_execute($stmt);
$trx = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$trx) {
    header("Location: transaksi.php");
    exit;
}

$pageTitle = "Invoice #" . $trx["id"];
require "header.php";
?>

<div class="invoice-wrap">
    <div class="invoice-card">
        <div class="invoice-top">
            <div><span class="eyebrow">TOPUPKU</span><h1>Invoice #<?= (int) $trx["id"] ?></h1></div>
            <button class="btn btn-primary print-btn" type="button" onclick="window.print()">Print</button>
        </div>
        <div class="invoice-status status status-<?= e($trx["status"]) ?>"><?= e(ucfirst($trx["status"])) ?></div>
        <div class="invoice-grid">
            <div><span>Pelanggan</span><strong><?= e($trx["user_name"]) ?></strong></div>
            <div><span>Email</span><strong><?= e($trx["email"]) ?></strong></div>
            <div><span>Produk</span><strong><?= e($trx["product_name"]) ?></strong></div>
            <div><span>Kategori</span><strong><?= e($trx["category"]) ?></strong></div>
            <div><span>Nominal</span><strong><?= number_format((int) $trx["nominal"], 0, ",", ".") ?></strong></div>
            <div><span>Target</span><strong><?= e($trx["target_number"]) ?></strong></div>
            <div><span>Tanggal</span><strong><?= e(date("d/m/Y H:i", strtotime($trx["created_at"]))) ?></strong></div>
            <div><span>Total</span><strong class="accent-text"><?= rupiah($trx["price"]) ?></strong></div>
        </div>
        <div class="invoice-note">Simpan invoice ini sebagai bukti transaksi TopupKu.</div>
    </div>
</div>

<?php require "footer.php"; ?>
