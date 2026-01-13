<?php
include '../auth_check.php';
include '../config/database.php';
include '../functions.php';
include 'layout/header.php';

// Proses Simpan
// Feature: Menyimpan kategori baru melalui helper add_category()
if (isset($_POST['simpan'])) {
    $nama = sanitize_input($_POST['name'] ?? '');
    if (!validate_string_length($nama, 1, 100)) {
        echo "<div class='alert alert-danger'>Nama kategori harus 1-100 karakter</div>";
    } elseif (add_category($conn, $nama)) {
        echo "<script>alert('Kategori berhasil ditambahkan!'); window.location='kategori.php';</script>";
        exit;
    } else {
        echo "<div class='alert alert-danger'>Gagal menyimpan: " . mysqli_error($conn) . "</div>";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Tambah Kategori Baru</h5>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Sains, Novel, Sejarah" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="kategori.php" class="btn btn-secondary">Kembali</a>
                        <button type="submit" name="simpan" class="btn btn-success">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>