<?php
session_start();
include '../koneksi.php';

// Proteksi halaman
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Session aman
$username = $_SESSION['username'] ?? 'Admin';
$role     = $_SESSION['role'] ?? 'admin';
$initial  = strtoupper(substr($username, 0, 1));

// Query data
$data = mysqli_query($conn, "
    SELECT * FROM log_aktivitas
    ORDER BY created_at DESC
");

// Debug query
if (!$data) {
    die("Query error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Log Aktivitas</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <div class="w-64 h-screen bg-gradient-to-b from-blue-600 to-purple-600 text-white p-6 flex flex-col justify-between">

        <div>

            <div class="mb-8">
                <h2 class="text-2xl font-bold">Admin Panel</h2>
                <p class="text-sm text-white/80">Manajemen Peminjaman</p>
            </div>

            <div class="bg-white/10 rounded-xl p-4 flex items-center space-x-3 mb-8">
                <div class="w-12 h-12 rounded-full bg-white/30 flex items-center justify-center text-lg font-bold">
                    <?= $initial ?>
                </div>
                <div>
                    <p class="font-semibold"><?= htmlspecialchars($username) ?></p>
                    <p class="text-xs text-white/70"><?= htmlspecialchars($role) ?></p>
                </div>
            </div>

            <div class="space-y-3 text-sm">

                <a href="admin.php" class="block hover:bg-white/10 px-3 py-2 rounded-lg">Dashboard</a>
                <a href="data_user.php" class="block hover:bg-white/10 px-3 py-2 rounded-lg">User</a>
                <a href="alat.php" class="block hover:bg-white/10 px-3 py-2 rounded-lg">Alat</a>
                <a href="kategori.php" class="block hover:bg-white/10 px-3 py-2 rounded-lg">Kategori</a>

                <hr class="border-white/20">

                <a href="data_peminjaman.php" class="block hover:bg-white/10 px-3 py-2 rounded-lg">Peminjaman</a>
                <a href="pengembalian.php" class="block hover:bg-white/10 px-3 py-2 rounded-lg">Pengembalian</a>
                <a href="log_aktivitas.php" class="block bg-white text-blue-600 px-3 py-2 rounded-lg">Log Aktivitas</a>

            </div>

        </div>

        <a href="../logout.php" class="bg-red-500 text-center py-2 rounded-lg hover:bg-red-600">
            Logout
        </a>

    </div>

    <!-- CONTENT -->
    <div class="flex-1 p-8 overflow-y-auto">

        <h1 class="text-2xl font-bold mb-6">
            Log Aktivitas Sistem
        </h1>

        <div class="bg-white rounded-lg shadow overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-3">No</th>
                        <th class="p-3">User</th>
                        <th class="p-3">Role</th>
                        <th class="p-3">Aktivitas</th>
                        <th class="p-3">Deskripsi</th>
                        <th class="p-3">Tanggal</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                <?php
                $no = 1;

                if ($data && mysqli_num_rows($data) > 0):
                    while ($d = mysqli_fetch_assoc($data)):
                ?>

                    <tr class="hover:bg-gray-50 text-center">
                        <td class="p-3"><?= $no++ ?></td>
                        <td class="p-3"><?= htmlspecialchars($d['username'] ?? '') ?></td>
                        <td class="p-3"><?= htmlspecialchars($d['role'] ?? '') ?></td>
                        <td class="p-3 font-medium"><?= htmlspecialchars($d['aktivitas'] ?? '') ?></td>
                        <td class="p-3"><?= htmlspecialchars($d['deskripsi'] ?? '') ?></td>
                        <td class="p-3">
                            <?= !empty($d['created_at']) ? date('d-m-Y H:i', strtotime($d['created_at'])) : '-' ?>
                        </td>
                    </tr>

                <?php 
                    endwhile;
                else:
                ?>

                    <tr>
                        <td colspan="6" class="p-6 text-center text-gray-500">
                            Belum ada aktivitas
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>