<?php
session_start();
include 'config/database.php';
include 'functions.php';

// Jika sudah login, redirect
if (isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] == 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: user/index.php");
    }
    exit;
}

$error = "";
$success = "";

// PROSES REGISTRASI
if (isset($_POST['register'])) {
    $username = sanitize_input($_POST['username'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $captcha_input = trim($_POST['captcha'] ?? '');
    
    // Validasi CAPTCHA
    if (!isset($_SESSION['captcha']) || strtoupper($captcha_input) !== $_SESSION['captcha']) {
        $error = "Kode CAPTCHA salah!";
    }
    // Validasi username
    elseif (!validate_string_length($username, 3, 50)) {
        $error = "Username harus 3-50 karakter!";
    }
    // Validasi email
    elseif (!validate_email($email)) {
        $error = "Format email tidak valid!";
    }
    // Validasi password match
    elseif ($password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak cocok!";
    }
    // Validasi panjang password
    elseif (!validate_string_length($password, 6, 255)) {
        $error = "Password minimal 6 karakter!";
    }
    else {
        // Tambah user baru dengan role member
        $result = add_user_admin($conn, [
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => 'member'
        ]);
        
        if ($result['success']) {
            $success = "Registrasi berhasil! Silakan login.";
            // Clear form
            unset($_POST);
        } else {
            $error = $result['message'];
        }
    }
    
    // Regenerate CAPTCHA after attempt
    unset($_SESSION['captcha']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - MalasBaca</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: #eef2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px 0;
        }
        .register-container {
            background: #fff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }
        .register-header h2 {
            color: #0055ff;
            margin-bottom: 10px;
            font-size: 24px;
        }
        .register-header p {
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
            box-sizing: border-box;
        }
        .form-group input:focus {
            border-color: #0055ff;
            background: #fff;
        }
        .captcha-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .captcha-group img {
            border: 2px solid #e0e7ff;
            border-radius: 8px;
            cursor: pointer;
            flex-shrink: 0;
        }
        .captcha-group input {
            flex: 1;
        }
        .btn-register {
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
        .btn-register:hover {
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
        .success-msg {
            background: #d1fae5;
            color: #065f46;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        .back-link, .login-link {
            display: block;
            margin-top: 15px;
            font-size: 13px;
            color: #9ca3af;
            text-decoration: none;
        }
        .back-link:hover, .login-link:hover {
            color: #0055ff;
        }
        .refresh-captcha {
            font-size: 11px;
            color: #6b7280;
            margin-top: 5px;
            display: block;
        }
    </style>
</head>
<body>

    <div class="register-container">
        <div class="register-header">
            <h2>Daftar Akun Baru</h2>
            <p>Buat akun untuk mengakses MalasBaca</p>
        </div>

        <?php if($error): ?>
            <div class="error-msg">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if($success): ?>
            <div class="success-msg">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Masukkan username" required autofocus value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Masukkan email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Minimal 6 karakter" required>
            </div>

            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="confirm_password" placeholder="Ulangi password" required>
            </div>

            <div class="form-group">
                <label>Kode Keamanan (CAPTCHA)</label>
                <div class="captcha-group">
                    <img src="captcha.php?<?php echo time(); ?>" alt="CAPTCHA" id="captchaImg" title="Klik untuk refresh">
                    <input type="text" name="captcha" placeholder="Masukkan kode" required maxlength="6">
                </div>
                <small class="refresh-captcha">Klik gambar untuk refresh kode</small>
            </div>

            <button type="submit" name="register" class="btn-register">Daftar Sekarang</button>
        </form>

        <a href="login.php" class="login-link">Sudah punya akun? Login di sini</a>
        <a href="index.php" class="back-link">← Kembali ke Beranda</a>
    </div>

    <script>
        // Refresh CAPTCHA on click
        document.getElementById('captchaImg').addEventListener('click', function() {
            this.src = 'captcha.php?' + new Date().getTime();
        });
    </script>

</body>
</html>
