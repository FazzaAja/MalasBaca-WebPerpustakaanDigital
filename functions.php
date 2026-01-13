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
if (!defined('UPLOAD_COVERS')) define('UPLOAD_COVERS', __DIR__ . '/uploads/covers/');
if (!defined('UPLOAD_PDFS')) define('UPLOAD_PDFS', __DIR__ . '/uploads/pdfs/');

// -------------------- Input Validation Helpers --------------------
/**
 * sanitize_input - Sanitize string input (trim, strip tags, escape)
 * Params: string $input
 * Returns: string
 */
if (!function_exists('sanitize_input')) {
    function sanitize_input($input) {
        if ($input === null) return '';
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
}

/**
 * validate_email - Validate email format
 * Params: string $email
 * Returns: bool
 */
if (!function_exists('validate_email')) {
    function validate_email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

/**
 * validate_string_length - Check if string length is within range
 * Params: string $str, int $min, int $max
 * Returns: bool
 */
if (!function_exists('validate_string_length')) {
    function validate_string_length($str, $min = 1, $max = 255) {
        $len = strlen($str);
        return $len >= $min && $len <= $max;
    }
}

/**
 * validate_integer - Validate and return integer, or null if invalid
 * Params: mixed $value
 * Returns: int|null
 */
if (!function_exists('validate_integer')) {
    function validate_integer($value) {
        if (is_numeric($value) && (int)$value == $value) {
            return (int)$value;
        }
        return null;
    }
}

/**
 * validate_image_upload - Validate uploaded image file
 * Params: array $file (from $_FILES)
 * Returns: array ['valid' => bool, 'error' => string?]
 */
if (!function_exists('validate_image_upload')) {
    function validate_image_upload($file) {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return ['valid' => false, 'error' => 'File tidak ditemukan'];
        }
        
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'error' => 'Upload error code: ' . $file['error']];
        }
        
        if ($file['size'] > $max_size) {
            return ['valid' => false, 'error' => 'Ukuran gambar maksimal 5MB'];
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime, $allowed_types)) {
            return ['valid' => false, 'error' => 'Format gambar tidak valid (hanya JPG, PNG, GIF)'];
        }
        
        return ['valid' => true];
    }
}

/**
 * validate_pdf_upload - Validate uploaded PDF file
 * Params: array $file (from $_FILES)
 * Returns: array ['valid' => bool, 'error' => string?]
 */
if (!function_exists('validate_pdf_upload')) {
    function validate_pdf_upload($file) {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return ['valid' => false, 'error' => 'File tidak ditemukan'];
        }
        
        $max_size = 50 * 1024 * 1024; // 50MB
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'error' => 'Upload error code: ' . $file['error']];
        }
        
        if ($file['size'] > $max_size) {
            return ['valid' => false, 'error' => 'Ukuran PDF maksimal 50MB'];
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if ($mime !== 'application/pdf') {
            return ['valid' => false, 'error' => 'File harus berformat PDF'];
        }
        
        return ['valid' => true];
    }
}

/**
 * sanitize_filename - Sanitize filename to prevent directory traversal
 * Params: string $filename
 * Returns: string
 */
if (!function_exists('sanitize_filename')) {
    function sanitize_filename($filename) {
        $filename = basename($filename);
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        return $filename;
    }
}

/**
 * cover_url - Return a proper URL for a book cover.
 * - If $cover is an absolute URL (http(s) or protocol-relative //), return it unchanged (CDN).
 * - Otherwise, assume it's a local file name and prefix with $path + 'uploads/covers/'.
 * Params: string $path, string $cover
 * Returns: string
 */
if (!function_exists('cover_url')) {
    function cover_url($path, $cover = "") {
        $cover = trim($cover ?? '');
        if ($cover === '') return '';
        // absolute URL or protocol-relative
        if (preg_match('/^(https?:)?\\/\\//i', $cover)) return $cover;
        return ($path ?? '') . 'uploads/covers/' . ltrim($cover, '/');
    }
}

