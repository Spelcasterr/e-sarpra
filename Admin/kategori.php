<?php
session_start();
include '../koneksi.php';
include '../config/log.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Kategori</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<!-- Header -->
<div class="bg-white shadow p-5 flex justify-between items-center">
    <h1 class="text-xl font-bold text-gray-800">Manajemen Kategori</h1>
    <a href="admin.php" class="text-sm text-blue-600 hover:underline">
        ← Kembali ke Dashboard
    </a>
</div>

<div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- FORM -->
    <div class="bg-white p-6 rounded shadow">
        <h2 class="font-semibold text-gray-700 mb-4">
            <?= $edit ? 'Edit Kategori' : 'Tambah Kategori' ?>
        </h2>

        <form method="post" class="space-y-4">
            <?php if ($edit): ?>
                <input type="hidden" name="id" value="<?= $data_edit['id'] ?>">
            <?php endif; ?>

            <input
                type="text"
                name="nama"
                required
                placeholder="Nama kategori"
                value="<?= $edit ? $data_edit['nama_kategori'] : '' ?>"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300"
            >

            <button
                type="submit"
                name="<?= $edit ? 'update' : 'simpan' ?>"
                class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition"
            >
                <?= $edit ? 'Update' : 'Simpan' ?>
            </button>
        </form>
    </div>

    <!-- TABEL -->
    <div class="md:col-span-2 bg-white p-6 rounded shadow">
        <h2 class="font-semibold text-gray-700 mb-4">Daftar Kategori</h2>

        <table class="w-full border border-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="border px-3 py-2 text-left">No</th>
                    <th class="border px-3 py-2 text-left">Nama Kategori</th>
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
                    <td class="border px-3 py-2 text-center">
                        <a href="?edit=<?= $d['id'] ?>"
                           class="text-blue-600 hover:underline text-sm">
                            Edit
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
