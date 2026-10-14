<?php
require_once "../koneksi.php";
requireAdmin();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $nominal = (int) ($_POST["nominal"] ?? 0);
    $price = (int) ($_POST["price"] ?? 0);

    if ($name === "" || $category === "" || $nominal <= 0 || $price <= 0) {
        $error = "Semua data wajib diisi dengan benar.";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO products (name, category, nominal, price, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
        mysqli_stmt_bind_param($stmt, "ssii", $name, $category, $nominal, $price);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        flash("success", "Produk berhasil ditambahkan.");
        header("Location: produk.php");
        exit;
    }
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Tambah Produk</title><link rel="stylesheet" href="../assets/css/style.css"></head><body><div class="admin-shell"><aside class="admin-sidebar"><a href="index.php" class="brand"><span class="brand-mark">T</span><span>TopupKu</span></a><nav><a href="index.php">Dashboard</a><a class="active" href="produk.php">Produk</a><a href="users.php">User</a><a href="saldo.php">Saldo</a><a href="transaksi.php">Transaksi</a><a href="logout.php">Logout</a></nav></aside><main class="admin-main"><div class="admin-top"><div><span class="eyebrow">PRODUCT</span><h1>Tambah Produk</h1></div><a href="produk.php" class="btn btn-soft">← Kembali</a></div><section class="form-card admin-form"><?php if (!empty($error)): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?><form method="post"><label>Nama Produk</label><input type="text" name="name" required><label>Kategori</label><input type="text" name="category" placeholder="Pulsa / Game / E-Wallet" required><label>Nominal</label><input type="number" name="nominal" min="1" required><label>Harga Jual</label><input type="number" name="price" min="1" required><button class="btn btn-primary btn-full" type="submit">Simpan Produk</button></form></section></main></div></body></html>