/**
 * get_books - Ambil daftar buku beserta nama kategori
 * Params: mysqli $conn, int $limit, int $offset
 * Returns: mysqli_result (loopable)
 */
if (!function_exists('get_books')) {
    function get_books($conn, $limit = 8, $offset = 0) {
        $limit = (int) $limit;
        $offset = (int) $offset;

        return mysqli_query($conn, "SELECT books.*, categories.name as cat_name FROM books LEFT JOIN categories ON books.category_id = categories.id ORDER BY books.id DESC LIMIT $limit OFFSET $offset");
    }
}

/**
 * get_popular_books - Ambil buku paling populer berdasarkan jumlah favorit
 * Params: mysqli $conn, int $limit (default 4)
 * Returns: mysqli_result
 */
if (!function_exists('get_popular_books')) {
    function get_popular_books($conn, $limit = 4) {
        $limit = (int) $limit;
        $sql = "SELECT books.*, COUNT(favorites.id) as fav_count FROM books LEFT JOIN favorites ON books.id = favorites.book_id GROUP BY books.id ORDER BY fav_count DESC LIMIT $limit";
        return mysqli_query($conn, $sql);
    }
}

/**
 * get_user_favorites - Ambil daftar buku yang difavoritkan oleh user (terbaru dulu)
 * Params: mysqli $conn, int $user_id
 * Returns: mysqli_result
 */
if (!function_exists('get_user_favorites')) {
    function get_user_favorites($conn, $user_id) {
        $user_id = (int) $user_id;
        $sql = "SELECT books.*, favorites.created_at as fav_at FROM books JOIN favorites ON books.id = favorites.book_id WHERE favorites.user_id = $user_id ORDER BY favorites.created_at DESC";
        return mysqli_query($conn, $sql);
    }
}

/**
 * get_books_filtered - Ambil daftar buku berdasarkan search atau kategori (atau semua)
 * Params: mysqli $conn, string|null $search, int|null $cat_id, int $limit, int $offset
 * Returns: mysqli_result
 */
if (!function_exists('get_books_filtered')) {
    function get_books_filtered($conn, $search = null, $cat_id = null, $limit = 8, $offset = 0) {
        $search = ($search !== null) ? mysqli_real_escape_string($conn, $search) : null;
        $cat_id = ($cat_id !== null) ? mysqli_real_escape_string($conn, $cat_id) : null;
        $limit = (int) $limit;
        $offset = (int) $offset;

        $base = "SELECT * FROM books";
        if ($search) {
            $base .= " WHERE title LIKE '%$search%' OR author LIKE '%$search%'";
        } elseif ($cat_id) {
            $base .= " WHERE category_id = '$cat_id'";
        } else {
            $base .= " ORDER BY id DESC";
        }
        $base .= " LIMIT $limit OFFSET $offset";
        return mysqli_query($conn, $base);
    }
}

/**
 * get_latest_books - Ambil buku terbaru terbatas untuk tampilan publik (mis. homepage)
 * Params: mysqli $conn, int $limit (default 8)
 * Returns: mysqli_result
 */
if (!function_exists('get_latest_books')) {
    function get_latest_books($conn, $limit = 8) {
        $limit = (int) $limit;
        return mysqli_query($conn, "SELECT * FROM books ORDER BY id DESC LIMIT $limit");
    }
}

/**
 * get_categories - Ambil semua kategori (untuk dropdown / list)
 * Params: mysqli $conn
 * Returns: mysqli_result
 */
if (!function_exists('get_categories')) {
    function get_categories($conn) {
        return mysqli_query($conn, "SELECT * FROM categories");
    }
}

/**
 * get_book_by_id - Ambil satu record buku berdasarkan id
 * Params: mysqli $conn, int|string $id
 * Returns: associative array|null
 */
