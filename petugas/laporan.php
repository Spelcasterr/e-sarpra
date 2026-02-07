<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../login.php");
    exit;
}

$current = basename($_SERVER['PHP_SELF']);

$transaksi = mysqli_query($conn, "
    SELECT u.username, a.nama_alat, p.jumlah, p.status, 
           p.tanggal_pinjam, p.tanggal_kembali
    FROM peminjaman p
    JOIN users u ON p.user_id = u.id
    JOIN alat a ON p.alat_id = a.id
    ORDER BY p.id DESC
");

$total_pinjam = mysqli_fetch_assoc(
    mysqli_query($conn,
        "SELECT SUM(jumlah) total FROM peminjaman WHERE status='disetujui'")
)['total'] ?? 0;

$total_kembali = mysqli_fetch_assoc(
    mysqli_query($conn,
        "SELECT SUM(jumlah) total FROM peminjaman WHERE status='dikembalikan'")
)['total'] ?? 0;

$stok = mysqli_query($conn, "SELECT nama_alat, stok FROM alat");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi</title>
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
                : 'bg-white hover:bg-gray-200 text-gray-700' ?>">
            Pengajuan Peminjaman
        </a>

        <a href="pengembalian.php"
           class="px-4 py-2 rounded font-medium
           <?= $current == 'pengembalian.php'
                ? 'bg-blue-600 text-white'
                : 'bg-white hover:bg-gray-200 text-gray-700' ?>">
            Persetujuan Pengembalian
        </a>

        <a href="laporan.php"
           class="px-4 py-2 rounded font-medium
           <?= $current == 'laporan.php'
                ? 'bg-blue-600 text-white'
                : 'bg-white hover:bg-gray-200 text-gray-700' ?>">
            Laporan
        </a>
    </div>

    <!-- Action -->
    <div class="flex gap-3 mb-6">
        <a href="laporan_pdf.php" target="_blank"
           class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded">
            Cetak PDF
        </a>
        <a href="laporan_excel.php"
           class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">
            Export Excel
        </a>
    </div>

    <!-- Ringkasan -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white p-4 rounded shadow">
            <p class="text-gray-500 text-sm">Total Barang Dipinjam</p>
            <p class="text-2xl font-bold text-blue-600">
                <?= $total_pinjam ?>
            </p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <p class="text-gray-500 text-sm">Total Barang Dikembalikan</p>
            <p class="text-2xl font-bold text-emerald-600">
                <?= $total_kembali ?>
            </p>
        </div>
    </div>

    <!-- Detail Transaksi -->
    <div class="bg-white rounded shadow mb-6">
        <div class="p-4 border-b font-semibold text-gray-700">
            Detail Transaksi
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">User</th>
                    <th class="px-4 py-3 text-left">Barang</th>
                    <th class="px-4 py-3 text-center">Jumlah</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Tgl Pinjam</th>
                    <th class="px-4 py-3 text-center">Tgl Kembali</th>
                </tr>
            </thead>
            <tbody class="divide-y">
            <?php while ($d = mysqli_fetch_assoc($transaksi)): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3"><?= htmlspecialchars($d['username']) ?></td>
                    <td class="px-4 py-3"><?= htmlspecialchars($d['nama_alat']) ?></td>
                    <td class="px-4 py-3 text-center"><?= $d['jumlah'] ?></td>
                    <td class="px-4 py-3 text-center">
                        <?= ucfirst($d['status']) ?>
                    </td>
                    <td class="px-4 py-3 text-center"><?= $d['tanggal_pinjam'] ?></td>
                    <td class="px-4 py-3 text-center">
                        <?= $d['tanggal_kembali'] ?: '-' ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Stok -->
    <div class="bg-white rounded shadow">
        <div class="p-4 border-b font-semibold text-gray-700">
            Sisa Stok Barang
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Barang</th>
                    <th class="px-4 py-3 text-center">Stok</th>
                </tr>
            </thead>
            <tbody class="divide-y">
            <?php while ($s = mysqli_fetch_assoc($stok)): ?>
                <tr>
                    <td class="px-4 py-3"><?= htmlspecialchars($s['nama_alat']) ?></td>
                    <td class="px-4 py-3 text-center font-semibold">
                        <?= $s['stok'] ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
