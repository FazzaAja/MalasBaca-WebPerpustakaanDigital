<?php
include '../auth_check.php';
include '../config/database.php';
include '../layout/header.php';

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM categories WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

// Proses Update
if (isset($_POST['update'])) {
    $nama = $_POST['name'];
    $update = mysqli_query($conn, "UPDATE categories SET name='$nama' WHERE id='$id'");
    
    if ($update) {
        echo "<script>alert('Kategori berhasil diperbarui!'); window.location='kategori.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Gagal update: " . mysqli_error($conn) . "</div>";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Edit Kategori</h5>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" name="name" class="form-control" value="<?= $data['name']; ?>" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="kategori.php" class="btn btn-secondary">Kembali</a>
                        <button type="submit" name="update" class="btn btn-primary">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>