if (!function_exists('get_book_by_id')) {
    function get_book_by_id($conn, $id) {
        $id = mysqli_real_escape_string($conn, $id);
        $res = mysqli_query($conn, "SELECT * FROM books WHERE id='$id'");
        return mysqli_fetch_assoc($res);
    }
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
if (!function_exists('add_book')) {
    function add_book($conn, $post, $files) {
        // Validasi input
        $title = sanitize_input($post['title'] ?? '');
        $author = sanitize_input($post['author'] ?? '');
        $cat_id = validate_integer($post['category_id'] ?? 0);
        $desc = sanitize_input($post['description'] ?? '');
        
        // Validasi panjang string
        if (!validate_string_length($title, 1, 255)) {
            return ['success' => false, 'error' => 'Judul buku harus 1-255 karakter'];
        }
        if (!validate_string_length($author, 1, 100)) {
            return ['success' => false, 'error' => 'Nama penulis harus 1-100 karakter'];
        }
        if ($cat_id === null || $cat_id <= 0) {
            return ['success' => false, 'error' => 'Kategori tidak valid'];
        }
        
        // Validasi file cover
        $cover_check = validate_image_upload($files['cover_image']);
        if (!$cover_check['valid']) {
            return ['success' => false, 'error' => 'Cover: ' . $cover_check['error']];
        }
        
        // Validasi file PDF
        $pdf_check = validate_pdf_upload($files['pdf_file']);
        if (!$pdf_check['valid']) {
            return ['success' => false, 'error' => 'PDF: ' . $pdf_check['error']];
        }
        
        $rand = rand();
        $cover_name = $rand . '_' . sanitize_filename($files['cover_image']['name']);
        $pdf_name = $rand . '_' . sanitize_filename($files['pdf_file']['name']);

        $tmp_cover = $files['cover_image']['tmp_name'];
        $tmp_pdf = $files['pdf_file']['tmp_name'];

        if (move_uploaded_file($tmp_cover, UPLOAD_COVERS . $cover_name) && move_uploaded_file($tmp_pdf, UPLOAD_PDFS . $pdf_name)) {
            $stmt = mysqli_prepare($conn, "INSERT INTO books (title, author, category_id, description, cover_image, pdf_file) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssisss", $title, $author, $cat_id, $desc, $cover_name, $pdf_name);
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    return ['success' => true];
                }
                $error = mysqli_error($conn);
                mysqli_stmt_close($stmt);
                return ['success' => false, 'error' => $error];
            }
            return ['success' => false, 'error' => 'Prepared statement failed'];
        }
        return ['success' => false, 'error' => 'Gagal mengunggah file'];
    }
}

/**
 * update_book - Memperbarui record buku, termasuk mengganti file (opsional)
 * Behavior:
 *  - Jika file cover/pdf baru di-upload, file lama dihapus dan digantikan
 *  - Jika tidak ada file baru, tetap gunakan nama file lama
 * Params: mysqli $conn, int|string $id, array $post, array $files
 * Returns: ['success' => bool, 'error' => string?]
 */
