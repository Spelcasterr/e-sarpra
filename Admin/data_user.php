<?php
session_start();
include '../koneksi.php';

/* =====================
   CEK LOGIN & ROLE
===================== */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$queryUser = mysqli_query($conn, "SELECT username, role FROM users WHERE id='$user_id'");
$dataUser  = mysqli_fetch_assoc($queryUser);

$username = $dataUser['username'];
$role     = $dataUser['role'];
$initial  = strtoupper(substr($username, 0, 1));

/* =====================
   MODE EDIT
===================== */
$edit = false;
$data_edit = [];

if (isset($_GET['edit'])) {
    $edit = true;
    $id_edit = $_GET['edit'];

    $ambil = mysqli_query($conn, "SELECT * FROM users WHERE id='$id_edit'");
    $data_edit = mysqli_fetch_assoc($ambil);
}

/* =====================
   DELETE USER
===================== */
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    // Cek apakah user masih punya peminjaman AKTIF (belum dikembalikan)
    $cek = mysqli_query($conn, "SELECT * FROM peminjaman WHERE user_id='$id' AND status NOT IN ('dikembalikan', 'selesai')");

    if (mysqli_num_rows($cek) > 0) {
        echo "<script>
            alert('User masih memiliki peminjaman aktif yang belum dikembalikan!');
            window.location='data_user.php';
        </script>";
        exit;
    }

    // Hapus dulu semua riwayat peminjaman user (sudah selesai) agar tidak kena foreign key constraint
    mysqli_query($conn, "DELETE FROM peminjaman WHERE user_id='$id'");

    // Baru hapus user
    mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
    header("Location: data_user.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data User</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex">

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

    <a href="data_user.php" class="flex items-center gap-3 bg-white text-[#1565C0] px-4 py-2.5 rounded-xl font-sora font-semibold shadow-sm">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0-3-3.85"/></svg>
        <span>Daftar User</span>
    </a>

    <a href="alat.php" class="flex items-center gap-3 text-white/80 hover:bg-white/10 hover:text-white px-4 py-2.5 rounded-xl transition-all font-sora">
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

<!-- MAIN CONTENT -->
<div class="flex-1 p-8 space-y-8">

    <h1 class="text-2xl font-bold text-gray-700">Manajemen User</h1>
    <button><h1></h1></button>

    <!-- TABLE -->
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-200 text-gray-600">
                <tr>
                    <th class="p-3 text-left">No</th>
                    <th class="p-3 text-left">Username</th>
                    <th class="p-3 text-left">Email</th>
                    <th class="p-3 text-left">Role</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php
                $no = 1;
                $query = mysqli_query($conn, "SELECT * FROM users");
                while ($data = mysqli_fetch_assoc($query)):
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="p-3"><?= $no++ ?></td>
                    <td class="p-3"><?= htmlspecialchars($data['username']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($data['email']) ?></td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded text-xs 
                            <?= $data['role']=='admin' ? 'bg-red-100 text-red-600' : 
                               ($data['role']=='petugas' ? 'bg-blue-100 text-blue-600' : 
                               'bg-green-100 text-green-600') ?>">
                            <?= htmlspecialchars($data['role']) ?>
                        </span>
                    </td>
                    <td class="p-3 text-center space-x-2">
                        <a href="?edit=<?= $data['id'] ?>" class="text-blue-600 hover:underline">Edit</a>
                        <a href="?hapus=<?= $data['id'] ?>"
                           onclick="return confirm('Yakin hapus user?')"
                           class="text-red-600 hover:underline">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- FORM -->
    <div class="bg-white rounded-xl shadow p-6 max-w-xl">
        <h2 class="text-lg font-semibold mb-4">
            <?= $edit ? "Edit User" : "Tambah User" ?>
        </h2>

        <form action="<?= $edit ? 'update_user.php' : 'simpan_user.php' ?>" method="post" class="space-y-4">

            <?php if ($edit): ?>
                <input type="hidden" name="id" value="<?= $data_edit['id'] ?>">
            <?php endif; ?>

            <div>
                <label class="text-sm">Username</label>
                <input type="text" name="username" required
                    value="<?= $edit ? htmlspecialchars($data_edit['username']) : '' ?>"
                    class="w-full px-4 py-2 border rounded-lg">
            </div>

            <div>
                <label class="text-sm">Email</label>
                <input type="email" name="email" required
                    value="<?= $edit ? htmlspecialchars($data_edit['email']) : '' ?>"
                    class="w-full px-4 py-2 border rounded-lg">
            </div>

            <div>
                <label class="text-sm">Password</label>
                <input type="password" name="password"
                    placeholder="Kosongkan jika tidak diubah"
                    class="w-full px-4 py-2 border rounded-lg">
            </div>

            <div>
                <label class="text-sm">Role</label>
                <select name="role" class="w-full px-4 py-2 border rounded-lg">
                    <option value="petugas" <?= ($edit && $data_edit['role']=='petugas')?'selected':'' ?>>Petugas</option>
                    <option value="peminjam" <?= ($edit && $data_edit['role']=='peminjam')?'selected':'' ?>>Peminjam</option>
                </select>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                <?= $edit ? "Update User" : "Simpan User" ?>
            </button>

        </form>
    </div>

</div>

</body>
</html>