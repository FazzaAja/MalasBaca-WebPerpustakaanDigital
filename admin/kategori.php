<?php
include '../auth_check.php';
include '../config/database.php';
include '../layout/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 text-primary">📂 Data Kategori Buku</h5>
        <a href="kategori_tambah.php" class="btn btn-primary btn-sm rounded-pill px-3">+ Tambah Kategori</a>
    </div>
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="5%">No</th>
                    <th>Nama Kategori</th>
                    <th width="20%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $query = mysqli_query($conn, "SELECT * FROM categories ORDER BY id DESC");
                while ($row = mysqli_fetch_assoc($query)) {
                ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td class="fw-bold text-dark"><?= $row['name']; ?></td>
                    <td>
                        <a href="kategori_edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm text-white">Edit</a>
                        <a href="kategori_hapus.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus kategori ini? Buku dengan kategori ini akan kehilangan label kategorinya.')">Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../layout/footer.php'; ?>