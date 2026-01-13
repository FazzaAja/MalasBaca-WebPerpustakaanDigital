<?php
include '../auth_check.php';
include '../config/database.php';
include '../functions.php';

$id = validate_integer($_GET['id'] ?? 0);
if ($id !== null && $id > 0 && delete_favorite($conn, $id)) {
    echo "<script>alert('Favorite berhasil dihapus'); window.location='index.php';</script>";
    exit;
}

echo "<script>alert('Gagal menghapus favorite atau ID tidak valid'); window.location='index.php';</script>";
?>
