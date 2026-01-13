<?php
session_start();
// Jika sudah login, lempar ke user/index (UX yang baik)
if (isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'member') {
    header("Location: user/index.php");
    exit;
}

$path = ""; 
include 'config/database.php';
include 'functions.php';

// Halaman: Beranda publik
// Fitur:
//  - Menampilkan teaser: buku terbaru (limited)
//  - Menampilkan semua kategori sebagai tag
//  - Arahkan pengguna non-logged-in ke login jika mengakses fitur yang terkunci

// Ambil 8 buku terbaru untuk tampilan rekomendasi di beranda
$query_books = get_latest_books($conn, 8);
// Ambil daftar kategori untuk tag
$query_categories = get_categories($conn);

include 'layout/header.php';
include 'layout/sidebar.php';
?>

<main class="main-content">
    <header>
        <div class="header-left">
            <button class="menu-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
            
            <div class="search-bar" onclick="window.location.href='login.php'" style="cursor: pointer;">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Login untuk mencari buku..." style="cursor: pointer;" readonly />
            </div>
        </div>
        <div class="user-profile-mini">
            <a href="login.php" class="see-all">Login / Register</a>
        </div>
    </header>

    <!-- Section: Rekomendasi Terkini (teaser) -->
    <section class="recommended">
        <div class="section-header">
            <h2>Rekomendasi Terkini</h2>
            <p style="font-size:12px; color:#888;">Login untuk akses penuh</p>
        </div>

        <div class="cards-grid">
            <?php while ($book = mysqli_fetch_assoc($query_books)) { ?>
                <div class="book-card" onclick="window.location.href='login.php'">
                    <div class="cover-wrapper">
                        <img src="<?php echo cover_url($path, $book['cover_image']); ?>" class="book-cover-img">
                        <div style="position:absolute; top:10px; right:10px; background:rgba(0,0,0,0.6); color:white; padding:5px; border-radius:50%;">
                            <i class="fas fa-lock" style="font-size:12px;"></i>
                        </div>
                    </div>
                    <div class="book-info">
                        <h3><?php echo $book['title']; ?></h3>
                        <p><?php echo $book['author']; ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>

    <!-- Section: Kategori (tags) - mengarahkan ke login jika pengguna belum login -->
    <section class="categories" style="margin-top: 40px;">
        <div class="section-header"><h2>Kategori</h2></div>
        <div class="tags">
            <span class="tag active">All</span>
            <?php while ($cat = mysqli_fetch_assoc($query_categories)) { ?>
                <a href="login.php" class="tag"><?php echo $cat['name']; ?> <i class="fas fa-lock" style="font-size:10px; margin-left:5px;"></i></a>
            <?php } ?>
        </div>
    </section>
</main>

<aside class="right-panel" id="rightPanel" style="justify-content: center;">
    <div class="book-detail">
        <div class="detail-cover" id="coverContainer" style="display: none;">
            <img id="detailImage" src="" alt="Cover" />
        </div>
        <div class="detail-info">
            <h2>Akses Terbatas</h2>
            <p class="description" style="text-align: center; color: #8da2c0;">
                Anda perlu login untuk melihat detail buku, membaca, mencari, atau menambahkan ke favorit.
            </p>
            <a href="login.php" class="read-now-btn">
                Login Sekarang <i class="fas fa-sign-in-alt"></i>
            </a>
        </div>
    </div>
</aside>

<?php include 'layout/footer.php'; ?>