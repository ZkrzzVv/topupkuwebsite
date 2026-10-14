<?php
require_once "koneksi.php";

if (isAdmin()) {
    header("Location: admin/index.php");
    exit;
}

if (isLoggedIn()) {
    header("Location: akun.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $identity = trim($_POST["identity"] ?? "");
    $password = $_POST["password"] ?? "";

    // Login admin terpisah dari tabel users.
    if ($identity === "admin" && hash_equals("admin123", $password)) {
        session_regenerate_id(true);
        $_SESSION["is_admin"] = true;
        $_SESSION["admin_username"] = "admin";
        header("Location: admin/index.php");
        exit;
    }

    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, name, email, password FROM users WHERE email = ? LIMIT 1"
    );
    mysqli_stmt_bind_param($stmt, "s", $identity);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if ($user && password_verify($password, $user["password"])) {
        session_regenerate_id(true);
        $_SESSION["user_id"] = (int) $user["id"];
        $_SESSION["user_name"] = $user["name"];
        header("Location: akun.php");
        exit;
    }

    $error = "Username/email atau password salah.";
}

$pageTitle = "Login";
require "header.php";
?>

<div class="auth-wrap">
    <section class="auth-card">
        <span class="eyebrow">TOPUPKU ACCESS</span>
        <h1>Masuk ke TopupKu</h1>
        <p>Login sebagai user atau admin dari satu halaman.</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <label>Email / Username</label>
            <input
                type="text"
                name="identity"
                required
                autocomplete="username"
                placeholder="email@contoh.com"
            >

            <label>Password</label>
            <input
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
            >

            <button class="btn btn-primary btn-full" type="submit">
                Login
            </button>
        </form>

        <p class="auth-foot">
            Belum punya akun?
            <a href="register.php">Daftar sekarang</a>
        </p>
    </section>
</div>

<?php require "footer.php"; ?>
