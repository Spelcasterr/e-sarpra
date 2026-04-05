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

$nama_user = htmlspecialchars($_SESSION['nama'] ?? 'Peminjam');
$inisial    = strtoupper(substr($nama_user, 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Sarpras — Peminjam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'blue-deep':  '#0D47A1',
                        'blue-main':  '#1565C0',
                        'blue-mid':   '#1976D2',
                        'blue-light': '#42A5F5',
                        'blue-pale':  '#E3F2FD',
                    },
                    fontFamily: {
                        sora: ['Sora', 'sans-serif'],
                        dm:   ['DM Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'DM Sans', sans-serif; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { scrollbar-width: none; }
        .card { transition: transform .25s, box-shadow .25s; }
        .card:hover { transform: translateY(-6px); }
        .card:hover .card-img { transform: scale(1.07); }
        .card-img { transition: transform .3s; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen">

<!-- ── NAVBAR ── -->
<nav class="bg-[#1565C0] sticky top-0 z-50 shadow-lg">
    <div class="max-w-6xl mx-auto px-7 h-[68px] flex items-center justify-between">

        <a href="peminjam.php" class="flex items-center gap-3 no-underline">
            <div class="w-[38px] h-[38px] bg-white/20 rounded-xl flex items-center justify-center font-sora font-extrabold text-base text-white">
                ES
            </div>
            <div>
                <div class="font-sora font-bold text-xl text-white tracking-tight">E-Sarpras</div>
                <div class="text-[11px] text-white/60 font-light tracking-wide">Portal Peminjam</div>
            </div>
        </a>

        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 bg-white/15 border border-white/25 rounded-full px-4 py-1.5">
                <div class="w-7 h-7 rounded-full bg-white/30 flex items-center justify-center font-sora font-bold text-xs text-white">
                    <?= $inisial ?>
                </div>
                <span class="text-sm font-semibold text-white font-sora"><?= $nama_user ?></span>
            </div>
            <a href="../logout.php"
               class="bg-red-600 hover:bg-red-700 text-white font-sora font-semibold text-sm px-5 py-2 rounded-full transition-colors no-underline">
                Logout ⎋
            </a>
        </div>
    </div>
</nav>

<!-- ── HERO ── -->
<section class="bg-gradient-to-br from-[#0D47A1] via-[#1976D2] to-[#42A5F5] px-7 py-14 overflow-hidden relative">
    <div class="absolute w-72 h-72 bg-white/10 rounded-full blur-3xl -bottom-20 -left-10 pointer-events-none"></div>
    <div class="absolute w-48 h-48 bg-yellow-400/15 rounded-full blur-3xl -top-10 right-24 pointer-events-none"></div>

    <div class="max-w-6xl mx-auto relative flex flex-wrap items-end justify-between gap-6">
        <div>
            <div class="inline-flex items-center gap-2 bg-white/15 border border-white/25 rounded-full px-4 py-1 text-xs text-white/90 font-medium tracking-wide mb-5">
                👋 &nbsp;Selamat datang, <?= $nama_user ?>!
            </div>
            <h1 class="font-sora font-extrabold text-4xl md:text-5xl text-white tracking-tight leading-tight mb-3">
                Pinjam Alat<br><span class="text-yellow-400">Dengan Mudah</span>
            </h1>
            <p class="text-white/70 text-[15px] max-w-md leading-relaxed">
                Temukan dan pinjam peralatan sekolah yang kamu butuhkan kapan saja.
            </p>
        </div>

        <div class="flex gap-3 flex-wrap self-end">
            <a href="peminjaman_saya.php"
               class="bg-white/15 border border-white/35 text-white font-sora font-semibold text-sm px-6 py-2.5 rounded-full transition-all hover:bg-white hover:text-[#1565C0] no-underline">
                📋 Peminjaman Saya
            </a>
        </div>
    </div>
</section>

<!-- ── KATEGORI TABS ── -->
<div class="max-w-6xl mx-auto px-7 pt-8">
    <p class="font-sora font-semibold text-[11px] tracking-[1.5px] uppercase text-slate-400 mb-3">
        Filter Kategori
    </p>
    <div class="flex gap-2.5 flex-wrap">
        <a href="peminjam.php"
           class="px-5 py-2 rounded-full text-[13.5px] font-semibold font-sora border-[1.5px] no-underline transition-all
           <?= $kategori_aktif === 0
               ? 'bg-[#1565C0] text-white border-[#1565C0] shadow-md'
               : 'bg-white text-slate-500 border-slate-200 hover:bg-[#E3F2FD] hover:border-[#42A5F5] hover:text-[#1565C0]' ?>">
            Semua Alat
        </a>
        <?php
        mysqli_data_seek($kategori, 0);
        while ($k = mysqli_fetch_assoc($kategori)):
        ?>
        <a href="peminjam.php?kategori=<?= $k['id'] ?>"
           class="px-5 py-2 rounded-full text-[13.5px] font-semibold font-sora border-[1.5px] no-underline transition-all
           <?= $kategori_aktif === (int)$k['id']
               ? 'bg-[#1565C0] text-white border-[#1565C0] shadow-md'
               : 'bg-white text-slate-500 border-slate-200 hover:bg-[#E3F2FD] hover:border-[#42A5F5] hover:text-[#1565C0]' ?>">
            <?= htmlspecialchars($k['nama_kategori']) ?>
        </a>
        <?php endwhile; ?>
    </div>
</div>

<!-- ── ALAT SECTION ── -->
<div class="max-w-6xl mx-auto px-7 pt-7 pb-16">

    <div class="flex items-center justify-between mb-6">
        <h2 class="font-sora font-bold text-[22px] text-slate-800 tracking-tight">
            Alat <span class="text-[#1565C0]">Tersedia</span>
        </h2>
        <div class="flex items-center gap-3">
            <a href="daftar_alat.php<?= $kategori_aktif ? '?kategori='.$kategori_aktif : '' ?>"
               class="text-[#1976D2] hover:text-[#0D47A1] font-sora font-semibold text-sm transition-colors no-underline">
                Lihat semua →
            </a>
            <div class="flex gap-2">
                <button onclick="slideLeft()"
                    class="w-10 h-10 rounded-full bg-white text-[#1565C0] shadow-md hover:bg-[#1565C0] hover:text-white transition-all text-lg flex items-center justify-center">
                    ‹
                </button>
                <button onclick="slideRight()"
                    class="w-10 h-10 rounded-full bg-white text-[#1565C0] shadow-md hover:bg-[#1565C0] hover:text-white transition-all text-lg flex items-center justify-center">
                    ›
                </button>
            </div>
        </div>
    </div>

    <!-- Slider -->
    <div id="slider" class="flex gap-[18px] overflow-x-auto scrollbar-hide pb-3 scroll-smooth">

        <?php if (mysqli_num_rows($alat) === 0): ?>
        <div class="min-w-full text-center py-16 text-slate-400">
            <div class="text-5xl mb-4 opacity-50">📦</div>
            <p>Tidak ada alat pada kategori ini.</p>
        </div>
        <?php endif; ?>

        <?php while ($a = mysqli_fetch_assoc($alat)): ?>
        <div class="card min-w-[220px] flex-shrink-0 bg-white rounded-2xl overflow-hidden border-[1.5px] border-slate-200 shadow-sm hover:border-[#42A5F5] hover:shadow-xl flex flex-col">

            <div class="bg-[#E3F2FD] h-40 flex items-center justify-center relative overflow-hidden">
                <img src="../gambar/<?= htmlspecialchars($a['gambar']) ?>"
                     alt="<?= htmlspecialchars($a['nama_alat']) ?>"
                     class="card-img max-h-28 max-w-28 object-contain">
                <span class="absolute top-2.5 right-2.5 bg-[#1565C0] text-white font-sora font-semibold text-[10px] px-2.5 py-0.5 rounded-full">
                    Tersedia
                </span>
            </div>

            <div class="p-4 flex flex-col flex-1">
                <p class="text-[11px] text-[#42A5F5] font-semibold font-sora uppercase tracking-widest mb-1">
                    <?= htmlspecialchars($a['nama_kategori']) ?>
                </p>
                <p class="font-sora font-bold text-[15px] text-slate-800 leading-tight mb-2 flex-1">
                    <?= htmlspecialchars($a['nama_alat']) ?>
                </p>
                <div class="flex items-center gap-1.5 mb-4">
                    <span class="w-2 h-2 rounded-full bg-green-500 ring-2 ring-green-200"></span>
                    <span class="text-xs text-slate-500">Stok: <strong class="text-slate-700"><?= $a['stok'] ?> unit</strong></span>
                </div>
                <a href="pinjam.php?id=<?= $a['id'] ?>"
                   class="block text-center bg-[#1565C0] hover:bg-[#0D47A1] text-white font-sora font-semibold text-[13px] py-2.5 rounded-xl no-underline transition-colors shadow-md">
                    Pinjam Sekarang
                </a>
            </div>
        </div>
        <?php endwhile; ?>

    </div>
</div>

<!-- ── FOOTER ── -->
<footer class="bg-[#0D47A1] text-white/50 text-center py-5 text-[13px]">
    <strong class="text-white font-sora">E-Sarpras</strong>
    &nbsp;·&nbsp; Sistem Peminjaman Sarana &amp; Prasarana Sekolah
</footer>

<script>
function slideLeft()  { document.getElementById('slider').scrollBy({ left: -260, behavior: 'smooth' }); }
function slideRight() { document.getElementById('slider').scrollBy({ left:  260, behavior: 'smooth' }); }
</script>
</body>
</html>
