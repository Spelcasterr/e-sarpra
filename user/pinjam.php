<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'peminjam') {
    header("Location: ../login.php");
    exit;
}

$id_alat = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_alat === 0) {
    header("Location: daftar_alat.php");
    exit;
}

// Prepared statement — validasi alat di server
$stmt = $conn->prepare("SELECT * FROM alat WHERE id = ? AND stok > 0");
$stmt->bind_param("i", $id_alat);
$stmt->execute();
$result = $stmt->get_result();
$data   = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    header("Location: daftar_alat.php?error=Alat+tidak+ditemukan+atau+stok+habis");
    exit;
}

$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ajukan Peminjaman | E-Sarpras</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="w-full max-w-xl bg-white rounded-2xl shadow p-8 relative">

    <!-- BACK -->
    <a href="javascript:history.back()"
       class="absolute top-6 left-6 text-sm text-gray-600 hover:text-black">
        ← kembali
    </a>

    <!-- JUDUL -->
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold">Ajukan Peminjaman</h1>
        <p class="text-gray-600 mt-2">
            Isi formulir di bawah untuk meminjam sarana atau prasarana
        </p>
    </div>

    <!-- PESAN ERROR -->
    <?php if ($error): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-6 text-sm">
        <?= $error ?>
    </div>
    <?php endif; ?>

    <!-- INFO ALAT -->
    <div class="bg-gray-100 rounded-xl p-4 mb-6 flex gap-4 items-center">
        <div class="w-24 h-24 bg-white border rounded flex items-center justify-center flex-shrink-0">
            <img src="../gambar/<?= htmlspecialchars($data['gambar']) ?>"
                 class="max-h-full object-contain">
        </div>

        <div>
            <p class="text-sm text-gray-500">Nama Alat</p>
            <p class="font-semibold text-lg">
                <?= htmlspecialchars($data['nama_alat']) ?>
            </p>
            <p class="text-sm text-gray-500 mt-2">Stok tersedia</p>
            <p class="font-medium text-green-600"><?= (int)$data['stok'] ?> unit</p>
        </div>
    </div>

    <!-- FORM -->
    <form action="proses_pinjam.php" method="post" class="space-y-5"
          onsubmit="return validasiForm()">

        <input type="hidden" name="alat_id" value="<?= (int)$data['id'] ?>">

        <!-- JUMLAH -->
        <div>
            <label class="block text-sm font-medium mb-1">Jumlah Pinjam</label>
            <input type="number"
                   id="jumlah"
                   name="jumlah"
                   min="1"
                   max="<?= (int)$data['stok'] ?>"
                   required
                   class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-black">
            <p class="text-xs text-gray-500 mt-1">
                Maksimal <?= (int)$data['stok'] ?> unit
            </p>
        </div>

        <!-- TANGGAL -->
        <div>
            <label class="block text-sm font-medium mb-1">Tanggal Pengembalian</label>
            <input type="date"
                   id="tanggal_kembali"
                   name="tanggal_kembali"
                   required
                   min="<?= date('Y-m-d') ?>"
                   class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-black">
        </div>

        <!-- SUBMIT -->
        <button type="submit"
                class="w-full bg-black text-white py-3 rounded-full hover:bg-gray-800 transition">
            Ajukan Peminjaman
        </button>

    </form>
</div>

<script>
const stokMax = <?= (int)$data['stok'] ?>;

function validasiForm() {
    const jumlah  = parseInt(document.getElementById('jumlah').value);
    const tgl     = document.getElementById('tanggal_kembali').value;
    const hari_ini = new Date().toISOString().split('T')[0];

    if (isNaN(jumlah) || jumlah < 1) {
        alert('Jumlah pinjam minimal 1.');
        return false;
    }

    if (jumlah > stokMax) {
        alert('Jumlah melebihi stok tersedia (' + stokMax + ' unit).');
        return false;
    }


    return true;
}
</script>

</body>
</html>