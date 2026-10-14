<?php
require_once "../koneksi.php";
requireAdmin();

$search = trim($_GET["search"] ?? "");
if ($search !== "") {
    $like = "%{$search}%";
    $stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE name LIKE ? OR category LIKE ? OR CAST(nominal AS CHAR) LIKE ? ORDER BY id DESC");
    mysqli_stmt_bind_param($stmt, "sss", $like, $like, $like);
    mysqli_stmt_execute($stmt);
    $products = mysqli_stmt_get_result($stmt);
} else {
    $products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
}
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Produk Admin</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body><div class="admin-shell"><aside class="admin-sidebar"><a href="index.php" class="brand"><span class="brand-mark">T</span><span>TopupKu</span></a><nav><a href="index.php">Dashboard</a><a class="active" href="produk.php">Produk</a><a href="users.php">User</a><a href="saldo.php">Saldo</a><a href="transaksi.php">Transaksi</a><a href="../index.php">Website</a><a href="logout.php">Logout</a></nav></aside><main class="admin-main">
<div class="admin-top"><div><span class="eyebrow">CATALOG</span><h1>Produk</h1></div><a href="tambah.php" class="btn btn-primary">+ Tambah Produk</a></div>
<form class="search-panel admin-search" method="get"><input type="text" name="search" placeholder="Cari produk..." value="<?= e($search) ?>"><button class="btn btn-primary" type="submit">Cari</button></form>
<div class="table-wrap"><table><thead><tr><th>ID</th><th>Nama</th><th>Kategori</th><th>Nominal</th><th>Harga</th><th>Aksi</th></tr></thead><tbody>
<?php while ($p = mysqli_fetch_assoc($products)): ?><tr><td>#<?= (int) $p["id"] ?></td><td><strong><?= e($p["name"]) ?></strong></td><td><?= e($p["category"]) ?></td><td><?= number_format((int) $p["nominal"], 0, ",", ".") ?></td><td><?= rupiah($p["price"]) ?></td><td class="inline-actions"><a class="btn btn-small btn-soft" href="edit.php?id=<?= (int) $p["id"] ?>">Edit</a><a class="btn btn-small btn-danger" href="hapus.php?id=<?= (int) $p["id"] ?>" data-confirm="Hapus produk ini? Jika ada transaksi, transaksi terkait juga dapat terhapus karena foreign key CASCADE.">Hapus</a></td></tr><?php endwhile; ?>
</tbody></table></div>
</main></div><script src="../assets/js/app.js"></script></body></html>
