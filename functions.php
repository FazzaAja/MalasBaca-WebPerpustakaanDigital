<?php
/**
 * functions.php
 * Centralized helper functions used by admin and public views.
 * - Keeps database and file logic out of view files.
 * - All file/disk paths are defined here (UPLOAD_COVERS, UPLOAD_PDFS).
 * - Add new helpers here to keep code DRY and testable.
 * Usage: include 'functions.php' or include '../functions.php' from admin pages
 */

// Upload directories (absolute filesystem paths)
define('UPLOAD_COVERS', __DIR__ . '/uploads/covers/');
define('UPLOAD_PDFS', __DIR__ . '/uploads/pdfs/');

/**
 * get_books - Ambil daftar buku beserta nama kategori
 * Params: mysqli $conn
 * Returns: mysqli_result (loopable)
 */
function get_books($conn) {
    return mysqli_query($conn, "SELECT books.*, categories.name as cat_name FROM books LEFT JOIN categories ON books.category_id = categories.id ORDER BY books.id DESC");
}

/**
 * get_popular_books - Ambil buku paling populer berdasarkan jumlah favorit
 * Params: mysqli $conn, int $limit (default 4)
 * Returns: mysqli_result
 */
function get_popular_books($conn, $limit = 4) {
    $limit = (int) $limit;
    $sql = "SELECT books.*, COUNT(favorites.id) as fav_count FROM books LEFT JOIN favorites ON books.id = favorites.book_id GROUP BY books.id ORDER BY fav_count DESC LIMIT $limit";
    return mysqli_query($conn, $sql);
}

/**
 * get_books_filtered - Ambil daftar buku berdasarkan search atau kategori (atau semua)
 * Params: mysqli $conn, string|null $search, int|null $cat_id
 * Returns: mysqli_result
 */
function get_books_filtered($conn, $search = null, $cat_id = null) {
    $search = ($search !== null) ? mysqli_real_escape_string($conn, $search) : null;
    $cat_id = ($cat_id !== null) ? mysqli_real_escape_string($conn, $cat_id) : null;

    $base = "SELECT * FROM books";
    if ($search) {
        $base .= " WHERE title LIKE '%$search%' OR author LIKE '%$search%'";
    } elseif ($cat_id) {
        $base .= " WHERE category_id = '$cat_id'";
    } else {
        $base .= " ORDER BY id DESC";
    }
    return mysqli_query($conn, $base);
}

/**
 * get_latest_books - Ambil buku terbaru terbatas untuk tampilan publik (mis. homepage)
 * Params: mysqli $conn, int $limit (default 8)
 * Returns: mysqli_result
 */
function get_latest_books($conn, $limit = 8) {
    $limit = (int) $limit;
    return mysqli_query($conn, "SELECT * FROM books ORDER BY id DESC LIMIT $limit");
}

/**
 * get_categories - Ambil semua kategori (untuk dropdown / list)
 * Params: mysqli $conn
 * Returns: mysqli_result
 */
function get_categories($conn) {
    return mysqli_query($conn, "SELECT * FROM categories");
} 

/**
 * get_book_by_id - Ambil satu record buku berdasarkan id
 * Params: mysqli $conn, int|string $id
 * Returns: associative array|null
 */
function get_book_by_id($conn, $id) {
    $id = mysqli_real_escape_string($conn, $id);
    $res = mysqli_query($conn, "SELECT * FROM books WHERE id='$id'");
    return mysqli_fetch_assoc($res);
} 

/**
 * add_book - Menangani proses penambahan buku lengkap dengan upload file
 * Steps:
 *  - Sanitasi input
 *  - Generate nama file unik untuk cover & pdf
 *  - Pindahkan file ke folder uploads
 *  - Insert record ke tabel books
 * Params: mysqli $conn, array $post (form fields), array $files ($_FILES)
 * Returns: ['success' => bool, 'error' => string?]
 */
function add_book($conn, $post, $files) {
    $title = mysqli_real_escape_string($conn, $post['title']);
    $author = mysqli_real_escape_string($conn, $post['author']);
    $cat_id = mysqli_real_escape_string($conn, $post['category_id']);
    $desc = mysqli_real_escape_string($conn, $post['description']);

    $rand = rand();
    $cover_name = $rand . '_' . basename($files['cover_image']['name']);
    $pdf_name = $rand . '_' . basename($files['pdf_file']['name']);

    $tmp_cover = $files['cover_image']['tmp_name'];
    $tmp_pdf = $files['pdf_file']['tmp_name'];

    if (move_uploaded_file($tmp_cover, UPLOAD_COVERS . $cover_name) && move_uploaded_file($tmp_pdf, UPLOAD_PDFS . $pdf_name)) {
        $sql = "INSERT INTO books (title, author, category_id, description, cover_image, pdf_file) VALUES ('$title', '$author', '$cat_id', '$desc', '$cover_name', '$pdf_name')";
        if (mysqli_query($conn, $sql)) {
            return ['success' => true];
        }
        return ['success' => false, 'error' => mysqli_error($conn)];
    }
    return ['success' => false, 'error' => 'Failed to move uploaded files'];
}

/**
 * update_book - Memperbarui record buku, termasuk mengganti file (opsional)
 * Behavior:
 *  - Jika file cover/pdf baru di-upload, file lama dihapus dan digantikan
 *  - Jika tidak ada file baru, tetap gunakan nama file lama
 * Params: mysqli $conn, int|string $id, array $post, array $files
 * Returns: ['success' => bool, 'error' => string?]
 */
