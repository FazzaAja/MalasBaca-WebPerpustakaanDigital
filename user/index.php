<?php
session_start();
include '../config/database.php';

// Cek User Login
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'member') {
    header("location:../login?pesan=belum_login");
    exit();
}

$id_user = $_SESSION['id_user']; // ID user yang sedang login

// Logic Pencarian
$where = "";
if (isset($_GET['q'])) {
    $q = mysqli_real_escape_string($conn, $_GET['q']);
    $where = "WHERE title LIKE '%$q%' OR author LIKE '%$q%'";
}

// Logic Tampil Buku + Cek apakah sudah difavoritkan user ini
$queryStr = "SELECT books.*, categories.name as cat_name, 
             (SELECT COUNT(*) FROM favorites WHERE favorites.book_id = books.id AND favorites.user_id = '$id_user') as is_fav 
             FROM books 
             LEFT JOIN categories ON books.category_id = categories.id 
             $where 
             ORDER BY books.id DESC";

$books = mysqli_query($conn, $queryStr);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Member</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .cover-img { height: 280px; object-fit: cover; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index">📖 Dashboard Member</a>
        <div class="d-flex align-items-center">
            <span class="text-white me-3">Halo, <strong><?= $_SESSION['username'] ?></strong></span>
            <a href="../logout" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    
    <div class="row mb-4 justify-content-center">
        <div class="col-md-8">
            <form action="" method="GET">
                <div class="input-group input-group-lg">
                    <input type="text" name="q" class="form-control" placeholder="Cari judul buku atau penulis..." value="<?= isset($_GET['q']) ? $_GET['q'] : '' ?>">
                    <button class="btn btn-dark" type="submit"><i class="bi bi-search"></i> Cari</button>
                </div>
            </form>
        </div>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_GET['msg'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <?php if(mysqli_num_rows($books) > 0): ?>
            <?php while ($b = mysqli_fetch_assoc($books)): ?>
            <div class="col-6 col-md-3">
                <div class="card h-100 shadow-sm border-0">
                    <img src="../uploads/covers/<?= $b['cover_image'] ?>" class="card-img-top cover-img">
                    <div class="card-body">
                        <span class="badge bg-secondary mb-1"><?= $b['cat_name'] ?></span>
                        <h6 class="card-title text-truncate"><?= $b['title'] ?></h6>
                        <small class="text-muted"><?= $b['author'] ?></small>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-between">
                        <a href="../uploads/pdfs/<?= $b['pdf_file'] ?>" target="_blank" class="btn btn-primary btn-sm w-75">
                            Baca Buku
                        </a>
                        
                        <?php if($b['is_fav'] > 0): ?>
                            <button class="btn btn-danger btn-sm" disabled><i class="bi bi-heart-fill"></i></button>
                        <?php else: ?>
                            <a href="" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-heart"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <h4 class="text-muted">Buku tidak ditemukan 😔</h4>
                <a href="index" class="btn btn-secondary mt-2">Reset Pencarian</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>