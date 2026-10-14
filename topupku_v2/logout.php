<?php
require_once "koneksi.php";
unset($_SESSION["user_id"]);
unset($_SESSION["user_name"]);
unset($_SESSION["cart"]);
header("Location: index.php");
exit;