function update_book($conn, $id, $post, $files) {
    $id = mysqli_real_escape_string($conn, $id);
    $title = mysqli_real_escape_string($conn, $post['title']);
    $author = mysqli_real_escape_string($conn, $post['author']);
    $cat_id = mysqli_real_escape_string($conn, $post['category_id']);
    $desc = mysqli_real_escape_string($conn, $post['description']);

    $data = get_book_by_id($conn, $id);
    $cover_final = $data['cover_image'];
    $pdf_final = $data['pdf_file'];

    if (!empty($files['cover_image']['name'])) {
        $rand = rand();
        $new_cover = $rand . '_' . basename($files['cover_image']['name']);
        if (move_uploaded_file($files['cover_image']['tmp_name'], UPLOAD_COVERS . $new_cover)) {
            if (file_exists(UPLOAD_COVERS . $data['cover_image'])) unlink(UPLOAD_COVERS . $data['cover_image']);
            $cover_final = $new_cover;
        }
    }

    if (!empty($files['pdf_file']['name'])) {
        $rand = rand();
        $new_pdf = $rand . '_' . basename($files['pdf_file']['name']);
        if (move_uploaded_file($files['pdf_file']['tmp_name'], UPLOAD_PDFS . $new_pdf)) {
            if (file_exists(UPLOAD_PDFS . $data['pdf_file'])) unlink(UPLOAD_PDFS . $data['pdf_file']);
            $pdf_final = $new_pdf;
        }
    }

    $sql = "UPDATE books SET title='$title', author='$author', category_id='$cat_id', description='$desc', cover_image='$cover_final', pdf_file='$pdf_final' WHERE id='$id'";
    if (mysqli_query($conn, $sql)) return ['success' => true];
    return ['success' => false, 'error' => mysqli_error($conn)];
}

/**
 * delete_book - Hapus record buku dan file fisiknya (cover + pdf)
 * Notes: returns boolean result of the DELETE query. Files are unlinked if exist.
 * Params: mysqli $conn, int|string $id
 * Returns: bool
 */
function delete_book($conn, $id) {
    $id = mysqli_real_escape_string($conn, $id);
    $data = get_book_by_id($conn, $id);
    if ($data) {
        if (file_exists(UPLOAD_COVERS . $data['cover_image'])) unlink(UPLOAD_COVERS . $data['cover_image']);
        if (file_exists(UPLOAD_PDFS . $data['pdf_file'])) unlink(UPLOAD_PDFS . $data['pdf_file']);
        $res = mysqli_query($conn, "DELETE FROM books WHERE id='$id'");
        return $res;
    }
    return false;
} 

// -------------------- Category helpers --------------------
/**
 * get_category_by_id - Ambil single kategori berdasarkan id
 * Params: mysqli $conn, int|string $id
 * Returns: associative array|null
 */
function get_category_by_id($conn, $id) {
    $id = mysqli_real_escape_string($conn, $id);
    $res = mysqli_query($conn, "SELECT * FROM categories WHERE id='$id'");
    return mysqli_fetch_assoc($res);
} 

/**
 * add_category - Tambah kategori baru
 * Params: mysqli $conn, string $name
 * Returns: mysqli_result|false
 */
function add_category($conn, $name) {
    $name = mysqli_real_escape_string($conn, $name);
    return mysqli_query($conn, "INSERT INTO categories (name) VALUES ('$name')");
} 

/**
 * update_category - Perbarui nama kategori
 * Params: mysqli $conn, int|string $id, string $name
 * Returns: mysqli_result|false
 */
function update_category($conn, $id, $name) {
    $id = mysqli_real_escape_string($conn, $id);
    $name = mysqli_real_escape_string($conn, $name);
    return mysqli_query($conn, "UPDATE categories SET name='$name' WHERE id='$id'");
} 

/**
 * delete_category - Hapus kategori (perhatikan FK behavior di DB)
 * Params: mysqli $conn, int|string $id
 * Returns: mysqli_result|false
 */
function delete_category($conn, $id) {
    $id = mysqli_real_escape_string($conn, $id);
    return mysqli_query($conn, "DELETE FROM categories WHERE id='$id'");
} 

// -------------------- Auth helpers --------------------
/**
 * login_user - Authenticate a user by username/email and password
 * NOTE: uses md5 for compatibility with existing DB; consider upgrading
 * Params: mysqli $conn, string $identifier (username or email), string $password
 * Returns: user associative array on success, false on failure
 */
function login_user($conn, $identifier, $password) {
    $identifier = mysqli_real_escape_string($conn, $identifier);
    $password_md5 = md5($password);
    $query = "SELECT * FROM users WHERE (username = '$identifier' OR email = '$identifier') AND password = '$password_md5'";
    $res = mysqli_query($conn, $query);
    if ($res && mysqli_num_rows($res) === 1) return mysqli_fetch_assoc($res);
    return false;
} 

/**
 * is_logged_in - Simple session helper to check login + optional role
 * Params: string|null $role (e.g., 'admin')
 * Returns: bool
 * Notes: Starts session if not already started. Compatible with both
 *        legacy `status`/`role` session keys and `user_role` used elsewhere.
 */
function is_logged_in($role = null) {
    if (!isset($_SESSION)) session_start();
    if (isset($_SESSION['status']) && $_SESSION['status'] === 'login') {
        if ($role === null) return true;
        // support both 'role' and 'user_role' session keys
        $r = $_SESSION['role'] ?? $_SESSION['user_role'] ?? null;
        return ($r === $role);
    }
    return false;
}


?>

