<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<!-- Navbar -->
<nav class="bg-white shadow px-6 py-4 flex justify-between items-center">
    <h1 class="text-xl font-bold text-gray-800">Dashboard Admin</h1>
    <a href="../logout.php"
       class="text-sm bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">
        Logout
    </a>
</nav>

<!-- Content -->
<div class="p-6">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">
        Menu Manajemen
    </h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

        <a href="kategori.php"
           class="bg-white p-5 rounded-lg shadow hover:shadow-md transition">
            <h3 class="font-semibold text-gray-800">Data Kategori</h3>
            <p class="text-sm text-gray-500">Kelola kategori barang</p>
        </a>

        <a href="alat.php"
           class="bg-white p-5 rounded-lg shadow hover:shadow-md transition">
            <h3 class="font-semibold text-gray-800">Data Alat</h3>
            <p class="text-sm text-gray-500">Kelola data barang</p>
        </a>

        <a href="data_user.php"
           class="bg-white p-5 rounded-lg shadow hover:shadow-md transition">
            <h3 class="font-semibold text-gray-800">Data User</h3>
            <p class="text-sm text-gray-500">Kelola admin, petugas, peminjam</p>
        </a>

        <a href="data_peminjaman.php"
           class="bg-white p-5 rounded-lg shadow hover:shadow-md transition">
            <h3 class="font-semibold text-gray-800">Data Peminjaman</h3>
            <p class="text-sm text-gray-500">Riwayat peminjaman barang</p>
        </a>

        <a href="pengembalian.php"
           class="bg-white p-5 rounded-lg shadow hover:shadow-md transition">
            <h3 class="font-semibold text-gray-800">Data Pengembalian</h3>
            <p class="text-sm text-gray-500">Riwayat pengembalian barang</p>
        </a>

        <a href="log_aktivitas.php"
           class="bg-white p-5 rounded-lg shadow hover:shadow-md transition">
            <h3 class="font-semibold text-gray-800">Log Aktivitas</h3>
            <p class="text-sm text-gray-500">Semua aktivitas sistem</p>
        </a>

    </div>
</div>

</body>
</html>
