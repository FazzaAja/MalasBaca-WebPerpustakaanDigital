<?php
session_start();
include 'functions.php';
// auth_check: memastikan user login dan ber-role 'admin'
// Digunakan oleh semua halaman di folder /admin. Jika tidak memenuhi, redirect ke ../login.php
if (!is_logged_in('admin')) {
    header("location:../login.php?pesan=belum_login");
    exit();
}
?>