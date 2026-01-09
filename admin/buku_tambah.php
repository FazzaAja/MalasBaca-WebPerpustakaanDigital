<?php
include '../auth_check.php';
include '../config/database.php';
include '../layout/header.php';

if (isset($_POST['simpan'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $cat_id = $_POST['category_id'];
    // $year dihapus
    $desc = $_POST['description'];

    // Upload Cover
    $rand = rand();
    $cover_name = $rand . '_' . $_FILES['cover_image']['name'];
    $tmp_cover = $_FILES['cover_image']['tmp_name'];
    
    // Upload PDF
    $pdf_name = $rand . '_' . $_FILES['pdf_file']['name'];
    $tmp_pdf = $_FILES['pdf_file']['tmp_name'];

    // Validasi sederhana
    if (move_uploaded_file($tmp_cover, '../uploads/covers/' . $cover_name) && 
        move_uploaded_file($tmp_pdf, '../uploads/pdfs/' . $pdf_name)) {
        
        // Query INSERT diperbarui (release_year dihapus)
        $sql = "INSERT INTO books (title, author, category_id, description, cover_image, pdf_file) 
                VALUES ('$title', '$author', '$cat_id', '$desc', '$cover_name', '$pdf_name')";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Buku berhasil ditambahkan!'); window.location='buku.php';</script>";
        } else {
            echo "<div class='alert alert-danger'>Database Error: " . mysqli_error($conn) . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Gagal upload file! Periksa permission folder.</div>";
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
                <form action="" method="POST" enctype="multipart/form-data">
                    
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

                    <div class="mb-3">
                        <label class="form-label">Cover Gambar (JPG/PNG)</label>
                        <input type="file" name="cover_image" class="form-control" accept="image/*" required>
                    </div>

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