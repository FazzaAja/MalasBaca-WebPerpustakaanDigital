<?php
include '../auth_check.php';
include '../config/database.php';
include '../functions.php';

// Endpoint: Hapus kategori
// Feature: gunakan delete_category() helper (perhatikan behavior FK di DB)
$id = validate_integer($_GET['id'] ?? 0);

// Eksekusi Hapus
if ($id !== null && $id > 0 && delete_category($conn, $id)) {
    echo "<script>alert('Data berhasil dihapus!'); window.location='kategori.php';</script>";
    exit;
} else {
    echo "<script>alert('Gagal menghapus data atau ID tidak valid!'); window.location='kategori.php';</script>";
}
?>