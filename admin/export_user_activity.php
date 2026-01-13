<?php
include '../auth_check.php';
include '../config/database.php';
include '../functions.php';

// Ambil semua data untuk export
$user_activity = get_user_activity_stats($conn, 999); // Ambil semua data
$user_role_dist = get_user_role_distribution($conn);
$count_user = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users"));
$count_fav = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM favorites"))['total'];
$avg_favorites = $count_user > 0 ? round($count_fav / $count_user, 1) : 0;

// Set headers untuk download Excel (CSV format)
$filename = "Laporan_Aktivitas_User_" . date('Y-m-d_His') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Output UTF-8 BOM untuk Excel compatibility
echo "\xEF\xBB\xBF";

// Buat output stream
$output = fopen('php://output', 'w');

// Header Info
fputcsv($output, ['LAPORAN AKTIVITAS USER - BOOKBASE']);
fputcsv($output, ['Tanggal Export', date('d/m/Y H:i:s')]);
fputcsv($output, ['Diexport oleh', $_SESSION['username']]);
fputcsv($output, []); // Baris kosong

// Summary Statistics
fputcsv($output, ['RINGKASAN STATISTIK']);
fputcsv($output, ['Total User', $count_user]);
fputcsv($output, ['Total Admin', $user_role_dist['admin']]);
fputcsv($output, ['Total Member', $user_role_dist['member']]);
fputcsv($output, ['Total Favorite', $count_fav]);
fputcsv($output, ['Rata-rata Favorite per User', $avg_favorites]);
fputcsv($output, []); // Baris kosong

// Header tabel aktivitas user
fputcsv($output, ['DAFTAR AKTIVITAS USER']);
fputcsv($output, ['Rank', 'ID User', 'Username', 'Email', 'Role', 'Total Favorite']);

// Data user activity
$rank = 1;
if ($user_activity && mysqli_num_rows($user_activity) > 0) {
    while ($ua = mysqli_fetch_assoc($user_activity)) {
        fputcsv($output, [
            $rank,
            $ua['id'],
            $ua['username'],
            $ua['email'],
            ucfirst($ua['role']),
            $ua['total_favorites']
        ]);
        $rank++;
    }
}

fputcsv($output, []); // Baris kosong

// User Terbaru
fputcsv($output, ['USER TERBARU (5 TERAKHIR)']);
fputcsv($output, ['ID', 'Username', 'Email', 'Role']);
$newest_users = get_newest_users($conn, 5);
if ($newest_users && mysqli_num_rows($newest_users) > 0) {
    while ($nu = mysqli_fetch_assoc($newest_users)) {
        fputcsv($output, [
            $nu['id'],
            $nu['username'],
            $nu['email'],
            ucfirst($nu['role'])
        ]);
    }
}

fputcsv($output, []); // Baris kosong

// User Tidak Aktif
fputcsv($output, ['USER BELUM AKTIF (Belum Ada Favorite)']);
fputcsv($output, ['ID', 'Username', 'Email']);
$inactive_users = get_inactive_users($conn, 999); // Ambil semua
if ($inactive_users && mysqli_num_rows($inactive_users) > 0) {
    while ($iu = mysqli_fetch_assoc($inactive_users)) {
        fputcsv($output, [
            $iu['id'],
            $iu['username'],
            $iu['email']
        ]);
    }
} else {
    fputcsv($output, ['Semua user sudah aktif']);
}

fputcsv($output, []); // Baris kosong
fputcsv($output, ['===== END OF REPORT =====']);

fclose($output);
exit;
?>
