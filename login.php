<?php
session_start();
include 'config/database.php'; // Sesuaikan dengan lokasi file koneksi Anda

// 1. Cek apakah user sudah login? Jika ya, lempar langsung ke admin
if (isset($_SESSION['status']) && $_SESSION['status'] == "login") {
    header("location:admin/index.php");
    exit();
}

$pesan_error = "";

// 2. Logic ketika tombol login ditekan
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']); // Security basic
    $pass  = md5($_POST['password']); // Menggunakan MD5 sesuai request sebelumnya

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND password='$pass'");
    $cek = mysqli_num_rows($query);

    if ($cek > 0) {
        $data = mysqli_fetch_assoc($query);
        
        $_SESSION['username'] = $data['username'];
        $_SESSION['id_user']  = $data['id']; // Penting untuk fitur favorit
        $_SESSION['status']   = "login";
        $_SESSION['role']     = $data['role'];

        if($data['role'] == "admin"){
            header("location:admin/index"); // Tanpa .php karena .htaccess
        } else {
            // Jika Member, lempar ke dashboard member
            header("location:user/index");
        }
    } else {
        $pesan_error = "Email atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin Perpus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow border-0">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4 fw-bold text-primary">Login Admin</h3>
                        
                        <?php if($pesan_error != ""): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $pesan_error; ?>
                            </div>
                        <?php endif; ?>

                        <?php 
                        if(isset($_GET['pesan'])){
                            if($_GET['pesan'] == "belum_login"){
                                echo "<div class='alert alert-warning'>Anda harus login dulu!</div>";
                            } else if($_GET['pesan'] == "logout"){
                                echo "<div class='alert alert-success'>Berhasil Logout!</div>";
                            }
                        }
                        ?>

                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="admin@contoh.com" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="******" required>
                            </div>
                            <button type="submit" name="login" class="btn btn-primary w-100">Masuk Dashboard</button>
                        </form>
                    </div>
                </div>
                <div class="text-center mt-3 text-muted">
                    <small>&copy; 2024 Perpustakaan Digital</small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>