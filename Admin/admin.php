<?php
session_start();
include '../koneksi.php';

/* ========================
   CEK LOGIN & ROLE
======================== */

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];

/* ========================
   AMBIL DATA USER LOGIN
======================== */

$queryUser = mysqli_query($conn, "SELECT username, role FROM users WHERE id = $user_id");

if (!$queryUser || mysqli_num_rows($queryUser) == 0) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

$dataUser = mysqli_fetch_assoc($queryUser);

if ($dataUser['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$username = $dataUser['username'];
$role     = $dataUser['role'];
$initial  = strtoupper(substr($username, 0, 1));

/* ========================
   DATA STATISTIK
======================== */

$total_alat = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) as total FROM alat")
)['total'] ?? 0;

$total_user = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='peminjam'")
)['total'] ?? 0;

$total_petugas = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='petugas'")
)['total'] ?? 0;

$total_aktif = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE status!='dikembalikan'")
)['total'] ?? 0;

$total_terlambat = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE status='terlambat'")
)['total'] ?? 0;

/* ========================
   PEMINJAMAN TERBARU
======================== */

$peminjaman_terbaru = mysqli_query($conn,"
    SELECT p.*, u.username, a.nama_alat
    FROM peminjaman p
    JOIN users u ON p.user_id = u.id
    JOIN alat a ON p.alat_id = a.id
    ORDER BY p.id DESC
    LIMIT 5
");

/* ========================
   LOG AKTIVITAS TERAKHIR
======================== */

$log_terakhir = mysqli_query($conn,"
    SELECT l.*, u.username 
    FROM log_aktivitas l
    JOIN users u ON l.user_id = u.id
    ORDER BY l.id DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex">

<!-- ========================
     SIDEBAR
======================== -->
<div class="w-64 min-h-screen bg-gradient-to-b from-blue-600 to-purple-600 text-white p-6 flex flex-col justify-between">

    <div>

        <!-- HEADER -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold">Admin Panel</h2>
            <p class="text-sm text-white/80">Manajemen Peminjaman</p>
        </div>

        <!-- PROFILE -->
        <div class="bg-white/10 rounded-xl p-4 flex items-center space-x-3 mb-8">

            <div class="w-12 h-12 rounded-full bg-white/30 flex items-center justify-center text-lg font-bold">
                <?= $initial ?>
            </div>

            <div>
                <p class="font-semibold">
                    <?= htmlspecialchars($username) ?>
                </p>
                <p class="text-xs text-white/70 capitalize">
                    <?= htmlspecialchars($role) ?>
                </p>
            </div>

        </div>

        <!-- MENU -->
        <div class="space-y-3 text-sm">

            <a href="admin.php" class="flex items-center space-x-2 bg-white text-blue-600 px-3 py-2 rounded-lg font-medium">
                <span>📊</span>
                <span>Dashboard</span>
            </a>

            <a href="data_user.php" class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg">
                <span>👥</span>
                <span>User</span>
            </a>

            <a href="alat.php" class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg">
                <span>📦</span>
                <span>Alat</span>
            </a>

            <a href="kategori.php" class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg">
                <span>📁</span>
                <span>Kategori</span>
            </a>

            <hr class="border-white/20">

            <a href="data_peminjaman.php" class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg">
                <span>📄</span>
                <span>Peminjaman</span>
            </a>

            <a href="pengembalian.php" class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg">
                <span>↩️</span>
                <span>Pengembalian</span>
            </a>

            <a href="log_aktivitas.php" class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg">
                <span>📝</span>
                <span>Log Aktivitas</span>
            </a>

        </div>
    </div>

    <a href="../logout.php"
       class="block bg-red-500 text-center py-2 rounded-lg hover:bg-red-600 font-medium">
       Logout
    </a>

</div>

<!-- ========================
     MAIN CONTENT
======================== -->
<div class="flex-1 p-8">

    <h1 class="text-2xl font-bold text-gray-700 mb-6">
        Dashboard
    </h1>

    <!-- CARD STATISTIK -->
    <div class="grid grid-cols-5 gap-6 mb-8">

        <div class="bg-white p-5 rounded shadow">
            <p class="text-sm text-gray-500">Total Alat</p>
            <h3 class="text-2xl font-bold"><?= $total_alat ?></h3>
        </div>

        <div class="bg-white p-5 rounded shadow">
            <p class="text-sm text-gray-500">Total User</p>
            <h3 class="text-2xl font-bold"><?= $total_user ?></h3>
        </div>

        <div class="bg-white p-5 rounded shadow">
            <p class="text-sm text-gray-500">Total Petugas</p>
            <h3 class="text-2xl font-bold"><?= $total_petugas ?></h3>
        </div>

        <div class="bg-white p-5 rounded shadow">
            <p class="text-sm text-gray-500">Peminjaman Aktif</p>
            <h3 class="text-2xl font-bold text-yellow-600"><?= $total_aktif ?></h3>
        </div>

        <div class="bg-white p-5 rounded shadow">
            <p class="text-sm text-gray-500">Terlambat</p>
            <h3 class="text-2xl font-bold text-red-600"><?= $total_terlambat ?></h3>
        </div>

    </div>

    <!-- GRID BAWAH -->
    <div class="grid grid-cols-2 gap-6">

        <!-- PEMINJAMAN TERBARU -->
        <div class="bg-white p-6 rounded shadow">
            <h2 class="font-semibold mb-4">Peminjaman Terbaru</h2>

            <?php if (mysqli_num_rows($peminjaman_terbaru) > 0): ?>
                <?php while($p = mysqli_fetch_assoc($peminjaman_terbaru)): ?>
                    <div class="border-b py-2 text-sm">
                        <b><?= htmlspecialchars($p['username']) ?></b> meminjam
                        <b><?= htmlspecialchars($p['nama_alat']) ?></b>
                        <span class="text-gray-500">(<?= htmlspecialchars($p['status']) ?>)</span>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-sm text-gray-500">Belum ada data</p>
            <?php endif; ?>

        </div>

        <!-- LOG AKTIVITAS -->
        <div class="bg-white p-6 rounded shadow">
            <h2 class="font-semibold mb-4">Aktivitas Terakhir</h2>

            <?php if (mysqli_num_rows($log_terakhir) > 0): ?>
                <?php while($log = mysqli_fetch_assoc($log_terakhir)): ?>
                    <div class="border-b py-2 text-sm">
                        <span class="font-semibold text-blue-600">
                            <?= htmlspecialchars($log['username']) ?>
                        </span>
                        <?= htmlspecialchars($log['deskripsi']) ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-sm text-gray-500">Belum ada aktivitas</p>
            <?php endif; ?>

        </div>

    </div>

</div>

</body>
</html>
