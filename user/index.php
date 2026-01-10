<?php
session_start();
// Cek Login
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'member') {
    header("Location: ../login");
    exit;
}

$path = "../"; 
include '../config/database.php';

// 1. QUERY BUKU TERPOPULER (FAVORIT TERBANYAK)
// Kita gunakan LEFT JOIN ke tabel favorites dan hitung jumlahnya
$query_popular = mysqli_query($conn, "
    SELECT books.*, COUNT(favorites.id) as fav_count 
    FROM books 
    LEFT JOIN favorites ON books.id = favorites.book_id 
    GROUP BY books.id 
    ORDER BY fav_count DESC 
    LIMIT 4
");

// 2. QUERY UTAMA (FILTER & SEARCH)
$cat_id = isset($_GET['kategori']) ? $_GET['kategori'] : null;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : null;

$query_str = "SELECT * FROM books";

if ($search) {
    $query_str .= " WHERE title LIKE '%$search%' OR author LIKE '%$search%'";
    $section_title = "Hasil Pencarian: '$search'";
} elseif ($cat_id) {
    $query_str .= " WHERE category_id = '$cat_id'";
    $section_title = "Menampilkan Kategori Pilihan";
} else {
    $query_str .= " ORDER BY id DESC"; // Default buku terbaru di bawah
    $section_title = "Semua Koleksi Buku";
}

$query_books = mysqli_query($conn, $query_str);
$query_categories = mysqli_query($conn, "SELECT * FROM categories");
$username = $_SESSION['username'];

include '../layout/header.php';
include '../layout/sidebar.php'; 
?>

<main class="main-content">
    <header>
        <div class="header-left">
            <button class="menu-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
            <form action="" method="GET" class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Cari judul atau penulis..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" />
            </form>
        </div>
        <div class="user-profile-mini">
            <div class="avatar">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($username); ?>&background=0D8ABC&color=fff" />
            </div>
            <span class="user-name"><?php echo $username; ?></span>
        </div>
    </header>

   <section class="popular-section" style="margin-bottom: 30px;">
        <div class="section-header">
            <h2><i class="fas fa-star" style="color: #f1c40f; margin-right:8px;"></i> Paling Banyak Disukai</h2>
        </div>

        <div class="cards-grid">
            <?php while ($pop = mysqli_fetch_assoc($query_popular)) { ?>
                <div class="book-card" onclick="showBookDetail(
                    '<?php echo $pop['id']; ?>',
                    '<?php echo addslashes($pop['title']); ?>',
                    '<?php echo addslashes($pop['author']); ?>',
                    '<?php echo addslashes(str_replace(array("\r", "\n"), '', substr($pop['description'], 0, 200))); ?>...', 
                    '<?php echo $pop['cover_image']; ?>',
                    '<?php echo $pop['pdf_file']; ?>',
                    'member'
                )">
                    <div class="cover-wrapper" style="position: relative; overflow: hidden;">
                        
                        <div style="
                            position: absolute; 
                            top: 10px; 
                            left: 10px; 
                            background: #f1c40f; /* Warna Kuning Emas */
                            color: #333;         /* Teks Gelap */
                            padding: 4px 8px; 
                            border-radius: 6px; 
                            font-size: 11px; 
                            z-index: 2; 
                            font-weight: bold;
                            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                        ">
                            <i class="fas fa-star"></i> <?php echo $pop['fav_count']; ?>
                        </div>

                        <img src="<?php echo $path; ?>uploads/covers/<?php echo $pop['cover_image']; ?>" class="book-cover-img">
                    </div>
                    
                    <div class="book-info">
                        <h3><?php echo $pop['title']; ?></h3>
                        <p><?php echo $pop['author']; ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>

    <hr style="border: 0; border-top: 1px solid #e0e7ff; margin-bottom: 30px;">

    <section class="categories" style="margin-bottom: 30px;">
        <div class="section-header"><h2>Jelajahi Kategori</h2></div>
        <div class="tags">
            <a href="./" class="tag <?php echo $cat_id == null ? 'active' : ''; ?>">Semua</a>
            <?php while ($cat = mysqli_fetch_assoc($query_categories)) { ?>
                <a href="./?kategori=<?php echo $cat['id']; ?>" 
                   class="tag <?php echo $cat_id == $cat['id'] ? 'active' : ''; ?>">
                   <?php echo $cat['name']; ?>
                </a>
            <?php } ?>
        </div>
    </section>

    <section class="recommended">
        <div class="section-header">
            <h2><?php echo $section_title; ?></h2>
            <?php if($search || $cat_id): ?>
                <a href="./" class="see-all">Reset Filter</a>
            <?php endif; ?>
        </div>

        <div class="cards-grid">
            <?php 
            if (mysqli_num_rows($query_books) > 0) {
                while ($book = mysqli_fetch_assoc($query_books)) { ?>
                    <div class="book-card" onclick="showBookDetail(
                        '<?php echo $book['id']; ?>',
                        '<?php echo addslashes($book['title']); ?>',
                        '<?php echo addslashes($book['author']); ?>',
                        '<?php echo addslashes(str_replace(array("\r", "\n"), '', substr($book['description'], 0, 200))); ?>...', 
                        '<?php echo $book['cover_image']; ?>',
                        '<?php echo $book['pdf_file']; ?>',
                        'member'
                    )">
                        <div class="cover-wrapper">
                            <img src="<?php echo $path; ?>uploads/covers/<?php echo $book['cover_image']; ?>" class="book-cover-img">
                        </div>
                        <div class="book-info">
                            <h3><?php echo $book['title']; ?></h3>
                            <p><?php echo $book['author']; ?></p>
                        </div>
                    </div>
                <?php } 
            } else {
                echo "<p style='grid-column: 1/-1; text-align:center; color:#999; padding: 20px;'>Buku tidak ditemukan di kategori ini.</p>";
            }
            ?>
        </div>
    </section>
