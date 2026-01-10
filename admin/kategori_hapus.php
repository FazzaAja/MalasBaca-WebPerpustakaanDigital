<?php
include '../auth_check.php';
include '../config/database.php';
include '../functions.php';

// Endpoint: Hapus kategori
// Feature: gunakan delete_category() helper (perhatikan behavior FK di DB)
$id = $_GET['id'];

// Eksekusi Hapus
if (delete_category($conn, $id)) {
    echo "<script>alert('Data berhasil dihapus!'); window.location='kategori.php';</script>";
    exit;
} else {
    echo "<script>alert('Gagal menghapus data!'); window.location='kategori.php';</script>";
}
?>