<?php
session_start();
include '../koneksi.php';
include '../config/log.php';
include '../config/auto_log.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];
$initial = strtoupper(substr($username, 0, 1));

/* =================
   CEK MODE EDIT
================= */
$edit = false;
$data_edit = null;

if (isset($_GET['edit'])) {
    $edit = true;
    $id_edit = $_GET['edit'];

    $ambil = mysqli_query($conn, "SELECT * FROM kategori WHERE id='$id_edit'");
    $data_edit = mysqli_fetch_assoc($ambil);
}

/* =================
   SIMPAN DATA
================= */
if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];

    mysqli_query($conn, "INSERT INTO kategori (nama_kategori) VALUES ('$nama')");
    simpanLog($conn, 'CRUD Kategori', 'Menambah kategori: ' . $nama);

    header("Location: kategori.php");
    exit;
}

/* =================
   UPDATE DATA
================= */
if (isset($_POST['update'])) {
    $id   = $_POST['id'];
    $nama = $_POST['nama'];

    mysqli_query($conn, "UPDATE kategori SET nama_kategori='$nama' WHERE id='$id'");
    simpanLog($conn, 'CRUD Kategori', 'Mengedit kategori: ' . $nama);

    header("Location: kategori.php");
    exit;
}

/* =================
   DELETE DATA
================= */
if (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];

    $cek = mysqli_query($conn, "SELECT * FROM alat WHERE kategori_id='$id_hapus'");
    
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>
                alert('Kategori tidak bisa dihapus karena masih ada barang!');
                window.location='kategori.php';
              </script>";
        exit;
    } else {
        mysqli_query($conn, "DELETE FROM kategori WHERE id='$id_hapus'");
        simpanLog($conn, 'CRUD Kategori', 'Menghapus kategori ID: ' . $id_hapus);

        header("Location: kategori.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Kategori</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="flex">

    <!-- SIDEBAR -->
    <div class="w-64 min-h-screen bg-gradient-to-b from-[#1565C0] to-[#0D47A1] text-white p-6 flex flex-col justify-between">

        <div>
            <div class="mb-8">
                <h2 class="text-2xl font-bold">Admin Panel</h2>
                <p class="text-sm text-white/80">Manajemen Peminjaman</p>
            </div>

            <!-- PROFILE -->
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

            <!-- MENU -->
            <div class="space-y-1 text-sm">

    <a href="admin.php" class="flex items-center gap-3 text-white/80 hover:bg-white/10 hover:text-white px-4 py-2.5 rounded-xl transition-all font-sora">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        <span>Dashboard</span>
    </a>

    <a href="data_user.php" class="flex items-center gap-3 text-white/80 hover:bg-white/10 hover:text-white px-4 py-2.5 rounded-xl transition-all font-sora">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0-3-3.85"/></svg>
        <span>Daftar User</span>
    </a>

    <a href="alat.php" class="flex items-center gap-3 text-white/80 hover:bg-white/10 hover:text-white px-4 py-2.5 rounded-xl transition-all font-sora">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
        <span>Daftar Alat</span>
    </a>

    <a href="kategori.php" class="flex items-center gap-3 bg-white text-[#1565C0] px-4 py-2.5 rounded-xl font-sora font-semibold shadow-sm">
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

        <div class="text-xs text-white/60">
            © <?= date('Y') ?> Sistem Peminjaman
        </div>
    </div>

    <!-- CONTENT -->
    <div class="flex-1 p-8">

        <h1 class="text-2xl font-bold mb-6">Manajemen Kategori</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- FORM -->
            <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="font-semibold mb-4">
                    <?= $edit ? 'Edit Kategori' : 'Tambah Kategori' ?>
                </h2>

                <form method="post" class="space-y-4">
                    <?php if ($edit): ?>
                        <input type="hidden" name="id" value="<?= $data_edit['id'] ?>">
                    <?php endif; ?>

                    <input type="text"
                           name="nama"
                           required
                           placeholder="Nama kategori"
                           value="<?= $edit ? $data_edit['nama_kategori'] : '' ?>"
                           class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-300">

                    <button type="submit"
                            name="<?= $edit ? 'update' : 'simpan' ?>"
                            class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                        <?= $edit ? 'Update' : 'Simpan' ?>
                    </button>
                </form>
            </div>

            <!-- TABEL -->
            <div class="md:col-span-2 bg-white p-6 rounded-xl shadow">
                <h2 class="font-semibold mb-4">Daftar Kategori</h2>

                <table class="w-full border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="border px-3 py-2 text-left">No</th>
                            <th class="border px-3 py-2 text-left">Nama</th>
                            <th class="border px-3 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $no = 1;
                    $data = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id DESC");

                    if (mysqli_num_rows($data) == 0):
                    ?>
                        <tr>
                            <td colspan="3" class="text-center py-4 text-gray-500">
                                Belum ada kategori
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php while ($d = mysqli_fetch_assoc($data)): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="border px-3 py-2"><?= $no++ ?></td>
                            <td class="border px-3 py-2"><?= $d['nama_kategori'] ?></td>
                            <td class="border px-3 py-2 text-center space-x-2">
                                <a href="?edit=<?= $d['id'] ?>"
                                   class="text-blue-600 hover:underline text-sm">
                                   Edit
                                </a>
                                <a href="?hapus=<?= $d['id'] ?>"
                                   onclick="return confirm('Yakin ingin menghapus?')"
                                   class="text-red-600 hover:underline text-sm">
                                   Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

</body>
</html>
