<?php
session_start();
include 'koneksi.php';

/* ======================
   CEK LOGIN (TIDAK MEMBLOKIR)
====================== */
$sudah_login = isset($_SESSION['role']) && $_SESSION['role'] === 'peminjam';

/* ======================
   KATEGORI AKTIF
====================== */
$kategori_aktif = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;

/* ======================
   DATA KATEGORI
====================== */
$kategori = mysqli_query($conn, "SELECT * FROM kategori");

/* ======================
   DATA ALAT (UNTUK SLIDER)
====================== */
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
</head>

<body class="bg-white min-h-screen">

<div class="max-w-6xl mx-auto p-6 relative">

    <!-- LOGIN -->
    <a href="login.php"
       class="absolute top-6 right-6 bg-gray-200 hover:bg-gray-300 px-6 py-2 rounded-full text-sm">
        login
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

        <a href="index.php"
           class="px-6 py-2 rounded-full text-sm
           <?= $kategori_aktif === 0 ? 'bg-black text-white' : 'bg-gray-200' ?>">
            Semua
        </a>

        <?php while ($k = mysqli_fetch_assoc($kategori)) { ?>
            <a href="index.php?kategori=<?= $k['id'] ?>"
               class="px-6 py-2 rounded-full text-sm
               <?= $kategori_aktif === (int)$k['id']
                    ? 'bg-black text-white'
                    : 'bg-gray-200' ?>">
                <?= htmlspecialchars($k['nama_kategori']) ?>
            </a>
        <?php } ?>

    </div>

    <!-- SLIDER -->
    <div class="relative">

        <!-- PANAH KIRI -->
        <button onclick="scrollSlider(-1)"
            class="absolute left-0 top-1/2 -translate-y-1/2 z-10
                   bg-black text-white w-10 h-10 rounded-full">
            ‹
        </button>

        <!-- PANAH KANAN -->
        <button onclick="scrollSlider(1)"
            class="absolute right-0 top-1/2 -translate-y-1/2 z-10
                   bg-black text-white w-10 h-10 rounded-full">
            ›
        </button>

        <!-- CONTAINER -->
        <div id="slider"
             class="flex gap-6 overflow-x-auto scroll-smooth px-12 py-4">

            <?php if (mysqli_num_rows($alat) === 0) { ?>
                <p class="text-gray-500">Tidak ada alat tersedia</p>
            <?php } ?>

            <?php while ($a = mysqli_fetch_assoc($alat)) { ?>
            <div class="min-w-[260px] bg-gray-200 rounded-2xl p-6 flex-shrink-0 flex flex-col items-center">

                <div class="bg-white w-40 h-40 flex items-center justify-center border mb-4">
                    <img src="gambar/<?= htmlspecialchars($a['gambar']) ?>"
                         class="max-h-full">
                </div>

                <div class="w-full text-left mb-6">
                    <p class="text-sm text-gray-600">nama</p>
                    <p class="font-semibold"><?= htmlspecialchars($a['nama_alat']) ?></p>
                    <p class="text-sm text-gray-600 mt-2">stok</p>
                    <p><?= $a['stok'] ?></p>
                </div>

            </div>
            <?php } ?>

        </div>
    </div>

</div>

<!-- SCRIPT -->
<script>
function scrollSlider(direction) {
    const slider = document.getElementById('slider');
    slider.scrollBy({
        left: direction * 300,
        behavior: 'smooth'
    });
}

function harusLogin() {
    alert('Silakan login terlebih dahulu untuk melakukan peminjaman');
}
</script>

</body>
</html>
