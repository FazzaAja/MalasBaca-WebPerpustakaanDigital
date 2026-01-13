<?php
// Pastikan $path tersedia untuk utilitas cover_url() di halaman admin
if (!isset($path)) $path = '../';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin MalasBaca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <style>
        body { background-color: #f8f9fa; overflow-x: hidden; }
        .card { border: none; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
  <div class="container flex-grow-1">
    <a class="navbar-brand fw-bold" href="index.php">� Admin MalasBaca</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="index.php">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="kategori.php">Kategori</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="buku.php">Data Buku</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="users.php">Pengguna</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-danger fw-bold" href="../logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mb-5">