</main>

<aside class="right-panel" id="rightPanel" style="justify-content: center; align-items: center; display: flex;">
    
    <div class="book-detail" style="text-align: center; width: 100%; display: flex; flex-direction: column; align-items: center;">
        
        <div class="detail-cover" id="coverContainer" style="display: none; margin: 0 auto 20px auto;">
            <img id="detailImage" src="" alt="Cover" />
        </div>

        <div class="detail-info" style="width: 100%;">
            <h2 id="detailTitle" style="font-size: 24px;">Selamat Datang!</h2>
            <p class="author" id="detailAuthor" style="display:none; margin-bottom: 10px;">-</p>
            
            <p class="description" id="detailDescription" style="text-align: center; margin-top: 10px; color: #8da2c0; padding: 0 10px;">
                Silakan pilih buku dari daftar Popular atau Kategori di samping untuk mulai membaca.
            </p>
            
            <div id="detailStats" class="stats" style="display: none; justify-content: center; gap: 15px; margin: 20px 0;">
                <div><strong>ID: <span id="detailId">-</span></strong><small>ID</small></div>
                <div class="border-lr" style="border-left:1px solid #ffffff33; border-right:1px solid #ffffff33; padding: 0 15px;">
                    <strong>PDF</strong><small>Format</small>
                </div>
                <div><strong>Free</strong><small>Price</small></div>
            </div>

            <div class="action-buttons" style="display:none;" id="actionButtons">
                <a href="#" id="readBtn" class="read-now-btn" target="_blank" style="margin-bottom: 10px; display: block;">
                    Baca Sekarang <i class="fas fa-book-open"></i>
                </a>
                <button class="read-now-btn" style="background:#ff4757; width: 100%;">
                    <i class="fas fa-heart"></i> Tambah ke Favorit
                </button>
            </div>
        </div>
    </div>

    <div style="
        position: absolute; 
        bottom: 20px; 
        width: 100%; 
        text-align: center; 
        font-size: 11px; 
        color: #576c8a; /* Warna abu-abu agak gelap agar tidak mencolok */
        font-weight: 500;
        pointer-events: none; /* Agar tidak mengganggu klik */
    ">
        &copy; <?php echo date('Y'); ?> BookBase Digital Library.
    </div>
</aside>

<?php include '../layout/footer.php'; ?>