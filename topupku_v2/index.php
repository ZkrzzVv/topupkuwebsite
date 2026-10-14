<?php
$pageTitle = "Home";
require_once "koneksi.php";

$search = trim($_GET["search"] ?? "");
$category = trim($_GET["category"] ?? "");
$sort = $_GET["sort"] ?? "newest";

$categories = [];
$catResult = mysqli_query($conn, "SELECT DISTINCT category FROM products WHERE category <> '' ORDER BY category ASC");
while ($cat = mysqli_fetch_assoc($catResult)) {
    $categories[] = $cat["category"];
}

$where = [];
$params = [];
$types = "";

if ($search !== "") {
    $where[] = "(name LIKE ? OR category LIKE ? OR CAST(nominal AS CHAR) LIKE ? )";
    $like = "%{$search}%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= "sss";
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
$sql .= " ORDER BY {$order} LIMIT 12";

$stmt = mysqli_prepare($conn, $sql);
if ($params) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$products = mysqli_stmt_get_result($stmt);

$latest = mysqli_query($conn, "SELECT * FROM products ORDER BY created_at DESC, id DESC LIMIT 6");
$best = mysqli_query($conn, "SELECT p.*, COUNT(t.id) AS total_sold FROM products p LEFT JOIN transactions t ON t.product_id = p.id AND t.status = 'success' GROUP BY p.id ORDER BY total_sold DESC, p.id DESC LIMIT 6");

require "header.php";
?>

<section class="hero">
    <div class="hero-copy">
        <span class="eyebrow">TOPUP CEPAT • AMAN • PRAKTIS</span>
        <h1>Semua kebutuhan top up, <span>di satu tempat.</span></h1>
        <p>Cari produk, pilih nominal, masukkan target, lalu checkout dengan saldo akunmu.</p>
        <div class="hero-actions">
            <a href="produk.php" class="btn btn-primary">Belanja Sekarang</a>
            <?php if (isLoggedIn()): ?>
                <a href="akun.php" class="btn btn-soft">Dashboard</a>
            <?php else: ?>
                <a href="register.php" class="btn btn-soft">Buat Akun</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="hero-card">
        <div class="hero-orb">⚡</div>
        <span>TOPUPKU</span>
        <strong>Fast checkout.</strong>
        <small>Saldo langsung terpotong setelah transaksi berhasil.</small>
    </div>
</section>

<section class="section-head">
    <div>
        <span class="eyebrow">TEMUKAN PRODUK</span>
        <h2>Belanja lebih cepat</h2>
    </div>
</section>

<form class="search-panel" method="get">
    <input type="text" name="search" placeholder="Cari produk, kategori, nominal..." value="<?= e($search) ?>">
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
    <button class="btn btn-primary" type="submit">Cari</button>
</form>

<section class="category-section">
    <div class="section-head compact">
        <div>
            <span class="eyebrow">PILIH SESUAI KEBUTUHAN</span>
            <h2>Kategori</h2>
        </div>
    </div>
    <div class="category-grid">
        <?php
        $categoryIcons = [
            "Games" => "🎮",
            "Pulsa" => "📱",
            "Pulsa & Data" => "📶",
            "Data" => "📶",
            "E-Wallet" => "💳",
            "Digital" => "🎵",
            "Voucher" => "⚡"
        ];
        foreach ($categories as $cat):
            $icon = $categoryIcons[$cat] ?? "✨";
        ?>
            <a class="category-card" href="produk.php?category=<?= urlencode($cat) ?>">
                <span class="category-icon"><?= $icon ?></span>
                <span>
                    <strong><?= e($cat) ?></strong>
                    <small>Lihat produk →</small>
                </span>
            </a>
        <?php endforeach; ?>
        <?php if (!$categories): ?>
            <a class="category-card" href="produk.php">
                <span class="category-icon">🛍️</span>
                <span>
                    <strong>Semua Produk</strong>
                    <small>Lihat katalog →</small>
                </span>
            </a>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="section-head compact">
        <div>
            <span class="eyebrow">PILIHANMU</span>
            <h2>Produk tersedia</h2>
        </div>
        <a href="produk.php" class="text-link">Lihat semua →</a>
    </div>
    <div class="product-grid">
        <?php while ($product = mysqli_fetch_assoc($products)): ?>
            <article class="product-card">
                <div class="product-icon">⚡</div>
                <div class="product-info">
                    <span class="pill"><?= e($product["category"]) ?></span>
                    <h3><?= e($product["name"]) ?></h3>
                    <p><?= number_format((int) $product["nominal"], 0, ",", ".") ?> • <?= rupiah($product["price"]) ?></p>
                </div>
                <div class="product-bottom">
                    <strong><?= rupiah($product["price"]) ?></strong>
                    <a class="btn btn-small btn-primary" href="detail.php?id=<?= (int) $product["id"] ?>">Beli</a>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
    <?php if (mysqli_num_rows($products) === 0): ?>
        <div class="empty">Produk tidak ditemukan.</div>
    <?php endif; ?>
</section>

<section class="section">
    <div class="section-head compact">
        <div>
            <span class="eyebrow">UPDATE</span>
            <h2>Produk terbaru</h2>
        </div>
    </div>
    <div class="product-grid">
        <?php while ($product = mysqli_fetch_assoc($latest)): ?>
            <article class="product-card">
                <div class="product-icon">✦</div>
                <span class="pill"><?= e($product["category"]) ?></span>
                <h3><?= e($product["name"]) ?></h3>
                <p><?= number_format((int) $product["nominal"], 0, ",", ".") ?></p>
                <div class="product-bottom">
                    <strong><?= rupiah($product["price"]) ?></strong>
                    <a class="text-link" href="detail.php?id=<?= (int) $product["id"] ?>">Detail →</a>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
</section>

<section class="section">
    <div class="section-head compact">
        <div>
            <span class="eyebrow">PALING DIBELI</span>
            <h2>Produk terlaris</h2>
        </div>
    </div>
    <div class="product-grid">
        <?php while ($product = mysqli_fetch_assoc($best)): ?>
            <article class="product-card">
                <div class="product-icon">★</div>
                <span class="pill"><?= e($product["category"]) ?></span>
                <h3><?= e($product["name"]) ?></h3>
                <p><?= (int) $product["total_sold"] ?> transaksi berhasil</p>
                <div class="product-bottom">
                    <strong><?= rupiah($product["price"]) ?></strong>
                    <a class="text-link" href="detail.php?id=<?= (int) $product["id"] ?>">Beli →</a>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
</section>

<?php require "footer.php"; ?>
