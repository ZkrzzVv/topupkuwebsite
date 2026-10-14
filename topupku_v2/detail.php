<?php
require_once "koneksi.php";

$id = (int) ($_GET["id"] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$product) {
    header("Location: produk.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    requireLogin();
    $target = trim($_POST["target_number"] ?? "");

    if ($target === "") {
        $error = "Target / nomor wajib diisi.";
    } elseif (strlen($target) > 20) {
        $error = "Target maksimal 20 karakter.";
    } else {
        $_SESSION["cart"] = [
            "product_id" => (int) $product["id"],
            "target_number" => $target
        ];
        header("Location: checkout.php");
        exit;
    }
}

$pageTitle = "Detail Produk";
require "header.php";
?>

<div class="detail-layout">
    <section class="detail-card">
        <div class="detail-icon">⚡</div>
        <span class="pill"><?= e($product["category"]) ?></span>
        <h1><?= e($product["name"]) ?></h1>
        <p class="muted">Nominal <?= number_format((int) $product["nominal"], 0, ",", ".") ?></p>
        <div class="detail-price"><?= rupiah($product["price"]) ?></div>
        <div class="info-list">
            <div><span>Produk</span><strong><?= e($product["name"]) ?></strong></div>
            <div><span>Kategori</span><strong><?= e($product["category"]) ?></strong></div>
            <div><span>Nominal</span><strong><?= number_format((int) $product["nominal"], 0, ",", ".") ?></strong></div>
        </div>
    </section>

    <section class="form-card">
        <span class="eyebrow">QUICK BUY</span>
        <h2>Masukkan target</h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>
        <?php if (!isLoggedIn()): ?>
            <div class="alert">Login diperlukan sebelum checkout.</div>
        <?php endif; ?>
        <form method="post">
            <label>Nomor / ID tujuan</label>
            <input type="text" name="target_number" placeholder="Contoh: 081234567890" maxlength="20" required>
            <button class="btn btn-primary btn-full" type="submit">Lanjut Checkout →</button>
        </form>
        <?php if (!isLoggedIn()): ?>
            <a href="login.php" class="btn btn-soft btn-full">Login untuk membeli</a>
        <?php endif; ?>
    </section>
</div>

<?php require "footer.php"; ?>
