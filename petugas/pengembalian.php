<?php
session_start();
include '../koneksi.php';
include '../config/log.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../login.php");
    exit;
}

$current = basename($_SERVER['PHP_SELF']);

$data = mysqli_query($conn, "
    SELECT p.*, u.username, a.nama_alat
    FROM peminjaman p
    JOIN users u ON p.user_id = u.id
    JOIN alat a ON p.alat_id = a.id
    WHERE p.status = 'menunggu_pengembalian'
    ORDER BY p.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Persetujuan Pengembalian</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<div class="max-w-7xl mx-auto p-6">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            Dashboard Petugas
        </h1>
        <a href="../logout.php"
           class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
            Logout
        </a>
    </div>

    <!-- Navigation -->
    <div class="flex gap-3 mb-6">
        <a href="petugas.php"
           class="px-4 py-2 rounded font-medium
           <?= $current == 'petugas.php'
                ? 'bg-blue-600 text-white'
                : 'bg-white text-gray-700 hover:bg-gray-200' ?>">
            Pengajuan Peminjaman
        </a>

        <a href="pengembalian.php"
           class="px-4 py-2 rounded font-medium
           <?= $current == 'pengembalian.php'
                ? 'bg-blue-600 text-white'
                : 'bg-white text-gray-700 hover:bg-gray-200' ?>">
            Persetujuan Pengembalian
        </a>

        <a href="laporan.php"
           class="px-4 py-2 rounded font-medium
           <?= $current == 'laporan.php'
                ? 'bg-blue-600 text-white'
                : 'bg-white text-gray-700 hover:bg-gray-200' ?>">
            Laporan
        </a>
    </div>

    <!-- Content -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="p-4 border-b">
            <h2 class="text-lg font-semibold text-gray-700">
                Daftar Pengajuan Pengembalian
            </h2>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">User</th>
                    <th class="px-4 py-3 text-left">Alat</th>
                    <th class="px-4 py-3 text-center">Jumlah</th>
                    <th class="px-4 py-3 text-center">Tanggal Kembali</th>
                    <th class="px-4 py-3 text-center">denda</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">

            <?php if (mysqli_num_rows($data) == 0): ?>
                <tr>
                    <td colspan="5" class="text-center py-6 text-gray-500">
                        Tidak ada pengajuan pengembalian
                    </td>
                </tr>
            <?php endif; ?>

            <?php while ($d = mysqli_fetch_assoc($data)): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3"><?= htmlspecialchars($d['username']) ?></td>
                    <td class="px-4 py-3"><?= htmlspecialchars($d['nama_alat']) ?></td>
                    <td class="px-4 py-3 text-center"><?= $d['jumlah'] ?></td>
                    <td class="px-4 py-3 text-center"><?= $d['tanggal_kembali'] ?></td>
                    <td class="px-4 py-3 text-center"><?= $d['denda'] ?></td>
                    <td class="px-4 py-3 text-center">
                        <a href="setujui_pengembalian.php?id=<?= $d['id'] ?>"
                           onclick="return confirm('Setujui pengembalian barang ini?')"
                           class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-1 rounded">
                            Setujui
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>

            </tbody>
        </table>
    </div>

</div>

</body>
</html>
