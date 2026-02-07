<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'peminjam') {
    header("Location: ../login.php");
    exit;
}

$kategori_aktif = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;

$kategori = mysqli_query($conn, "SELECT * FROM kategori");

$query = "
    SELECT alat.*, kategori.nama_kategori
    FROM alat
    JOIN kategori ON alat.kategori_id = kategori.id
    WHERE alat.stok > 0
";

if ($kategori_aktif > 0) {
    $query .= " AND alat.kategori_id = $kategori_aktif";
}

$query .= " ORDER BY alat.id DESC";
$alat = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>E-Sarpras</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { scrollbar-width: none; }
    </style>
</head>

<body class="bg-white min-h-screen">

<div class="max-w-6xl mx-auto p-6 relative">

    <!-- LOGOUT -->
    <a href="../logout.php"
       class="absolute top-6 right-6 bg-gray-200 px-6 py-2 rounded-full text-sm">
        logout
    </a>

    <!-- JUDUL -->
    <div class="text-center mt-10 mb-12">
        <h1 class="text-4xl font-bold">E-Sarpras</h1>
        <p class="text-lg text-gray-600 mt-2">
            Sistem peminjaman sarana dan prasarana sekolah
        </p>
    </div>

    <!-- KATEGORI -->
    <div class="flex justify-center gap-4 mb-10 flex-wrap">
        <a href="peminjam.php"
           class="px-6 py-2 rounded-full text-sm
           <?= $kategori_aktif === 0 ? 'bg-black text-white' : 'bg-gray-200' ?>">
            Semua
        </a>

        <?php while ($k = mysqli_fetch_assoc($kategori)) { ?>
            <a href="peminjam.php?kategori=<?= $k['id'] ?>"
               class="px-6 py-2 rounded-full text-sm
               <?= $kategori_aktif === (int)$k['id']
                   ? 'bg-black text-white'
                   : 'bg-gray-200' ?>">
                <?= htmlspecialchars($k['nama_kategori']) ?>
            </a>
        <?php } ?>
    </div>

    <!-- LIHAT SEMUA -->
    <div class="text-right mb-4">
        <a href="daftar_alat.php<?= $kategori_aktif ? '?kategori='.$kategori_aktif : '' ?>"
           class="text-sm text-gray-700">
            Lihat semua nya
        </a>
    </div>

    <!-- SLIDER WRAPPER -->
    <div class="relative">

        <!-- PANAH KIRI -->
        <button onclick="slideLeft()"
            class="absolute -left-4 top-1/2 -translate-y-1/2
                   bg-black text-white w-10 h-10 rounded-full z-10">
            ‹
        </button>

        <!-- PANAH KANAN -->
        <button onclick="slideRight()"
            class="absolute -right-4 top-1/2 -translate-y-1/2
                   bg-black text-white w-10 h-10 rounded-full z-10">
            ›
        </button>

        <!-- SLIDER -->
        <div id="slider"
             class="overflow-x-auto scrollbar-hide">
            <div class="flex gap-8 pb-4">

                <?php if (mysqli_num_rows($alat) === 0) { ?>
                    <p class="text-gray-500">
                        Tidak ada alat pada kategori ini
                    </p>
                <?php } ?>

                <?php while ($a = mysqli_fetch_assoc($alat)) { ?>
                    <div class="min-w-[260px] bg-gray-200 rounded-2xl
                                p-6 flex flex-col items-center">

                        <div class="bg-white w-40 h-40 flex items-center justify-center
                                    border mb-4">
                            <img src="../gambar/<?= htmlspecialchars($a['gambar']) ?>"
                                 class="max-h-full object-contain">
                        </div>

                        <div class="w-full text-left mb-6">
                            <p class="text-sm text-gray-600">nama</p>
                            <p class="font-semibold">
                                <?= htmlspecialchars($a['nama_alat']) ?>
                            </p>

                            <p class="text-sm text-gray-600 mt-2">stok</p>
                            <p><?= $a['stok'] ?></p>
                        </div>

                        <a href="pinjam.php?id=<?= $a['id'] ?>"
                           class="bg-white px-6 py-2 rounded-full text-sm">
                            pinjam
                        </a>
                    </div>
                <?php } ?>

            </div>
        </div>
    </div>

    <!-- PEMINJAMAN SAYA -->
    <div class="text-right mt-10">
        <a href="peminjaman_saya.php"
           class="bg-gray-200 px-6 py-2 rounded-full text-sm">
            peminjaman saya
        </a>
    </div>

</div>

<!-- SCRIPT SLIDER -->
<script>
function slideLeft() {
    document.getElementById('slider')
        .scrollBy({ left: -300, behavior: 'smooth' });
}

function slideRight() {
    document.getElementById('slider')
        .scrollBy({ left: 300, behavior: 'smooth' });
}
</script>

</body>
</html>
