<?php
session_start();
include 'config/database.php';

// Jika sudah login, langsung arahkan sesuai role (agar tidak perlu login lagi)
if (isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] == 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: user/index.php");
    }
    exit;
}

$error = "";

// PROSES LOGIN
if (isset($_POST['login'])) {
    // 1. Ambil input dan bersihkan (Sanitasi)
    $identifier = mysqli_real_escape_string($conn, $_POST['username']); // Ini bisa jadi Username atau Email
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // 2. Enkripsi password input dengan MD5
    $password_md5 = md5($password);

    // 3. Query Cek Username ATAU Email DAN Password
    // Logika: (username COCOK atau email COCOK) DAN password COCOK
    $query = "SELECT * FROM users 
              WHERE (username = '$identifier' OR email = '$identifier') 
              AND password = '$password_md5'";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        // SET SESSION
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['user_role'] = $row['role'];

        // 4. CEK ROLE & REDIRECT (Perbaikan Utama)
        if ($row['role'] == 'admin') {
            header("Location: admin/index.php");
        } else if ($row['role'] == 'member') {
            // Arahkan member ke folder user
            header("Location: user/index.php");
        }
        exit;
    } else {
        $error = "Username/Email atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BookBase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        /* CSS Khusus Halaman Login agar rapi di tengah */
        body {
            background-color: #eef2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background: #fff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-header h2 {
            color: #0055ff;
            margin-bottom: 10px;
            font-size: 24px;
        }
        .login-header p {
            color: #9ca3af;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #1f2937;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e0e7ff;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            background: #f9fafb;
        }
        .form-group input:focus {
            border-color: #0055ff;
            background: #fff;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background: #0055ff;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-login:hover {
            background: #0044cc;
        }
        .error-msg {
            background: #fee2e2;
            color: #ef4444;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        .back-link {
            display: block;
            margin-top: 20px;
            font-size: 12px;
            color: #9ca3af;
            text-decoration: none;
        }
        .back-link:hover {
            color: #0055ff;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-header">
            <h2>BookBase Login</h2>
            <p>Silakan masuk untuk melanjutkan</p>
        </div>

        <?php if($error): ?>
            <div class="error-msg">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>Username atau Email</label>
                <input type="text" name="username" placeholder="Masukkan username atau email" required autofocus>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Masukkan password" required>
            </div>

            <button type="submit" name="login" class="btn-login">Masuk Sekarang</button>
        </form>

        <a href="index" class="back-link">← Kembali ke Beranda</a>
    </div>

</body>
</html>