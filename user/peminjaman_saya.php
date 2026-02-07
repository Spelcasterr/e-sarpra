<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'peminjam') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['id'];

$data = mysqli_query($conn, "
    SELECT p.*, a.nama_alat
    FROM peminjaman p
    JOIN alat a ON p.alat_id = a.id
    WHERE p.user_id = $user_id
    ORDER BY p.id DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Peminjaman Saya | E-Sarpras</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

<div class="max-w-6xl mx-auto p-6">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold">Peminjaman Saya</h1>
            <p class="text-gray-600 text-sm mt-1">
                Riwayat dan status peminjaman sarana & prasarana
            </p>
        </div>

        <a href="peminjam.php"
           class="text-sm bg-gray-200 px-5 py-2 rounded-full hover:bg-gray-300">
            ← kembali
        </a>
    </div>

    <!-- TABLE -->
    <div class="bg-white shadow rounded-xl overflow-hidden">

        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">No</th>
                    <th class="px-4 py-3 text-left">Barang</th>
                    <th class="px-4 py-3 text-center">Jumlah</th>
                    <th class="px-4 py-3 text-center">Tgl Pinjam</th>
                    <th class="px-4 py-3 text-center">Tgl Kembali</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y">

            <?php
            $no = 1;
            if (mysqli_num_rows($data) === 0): ?>
                <tr>
                    <td colspan="7" class="text-center py-10 text-gray-500">
                        Belum ada peminjaman
                    </td>
                </tr>
            <?php endif; ?>

            <?php while ($row = mysqli_fetch_assoc($data)): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3"><?= $no++ ?></td>
                    <td class="px-4 py-3 font-medium">
                        <?= htmlspecialchars($row['nama_alat']) ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <?= $row['jumlah'] ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <?= $row['tanggal_pinjam'] ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <?= $row['tanggal_kembali'] ?>
                    </td>

                    <!-- STATUS -->
                    <td class="px-4 py-3 text-center">
                        <?php
                        $status = $row['status'];
                        $badge = match ($status) {
                            'menunggu' => 'bg-orange-100 text-orange-600',
                            'disetujui' => 'bg-green-100 text-green-600',
                            'ditolak' => 'bg-red-100 text-red-600',
                            'menunggu_pengembalian' => 'bg-blue-100 text-blue-600',
                            'dikembalikan' => 'bg-gray-200 text-gray-600',
                            default => 'bg-gray-100 text-gray-600'
                        };
                        ?>
                        <span class="px-3 py-1 rounded-full text-xs font-medium <?= $badge ?>">
                            <?= ucfirst(str_replace('_', ' ', $status)) ?>
                        </span>
                    </td>

                    <!-- AKSI -->
                    <td class="px-4 py-3 text-center">
                        <?php if ($status === 'disetujui'): ?>
                            <a href="ajukan_pengembalian.php?id=<?= $row['id'] ?>"
                               onclick="return confirm('Ajukan pengembalian barang ini?')"
                               class="bg-black text-white px-4 py-1 rounded-full text-xs hover:bg-gray-800">
                                Kembalikan
                            </a>
                        <?php elseif ($status === 'menunggu_pengembalian'): ?>
                            <span class="text-xs text-gray-500">
                                Menunggu petugas
                            </span>
                        <?php elseif ($status === 'dikembalikan'): ?>
                            <span class="text-xs text-gray-500">
                                Selesai
                            </span>
                        <?php else: ?>
                            <span class="text-xs text-gray-400">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>

            </tbody>
        </table>

    </div>

</div>

</body>
</html>
