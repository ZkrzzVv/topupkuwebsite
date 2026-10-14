<?php
require_once "../koneksi.php";
requireAdmin();

$id = (int) ($_GET["id"] ?? 0);
if ($id > 0) {
    $stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        flash("success", "Produk berhasil dihapus.");
    } else {
        flash("danger", "Produk gagal dihapus: " . mysqli_error($conn));
    }
    mysqli_stmt_close($stmt);
}
header("Location: produk.php");
exit;
