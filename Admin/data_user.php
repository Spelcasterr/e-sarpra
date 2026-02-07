<?php
session_start();
include '../koneksi.php';

/* =====================
   CEK ROLE ADMIN
===================== */
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

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

    // cek apakah user punya peminjaman
    $cek = mysqli_query($conn, "SELECT * FROM peminjaman WHERE user_id = '$id'");

    if (mysqli_num_rows($cek) > 0) {
        echo "<script>
            alert('User masih memiliki data peminjaman, tidak bisa dihapus!');
            window.location='data_user.php';
        </script>";
        exit;
    }

    // aman → hapus user
    mysqli_query($conn, "DELETE FROM users WHERE id = '$id'");

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
<body class="bg-gray-100 min-h-screen p-6">

<div class="max-w-6xl mx-auto space-y-8">

    <!-- HEADER -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-700">Manajemen User</h1>
        <a href="admin.php" class="text-sm text-gray-500 hover:text-blue-600">
            ← Dashboard
        </a>
    </div>

    <!-- TABLE USER -->
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
                while ($data = mysqli_fetch_assoc($query)) :
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="p-3"><?= $no++ ?></td>
                    <td class="p-3"><?= htmlspecialchars($data['username']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($data['email']) ?></td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded text-xs 
                            <?= $data['role']=='admin' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600' ?>">
                            <?= $data['role'] ?>
                        </span>
                    </td>
                    <td class="p-3 text-center space-x-2">
                        <a href="?edit=<?= $data['id'] ?>" 
                           class="text-blue-600 hover:underline">
                            Edit
                        </a>
                        <a href="?hapus=<?= $data['id'] ?>"
                           onclick="return confirm('Yakin hapus user?')"
                           class="text-red-600 hover:underline">
                            Hapus
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- FORM USER -->
    <div class="bg-white rounded-xl shadow p-6 max-w-xl">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">
            <?= $edit ? "Edit User" : "Tambah User" ?>
        </h2>

        <form action="<?= $edit ? 'update_user.php' : 'simpan_user.php' ?>" method="post" class="space-y-4">

            <?php if ($edit): ?>
                <input type="hidden" name="id" value="<?= $data_edit['id'] ?>">
            <?php endif; ?>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Username</label>
                <input type="text" name="username" required
                    value="<?= $edit ? htmlspecialchars($data_edit['username']) : '' ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Email</label>
                <input type="email" name="email" required
                    value="<?= $edit ? htmlspecialchars($data_edit['email']) : '' ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Password</label>
                <input type="password" name="password"
                    placeholder="Kosongkan jika tidak diubah"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Role</label>
                <select name="role"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="petugas" <?= ($edit && $data_edit['role']=='petugas') ? 'selected' : '' ?>>
                        Petugas
                    </option>
                    <option value="peminjam" <?= ($edit && $data_edit['role']=='peminjam') ? 'selected' : '' ?>>
                        Peminjam
                    </option>
                </select>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                <?= $edit ? "Update User" : "Simpan User" ?>
            </button>

        </form>
    </div>

</div>

</body>
</html>
