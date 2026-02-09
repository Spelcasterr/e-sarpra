<?php
session_start();
include '../koneksi.php';
include '../config/log.php';

$edit = false;

/* =================
   DELETE DATA
================= */
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];

    // CEK apakah alat masih dipakai di peminjaman
    $cek = mysqli_query($conn, "SELECT id FROM peminjaman WHERE alat_id=$id");

    if (mysqli_num_rows($cek) > 0) {
        echo "<script>
            alert('Alat masih pernah dipinjam, tidak bisa dihapus!');
            window.location='alat.php';
        </script>";
        exit;
    }

    // Kalau aman baru hapus
    mysqli_query($conn, "DELETE FROM alat WHERE id=$id");

    header("Location: alat.php");
    exit;
}

/* =================
   MODE EDIT
================= */
if (isset($_GET['edit'])) {
    $edit = true;
    $id_edit = $_GET['edit'];

    $ambil = mysqli_query($conn,"SELECT * FROM alat WHERE id='$id_edit'");
    $data_edit = mysqli_fetch_assoc($ambil);
}

/* =================
   SIMPAN DATA
================= */
if (isset($_POST['simpan'])) {

    $nama = $_POST['nama'];
    $kategori = $_POST['kategori'];
    $stok = $_POST['stok'];

    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];

    if ($gambar != "") {
        $gambar = time()."_".$gambar;
        move_uploaded_file($tmp, "../gambar/".$gambar);
    }

    mysqli_query($conn,"INSERT INTO alat 
    (nama_alat,kategori_id,stok,gambar)
    VALUES ('$nama','$kategori','$stok','$gambar')");

    simpanLog($conn,'CRUD Alat','Menambah alat: '.$nama);

    header("Location: alat.php");
}

/* =================
   UPDATE DATA
================= */
if (isset($_POST['update'])) {

    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $kategori = $_POST['kategori'];
    $stok = $_POST['stok'];

    $gambar_lama = $_POST['gambar_lama'];

    if ($_FILES['gambar']['name'] != "") {

        $gambar = time()."_".$_FILES['gambar']['name'];
        $tmp = $_FILES['gambar']['tmp_name'];

        move_uploaded_file($tmp, "../gambar/".$gambar);

        if ($gambar_lama != "") {
            unlink("../gambar/".$gambar_lama);
        }

    } else {
        $gambar = $gambar_lama;
    }

    mysqli_query($conn,"UPDATE alat SET
        nama_alat='$nama',
        kategori_id='$kategori',
        stok='$stok',
        gambar='$gambar'
        WHERE id='$id'
    ");

    header("Location: alat.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Alat</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-2xl font-bold mb-6">
        <?= $edit ? "Edit Alat" : "Tambah Alat" ?>
    </h2>

    <form method="post" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <?php if($edit): ?>
            <input type="hidden" name="id" value="<?= $data_edit['id'] ?>">
            <input type="hidden" name="gambar_lama" value="<?= $data_edit['gambar'] ?>">
        <?php endif; ?>

        <div>
            <label class="block text-sm font-semibold mb-1">Nama Alat</label>
            <input type="text" name="nama"
                   value="<?= $edit ? $data_edit['nama_alat'] : '' ?>"
                   class="w-full border rounded px-3 py-2"
                   required>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Kategori</label>
            <select name="kategori" class="w-full border rounded px-3 py-2" required>
                <?php
                $k = mysqli_query($conn,"SELECT * FROM kategori");
                while($d = mysqli_fetch_assoc($k)){
                ?>
                <option value="<?= $d['id'] ?>"
                    <?= ($edit && $d['id'] == $data_edit['kategori_id']) ? 'selected' : '' ?>>
                    <?= $d['nama_kategori'] ?>
                </option>
                <?php } ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Stok</label>
            <input type="number" name="stok"
                   value="<?= $edit ? $data_edit['stok'] : '' ?>"
                   class="w-full border rounded px-3 py-2"
                   required>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Gambar</label>
            <input type="file" name="gambar" class="w-full">
            <?php if($edit && $data_edit['gambar'] != ""): ?>
                <img src="../gambar/<?= $data_edit['gambar'] ?>" class="mt-2 w-24 rounded">
            <?php endif; ?>
        </div>

        <div class="md:col-span-2">
            <button type="submit"
                    name="<?= $edit ? 'update' : 'simpan' ?>"
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                <?= $edit ? 'Update' : 'Simpan' ?>
            </button>
        </div>
    </form>
</div>

<div class="max-w-6xl mx-auto mt-8 bg-white p-6 rounded shadow">
    <h3 class="text-xl font-bold mb-4">Data Alat</h3>

    <table class="w-full border-collapse">
        <thead class="bg-gray-200">
            <tr>
                <th class="border p-2">No</th>
                <th class="border p-2">Nama</th>
                <th class="border p-2">Kategori</th>
                <th class="border p-2">Stok</th>
                <th class="border p-2">Gambar</th>
                <th class="border p-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $no = 1;
        $data = mysqli_query($conn,"
            SELECT alat.*, kategori.nama_kategori
            FROM alat
            JOIN kategori ON alat.kategori_id = kategori.id
        ");
        while($d = mysqli_fetch_assoc($data)){
        ?>
            <tr class="text-center">
                <td class="border p-2"><?= $no++ ?></td>
                <td class="border p-2"><?= $d['nama_alat'] ?></td>
                <td class="border p-2"><?= $d['nama_kategori'] ?></td>
                <td class="border p-2"><?= $d['stok'] ?></td>
                <td class="border p-2">
                    <img src="../gambar/<?= $d['gambar'] ?>" class="w-20 mx-auto">
                </td>
                <td class="border p-2">
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

</body>
</html>
