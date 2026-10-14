<?php
$pageTitle = "Produk";
require_once "koneksi.php";

$search = trim($_GET["search"] ?? "");
$category = trim($_GET["category"] ?? "");
$sort = $_GET["sort"] ?? "newest";

$categories = [];
$result = mysqli_query($conn, "SELECT DISTINCT category FROM products WHERE category <> '' ORDER BY category ASC");
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row["category"];
}

$where = [];
$params = [];
$types = "";

if ($search !== "") {
    $where[] = "(name LIKE ? OR category LIKE ? OR CAST(nominal AS CHAR) LIKE ?)";
    $like = "%{$search}%";
    $params = [$like, $like, $like];
    $types = "sss";
}

if ($category !== "") {
    $where[] = "category = ?";
    $params[] = $category;
    $types .= "s";
}

$order = "created_at DESC, id DESC";
if ($sort === "price_low") {
    $order = "price ASC, id DESC";
} elseif ($sort === "price_high") {
    $order = "price DESC, id DESC";
} elseif ($sort === "name") {
    $order = "name ASC, id DESC";
}

$sql = "SELECT * FROM products";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY {$order}";

$stmt = mysqli_prepare($conn, $sql);
if ($params) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$products = mysqli_stmt_get_result($stmt);

require "header.php";
?>

<section class="page-heading">
    <span class="eyebrow">CATALOG</span>
    <h1>Produk Topup</h1>
    <p>Cari produk yang kamu butuhkan dan langsung quick buy.</p>
</section>

<form class="search-panel" method="get">
    <input type="text" name="search" placeholder="Cari produk..." value="<?= e($search) ?>">
    <select name="category">
        <option value="">Semua kategori</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= e($cat) ?>" <?= $category === $cat ? "selected" : "" ?>><?= e($cat) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="sort">
        <option value="newest" <?= $sort === "newest" ? "selected" : "" ?>>Terbaru</option>
        <option value="price_low" <?= $sort === "price_low" ? "selected" : "" ?>>Harga terendah</option>
        <option value="price_high" <?= $sort === "price_high" ? "selected" : "" ?>>Harga tertinggi</option>
        <option value="name" <?= $sort === "name" ? "selected" : "" ?>>Nama A-Z</option>
    </select>
    <button class="btn btn-primary" type="submit">Terapkan</button>
</form>

<div class="product-grid product-grid-large">
    <?php while ($product = mysqli_fetch_assoc($products)): ?>
        <article class="product-card">
            <div class="product-icon">⚡</div>
            <span class="pill"><?= e($product["category"]) ?></span>
            <h3><?= e($product["name"]) ?></h3>
            <p>Nominal: <?= number_format((int) $product["nominal"], 0, ",", ".") ?></p>
            <div class="product-bottom">
                <strong><?= rupiah($product["price"]) ?></strong>
                <div class="inline-actions">
                    <a class="btn btn-small btn-soft" href="detail.php?id=<?= (int) $product["id"] ?>">Detail</a>
                    <a class="btn btn-small btn-primary" href="detail.php?id=<?= (int) $product["id"] ?>&quick=1">Quick Buy</a>
                </div>
            </div>
        </article>
    <?php endwhile; ?>
</div>

<?php if (mysqli_num_rows($products) === 0): ?>
    <div class="empty">Belum ada produk yang cocok.</div>
<?php endif; ?>

<?php require "footer.php"; ?>
