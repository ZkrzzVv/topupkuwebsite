<?php
require_once "../koneksi.php";
unset($_SESSION["is_admin"]);
unset($_SESSION["admin_username"]);
header("Location: /login.php");
exit;
