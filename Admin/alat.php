<?php
session_start();
include '../koneksi.php';
include '../log_helper.php';

/* =====================
   CEK LOGIN & ROLE
===================== */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$userQ = mysqli_query($conn,"SELECT username, role FROM users WHERE id='$user_id'");
$userData = mysqli_fetch_assoc($userQ);

$username = $userData['username'];
$role     = $userData['role'];
$initial  = strtoupper(substr($username,0,1));

$edit = false;

/* =================
   DELETE DATA
================= */
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];

    // Ambil nama alat untuk log
    $ambilAlat = mysqli_query($conn, "SELECT nama_alat FROM alat WHERE id=$id");
    $dataAlat  = mysqli_fetch_assoc($ambilAlat);

    // Cek peminjaman yang masih aktif
    $cek = mysqli_query($conn, "
        SELECT id FROM peminjaman 
        WHERE alat_id = $id 
        AND status NOT IN ('dikembalikan', 'ditolak')
    ");

    if (mysqli_num_rows($cek) > 0) {
        echo "<script>
            alert('Alat masih sedang dipinjam!');
            window.location='alat.php';
        </script>";
        exit;
    }

    // Hapus riwayat peminjaman dulu
    mysqli_query($conn, "DELETE FROM peminjaman WHERE alat_id = $id");

    // Baru hapus alat
    mysqli_query($conn, "DELETE FROM alat WHERE id = $id");

    tambah_log(
        $conn,
        $user_id,
        $username,
        $role,
        "Hapus Alat",
        "Menghapus alat: " . $dataAlat['nama_alat']
    );

    header("Location: alat.php");
    exit;
}

/* =================
   MODE EDIT
================= */
if (isset($_GET['edit'])) {
    $edit = true;
    $id_edit = (int)$_GET['edit'];

    $ambil = mysqli_query($conn,"SELECT * FROM alat WHERE id=$id_edit");
    $data_edit = mysqli_fetch_assoc($ambil);
}

