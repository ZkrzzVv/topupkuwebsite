<?php
require_once "koneksi.php";

if (isLoggedIn()) {
    header("Location: akun.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm = $_POST["confirm_password"] ?? "";

    if ($name === "" || $email === "" || $password === "") {
        $error = "Semua field wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter.";
    } elseif ($password !== $confirm) {
        $error = "Konfirmasi password tidak sama.";
    } else {
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        $exists = mysqli_fetch_assoc(mysqli_stmt_get_result($check));
        mysqli_stmt_close($check);

        if ($exists) {
            $error = "Email sudah terdaftar.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $saldo = 0;
            $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password, saldo, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
            mysqli_stmt_bind_param($stmt, "sssi", $name, $email, $hash, $saldo);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            flash("success", "Registrasi berhasil. Silakan login.");
            header("Location: login.php");
            exit;
        }
    }
}

$pageTitle = "Daftar";
require "header.php";
?>

<div class="auth-wrap">
    <section class="auth-card">
        <span class="eyebrow">JOIN TOPUPKU</span>
        <h1>Buat akun</h1>
        <p>Daftar untuk mulai melakukan top up.</p>
        <?php if (!empty($error)): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <form method="post">
            <label>Nama</label>
            <input type="text" name="name" required>
            <label>Email</label>
            <input type="email" name="email" required>
            <label>Password</label>
            <input type="password" name="password" minlength="6" required>
            <label>Konfirmasi Password</label>
            <input type="password" name="confirm_password" minlength="6" required>
            <button class="btn btn-primary btn-full" type="submit">Daftar</button>
        </form>
        <p class="auth-foot">Sudah punya akun? <a href="login.php">Login</a></p>
    </section>
</div>

<?php require "footer.php"; ?>
