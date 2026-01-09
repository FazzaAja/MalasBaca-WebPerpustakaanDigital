<?php
include '../auth_check.php';
include '../config/database.php';

$id = $_GET['id'];

// Eksekusi Hapus
// Karena di tabel Books (Schema ERD sebelumnya) category_id biasanya SET NULL atau CASCADE
// Kita asumsikan aman menghapus kategori (Buku dengan kategori ini akan jadi category_id = NULL atau ikut terhapus tergantung settingan Foreign Key di DB)
$hapus = mysqli_query($conn, "DELETE FROM categories WHERE id='$id'");

if ($hapus) {
    echo "<script>alert('Data berhasil dihapus!'); window.location='kategori.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus data!'); window.location='kategori.php';</script>";
}
?>