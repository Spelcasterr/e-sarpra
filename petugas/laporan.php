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
                <a href="petugas.php"
                   class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg">
                    <span>Peminjaman</span>
                </a>

                <a href="pengembalian.php"
                   class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg">
                    <span>Pengembalian</span>
                </a>

                <a href="laporan.php"
                   class="flex items-center space-x-2 bg-white text-blue-600 px-3 py-2 rounded-lg font-medium">
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
            Laporan Transaksi
        </h1>

        <!-- Action Button -->
        <div class="flex gap-3">
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
        <div class="grid grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow p-6">
                <p class="text-gray-500 text-sm">Total Barang Dipinjam</p>
                <p class="text-3xl font-bold text-blue-600">
                    <?= $total_pinjam ?>
                </p>
            </div>
            <div class="bg-white rounded-xl shadow p-6">
                <p class="text-gray-500 text-sm">Total Barang Dikembalikan</p>
                <p class="text-3xl font-bold text-green-600">
                    <?= $total_kembali ?>
                </p>
            </div>
        </div>

        <!-- Detail Transaksi -->
        <div class="bg-white rounded-xl shadow overflow-x-auto">
            <table class="w-full text-sm text-gray-700">
                <thead class="bg-gray-100 text-gray-800">
                    <tr>
                        <th class="p-3 text-left">User</th>
                        <th class="p-3 text-left">Barang</th>
                        <th class="p-3 text-center">Jumlah</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-center">Tgl Pinjam</th>
                        <th class="p-3 text-center">Tgl Kembali</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                <?php while ($d = mysqli_fetch_assoc($transaksi)): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="p-3"><?= htmlspecialchars($d['username']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($d['nama_alat']) ?></td>
                        <td class="p-3 text-center"><?= $d['jumlah'] ?></td>
                        <td class="p-3 text-center"><?= ucfirst($d['status']) ?></td>
                        <td class="p-3 text-center"><?= $d['tanggal_pinjam'] ?></td>
                        <td class="p-3 text-center">
                            <?= $d['tanggal_kembali'] ?: '-' ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Stok -->
        <div class="bg-white rounded-xl shadow overflow-x-auto">
            <div class="p-4 font-semibold text-gray-700">
                Sisa Stok Barang
            </div>
            <table class="w-full text-sm text-gray-700">
                <thead class="bg-gray-100 text-gray-800">
                    <tr>
                        <th class="p-3 text-left">Barang</th>
                        <th class="p-3 text-center">Stok</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                <?php while ($s = mysqli_fetch_assoc($stok)): ?>
                    <tr>
                        <td class="p-3"><?= htmlspecialchars($s['nama_alat']) ?></td>
                        <td class="p-3 text-center font-semibold">
                            <?= $s['stok'] ?>
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