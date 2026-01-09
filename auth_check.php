<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login" || $_SESSION['role'] != 'admin') {
    header("location:../login.php?pesan=belum_login"); // Mundur satu folder karena file ini akan di-include di folder /admin
    exit();
}
?>