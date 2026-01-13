<?php
include '../auth_check.php';
include '../config/database.php';
include '../functions.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Cegah menghapus diri sendiri
if (isset($_SESSION['user_id']) && $id === (int) $_SESSION['user_id']) {
    echo "<script>alert('Tidak bisa menghapus akun sendiri'); window.location='users.php';</script>";
    exit;
}

if ($id > 0 && delete_user($conn, $id)) {
    echo "<script>alert('User berhasil dihapus'); window.location='users.php';</script>";
    exit;
}

echo "<script>alert('Gagal menghapus user'); window.location='users.php';</script>";
?>
