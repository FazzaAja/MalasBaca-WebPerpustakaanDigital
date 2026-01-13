<?php
include '../auth_check.php';
include '../config/database.php';
include '../functions.php';
include 'layout/header.php';

if (isset($_POST['simpan'])) {
    $result = add_book($conn, $_POST, $_FILES);
    if ($result['success']) {
        echo "<script>alert('Buku berhasil ditambahkan!'); window.location='buku.php';</script>";
        exit;
    } else {
        $err = isset($result['error']) ? $result['error'] : 'Terjadi kesalahan';
        echo "<div class='alert alert-danger'>" . htmlspecialchars($err) . "</div>";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Tambah Buku Baru</h5>
            </div>
            <div class="card-body">
                <!-- FORM: Tambah Buku - fields: title, category, author, description, cover image, pdf file -->
                <form action="" method="POST" enctype="multipart/form-data">
                    
                    <!-- Field group: Judul Buku & Kategori -->
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Judul Buku</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">- Pilih -</option>
                                <?php
                                $c_query = mysqli_query($conn, "SELECT * FROM categories");
                                while($c = mysqli_fetch_assoc($c_query)) {
                                    echo "<option value='".$c['id']."'>".$c['name']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Penulis</label>
                        <input type="text" name="author" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi / Sinopsis</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <!-- Field: Upload Cover Image. Required. Stored in uploads/covers/ after add_book() -->
                    <div class="mb-3">
                        <label class="form-label">Cover Gambar (JPG/PNG)</label>
                        <input type="file" name="cover_image" class="form-control" accept="image/*" required>
                    </div>

                    <!-- Field: Upload PDF file. Required. Stored in uploads/pdfs/ after add_book() -->
                    <div class="mb-3">
                        <label class="form-label">File Buku (PDF)</label>
                        <input type="file" name="pdf_file" class="form-control" accept=".pdf" required>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="buku.php" class="btn btn-secondary">Kembali</a>
                        <button type="submit" name="simpan" class="btn btn-success">Simpan Buku</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>