if (!function_exists('update_book')) {
    function update_book($conn, $id, $post, $files) {
        $id = validate_integer($id);
        if ($id === null || $id <= 0) {
            return ['success' => false, 'error' => 'ID tidak valid'];
        }
        
        $title = sanitize_input($post['title'] ?? '');
        $author = sanitize_input($post['author'] ?? '');
        $cat_id = validate_integer($post['category_id'] ?? 0);
        $desc = sanitize_input($post['description'] ?? '');
        
        if (!validate_string_length($title, 1, 255)) {
            return ['success' => false, 'error' => 'Judul buku harus 1-255 karakter'];
        }
        if (!validate_string_length($author, 1, 100)) {
            return ['success' => false, 'error' => 'Nama penulis harus 1-100 karakter'];
        }
        if ($cat_id === null || $cat_id <= 0) {
            return ['success' => false, 'error' => 'Kategori tidak valid'];
        }

        $data = get_book_by_id($conn, $id);
        if (!$data) {
            return ['success' => false, 'error' => 'Buku tidak ditemukan'];
        }
        
        $cover_final = $data['cover_image'];
        $pdf_final = $data['pdf_file'];

        if (!empty($files['cover_image']['name'])) {
            $cover_check = validate_image_upload($files['cover_image']);
            if (!$cover_check['valid']) {
                return ['success' => false, 'error' => 'Cover: ' . $cover_check['error']];
            }
            
            $rand = rand();
            $new_cover = $rand . '_' . sanitize_filename($files['cover_image']['name']);
            if (move_uploaded_file($files['cover_image']['tmp_name'], UPLOAD_COVERS . $new_cover)) {
                if (file_exists(UPLOAD_COVERS . $data['cover_image'])) unlink(UPLOAD_COVERS . $data['cover_image']);
                $cover_final = $new_cover;
            }
        }

        if (!empty($files['pdf_file']['name'])) {
            $pdf_check = validate_pdf_upload($files['pdf_file']);
            if (!$pdf_check['valid']) {
                return ['success' => false, 'error' => 'PDF: ' . $pdf_check['error']];
            }
            
            $rand = rand();
            $new_pdf = $rand . '_' . sanitize_filename($files['pdf_file']['name']);
            if (move_uploaded_file($files['pdf_file']['tmp_name'], UPLOAD_PDFS . $new_pdf)) {
                if (file_exists(UPLOAD_PDFS . $data['pdf_file'])) unlink(UPLOAD_PDFS . $data['pdf_file']);
                $pdf_final = $new_pdf;
            }
        }

        $stmt = mysqli_prepare($conn, "UPDATE books SET title=?, author=?, category_id=?, description=?, cover_image=?, pdf_file=? WHERE id=?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssisssi", $title, $author, $cat_id, $desc, $cover_final, $pdf_final, $id);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                return ['success' => true];
            }
            $error = mysqli_error($conn);
            mysqli_stmt_close($stmt);
            return ['success' => false, 'error' => $error];
        }
        return ['success' => false, 'error' => 'Prepared statement failed'];
    }
}

/**
 * delete_book - Hapus record buku dan file fisiknya (cover + pdf)
 * Notes: returns boolean result of the DELETE query. Files are unlinked if exist.
 * Params: mysqli $conn, int|string $id
 * Returns: bool
 */
if (!function_exists('delete_book')) {
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
}

// -------------------- Category helpers --------------------
/**
 * get_category_by_id - Ambil single kategori berdasarkan id
 * Params: mysqli $conn, int|string $id
 * Returns: associative array|null
 */
if (!function_exists('get_category_by_id')) {
    function get_category_by_id($conn, $id) {
        $id = mysqli_real_escape_string($conn, $id);
        $res = mysqli_query($conn, "SELECT * FROM categories WHERE id='$id'");
        return mysqli_fetch_assoc($res);
    }
}

/**
 * add_category - Tambah kategori baru
 * Params: mysqli $conn, string $name
 * Returns: mysqli_result|false
 */
if (!function_exists('add_category')) {
    function add_category($conn, $name) {
        $name = sanitize_input($name);
        if (!validate_string_length($name, 1, 100)) {
            return false;
        }
        $stmt = mysqli_prepare($conn, "INSERT INTO categories (name) VALUES (?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $name);
            $result = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return $result;
        }
        return false;
    }
}

/**
 * update_category - Perbarui nama kategori
 * Params: mysqli $conn, int|string $id, string $name
 * Returns: mysqli_result|false
 */
if (!function_exists('update_category')) {
    function update_category($conn, $id, $name) {
        $id = validate_integer($id);
        $name = sanitize_input($name);
        if ($id === null || $id <= 0 || !validate_string_length($name, 1, 100)) {
            return false;
        }
        $stmt = mysqli_prepare($conn, "UPDATE categories SET name=? WHERE id=?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $name, $id);
            $result = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return $result;
        }
        return false;
    }
}

/**
 * delete_category - Hapus kategori (perhatikan FK behavior di DB)
 * Params: mysqli $conn, int|string $id
 * Returns: mysqli_result|false
 */
if (!function_exists('delete_category')) {
    function delete_category($conn, $id) {
        $id = mysqli_real_escape_string($conn, $id);
        return mysqli_query($conn, "DELETE FROM categories WHERE id='$id'");
    }
}

