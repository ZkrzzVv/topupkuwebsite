<?php
require_once "../koneksi.php";
requireAdmin();

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = (int) ($_POST["user_id"] ?? 0);
    $amount = (int) ($_POST["amount"] ?? 0);
    $action = $_POST["action"] ?? "";

    if ($userId <= 0 || $amount <= 0) {
        $error = "Pilih user dan masukkan nominal yang valid.";
    } elseif (!in_array($action, ["add", "withdraw"], true)) {
        $error = "Aksi saldo tidak valid.";
    } else {
        mysqli_begin_transaction($conn);

        try {
            $stmt = mysqli_prepare(
                $conn,
                "SELECT id, name, email, saldo
                 FROM users
                 WHERE id = ?
                 FOR UPDATE"
            );
            mysqli_stmt_bind_param($stmt, "i", $userId);
            mysqli_stmt_execute($stmt);
            $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);

            if (!$user) {
                throw new Exception("User tidak ditemukan.");
            }

            $currentBalance = (int) $user["saldo"];

            if ($action === "withdraw" && $amount > $currentBalance) {
                throw new Exception(
                    "Saldo tidak cukup. Saldo user saat ini " .
                    rupiah($currentBalance) .
                    "."
                );
            }

            if ($action === "add") {
                $sql = "UPDATE users
                        SET saldo = saldo + ?, updated_at = NOW()
                        WHERE id = ?";
            } else {
                $sql = "UPDATE users
                        SET saldo = saldo - ?, updated_at = NOW()
                        WHERE id = ?";
            }

            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $amount, $userId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            mysqli_commit($conn);

            $newBalance = $action === "add"
                ? $currentBalance + $amount
                : $currentBalance - $amount;

            $word = $action === "add"
                ? "ditambahkan"
                : "dikurangi";

            flash(
                "success",
                "Saldo " .
                e($user["name"]) .
                " berhasil " .
                $word .
                " " .
                rupiah($amount) .
                ". Saldo sekarang " .
                rupiah($newBalance) .
                "."
            );

            header("Location: saldo.php");
            exit;
        } catch (Throwable $exception) {
            mysqli_rollback($conn);
            $error = $exception->getMessage();
        }
    }
}

$users = mysqli_query(
    $conn,
    "SELECT id, name, email, saldo
     FROM users
     ORDER BY name ASC"
);

$pageTitle = "Kelola Saldo";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title><?= e($pageTitle) ?> - TopupKu Admin</title>
    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >
</head>
<body>
<div class="admin-shell">
    <aside class="admin-sidebar">
        <a
            href="index.php"
            class="brand"
        >
            <span class="brand-mark">T</span>
            <span>TopupKu</span>
        </a>

        <div class="admin-profile">
            <span>👑</span>
            <div>
                <strong>Admin</strong>
                <small>
                    <?= e($_SESSION["admin_username"] ?? "admin") ?>
                </small>
            </div>
        </div>

        <nav>
            <a href="index.php">Dashboard</a>
            <a href="produk.php">Produk</a>
            <a href="users.php">User</a>
            <a
                class="active"
                href="saldo.php"
            >
                Saldo
            </a>
            <a href="transaksi.php">Transaksi</a>
            <a href="../index.php">Website</a>
            <a href="logout.php">Logout</a>
        </nav>
    </aside>

    <main class="admin-main">
        <div class="admin-top">
            <div>
                <span class="eyebrow">BALANCE CONTROL</span>
                <h1>Kelola Saldo</h1>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <section class="form-card admin-form">
            <div class="panel-head">
                <div>
                    <h2>Tambah / Tarik Saldo</h2>
                    <p class="muted">
                        Hanya admin yang dapat mengubah saldo user.
                    </p>
                </div>
            </div>

            <form method="post">
                <label for="user_id">Pilih User</label>

                <select
                    id="user_id"
                    name="user_id"
                    required
                >
                    <option value="">
                        -- pilih user --
                    </option>

                    <?php while ($user = mysqli_fetch_assoc($users)): ?>
                        <option value="<?= (int) $user["id"] ?>">
                            <?= e($user["name"]) ?>
                            —
                            <?= e($user["email"]) ?>
                            —
                            <?= rupiah($user["saldo"]) ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label for="amount">Nominal</label>

                <input
                    id="amount"
                    type="number"
                    name="amount"
                    min="1"
                    step="1"
                    placeholder="50000"
                    required
                >

                <div class="balance-action-grid">
                    <button
                        class="btn btn-primary"
                        type="submit"
                        name="action"
                        value="add"
                    >
                        + Tambah Saldo
                    </button>

                    <button
                        class="btn btn-danger"
                        type="submit"
                        name="action"
                        value="withdraw"
                        data-confirm="Tarik/kurangi saldo user sebesar nominal ini?"
                    >
                        − Tarik Saldo
                    </button>
                </div>
            </form>
        </section>

        <section class="section">
            <div class="section-head compact">
                <div>
                    <span class="eyebrow">USER BALANCES</span>
                    <h2>Saldo User</h2>
                </div>
            </div>

            <div class="table-wrap panel">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php
                    $list = mysqli_query(
                        $conn,
                        "SELECT name, email, saldo
                         FROM users
                         ORDER BY saldo DESC, name ASC"
                    );
                    ?>

                    <?php while ($user = mysqli_fetch_assoc($list)): ?>
                        <tr>
                            <td><?= e($user["name"]) ?></td>
                            <td><?= e($user["email"]) ?></td>
                            <td>
                                <strong>
                                    <?= rupiah($user["saldo"]) ?>
                                </strong>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<script src="../assets/js/app.js"></script>
</body>
</html>
