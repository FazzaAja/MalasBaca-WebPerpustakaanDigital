<?php 
include '../auth_check.php';
include '../config/database.php';
include '../layout/header.php'; 

// Hitung Data untuk Statistik
$count_buku = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM books"));
$count_kategori = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM categories"));
$count_user = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users"));
?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="alert alert-info border-0 shadow-sm">
            Selamat Datang, <strong><?= $_SESSION['username']; ?></strong>! Anda login sebagai Administrator.
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card text-white bg-primary shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Buku</h6>
                        <h2 class="mt-2 mb-0"><?= $count_buku; ?></h2>
                    </div>
                    <div class="fs-1 opacity-50">📚</div>
                </div>
            </div>
            <a href="buku.php" class="card-footer text-white text-decoration-none bg-primary bg-opacity-75 text-center">Lihat Detail &rarr;</a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-white bg-success shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Kategori</h6>
                        <h2 class="mt-2 mb-0"><?= $count_kategori; ?></h2>
                    </div>
                    <div class="fs-1 opacity-50">🏷️</div>
                </div>
            </div>
            <a href="kategori.php" class="card-footer text-white text-decoration-none bg-success bg-opacity-75 text-center">Lihat Detail &rarr;</a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-white bg-warning shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Pengguna</h6>
                        <h2 class="mt-2 mb-0"><?= $count_user; ?></h2>
                    </div>
                    <div class="fs-1 opacity-50">👥</div>
                </div>
            </div>
            <div class="card-footer bg-warning bg-opacity-75 text-center text-white">
                Data Pengguna
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>