// -------------------- Auth helpers --------------------
/**
 * login_user - Authenticate a user by username/email and password
 * NOTE: uses md5 for compatibility with existing DB; consider upgrading
 * Params: mysqli $conn, string $identifier (username or email), string $password
 * Returns: user associative array on success, false on failure
 */
if (!function_exists('login_user')) {
    function login_user($conn, $identifier, $password) {
        $identifier = mysqli_real_escape_string($conn, $identifier);
        $password_md5 = md5($password);
        $query = "SELECT * FROM users WHERE (username = '$identifier' OR email = '$identifier') AND password = '$password_md5'";
        $res = mysqli_query($conn, $query);
        if ($res && mysqli_num_rows($res) === 1) return mysqli_fetch_assoc($res);
        return false;
    }
}

/**
 * is_logged_in - Simple session helper to check login + optional role
 * Params: string|null $role (e.g., 'admin')
 * Returns: bool
 * Notes: Starts session if not already started. Compatible with both
 *        legacy `status`/`role` session keys and `user_role` used elsewhere.
 */
if (!function_exists('is_logged_in')) {
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
}

/**
 * add_favorite - Menambahkan favorite (menghindari duplikat)
 * Params: mysqli $conn, int $user_id, int $book_id
 * Returns: array ['success' => bool, 'message' => string]
 */
if (!function_exists('add_favorite')) {
    function add_favorite($conn, $user_id, $book_id) {
        $user_id = (int) $user_id;
        $book_id = (int) $book_id;

        // Cek apakah sudah ada
        $sql = "SELECT id FROM favorites WHERE user_id = ? AND book_id = ? LIMIT 1";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $book_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) > 0) {
                mysqli_stmt_close($stmt);
                return ['success' => false, 'message' => 'Anda sudah menandai buku ini sebagai favorit.'];
            }
            mysqli_stmt_close($stmt);
        } else {
            return ['success' => false, 'message' => 'Prepared statement gagal: ' . mysqli_error($conn)];
        }

        // Insert favorite
        $insert = "INSERT INTO favorites (user_id, book_id, created_at) VALUES (?, ?, NOW())";
        if ($stmt = mysqli_prepare($conn, $insert)) {
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $book_id);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                return ['success' => true, 'message' => 'Berhasil menambahkan ke favorit.'];
            } else {
                $err = mysqli_error($conn);
                mysqli_stmt_close($stmt);
                return ['success' => false, 'message' => 'Insert gagal: ' . $err];
            }
        } else {
            return ['success' => false, 'message' => 'Prepared statement insert gagal: ' . mysqli_error($conn)];
        }
    }
}

/**
 * get_recent_favorites - Ambil daftar favorit terbaru beserta user dan buku
 * Params: mysqli $conn, int $limit
 * Returns: mysqli_result
 */
if (!function_exists('get_recent_favorites')) {
    function get_recent_favorites($conn, $limit = 10) {
        $limit = (int) $limit;
        $sql = "SELECT favorites.id, favorites.created_at, users.username, users.email, books.title FROM favorites JOIN users ON favorites.user_id = users.id JOIN books ON favorites.book_id = books.id ORDER BY favorites.created_at DESC LIMIT $limit";
        return mysqli_query($conn, $sql);
    }
}

/**
 * delete_favorite - Hapus satu baris favorit berdasarkan id
 * Params: mysqli $conn, int|string $id
 * Returns: bool
 */
if (!function_exists('delete_favorite')) {
    function delete_favorite($conn, $id) {
        $id = (int) $id;
        return mysqli_query($conn, "DELETE FROM favorites WHERE id = $id");
    }
}

// -------------------- User management (admin) --------------------
/**
 * get_users - Ambil daftar pengguna untuk halaman admin
 * Params: mysqli $conn, int $limit, int $offset
 * Returns: mysqli_result
 */
if (!function_exists('get_users')) {
    function get_users($conn, $limit = 200, $offset = 0) {
        $limit = (int) $limit;
        $offset = (int) $offset;
        $sql = "SELECT id, username, email, role FROM users ORDER BY id DESC LIMIT $limit OFFSET $offset";
        return mysqli_query($conn, $sql);
    }
}

