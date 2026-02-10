<?php
session_start();
include '../koneksi.php';

/* =====================
   UPDATE DENDA OTOMATIS (TELAT)
===================== */
$today = date('Y-m-d');

mysqli_query($conn, "
    UPDATE peminjaman
    SET status = 'terlambat',
        denda = DATEDIFF('$today', tanggal_kembali) * 5000
    WHERE status != 'dikembalikan'
      AND tanggal_kembali IS NOT NULL
      AND tanggal_kembali < '$today'
");

/* =====================
   CEK ROLE ADMIN
===================== */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
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
    $id_edit = (int)$_GET['edit'];

    $ambil = mysqli_query($conn, "SELECT * FROM peminjaman WHERE id=$id_edit");
    $data_edit = mysqli_fetch_assoc($ambil);

    if (!$data_edit) {
        die("Data tidak ditemukan");
    }
}

/* =====================
   UPDATE
===================== */
if (isset($_POST['update'])) {

    $id = (int)$_POST['id'];
    $tanggal_kembali = mysqli_real_escape_string($conn, $_POST['tanggal_kembali']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $status_valid = [
        'menunggu',
        'disetujui',
        'ditolak',
        'menunggu_pengembalian',
        'dikembalikan',
        'terlambat'
    ];

    if (!in_array($status, $status_valid)) {
        echo "<script>alert('Status tidak valid');window.location='data_peminjaman.php';</script>";
        exit;
    }

    $denda = 0;

    // HITUNG DENDA JIKA SUDAH DIKEMBALIKAN
    if ($status == 'dikembalikan') {

        $data = mysqli_fetch_assoc(mysqli_query($conn,"
            SELECT tanggal_kembali 
            FROM peminjaman 
            WHERE id=$id
        "));

        if ($data) {
            $tgl_seharusnya = $data['tanggal_kembali'];
            $today = date('Y-m-d');

            if ($today > $tgl_seharusnya) {
                $hari_telat = (strtotime($today) - strtotime($tgl_seharusnya)) / 86400;
                $denda = $hari_telat * 5000;
            }
        }
    }

    $update = mysqli_query($conn, "
        UPDATE peminjaman 
        SET tanggal_kembali='$tanggal_kembali',
            status='$status',
            denda='$denda'
        WHERE id=$id
    ");

    if (!$update) {
        die("Update gagal: " . mysqli_error($conn));
    }

    header("Location: data_peminjaman.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Peminjaman</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">

<div class="max-w-7xl mx-auto space-y-8">

    <!-- HEADER -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-700">
            Data Peminjaman Barang
        </h1>
        <a href="admin.php" class="text-sm text-gray-500 hover:text-blue-600">
            ← Dashboard
        </a>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-200 text-gray-600">
                <tr>
                    <th class="p-3 text-left">No</th>
                    <th class="p-3 text-left">Peminjam</th>
                    <th class="p-3 text-left">Alat</th>
                    <th class="p-3 text-center">Jumlah</th>
                    <th class="p-3 text-left">Tgl Pinjam</th>
                    <th class="p-3 text-left">Tgl Kembali</th>
                    <th class="p-3 text-center">Status</th>
                    <th class="p-3 text-center">Denda</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php
                $no = 1;
                $data = mysqli_query($conn,"
                    SELECT peminjaman.*, users.username, alat.nama_alat
                    FROM peminjaman
                    JOIN users ON peminjaman.user_id = users.id
                    JOIN alat ON peminjaman.alat_id = alat.id
                    WHERE peminjaman.status != 'dikembalikan'
                ");

                while ($d = mysqli_fetch_assoc($data)) :
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="p-3"><?= $no++ ?></td>
                    <td class="p-3"><?= htmlspecialchars($d['username']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($d['nama_alat']) ?></td>
                    <td class="p-3 text-center"><?= $d['jumlah'] ?></td>
                    <td class="p-3"><?= $d['tanggal_pinjam'] ?></td>
                    <td class="p-3"><?= $d['tanggal_kembali'] ?: '-' ?></td>
                    <td class="p-3 text-center">
    <span class="px-3 py-1 rounded-full text-xs font-medium
    <?php
        if ($d['status'] == 'terlambat') {
            echo 'bg-red-100 text-red-700';
        } elseif ($d['status'] == 'menunggu_pengembalian') {
            echo 'bg-yellow-100 text-yellow-700';
        } elseif ($d['status'] == 'disetujui') {
            echo 'bg-green-100 text-green-700';
        } else {
            echo 'bg-gray-100 text-gray-600';
        }
    ?>">
        <?= ucfirst(str_replace('_', ' ', $d['status'])) ?>
    </span>
</td>
                    <td class="p-3 text-center">
                        Rp <?= number_format($d['denda']) ?>
                    </td>

                    <td class="p-3 text-center space-x-2">
                        <a href="?edit=<?= $d['id'] ?>"
                           class="text-blue-600 hover:underline">
                            Edit
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- FORM EDIT -->
    <?php if ($edit): ?>
    <div class="bg-white rounded-xl shadow p-6 max-w-xl">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">
            Edit Status Peminjaman
        </h2>

        <form method="post" class="space-y-4">

            <input type="hidden" name="id" value="<?= $data_edit['id'] ?>">

            <div>
                <label class="block text-sm text-gray-600 mb-1">
                    Tanggal Kembali
                </label>
                <input type="date" name="tanggal_kembali"
                       value="<?= $data_edit['tanggal_kembali'] ?>"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">
                    Status
                </label>
                <select name="status"
    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">

    <option value="menunggu" <?= $data_edit['status']=='menunggu'?'selected':'' ?>>Menunggu</option>
    <option value="disetujui" <?= $data_edit['status']=='disetujui'?'selected':'' ?>>Disetujui</option>
    <option value="ditolak" <?= $data_edit['status']=='ditolak'?'selected':'' ?>>Ditolak</option>
    <option value="menunggu_pengembalian" <?= $data_edit['status']=='menunggu_pengembalian'?'selected':'' ?>>Menunggu Pengembalian</option>
    <option value="dikembalikan" <?= $data_edit['status']=='dikembalikan'?'selected':'' ?>>Dikembalikan</option>

</select>

            </div>

            <button name="update"
                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                Update Data
            </button>

        </form>
    </div>
    <?php endif; ?>

</div>

</body>
</html>
