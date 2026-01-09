<?php
include '../auth_check.php';
include '../config/database.php';
include '../layout/header.php';

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM books WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

if (isset($_POST['update'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $cat_id = $_POST['category_id'];
    // $year dihapus
    $desc = $_POST['description'];
    
    // Default file (pakai yang lama)
    $cover_final = $data['cover_image'];
    $pdf_final = $data['pdf_file'];

    // 1. Cek Ganti Cover
    if ($_FILES['cover_image']['name'] != "") {
        $rand = rand();
        $new_cover = $rand . '_' . $_FILES['cover_image']['name'];
        if (move_uploaded_file($_FILES['cover_image']['tmp_name'], '../uploads/covers/' . $new_cover)) {
            if (file_exists('../uploads/covers/' . $data['cover_image'])) {
                unlink('../uploads/covers/' . $data['cover_image']);
            }
            $cover_final = $new_cover;
        }
    }

    // 2. Cek Ganti PDF
    if ($_FILES['pdf_file']['name'] != "") {
        $rand = rand();
        $new_pdf = $rand . '_' . $_FILES['pdf_file']['name'];
        if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], '../uploads/pdfs/' . $new_pdf)) {
            if (file_exists('../uploads/pdfs/' . $data['pdf_file'])) {
                unlink('../uploads/pdfs/' . $data['pdf_file']);
            }
            $pdf_final = $new_pdf;
        }
    }

    // Query UPDATE diperbarui (release_year dihapus)
    $sql = "UPDATE books SET 
            title='$title', author='$author', category_id='$cat_id', 
            description='$desc', 
            cover_image='$cover_final', pdf_file='$pdf_final' 
            WHERE id='$id'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Buku berhasil diperbarui!'); window.location='buku.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Gagal update: " . mysqli_error($conn) . "</div>";
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

                    <div class="mb-3">
                        <label class="form-label">Cover Saat Ini</label><br>
                        <img src="../uploads/covers/<?= $data['cover_image'] ?>" width="100" class="mb-2 img-thumbnail">
                        <input type="file" name="cover_image" class="form-control" accept="image/*">
                        <small class="text-muted">*Biarkan kosong jika tidak ingin mengganti cover</small>
                    </div>

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