<?php 
include '../auth_check.php';
include '../config/database.php';
include '../functions.php';
include 'layout/header.php'; 

// Hitung Data untuk Statistik
$count_buku = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM books"));
$count_kategori = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM categories"));
$count_user = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users"));
$fav_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM favorites");
$count_fav = ($fav_query && mysqli_num_rows($fav_query) === 1) ? (int) mysqli_fetch_assoc($fav_query)['total'] : 0;
$favorites = get_recent_favorites($conn, 15);

// Data untuk Laporan Aktivitas User
$user_activity = get_user_activity_stats($conn, 10);
$newest_users = get_newest_users($conn, 5);
$inactive_users = get_inactive_users($conn, 5);
$user_role_dist = get_user_role_distribution($conn);
$avg_favorites = $count_user > 0 ? round($count_fav / $count_user, 1) : 0;
?>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="alert alert-info border-0 shadow-sm">
            Selamat Datang, <strong><?= $_SESSION['username']; ?></strong>! Anda login sebagai Administrator.
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-3">
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

    <div class="col-md-3">
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

    <div class="col-md-3">
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
            <a href="users.php" class="card-footer bg-warning bg-opacity-75 text-center text-white text-decoration-none">
                Kelola Pengguna →
            </a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-info shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Favorite</h6>
                        <h2 class="mt-2 mb-0"><?= $count_fav; ?></h2>
                    </div>
                    <div class="fs-1 opacity-50">⭐</div>
                </div>
            </div>
            <div class="card-footer bg-info bg-opacity-75 text-center text-white">
                Manajemen Favorite
            </div>
        </div>
    </div>
</div>

<!-- Laporan Aktivitas User -->
<div class="row g-3 mt-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0"><i class="fas fa-chart-line"></i> Laporan Aktivitas User</h4>
            <a href="export_user_activity.php" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Export ke Excel
            </a>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="col-md-4">
        <div class="card border-primary shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Rata-rata Favorite</h6>
                <h2 class="card-title text-primary mb-0"><?= $avg_favorites; ?></h2>
                <small class="text-muted">per user</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-success shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Admin</h6>
                <h2 class="card-title text-success mb-0"><?= $user_role_dist['admin']; ?></h2>
                <small class="text-muted">pengguna</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-info shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Member</h6>
                <h2 class="card-title text-info mb-0"><?= $user_role_dist['member']; ?></h2>
                <small class="text-muted">pengguna</small>
            </div>
        </div>
    </div>
</div>

<!-- User Activity Table -->
<div class="row g-3 mt-3">
    <div class="col-lg-7">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">👥 User Paling Aktif</h5>
                <span class="badge bg-primary">Top 10</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="8%">Rank</th>
                                <th>Username</th>
                                <th width="15%">Role</th>
                                <th width="20%">Total Favorite</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $rank = 1;
                            if ($user_activity && mysqli_num_rows($user_activity) > 0):
                                while ($ua = mysqli_fetch_assoc($user_activity)):
                            ?>
                            <tr>
                                <td>
                                    <?php if ($rank <= 3): ?>
                                        <span class="badge bg-warning text-dark">🏆 <?= $rank; ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= $rank; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($ua['username']); ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($ua['email']); ?></small>
                                </td>
                                <td>
                                    <?php if ($ua['role'] === 'admin'): ?>
                                        <span class="badge bg-danger">Admin</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Member</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                            <?php 
                                            $max_fav = 100; // scaling untuk progress bar
                                            $percentage = min(($ua['total_favorites'] / $max_fav) * 100, 100);
                                            ?>
                                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $percentage; ?>%" aria-valuenow="<?= $ua['total_favorites']; ?>" aria-valuemin="0" aria-valuemax="100">
                                                <?= $ua['total_favorites']; ?>
                                            </div>
                                        </div>
                                        <span class="badge bg-success"><?= $ua['total_favorites']; ?></span>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                                $rank++;
                                endwhile;
                            else:
                            ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada data aktivitas</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-5">
        <!-- User Terbaru -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0 text-success">✨ User Terbaru</h6>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php 
                    if ($newest_users && mysqli_num_rows($newest_users) > 0):
                        while ($nu = mysqli_fetch_assoc($newest_users)):
                    ?>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold"><?= htmlspecialchars($nu['username']); ?></div>
                            <small class="text-muted"><?= htmlspecialchars($nu['email']); ?></small>
                        </div>
                        <?php if ($nu['role'] === 'admin'): ?>
                            <span class="badge bg-danger">Admin</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Member</span>
                        <?php endif; ?>
                    </li>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <li class="list-group-item text-center text-muted">Belum ada user baru</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        
        <!-- User Tidak Aktif -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0 text-warning">⚠️ User Belum Aktif</h6>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php 
                    if ($inactive_users && mysqli_num_rows($inactive_users) > 0):
                        while ($iu = mysqli_fetch_assoc($inactive_users)):
                    ?>
                    <li class="list-group-item px-0">
                        <div class="fw-bold"><?= htmlspecialchars($iu['username']); ?></div>
                        <small class="text-muted"><?= htmlspecialchars($iu['email']); ?></small>
                        <small class="text-danger d-block">Belum ada favorite</small>
                    </li>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <li class="list-group-item text-center text-muted">Semua user sudah aktif</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 text-primary">⭐ Favorite Terbaru</h5>
        <small class="text-muted">Maks 15 entri terbaru</small>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Pengguna</th>
                        <th>Judul Buku</th>
                        <th width="20%">Ditambahkan</th>
                        <th width="12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    if ($favorites && mysqli_num_rows($favorites) > 0) {
                        while ($fav = mysqli_fetch_assoc($favorites)) {
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?= htmlspecialchars($fav['username']); ?></div>
                            <small class="text-muted"><?= htmlspecialchars($fav['email']); ?></small>
                        </td>
                        <td><?= htmlspecialchars($fav['title']); ?></td>
                        <td><?= date('d M Y H:i', strtotime($fav['created_at'])); ?></td>
                        <td>
                            <a href="favorite_hapus.php?id=<?= $fav['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus favorite ini?');">Hapus</a>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">Belum ada data favorite.</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>