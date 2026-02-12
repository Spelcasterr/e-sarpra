<?php
session_start();
include '../koneksi.php';

/* =====================
   CEK LOGIN & ROLE
===================== */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$username = $_SESSION['username'];
$role     = $_SESSION['role'];
$initial  = strtoupper(substr($username, 0, 1));

$data = mysqli_query($conn, "
    SELECT l.*, u.username
    FROM log_aktivitas l
    LEFT JOIN users u ON l.user_id = u.id
    ORDER BY l.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Log Aktivitas</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="flex">

    <!-- SIDEBAR -->
    <div class="w-64 min-h-screen bg-gradient-to-b from-blue-600 to-purple-600 text-white p-6 flex flex-col justify-between">

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
                    <p class="text-xs text-white/70 capitalize">
                        <?= htmlspecialchars($role) ?>
                    </p>
                </div>
            </div>

            <div class="space-y-3 text-sm">

            <a href="admin.php" class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg">
                <span>📊</span><span>Dashboard</span>
            </a>

            <a href="data_user.php" class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg font-medium">
                <span>👥</span><span>User</span>
            </a>

            <a href="alat.php" class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg">
                <span>📦</span><span>Alat</span>
            </a>

            <a href="kategori.php" class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg">
                <span>📁</span><span>Kategori</span>
            </a>

            <hr class="border-white/20">

            <a href="data_peminjaman.php" class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg">
                <span>📄</span><span>Peminjaman</span>
            </a>

            <a href="pengembalian.php" class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg">
                <span>↩️</span><span>Pengembalian</span>
            </a>

            <a href="log_aktivitas.php" class="flex items-center space-x-2 bg-white text-blue-600 px-3 py-2 rounded-lg">
                <span>📝</span><span>Log Aktivitas</span>
            </a>

        </div>
        </div>

        <div class="text-xs text-white/60">
            © <?= date('Y') ?> Sistem Peminjaman
        </div>
    </div>

    <!-- CONTENT -->
    <div class="flex-1 p-8 space-y-8">

        <h1 class="text-2xl font-bold">
            Log Aktivitas Sistem
        </h1>

        <div class="bg-white rounded-xl shadow overflow-x-auto">
            <table class="w-full text-sm text-gray-700">
                <thead class="bg-gray-100 text-gray-800">
                    <tr>
                        <th class="p-3 text-left">No</th>
                        <th class="p-3 text-left">User</th>
                        <th class="p-3 text-left">Role</th>
                        <th class="p-3 text-left">Aktivitas</th>
                        <th class="p-3 text-left">Deskripsi</th>
                        <th class="p-3 text-left">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y">

                <?php
                $no = 1;

                if (mysqli_num_rows($data) == 0) {
                    echo "
                    <tr>
                        <td colspan='6' class='p-6 text-center text-gray-500'>
                            Belum ada aktivitas
                        </td>
                    </tr>";
                }

                while ($d = mysqli_fetch_assoc($data)):
                ?>
                    <tr class="hover:bg-gray-50">
                        <td class="p-3"><?= $no++ ?></td>
                        <td class="p-3"><?= htmlspecialchars($d['username'] ?? '-') ?></td>
                        <td class="p-3 capitalize"><?= htmlspecialchars($d['role']) ?></td>
                        <td class="p-3 font-medium"><?= htmlspecialchars($d['aktivitas']) ?></td>
                        <td class="p-3 text-gray-600"><?= htmlspecialchars($d['deskripsi']) ?></td>
                        <td class="p-3 text-gray-500">
                            <?= date('d-m-Y H:i', strtotime($d['created_at'])) ?>
                        </td>
                    </tr>
                <?php endwhile; ?>

                </tbody>
            </table>
        </div>

    </div>
</div>

</body>
</html>
