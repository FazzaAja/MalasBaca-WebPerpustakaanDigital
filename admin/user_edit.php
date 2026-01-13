<?php
include '../auth_check.php';
include '../config/database.php';
include '../functions.php';
include 'layout/header.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$data = $id > 0 ? get_user_by_id($conn, $id) : null;

if (!$data) {
    echo "<script>alert('User tidak ditemukan'); window.location='users.php';</script>";
    exit;
}

$alert = null; $alert_type = 'info';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $res = update_user_admin($conn, $id, $_POST);
    if ($res['success']) {
        echo "<script>alert('User berhasil diperbarui'); window.location='users.php';</script>";
        exit;
    }
    $alert = $res['message'];
    $alert_type = 'danger';
}
?>

<?php if ($alert): ?>
<div class="alert alert-<?php echo $alert_type; ?> alert-dismissible fade show" role="alert">
    <?php echo htmlspecialchars($alert); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit Pengguna</h5>
                <a href="users.php" class="btn btn-sm btn-outline-secondary">Kembali</a>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($data['username']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($data['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="member" <?php echo $data['role'] === 'member' ? 'selected' : ''; ?>>Member</option>
                            <option value="admin" <?php echo $data['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Baru (opsional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
                        <small class="text-muted">Jika diisi, password akan di-reset (md5).</small>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="users.php" class="btn btn-secondary">Batal</a>
                        <button type="submit" name="update_user" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>
