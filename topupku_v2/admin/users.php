<?php
require_once "../koneksi.php";
requireAdmin();

$search = trim($_GET["search"] ?? "");
if ($search !== "") {
    $like = "%{$search}%";
    $stmt = mysqli_prepare($conn, "SELECT id, name, email, saldo, created_at FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY id DESC");
    mysqli_stmt_bind_param($stmt, "ss", $like, $like);
    mysqli_stmt_execute($stmt);
    $users = mysqli_stmt_get_result($stmt);
} else {
    $users = mysqli_query($conn, "SELECT id, name, email, saldo, created_at FROM users ORDER BY id DESC");
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>User Admin</title><link rel="stylesheet" href="../assets/css/style.css"></head><body><div class="admin-shell"><aside class="admin-sidebar"><a href="index.php" class="brand"><span class="brand-mark">T</span><span>TopupKu</span></a><nav><a href="index.php">Dashboard</a><a href="produk.php">Produk</a><a class="active" href="users.php">User</a><a href="saldo.php">Saldo</a><a href="transaksi.php">Transaksi</a><a href="logout.php">Logout</a></nav></aside><main class="admin-main"><div class="admin-top"><div><span class="eyebrow">CUSTOMERS</span><h1>User</h1></div><a href="saldo.php" class="btn btn-primary">Kelola Saldo</a></div><form class="search-panel admin-search" method="get"><input type="text" name="search" placeholder="Cari nama/email..." value="<?= e($search) ?>"><button class="btn btn-primary" type="submit">Cari</button></form><div class="table-wrap"><table><thead><tr><th>ID</th><th>Nama</th><th>Email</th><th>Saldo</th><th>Daftar</th></tr></thead><tbody><?php while ($u = mysqli_fetch_assoc($users)): ?><tr><td>#<?= (int) $u["id"] ?></td><td><?= e($u["name"]) ?></td><td><?= e($u["email"]) ?></td><td><strong><?= rupiah($u["saldo"]) ?></strong></td><td><?= e(date("d/m/Y", strtotime($u["created_at"]))) ?></td></tr><?php endwhile; ?></tbody></table></div></main></div></body></html>
