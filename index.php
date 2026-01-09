<?php
session_start();

// Jika belum ada session status login, lempar ke halaman login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php");
    exit();
}

// Jika sudah login, cek role-nya
if ($_SESSION['role'] == "admin") {
    header("location:admin/index.php");
} else if ($_SESSION['role'] == "member") {
    // Nanti jika ada halaman member, arahkan ke member/index.php
    echo "Halo Member! (Halaman member belum dibuat)";
} else {
    // Role tidak dikenali
    echo "Role tidak valid.";
}
?>