<?php
include '../auth_check.php';
include '../config/database.php';
include '../layout/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 text-primary">📚 Data Buku</h5>
        <a href="buku_tambah.php" class="btn btn-primary btn-sm rounded-pill px-3">+ Tambah Buku</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="10%">Cover</th>
                        <th>Info Buku</th>
                        <th>Kategori</th>
                        <th>File</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    // Join tabel books dan categories (LEFT JOIN agar jika kategori dihapus, buku tetap muncul)
                    $query = mysqli_query($conn, "SELECT books.*, categories.name as cat_name 
                                                  FROM books 
                                                  LEFT JOIN categories ON books.category_id = categories.id 
                                                  ORDER BY books.id DESC");
                    
                    while ($row = mysqli_fetch_assoc($query)) {
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td>
                            <img src="../uploads/covers/<?= $row['cover_image']; ?>" 
                                 class="img-thumbnail" 
                                 style="height: 80px; width: 60px; object-fit: cover;">
                        </td>
                        <td>
                            <h6 class="mb-0"><?= $row['title']; ?></h6>
                            <small class="text-muted">Penulis: <?= $row['author']; ?></small><br>
                        </td>
                        <td>
                            <span class="badge bg-secondary"><?= $row['cat_name'] ?? 'Tak Berkategori'; ?></span>
                        </td>
                        <td>
                            <a href="../uploads/pdfs/<?= $row['pdf_file']; ?>" target="_blank" class="btn btn-sm btn-outline-danger">
                                📄 PDF
                            </a>
                        </td>
                        <td>
                            <a href="buku_edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm text-white mb-1">Edit</a>
                            <a href="buku_hapus.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm mb-1" 
                               onclick="return confirm('Yakin hapus buku ini? File fisik juga akan dihapus.')">Hapus</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>