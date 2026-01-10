<?php
include '../auth_check.php';
include '../config/database.php';
include '../functions.php';

// Endpoint: Hapus buku
// Feature: panggil delete_book() untuk menghapus record dan file fisik (cover + pdf)
$id = $_GET['id'];
if (delete_book($conn, $id)) {
    echo "<script>alert('Buku dan file berhasil dihapus!'); window.location='buku.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus!'); window.location='buku.php';</script>";
}
?>