/* =================
   SIMPAN DATA
================= */
if (isset($_POST['simpan'])) {

    $nama     = $_POST['nama'];
    $kategori = $_POST['kategori'];
    $stok     = $_POST['stok'];

    $gambar = $_FILES['gambar']['name'];
    $tmp    = $_FILES['gambar']['tmp_name'];

    if ($gambar != "") {
        $gambar = time()."_".$gambar;
        move_uploaded_file($tmp,"../gambar/".$gambar);
    }

    mysqli_query($conn,"INSERT INTO alat 
        (nama_alat, kategori_id, stok, gambar)
        VALUES ('$nama', '$kategori', '$stok', '$gambar')
    ");

    tambah_log(
        $conn,
        $user_id,
        $username,
        $role,
        "Tambah Alat",
        "Menambahkan alat: " . $nama
    );

    header("Location: alat.php");
    exit;
}

/* =================
   UPDATE DATA
================= */
if (isset($_POST['update'])) {

    $id          = $_POST['id'];
    $nama        = $_POST['nama'];
    $kategori    = $_POST['kategori'];
    $stok        = $_POST['stok'];
    $gambar_lama = $_POST['gambar_lama'];

    if ($_FILES['gambar']['name'] != "") {
        $gambar = time()."_".$_FILES['gambar']['name'];
        move_uploaded_file($_FILES['gambar']['tmp_name'],"../gambar/".$gambar);

        if ($gambar_lama != "" && file_exists("../gambar/".$gambar_lama)) {
            unlink("../gambar/".$gambar_lama);
        }
    } else {
        $gambar = $gambar_lama;
    }

    mysqli_query($conn,"UPDATE alat SET
        nama_alat   = '$nama',
        kategori_id = '$kategori',
        stok        = '$stok',
        gambar      = '$gambar'
        WHERE id    = '$id'
    ");

    tambah_log(
        $conn,
        $user_id,
        $username,
        $role,
        "Edit Alat",
        "Mengedit alat: " . $nama
    );

    header("Location: alat.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Alat</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex h-screen overflow-hidden">

<!-- SIDEBAR -->
<div class="w-64 h-screen bg-gradient-to-b from-[#1565C0] to-[#0D47A1] text-white p-6 flex flex-col justify-between flex-shrink-0 overflow-y-auto">
    <div>

        <div class="mb-8">
            <h2 class="text-2xl font-bold">Admin Panel</h2>
            <p class="text-sm text-white/80">Manajemen Peminjaman</p>
        </div>

        <div class="bg-white/10 rounded-xl p-4 flex items-center space-x-3 mb-8">
            <div class="w-12 h-12 rounded-full bg-white/30 flex items-center justify-center text-lg font-bold">
                <?= $initial ?>
            </div>
            <div>
                <p class="font-semibold"><?= htmlspecialchars($username) ?></p>
                <p class="text-xs text-white/70 capitalize"><?= htmlspecialchars($role) ?></p>
            </div>
        </div>

        <div class="space-y-1 text-sm">

            <a href="admin.php" class="flex items-center gap-3 text-white/80 hover:bg-white/10 hover:text-white px-4 py-2.5 rounded-xl transition-all font-sora">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                <span>Dashboard</span>
            </a>

            <a href="data_user.php" class="flex items-center gap-3 text-white/80 hover:bg-white/10 hover:text-white px-4 py-2.5 rounded-xl transition-all font-sora">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0-3-3.85"/></svg>
                <span>Daftar User</span>
            </a>

            <a href="alat.php" class="flex items-center gap-3 bg-white text-[#1565C0] px-4 py-2.5 rounded-xl font-sora font-semibold shadow-sm">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                <span>Daftar Alat</span>
            </a>

            <a href="kategori.php" class="flex items-center gap-3 text-white/80 hover:bg-white/10 hover:text-white px-4 py-2.5 rounded-xl transition-all font-sora">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                <span>Daftar Kategori</span>
            </a>

            <div class="border-t border-white/15 my-2"></div>

            <a href="data_peminjaman.php" class="flex items-center gap-3 text-white/80 hover:bg-white/10 hover:text-white px-4 py-2.5 rounded-xl transition-all font-sora">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                <span>Data Peminjaman</span>
            </a>

            <a href="pengembalian.php" class="flex items-center gap-3 text-white/80 hover:bg-white/10 hover:text-white px-4 py-2.5 rounded-xl transition-all font-sora">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 14 4 19 9 24"/><path d="M20 9a9 9 0 0 0-9-9H4"/><path d="M4 19h11a9 9 0 0 0 0-18"/></svg>
                <span>Data Pengembalian</span>
            </a>

            <a href="log_aktivitas.php" class="flex items-center gap-3 text-white/80 hover:bg-white/10 hover:text-white px-4 py-2.5 rounded-xl transition-all font-sora">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                <span>Log Aktivitas</span>
            </a>

        </div>
    </div>

    <a href="../logout.php"
       class="block bg-red-500 text-center py-2 rounded-lg hover:bg-red-600 font-medium">
       Logout
    </a>
</div>

<!-- MAIN -->
<div class="flex-1 p-8 space-y-8 overflow-y-auto">

    <h1 class="text-2xl font-bold text-gray-700">
        <?= $edit ? "Edit Alat" : "Manajemen Alat" ?>
    </h1>

    <!-- FORM -->
    <div class="bg-white p-6 rounded shadow">
        <form method="post" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <?php if($edit): ?>
                <input type="hidden" name="id" value="<?= $data_edit['id'] ?>">
                <input type="hidden" name="gambar_lama" value="<?= $data_edit['gambar'] ?>">
            <?php endif; ?>

            <div>
                <label class="block text-sm mb-1">Nama Alat</label>
                <input type="text" name="nama"
                    value="<?= $edit ? htmlspecialchars($data_edit['nama_alat']) : '' ?>"
                    class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm mb-1">Kategori</label>
                <select name="kategori" class="w-full border rounded px-3 py-2" required>
                    <?php
                    $k = mysqli_query($conn,"SELECT * FROM kategori");
                    while($d = mysqli_fetch_assoc($k)){
                    ?>
                    <option value="<?= $d['id'] ?>"
                        <?= ($edit && $d['id']==$data_edit['kategori_id'])?'selected':'' ?>>
                        <?= $d['nama_kategori'] ?>
                    </option>
                    <?php } ?>
                </select>
            </div>

            <div>
                <label class="block text-sm mb-1">Stok</label>
                <input type="number" name="stok"
                    value="<?= $edit ? $data_edit['stok'] : '' ?>"
                    class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm mb-1">Gambar</label>
                <input type="file" name="gambar" class="w-full">
                <?php if($edit && $data_edit['gambar']!=""): ?>
                    <img src="../gambar/<?= $data_edit['gambar'] ?>" class="mt-2 w-24 rounded">
                <?php endif; ?>
            </div>

            <div class="md:col-span-2">
                <button type="submit"
                    name="<?= $edit ? 'update' : 'simpan' ?>"
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    <?= $edit ? "Update" : "Simpan" ?>
                </button>
            </div>
        </form>
    </div>

    <!-- TABLE -->
    <div class="bg-white p-6 rounded shadow overflow-x-auto">
        <h2 class="text-lg font-semibold mb-4">Data Alat</h2>

        <table class="w-full text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-3">No</th>
                    <th class="p-3">Nama</th>
                    <th class="p-3">Kategori</th>
                    <th class="p-3">Stok</th>
                    <th class="p-3">Gambar</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
            <?php
            $no   = 1;
            $data = mysqli_query($conn,"
                SELECT alat.*, kategori.nama_kategori
                FROM alat
                JOIN kategori ON alat.kategori_id = kategori.id
            ");
            while($d = mysqli_fetch_assoc($data)){
            ?>
                <tr class="text-center hover:bg-gray-50">
                    <td class="p-3"><?= $no++ ?></td>
                    <td class="p-3"><?= htmlspecialchars($d['nama_alat']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($d['nama_kategori']) ?></td>
                    <td class="p-3"><?= $d['stok'] ?></td>
                    <td class="p-3">
                        <img src="../gambar/<?= $d['gambar'] ?>" class="w-20 mx-auto rounded">
                    </td>
                    <td class="p-3">
                        <a href="?edit=<?= $d['id'] ?>" class="text-blue-600 hover:underline">Edit</a> |
                        <a href="?hapus=<?= $d['id'] ?>"
                           onclick="return confirm('Yakin hapus data?')"
                           class="text-red-600 hover:underline">Hapus</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>