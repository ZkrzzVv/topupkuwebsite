<?php
require_once "koneksi.php";
requireLogin();

$product = cartProduct();

if (isset($_GET["clear"])) {
    unset($_SESSION["cart"]);
    flash("success", "Keranjang dikosongkan.");
    header("Location: keranjang.php");
    exit;
}

$pageTitle = "Keranjang";
require "header.php";
?>

<section class="page-heading">
    <span class="eyebrow">ORDER</span>
    <h1>Keranjang</h1>
    <p>Periksa produk dan target sebelum checkout.</p>
</section>

<?php if ($product): ?>
    <div class="cart-card">
        <div class="product-icon">⚡</div>
        <div>
            <span class="pill"><?= e($product["category"]) ?></span>
            <h2><?= e($product["name"]) ?></h2>
            <p>Target: <strong><?= e($_SESSION["cart"]["target_number"]) ?></strong></p>
        </div>
        <strong class="cart-price"><?= rupiah($product["price"]) ?></strong>
        <div class="inline-actions">
            <a href="checkout.php" class="btn btn-primary">Checkout</a>
            <a href="keranjang.php?clear=1" class="btn btn-danger" data-confirm="Kosongkan keranjang?">Hapus</a>
        </div>
    </div>
<?php else: ?>
    <div class="empty">
        <div class="empty-icon">🛒</div>
        <h2>Keranjang kosong</h2>
        <p>Pilih produk dulu untuk mulai transaksi.</p>
        <a href="produk.php" class="btn btn-primary">Lihat Produk</a>
    </div>
<?php endif; ?>

<?php require "footer.php"; ?>
