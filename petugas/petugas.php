<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../login.php");
    exit;
}

$page = 'pengajuan'; // penanda halaman aktif

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
<body class="bg-gray-100 min-h-screen">

<div class="max-w-6xl mx-auto p-6">

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
           class="<?= $page == 'pengajuan'
                ? 'bg-blue-600 text-white'
                : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?>
                px-4 py-2 rounded font-medium">
            Pengajuan Peminjaman
        </a>

        <a href="pengembalian.php"
           class="<?= $page == 'pengembalian'
                ? 'bg-emerald-600 text-white'
                : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?>
                px-4 py-2 rounded font-medium">
            Persetujuan Pengembalian
        </a>

        <a href="laporan.php"
           class="<?= $page == 'laporan'
                ? 'bg-indigo-600 text-white'
                : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?>
                px-4 py-2 rounded font-medium">
            Laporan
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="p-4 border-b">
            <h2 class="text-lg font-semibold text-gray-700">
                Pengajuan Peminjaman
            </h2>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">User</th>
                    <th class="px-4 py-3 text-left">Alat</th>
                    <th class="px-4 py-3 text-center">Jumlah</th>
                    <th class="px-4 py-3 text-center">Tanggal Kembali</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">

            <?php if (mysqli_num_rows($data) == 0): ?>
                <tr>
                    <td colspan="5" class="text-center py-6 text-gray-500">
                        Tidak ada pengajuan peminjaman
                    </td>
                </tr>
            <?php endif; ?>

            <?php while ($d = mysqli_fetch_assoc($data)): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3"><?= htmlspecialchars($d['username']) ?></td>
                    <td class="px-4 py-3"><?= htmlspecialchars($d['nama_alat']) ?></td>
                    <td class="px-4 py-3 text-center"><?= $d['jumlah'] ?></td>
                    <td class="px-4 py-3 text-center"><?= $d['tanggal_kembali'] ?></td>
                    <td class="px-4 py-3 text-center space-x-2">
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

</body>
</html>
 