<?php
session_start();

$host = "localhost";
$username = "root";
$password = "";
$database = "topupku";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

function rupiah($value)
{
    return "Rp " . number_format((int) $value, 0, ",", ".");
}

function isLoggedIn()
{
    return isset($_SESSION["user_id"]);
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

function isAdmin()
{
    return isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] === true;
}

function requireAdmin()
{
    if (!isAdmin()) {
        header("Location: /login.php");
        exit;
    }
}

function flash($type, $message)
{
    $_SESSION["flash"] = [
        "type" => $type,
        "message" => $message
    ];
}

function getFlash()
{
    $flash = $_SESSION["flash"] ?? null;
    unset($_SESSION["flash"]);
    return $flash;
}

function cartProduct()
{
    if (empty($_SESSION["cart"]["product_id"])) {
        return null;
    }

    global $conn;

    $id = (int) $_SESSION["cart"]["product_id"];
    $stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $product ?: null;
}

function productCount()
{
    global $conn;
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM products");
    return (int) mysqli_fetch_assoc($result)["total"];
}

function userCount()
{
    global $conn;
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
    return (int) mysqli_fetch_assoc($result)["total"];
}

function transactionCount()
{
    global $conn;
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM transactions");
    return (int) mysqli_fetch_assoc($result)["total"];
}
?>
