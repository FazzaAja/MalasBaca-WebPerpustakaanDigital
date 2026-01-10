<?php
session_start();

// Menghapus semua session
session_unset();
session_destroy();

// Redirect ke halaman login dengan pesan
header("location:index.php");
exit();
?>