/**
 * get_user_by_id - Ambil satu user berdasar id
 * Params: mysqli $conn, int|string $id
 * Returns: associative array|null
 */
if (!function_exists('get_user_by_id')) {
    function get_user_by_id($conn, $id) {
        $id = (int) $id;
        $res = mysqli_query($conn, "SELECT id, username, email, role, password FROM users WHERE id = $id LIMIT 1");
        return $res ? mysqli_fetch_assoc($res) : null;
    }
}

/**
 * add_user_admin - Tambah user baru dari panel admin
 * Params: mysqli $conn, array $data (username, email, password, role)
 * Returns: array ['success' => bool, 'message' => string]
 */
if (!function_exists('add_user_admin')) {
    function add_user_admin($conn, $data) {
        $username = sanitize_input($data['username'] ?? '');
        $email    = sanitize_input($data['email'] ?? '');
        $password = trim($data['password'] ?? '');
        $role     = strtolower(trim($data['role'] ?? 'member'));

        if (!validate_string_length($username, 3, 50)) {
            return ['success' => false, 'message' => 'Username harus 3-50 karakter'];
        }
        
        if (!validate_email($email)) {
            return ['success' => false, 'message' => 'Format email tidak valid'];
        }
        
        if (!validate_string_length($password, 6, 255)) {
            return ['success' => false, 'message' => 'Password minimal 6 karakter'];
        }

        if (!in_array($role, ['admin', 'member'], true)) {
            $role = 'member';
        }

        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ss", $username, $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) > 0) {
                mysqli_stmt_close($stmt);
                return ['success' => false, 'message' => 'Username atau email sudah terpakai'];
            }
            mysqli_stmt_close($stmt);
        }

        $password_md5 = md5($password);
        $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $password_md5, $role);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                return ['success' => true, 'message' => 'User berhasil ditambahkan'];
            }
            $error = mysqli_error($conn);
            mysqli_stmt_close($stmt);
            return ['success' => false, 'message' => 'Gagal menyimpan: ' . $error];
        }
        return ['success' => false, 'message' => 'Prepared statement failed'];
    }
}

/**
 * update_user_admin - Perbarui data user (username, email, role, password opsional)
 * Params: mysqli $conn, int $id, array $data
 * Returns: array ['success' => bool, 'message' => string]
 */
if (!function_exists('update_user_admin')) {
    function update_user_admin($conn, $id, $data) {
        $id = validate_integer($id);
        if ($id === null || $id <= 0) {
            return ['success' => false, 'message' => 'ID tidak valid'];
        }
        
        $username = sanitize_input($data['username'] ?? '');
        $email    = sanitize_input($data['email'] ?? '');
        $password = trim($data['password'] ?? '');
        $role     = strtolower(trim($data['role'] ?? 'member'));

        if (!validate_string_length($username, 3, 50)) {
            return ['success' => false, 'message' => 'Username harus 3-50 karakter'];
        }
        
        if (!validate_email($email)) {
            return ['success' => false, 'message' => 'Format email tidak valid'];
        }

        if (!in_array($role, ['admin', 'member'], true)) {
            $role = 'member';
        }

        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssi", $username, $email, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) > 0) {
                mysqli_stmt_close($stmt);
                return ['success' => false, 'message' => 'Username atau email sudah digunakan user lain'];
            }
            mysqli_stmt_close($stmt);
        }

        if ($password !== '') {
            if (!validate_string_length($password, 6, 255)) {
                return ['success' => false, 'message' => 'Password minimal 6 karakter'];
            }
            $password_md5 = md5($password);
            $stmt = mysqli_prepare($conn, "UPDATE users SET username=?, email=?, password=?, role=? WHERE id=?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssssi", $username, $email, $password_md5, $role, $id);
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    return ['success' => true, 'message' => 'User berhasil diperbarui'];
                }
                $error = mysqli_error($conn);
                mysqli_stmt_close($stmt);
                return ['success' => false, 'message' => 'Gagal memperbarui: ' . $error];
            }
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE users SET username=?, email=?, role=? WHERE id=?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sssi", $username, $email, $role, $id);
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    return ['success' => true, 'message' => 'User berhasil diperbarui'];
                }
                $error = mysqli_error($conn);
                mysqli_stmt_close($stmt);
                return ['success' => false, 'message' => 'Gagal memperbarui: ' . $error];
            }
        }
        return ['success' => false, 'message' => 'Prepared statement failed'];
    }
}

