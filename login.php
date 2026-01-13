<?php
session_start();
include 'config/database.php';
include 'functions.php';

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
// Feature: Authenticate user using login_user() helper, then set session and redirect based on role
if (isset($_POST['login'])) {
    $identifier = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($identifier) || empty($password)) {
        $error = "Username/Email dan Password wajib diisi!";
    } else {
        $user = login_user($conn, $identifier, $password);
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            // keep legacy keys used elsewhere
            $_SESSION['status'] = 'login';
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: user/index.php");
            }
            exit;
        } else {
            $error = "Username/Email atau Password salah!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MalasBaca</title>
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
            <h2>MalasBaca Login</h2>
            <p>Silakan masuk untuk melanjutkan</p>
        </div>

        <?php if($error): ?>
            <div class="error-msg">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- FORM: Login - masukkan username/email & password -->
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

        <a href="register.php" class="back-link">Belum punya akun? Daftar di sini</a>
        <a href="index.php" class="back-link">← Kembali ke Beranda</a>
    </div>

</body>
</html>