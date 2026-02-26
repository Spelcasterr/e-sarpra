<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../login.php");
    exit;
}

$username = $_SESSION['username'];
$role     = $_SESSION['role'];
$initial  = strtoupper(substr($username, 0, 1));

$page = 'pengajuan';

$data = mysqli_query($conn, "
    SELECT p.*, u.username, a.nama_alat
    FROM peminjaman p
    JOIN users u ON p.user_id = u.id
    JOIN alat a ON p.alat_id = a.id
    WHERE p.status = 'menunggu'
    ORDER BY p.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Petugas</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="flex">

    <!-- SIDEBAR -->
    <div class="w-64 min-h-screen bg-gradient-to-b from-blue-600 to-purple-600 text-white p-6 flex flex-col justify-between">

        <div>
            <div class="mb-8">
                <h2 class="text-2xl font-bold">Petugas Panel</h2>
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

            <nav class="space-y-3 text-sm">
                <a href="petugas.php" class="flex items-center space-x-2 bg-white text-blue-600 px-3 py-2 rounded-lg font-medium">
                    <span>Peminjaman</span>
                </a>

                <a href="pengembalian.php" class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg">
                    <span>Pengembalian</span>
                </a>

                <a href="laporan.php" class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg">
                    <span>Laporan</span>
                </a>

                <a href="../logout.php"
                    class="block bg-red-500 text-center py-2 rounded-lg hover:bg-red-600 font-medium">
                    Logout
                </a>
            </nav>
        </div>

        <div class="text-xs text-white/60">
            © <?= date('Y') ?> Sistem Peminjaman
        </div>
    </div>

    <!-- CONTENT -->
    <div class="flex-1 p-8 space-y-8">

        <h1 class="text-2xl font-bold">
            Pengajuan Peminjaman
        </h1>

        <div class="bg-white rounded-xl shadow overflow-x-auto">
            <table class="w-full text-sm text-gray-700">
                <thead class="bg-gray-100 text-gray-800">
                    <tr>
                        <th class="p-3 text-left">User</th>
                        <th class="p-3 text-left">Alat</th>
                        <th class="p-3 text-center">Jumlah</th>
                        <th class="p-3 text-center">Tanggal Kembali</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">

                <?php if (mysqli_num_rows($data) == 0): ?>
                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-500">
                            Tidak ada pengajuan peminjaman
                        </td>
                    </tr>
                <?php endif; ?>

                <?php while ($d = mysqli_fetch_assoc($data)): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="p-3"><?= htmlspecialchars($d['username']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($d['nama_alat']) ?></td>
                        <td class="p-3 text-center"><?= $d['jumlah'] ?></td>
                        <td class="p-3 text-center"><?= $d['tanggal_kembali'] ?></td>
                        <td class="p-3 text-center space-x-2">
                            <a href="setujui.php?id=<?= $d['id'] ?>"
                               onclick="return confirm('Setujui peminjaman ini?')"
                               class="inline-block bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">
                               Setujui
                            </a>

                            <a href="tolak.php?id=<?= $d['id'] ?>"
                               onclick="return confirm('Tolak peminjaman ini?')"
                               class="inline-block bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                               Tolak
                            </a>
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