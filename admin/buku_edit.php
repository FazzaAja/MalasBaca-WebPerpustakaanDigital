<?php
include '../auth_check.php';
include '../config/database.php';
include '../functions.php';
include '../layout/header.php';

// Halaman: Edit Buku
// Feature: prefill form dengan data buku (get_book_by_id) dan proses update via update_book().
$id = $_GET['id'];
$data = get_book_by_id($conn, $id);

if (isset($_POST['update'])) {
    $result = update_book($conn, $id, $_POST, $_FILES);
    if ($result['success']) {
        echo "<script>alert('Buku berhasil diperbarui!'); window.location='buku.php';</script>";
        exit;
    } else {
        $err = isset($result['error']) ? $result['error'] : 'Gagal update';
        echo "<div class='alert alert-danger'>" . htmlspecialchars($err) . "</div>";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Edit Buku</h5>
            </div>
            <div class="card-body">
                <!-- FORM: Edit Buku - fields sama dengan tambah, file upload optional -->
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Judul Buku</label>
                        <input type="text" name="title" class="form-control" value="<?= $data['title'] ?>" required>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-select" required>
                                <?php
                                // Ambil daftar kategori untuk dropdown (dipakai untuk memilih kategori buku)
                                $c_query = mysqli_query($conn, "SELECT * FROM categories");
                                while($c = mysqli_fetch_assoc($c_query)) {
                                    $selected = ($c['id'] == $data['category_id']) ? 'selected' : '';
                                    echo "<option value='".$c['id']."' $selected>".$c['name']."</option>";
                                }
                                ?> 
                            </select>
                        </div>
                        <div class="col">
                             <label class="form-label">Penulis</label>
                             <input type="text" name="author" class="form-control" value="<?= $data['author'] ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3"><?= $data['description'] ?></textarea>
                    </div>

                    <!-- Menampilkan cover saat ini dan opsi untuk mengunggah cover baru. Jika kosong, cover lama tetap digunakan. -->
                    <div class="mb-3">
                        <label class="form-label">Cover Saat Ini</label><br>
                        <img src="../uploads/covers/<?= $data['cover_image'] ?>" width="100" class="mb-2 img-thumbnail">
                        <input type="file" name="cover_image" class="form-control" accept="image/*">
                        <small class="text-muted">*Biarkan kosong jika tidak ingin mengganti cover</small>
                    </div>

                    <!-- Menampilkan link ke PDF lama dan opsi upload PDF baru. Jika kosong, file lama tetap dipertahankan. -->
                    <div class="mb-3">
                        <label class="form-label">File PDF Saat Ini</label><br>
                        <a href="../uploads/pdfs/<?= $data['pdf_file'] ?>" class="btn btn-sm btn-outline-primary mb-2" target="_blank">Cek PDF Lama</a>
                        <input type="file" name="pdf_file" class="form-control" accept=".pdf">
                        <small class="text-muted">*Biarkan kosong jika tidak ingin mengganti file PDF</small>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="buku.php" class="btn btn-secondary">Kembali</a>
                        <button type="submit" name="update" class="btn btn-primary">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>