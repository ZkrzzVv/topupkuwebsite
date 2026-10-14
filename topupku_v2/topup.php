<?php
requireLogin();

$userId = (int) $_SESSION["user_id"];
$stmt = mysqli_prepare(
    $conn,
    "SELECT saldo
     FROM users
     WHERE id = ?
     LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

$currentBalance = (int) ($user["saldo"] ?? 0);

$pageTitle = "Top Up Saldo";
require "header.php";
?>

<section class="page-heading">
    <div>
        <span class="eyebrow">TOP UP SALDO</span>
        <h1>Isi saldo lewat QRIS</h1>
        <p>
            Transfer ke QRIS resmi TopupKu, lalu kirim bukti pembayaran
            ke admin. Saldo akan ditambahkan manual setelah pembayaran
            diverifikasi.
        </p>
    </div>
</section>

<section class="qris-layout">
    <div class="panel qris-card">
        <span class="pill">MANUAL VERIFICATION</span>
        <h2>Scan QRIS</h2>

        <div class="qris-placeholder">
            <div class="qris-placeholder-icon">▦</div>
            <strong>QRIS BELUM DIATUR</strong>
            <small>
                Ganti area ini dengan QRIS resmi milikmu sebelum dipakai
                dengan uang asli.
            </small>
        </div>

        <div class="alert alert-warning">
            Jangan gunakan QRIS contoh/placeholder.
            Pastikan QRIS yang dipasang adalah QRIS resmi milik
            pemilik TopupKu.
        </div>
    </div>

    <div class="panel">
        <span class="eyebrow">CARA TOP UP</span>
        <h2>Alurnya sederhana</h2>

        <ol class="steps">
            <li>Scan QRIS resmi TopupKu.</li>
            <li>Transfer nominal yang diinginkan.</li>
            <li>Simpan bukti pembayaran.</li>
            <li>Kirim bukti ke admin.</li>
            <li>Admin memverifikasi lalu menambahkan saldo.</li>
        </ol>

        <div class="balance-preview">
            <span>Saldo saat ini</span>
            <strong><?= rupiah($currentBalance) ?></strong>
            <small>
                Saldo akan berubah setelah admin melakukan verifikasi.
            </small>
        </div>
    </div>
</section>

<?php require "footer.php"; ?>
