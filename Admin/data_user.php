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

    $cek = mysqli_query($conn, "SELECT * FROM peminjaman WHERE user_id='$id'");

    if (mysqli_num_rows($cek) > 0) {
        echo "<script>
            alert('User masih memiliki data peminjaman!');
            window.location='data_user.php';
        </script>";
        exit;
    }

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
<div class="w-64 min-h-screen bg-gradient-to-b from-blue-600 to-purple-600 text-white p-6 flex flex-col justify-between">

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
        <div class="space-y-3 text-sm">

            <a href="admin.php" class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg">
                <span>📊</span><span>Dashboard</span>
            </a>

            <a href="data_user.php" class="flex items-center space-x-2 bg-white text-blue-600 px-3 py-2 rounded-lg font-medium">
                <span>👥</span><span>User</span>
            </a>

            <a href="alat.php" class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg">
                <span>📦</span><span>Alat</span>
            </a>

            <a href="kategori.php" class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg">
                <span>📁</span><span>Kategori</span>
            </a>

            <hr class="border-white/20">

            <a href="data_peminjaman.php" class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg">
                <span>📄</span><span>Peminjaman</span>
            </a>

            <a href="pengembalian.php" class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg">
                <span>↩️</span><span>Pengembalian</span>
            </a>

            <a href="log_aktivitas.php" class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg">
                <span>📝</span><span>Log Aktivitas</span>
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
