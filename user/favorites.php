<?php
session_start();
// Cek Login
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'member') {
    header("Location: ../login");
    exit;
}

$path = "../";
include '../config/database.php';
include '../functions.php';

// Handle favorite form submission (PRG) - same behavior as dashboard
$fav_msg = null;
if (isset($_SESSION['fav_msg'])) {
    $fav_msg = $_SESSION['fav_msg'];
    unset($_SESSION['fav_msg']);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['favorite_book_id'])) {
    $res = add_favorite($conn, $_SESSION['user_id'], (int)$_POST['favorite_book_id']);
    $_SESSION['fav_msg'] = $res['message'];
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// Ambil daftar favorit user (terbaru dulu)
$query_favs = get_user_favorites($conn, $_SESSION['user_id']);
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
                <input type="text" name="search" placeholder="Cari judul atau penulis..." value="" />
            </form>
        </div>
        <div class="user-profile-mini">
            <div class="avatar">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($username); ?>&background=0D8ABC&color=fff" />
            </div>
            <span class="user-name"><?php echo $username; ?></span>
        </div>
    </header>

    <?php if(!empty($fav_msg)): ?>
        <div style="background:#e9f6f2; border:1px solid #c7f0dd; color:#066a44; padding:10px; border-radius:6px; margin: 15px 0;">
            <?php echo htmlspecialchars($fav_msg); ?>
        </div>
    <?php endif; ?>

    <section class="recommended">
        <div class="section-header">
            <h2>Buku Favorit Saya</h2>
            <p style="font-size:12px; color:#888;">Menampilkan yang terbaru terlebih dulu</p>
        </div>

        <div class="cards-grid">
            <?php if ($query_favs && mysqli_num_rows($query_favs) > 0): ?>
                <?php while ($book = mysqli_fetch_assoc($query_favs)) { ?>
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
                            <img src="<?php echo cover_url($path, $book['cover_image']); ?>" class="book-cover-img">
                        </div>
                        <div class="book-info">
                            <h3><?php echo $book['title']; ?></h3>
                            <p><?php echo $book['author']; ?></p>
                        </div>
                    </div>
                <?php } ?>
            <?php else: ?>
                <p style='grid-column: 1/-1; text-align:center; color:#999; padding: 20px;'>Anda belum menambahkan buku ke favorit.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<aside class="right-panel" id="rightPanel" style="justify-content: center; align-items: center; display: flex;">
    <!-- Right panel: menampilkan detail buku saat diklik (cover, deskripsi, aksi baca/favorit) -->
    <div class="book-detail" style="text-align: center; width: 100%; display: flex; flex-direction: column; align-items: center;">
        
        <div class="detail-cover" id="coverContainer" style="display: none; margin: 0 auto 20px auto;">
            <img id="detailImage" src="" alt="Cover" />
        </div>

        <div class="detail-info" style="width: 100%;">
            <h2 id="detailTitle" style="font-size: 24px;">Favorit Anda</h2>
            <p class="author" id="detailAuthor" style="display:none; margin-bottom: 10px;">-</p>
            
            <p class="description" id="detailDescription" style="text-align: center; margin-top: 10px; color: #8da2c0; padding: 0 10px;">
                Pilih buku dari daftar favorit untuk melihat detail atau membaca.
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
                <form method="POST" style="margin-top:10px;">
                    <input type="hidden" name="favorite_book_id" id="favBookId" value="">
                    <button type="submit" class="read-now-btn" style="background:#ff4757; width: 100%;">
                        <i class="fas fa-heart"></i> Tambah ke Favorit
                    </button>
                </form>
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
