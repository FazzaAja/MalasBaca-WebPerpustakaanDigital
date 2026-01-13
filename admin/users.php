<?php
include '../auth_check.php';
include '../config/database.php';
include '../functions.php';
include 'layout/header.php';

// Flash message helper
$alert = null; $alert_type = 'info';

// Handle add user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $res = add_user_admin($conn, $_POST);
    $alert = $res['message'];
    $alert_type = $res['success'] ? 'success' : 'danger';
}

// Load users list
$users = get_users($conn, 300);
?>

<?php if ($alert): ?>
<div class="alert alert-<?php echo $alert_type; ?> alert-dismissible fade show" role="alert">
    <?php echo htmlspecialchars($alert); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Tambah Pengguna</h5>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                        <small class="text-muted">Disimpan dengan md5 (sesuai sistem saat ini)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="member">Member</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" name="add_user" class="btn btn-primary w-100">Simpan Pengguna</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Pengguna</h5>
                <small class="text-muted">Maks 300 entri terbaru</small>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="6%">ID</th>
                                <th>Username / Email</th>
                                <th width="15%">Role</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($users && mysqli_num_rows($users) > 0): ?>
                                <?php while ($u = mysqli_fetch_assoc($users)): ?>
                                <tr>
                                    <td><?php echo $u['id']; ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($u['username']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($u['email']); ?></small>
                                    </td>
                                    <td>
                                        <?php if ($u['role'] === 'admin'): ?>
                                            <span class="badge bg-danger">Admin</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Member</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="user_edit.php?id=<?php echo $u['id']; ?>" class="btn btn-warning btn-sm text-white">Edit</a>
                                        <?php if (isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$u['id']): ?>
                                            <button class="btn btn-outline-secondary btn-sm" disabled>Hapus</button>
                                        <?php else: ?>
                                            <a href="user_hapus.php?id=<?php echo $u['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus user ini? Favorit miliknya juga akan dihapus.');">Hapus</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center text-muted">Belum ada data pengguna.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>
