<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-blue-900 text-white p-5">
        <h1 class="text-xl font-bold mb-6">Admin Panel</h1>

        <nav class="space-y-2">
            <a href="dashboard.php" class="block hover:bg-blue-700 p-2 rounded">Dashboard</a>
            <a href="kategori.php" class="block hover:bg-blue-700 p-2 rounded">Kategori</a>
            <a href="alat.php" class="block hover:bg-blue-700 p-2 rounded">Alat</a>
            <a href="user.php" class="block hover:bg-blue-700 p-2 rounded">User</a>
            <a href="log.php" class="block hover:bg-blue-700 p-2 rounded">Log Aktivitas</a>
            <a href="laporan.php" class="block hover:bg-blue-700 p-2 rounded">Laporan</a>
            <a href="../logout.php" class="block bg-red-600 hover:bg-red-700 p-2 rounded mt-6 text-center">Logout</a>
        </nav>
    </aside>

    <!-- Content -->
    <main class="flex-1 p-6">
