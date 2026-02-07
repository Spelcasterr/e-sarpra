<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'peminjam') {
    header("Location: ../login.php");
    exit;
}

$kategori_aktif = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;

$data_kategori = mysqli_query($conn, "SELECT * FROM kategori");

$query_alat = "
    SELECT alat.*, kategori.nama_kategori
    FROM alat
    JOIN kategori ON alat.kategori_id = kategori.id
    WHERE alat.stok > 0
";

if ($kategori_aktif > 0) {
    $query_alat .= " AND alat.kategori_id = $kategori_aktif";
}

$data_alat = mysqli_query($conn, $query_alat);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>E-Sarpra | Dashboard Peminjam</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<div class="max-w-7xl mx-auto p-6">

    <!-- HEADER -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">
            E-Sarpra
        </h1>
        <p class="text-gray-600 mt-2 max-w-2xl">
            Sistem peminjaman sarana dan prasarana sekolah yang memudahkan
            pengguna melihat ketersediaan alat, melakukan peminjaman,
            dan memantau status peminjaman secara online.
        </p>
    </div>

    <!-- ACTION BAR -->
    <div class="flex justify-between items-center mb-6">
    <div class="flex items-center gap-3">
        <a href="javascript:history.back()"
           class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded">
            ← Kembali
        </a>

        <h2 class="text-xl font-semibold text-gray-700">
            Kategori Alat
        </h2>
    </div>

    <div class="space-x-2">
        <a href="peminjaman_saya.php"
           class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
            Peminjaman Saya
        </a>
        <a href="../logout.php"
           class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
            Logout
        </a>
    </div>
</div>


    <!-- FILTER KATEGORI -->
    <div class="flex flex-wrap gap-2 mb-8">
        <a href="daftar_alat.php"
           class="px-4 py-2 rounded border
           <?= $kategori_aktif === 0
               ? 'bg-blue-600 text-white border-blue-600'
               : 'bg-white text-gray-700 hover:bg-gray-50' ?>">
            Semua
        </a>

        <?php while ($k = mysqli_fetch_assoc($data_kategori)) { ?>
            <a href="daftar_alat.php?kategori=<?= $k['id'] ?>"
               class="px-4 py-2 rounded border
               <?= $kategori_aktif === (int)$k['id']
                   ? 'bg-blue-600 text-white border-blue-600'
                   : 'bg-white text-gray-700 hover:bg-gray-50' ?>">
                <?= htmlspecialchars($k['nama_kategori']) ?>
            </a>
        <?php } ?>
    </div>

    <!-- TABLE -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-700">
                Daftar Alat Tersedia
            </h3>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">No</th>
                    <th class="px-4 py-3 text-left">Nama Alat</th>
                    <th class="px-4 py-3 text-left">Kategori</th>
                    <th class="px-4 py-3 text-center">Stok</th>
                    <th class="px-4 py-3 text-center">Gambar</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">

            <?php
            $no = 1;
            if (mysqli_num_rows($data_alat) === 0): ?>
                <tr>
                    <td colspan="6" class="text-center py-8 text-gray-500">
                        Tidak ada alat tersedia
                    </td>
                </tr>
            <?php endif; ?>

            <?php while ($a = mysqli_fetch_assoc($data_alat)) { ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3"><?= $no++ ?></td>
                    <td class="px-4 py-3 font-medium text-gray-800">
                        <?= htmlspecialchars($a['nama_alat']) ?>
                    </td>
                    <td class="px-4 py-3">
                        <?= htmlspecialchars($a['nama_kategori']) ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <?= $a['stok'] ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <img src="../gambar/<?= htmlspecialchars($a['gambar']) ?>"
                             class="w-20 mx-auto rounded shadow">
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="pinjam.php?id=<?= $a['id'] ?>"
                           class="bg-green-500 hover:bg-green-600 text-white px-4 py-1 rounded">
                            Pinjam
                        </a>
                    </td>
                </tr>
            <?php } ?>

            </tbody>
        </table>
    </div>

</div>

</body>
</html>
