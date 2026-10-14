<?php
require_once "koneksi.php";
requireLogin();

$product = cartProduct();
if (!$product) {
    flash("warning", "Keranjang masih kosong.");
    header("Location: produk.php");
    exit;
}

$userId = (int) $_SESSION["user_id"];
$stmt = mysqli_prepare($conn, "SELECT id, name, email, saldo FROM users WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$user) {
    unset($_SESSION["user_id"]);
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ((int) $user["saldo"] < (int) $product["price"]) {
        $error = "Saldo tidak cukup. Saldo kamu " . rupiah($user["saldo"]) . ".";
    } else {
        mysqli_begin_transaction($conn);
        try {
            $price = (int) $product["price"];
            $update = mysqli_prepare($conn, "UPDATE users SET saldo = saldo - ? WHERE id = ? AND saldo >= ?");
            mysqli_stmt_bind_param($update, "iii", $price, $userId, $price);
            mysqli_stmt_execute($update);

            if (mysqli_stmt_affected_rows($update) !== 1) {
                throw new Exception("Saldo berubah. Silakan coba lagi.");
            }
            mysqli_stmt_close($update);

            $target = $_SESSION["cart"]["target_number"];
            $status = "success";
            $insert = mysqli_prepare($conn, "INSERT INTO transactions (user_id, product_id, target_number, price, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
            mysqli_stmt_bind_param($insert, "iisis", $userId, $product["id"], $target, $price, $status);
            mysqli_stmt_execute($insert);
            $transactionId = mysqli_insert_id($conn);
            mysqli_stmt_close($insert);

            mysqli_commit($conn);
            unset($_SESSION["cart"]);
            header("Location: transaksi.php?success=1&id=" . $transactionId);
            exit;
        } catch (Throwable $e) {
            mysqli_rollback($conn);
            $error = "Checkout gagal: " . $e->getMessage();
        }
    }
}

$pageTitle = "Checkout";
require "header.php";
?>

<div class="checkout-layout">
    <section class="form-card">
        <span class="eyebrow">CHECKOUT</span>
        <h1>Konfirmasi pesanan</h1>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>
        <div class="summary-box">
            <div><span>Produk</span><strong><?= e($product["name"]) ?></strong></div>
            <div><span>Kategori</span><strong><?= e($product["category"]) ?></strong></div>
            <div><span>Target</span><strong><?= e($_SESSION["cart"]["target_number"]) ?></strong></div>
            <div><span>Total</span><strong class="accent-text"><?= rupiah($product["price"]) ?></strong></div>
        </div>
        <form method="post">
            <button class="btn btn-primary btn-full" type="submit">Bayar Sekarang</button>
        </form>
        <a href="keranjang.php" class="btn btn-soft btn-full">Kembali</a>
    </section>

    <aside class="balance-card">
        <span>Saldo tersedia</span>
        <strong><?= rupiah($user["saldo"]) ?></strong>
        <small>Pastikan saldo cukup sebelum pembayaran.</small>
    </aside>
</div>

<?php require "footer.php"; ?>
