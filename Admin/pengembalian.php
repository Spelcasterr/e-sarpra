<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$data = mysqli_query($conn, "
    SELECT p.*, u.username, a.nama_alat
    FROM peminjaman p
    JOIN users u ON p.user_id = u.id
    JOIN alat a ON p.alat_id = a.id
    WHERE p.status = 'dikembalikan'
    ORDER BY p.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>History Pengembalian</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">

<div class="max-w-7xl mx-auto space-y-6">

    <!-- HEADER -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-700">
            History Pengembalian Barang
        </h1>
        <a href="admin.php" class="text-sm text-gray-500 hover:text-blue-600">
            ← Dashboard
        </a>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-200 text-gray-600">
                <tr>
                    <th class="p-3 text-left">No</th>
                    <th class="p-3 text-left">User</th>
                    <th class="p-3 text-left">Barang</th>
                    <th class="p-3 text-center">Jumlah</th>
                    <th class="p-3 text-left">Tgl Pinjam</th>
                    <th class="p-3 text-left">Tgl Kembali</th>
                    <th class="p-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php
                $no = 1;
                if (mysqli_num_rows($data) == 0):
                ?>
                    <tr>
                        <td colspan="7" class="p-6 text-center text-gray-500">
                            Belum ada data pengembalian
                        </td>
                    </tr>
                <?php
                endif;

                while ($d = mysqli_fetch_assoc($data)):
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="p-3"><?= $no++ ?></td>
                    <td class="p-3"><?= htmlspecialchars($d['username']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($d['nama_alat']) ?></td>
                    <td class="p-3 text-center"><?= $d['jumlah'] ?></td>
                    <td class="p-3"><?= $d['tanggal_pinjam'] ?></td>
                    <td class="p-3"><?= $d['tanggal_kembali'] ?></td>
                    <td class="p-3 text-center">
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                            Dikembalikan
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- FOOTER -->
    <div class="flex justify-end">
        <a href="../logout.php"
           class="text-sm text-red-600 hover:underline">
            Logout
        </a>
    </div>

</div>

</body>
</html>
