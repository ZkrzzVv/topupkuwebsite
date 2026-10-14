<?php
require_once "../koneksi.php";
requireAdmin();

$id = (int) ($_GET["id"] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);
if (!$product) { header("Location: produk.php"); exit; }

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $nominal = (int) ($_POST["nominal"] ?? 0);
    $price = (int) ($_POST["price"] ?? 0);
    if ($name === "" || $category === "" || $nominal <= 0 || $price <= 0) {
        $error = "Semua data wajib diisi dengan benar.";
    } else {
        $update = mysqli_prepare($conn, "UPDATE products SET name = ?, category = ?, nominal = ?, price = ?, updated_at = NOW() WHERE id = ?");
        mysqli_stmt_bind_param($update, "ssiii", $name, $category, $nominal, $price, $id);
        mysqli_stmt_execute($update);
        mysqli_stmt_close($update);
        flash("success", "Produk berhasil diperbarui.");
        header("Location: produk.php");
        exit;
    }
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Edit Produk</title><link rel="stylesheet" href="../assets/css/style.css"></head><body><div class="admin-shell"><aside class="admin-sidebar"><a href="index.php" class="brand"><span class="brand-mark">T</span><span>TopupKu</span></a><nav><a href="index.php">Dashboard</a><a class="active" href="produk.php">Produk</a><a href="users.php">User</a><a href="saldo.php">Saldo</a><a href="transaksi.php">Transaksi</a><a href="logout.php">Logout</a></nav></aside><main class="admin-main"><div class="admin-top"><div><span class="eyebrow">PRODUCT</span><h1>Edit Produk</h1></div><a href="produk.php" class="btn btn-soft">← Kembali</a></div><section class="form-card admin-form"><?php if (!empty($error)): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?><form method="post"><label>Nama Produk</label><input type="text" name="name" value="<?= e($product["name"]) ?>" required><label>Kategori</label><input type="text" name="category" value="<?= e($product["category"]) ?>" required><label>Nominal</label><input type="number" name="nominal" value="<?= (int) $product["nominal"] ?>" min="1" required><label>Harga Jual</label><input type="number" name="price" value="<?= (int) $product["price"] ?>" min="1" required><button class="btn btn-primary btn-full" type="submit">Simpan Perubahan</button></form></section></main></div></body></html>