/**
 * delete_user - Hapus user beserta relasi favoritnya (agar FK aman)
 * Params: mysqli $conn, int $id
 * Returns: bool
 */
if (!function_exists('delete_user')) {
    function delete_user($conn, $id) {
        $id = (int) $id;
        mysqli_query($conn, "DELETE FROM favorites WHERE user_id = $id");
        return mysqli_query($conn, "DELETE FROM users WHERE id = $id");
    }
}

// -------------------- User Activity Reporting --------------------
/**
 * get_user_activity_stats - Dapatkan statistik aktivitas user (jumlah favorite per user)
 * Params: mysqli $conn, int $limit
 * Returns: mysqli_result
 */
if (!function_exists('get_user_activity_stats')) {
    function get_user_activity_stats($conn, $limit = 10) {
        $limit = (int) $limit;
        $sql = "SELECT users.id, users.username, users.email, users.role, COUNT(favorites.id) as total_favorites FROM users LEFT JOIN favorites ON users.id = favorites.user_id GROUP BY users.id ORDER BY total_favorites DESC, users.id DESC LIMIT $limit";
        return mysqli_query($conn, $sql);
    }
}

/**
 * get_newest_users - Dapatkan user terbaru
 * Params: mysqli $conn, int $limit
 * Returns: mysqli_result
 */
if (!function_exists('get_newest_users')) {
    function get_newest_users($conn, $limit = 5) {
        $limit = (int) $limit;
        $sql = "SELECT id, username, email, role FROM users ORDER BY id DESC LIMIT $limit";
        return mysqli_query($conn, $sql);
    }
}

/**
 * get_inactive_users - Dapatkan user yang belum pernah menambah favorite
 * Params: mysqli $conn, int $limit
 * Returns: mysqli_result
 */
if (!function_exists('get_inactive_users')) {
    function get_inactive_users($conn, $limit = 10) {
        $limit = (int) $limit;
        $sql = "SELECT users.id, users.username, users.email FROM users LEFT JOIN favorites ON users.id = favorites.user_id WHERE favorites.id IS NULL AND users.role = 'member' ORDER BY users.id DESC LIMIT $limit";
        return mysqli_query($conn, $sql);
    }
}

/**
 * get_user_registration_stats - Statistik pendaftaran user per bulan (6 bulan terakhir)
 * Params: mysqli $conn
 * Returns: array
 */
if (!function_exists('get_user_registration_stats')) {
    function get_user_registration_stats($conn) {
        // Note: Assuming there's a created_at or registration date column
        // If not available, will return empty array
        $result = [];
        $sql = "SELECT DATE_FORMAT(FROM_UNIXTIME(id), '%Y-%m') as month, COUNT(*) as total FROM users GROUP BY month ORDER BY month DESC LIMIT 6";
        $query = mysqli_query($conn, $sql);
        if ($query) {
            while ($row = mysqli_fetch_assoc($query)) {
                $result[] = $row;
            }
        }
        return array_reverse($result);
    }
}

/**
 * get_user_role_distribution - Distribusi user berdasarkan role
 * Params: mysqli $conn
 * Returns: array ['admin' => count, 'member' => count]
 */
if (!function_exists('get_user_role_distribution')) {
    function get_user_role_distribution($conn) {
        $result = ['admin' => 0, 'member' => 0];
        $sql = "SELECT role, COUNT(*) as total FROM users GROUP BY role";
        $query = mysqli_query($conn, $sql);
        if ($query) {
            while ($row = mysqli_fetch_assoc($query)) {
                $result[$row['role']] = (int) $row['total'];
            }
        }
        return $result;
    }
}
?>
