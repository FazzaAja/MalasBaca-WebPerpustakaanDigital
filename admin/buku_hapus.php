<?php
include '../auth_check.php';
include '../config/database.php';

$id = $_GET['id'];

// 1. Ambil data file sebelum dihapus
$query = mysqli_query($conn, "SELECT cover_image, pdf_file FROM books WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

// 2. Hapus File Fisik
$path_cover = "../uploads/covers/" . $data['cover_image'];
$path_pdf = "../uploads/pdfs/" . $data['pdf_file'];

if (file_exists($path_cover)) { unlink($path_cover); }
if (file_exists($path_pdf)) { unlink($path_pdf); }

// 3. Hapus Record Database
$hapus = mysqli_query($conn, "DELETE FROM books WHERE id='$id'");

if ($hapus) {
    echo "<script>alert('Buku dan file berhasil dihapus!'); window.location='buku.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus!'); window.location='buku.php';</script>";
}
?>