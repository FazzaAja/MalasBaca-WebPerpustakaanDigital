<?php
session_start();
include 'config/database.php';

// Jika sudah login, lempar ke dashboard masing-masing
if (isset($_SESSION['status']) && $_SESSION['status'] == "login") {
    if ($_SESSION['role'] == 'admin') header("location:admin/index");
    if ($_SESSION['role'] == 'member') header("location:user/index");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Perpustakaan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root { --primary-color: #4e73df; }
        body { background-color: #f8f9fc; }
        .navbar { box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); }
        .book-card { transition: all 0.3s ease; border: none; border-radius: 10px; overflow: hidden; background: white; }
        .book-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .cover-container { height: 250px; overflow: hidden; position: relative; }
        .cover-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
        .book-card:hover .cover-img { transform: scale(1.05); }
        .category-badge { position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.7); color: white; padding: 5px 10px; border-radius: 20px; font-size: 0.7rem; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="#"><i class="bi bi-book-half"></i> PerpusDigital</a>
            <a href="login" class="btn btn-primary px-4 rounded-pill">Login</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center mb-5">
            <div class="col-md-6">
                <form action="login" method="GET"> <input type="hidden" name="pesan" value="belum_login">
                    <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden">
                        <input type="text" class="form-control border-0 bg-white" placeholder="Cari buku apa hari ini?">
                        <button class="btn btn-primary px-4" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold text-dark">📚 Pustaka Buku</h5>
            <small class="text-muted">Login untuk akses penuh</small>
        </div>

        <div class="row g-4">
            <?php
            // Query Tampil Buku
            $query = mysqli_query($conn, "SELECT books.*, categories.name as cat_name FROM books LEFT JOIN categories ON books.category_id = categories.id ORDER BY books.id DESC");
            while ($buku = mysqli_fetch_assoc($query)) {
            ?>
            <div class="col-6 col-md-3">
                <div class="book-card shadow-sm h-100">
                    <div class="cover-container">
                        <img src="uploads/covers/<?= $buku['cover_image'] ?>" class="cover-img">
                        <span class="category-badge"><?= $buku['cat_name'] ?></span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title fw-bold text-truncate mb-1"><?= $buku['title'] ?></h6>
                        <small class="text-muted d-block mb-3"><?= $buku['author'] ?></small>
                        
                        <div class="d-grid gap-2">
                            <a href="login?pesan=belum_login" class="btn btn-outline-primary btn-sm rounded-pill">Baca Sekarang</a>
                            <a href="login?pesan=belum_login" class="btn btn-light btn-sm text-danger"><i class="bi bi-heart"></i> Simpan</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

    <footer class="mt-5 py-4 text-center text-muted border-top bg-white">
        <small>&copy; 2024 Perpustakaan Digital</small>
    </footer>

</body>
</html>