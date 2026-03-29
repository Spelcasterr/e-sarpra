<?php
session_start();
include '../koneksi.php';

/* =====================
   CEK LOGIN & ROLE
===================== */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$username = $_SESSION['username'];
$role     = $_SESSION['role'];
$initial  = strtoupper(substr($username, 0, 1));

/* =====================
   FUNGSI LOG AKTIVITAS
===================== */
function catat_log($conn, $user_id, $role, $aktivitas, $deskripsi) {
    $user_id   = (int) $user_id;
    $role      = mysqli_real_escape_string($conn, $role);
    $aktivitas = mysqli_real_escape_string($conn, $aktivitas);
    $deskripsi = mysqli_real_escape_string($conn, $deskripsi);

    mysqli_query($conn, "
        INSERT INTO log_aktivitas (user_id, role, aktivitas, deskripsi, created_at)
        VALUES ('$user_id', '$role', '$aktivitas', '$deskripsi', NOW())
    ");
}

/* =====================
   UPDATE DENDA OTOMATIS
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
   UPDATE DATA
===================== */
if (isset($_POST['update'])) {

    $id = (int)$_POST['id'];
    $tanggal_kembali = mysqli_real_escape_string($conn, $_POST['tanggal_kembali']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $denda = 0;

    // Ambil data peminjaman sebelum diupdate (untuk keperluan log)
    $data_lama = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT p.*, u.username, a.nama_alat
        FROM peminjaman p
        JOIN users u ON p.user_id = u.id
        JOIN alat a ON p.alat_id = a.id
        WHERE p.id = $id
    "));

    if ($data_lama) {
        $tgl_seharusnya = $data_lama['tanggal_kembali'];
        $today_check    = date('Y-m-d');

        if ($status == 'dikembalikan' && $today_check > $tgl_seharusnya) {
            $hari_telat = (strtotime($today_check) - strtotime($tgl_seharusnya)) / 86400;
            $denda = $hari_telat * 5000;
        }
    }

    mysqli_query($conn, "
        UPDATE peminjaman 
        SET tanggal_kembali='$tanggal_kembali',
            status='$status',
            denda='$denda'
        WHERE id=$id
    ");

    // ---- CATAT LOG ----
    $nama_peminjam = $data_lama['username']  ?? 'Unknown';
    $nama_alat     = $data_lama['nama_alat'] ?? 'Unknown';

    if ($status === 'dikembalikan') {
        $label_aktivitas = 'Pengembalian Alat';
        $label_deskripsi = "Admin menandai alat \"$nama_alat\" milik $nama_peminjam sebagai dikembalikan"
                         . ($denda > 0 ? " (denda: Rp " . number_format($denda) . ")" : "");
    } else {
        $label_aktivitas = 'Update Status Peminjaman';
        $label_deskripsi = "Admin mengubah status peminjaman alat \"$nama_alat\" milik $nama_peminjam menjadi: $status";
    }

    catat_log(
        $conn,
        $_SESSION['user_id'],
        $_SESSION['role'],
        $label_aktivitas,
        $label_deskripsi
    );
    // -------------------

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

<body class="bg-gray-100">

<div class="flex">

    <!-- SIDEBAR -->
    <div class="w-64 min-h-screen bg-gradient-to-b from-blue-600 to-purple-600 text-white p-6 flex flex-col justify-between">

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
                    <p class="text-xs text-white/70 capitalize">
                        <?= htmlspecialchars($role) ?>
                    </p>
                </div>
            </div>

            <div class="space-y-3 text-sm">

            <a href="admin.php" class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg">
                <span>📊</span><span>Dashboard</span>
            </a>

            <a href="data_user.php" class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg font-medium">
                <span>👥</span><span>User</span>
            </a>

            <a href="alat.php" class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg">
                <span>📦</span><span>Alat</span>
            </a>

            <a href="kategori.php" class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-lg">
                <span>📁</span><span>Kategori</span>
            </a>

            <hr class="border-white/20">

            <a href="data_peminjaman.php" class="flex items-center space-x-2 bg-white text-blue-600 px-3 py-2 rounded-lg">
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

        <div class="text-xs text-white/60">
            © <?= date('Y') ?> Sistem Peminjaman
        </div>
    </div>

    <!-- CONTENT -->
    <div class="flex-1 p-8 space-y-8">

        <h1 class="text-2xl font-bold">Data Peminjaman Aktif</h1>

        <!-- FORM EDIT (muncul jika mode edit aktif) -->
        <?php if ($edit): ?>
        <div class="bg-white rounded-xl shadow p-6 max-w-lg">
            <h2 class="text-lg font-semibold mb-4">Edit Peminjaman #<?= $data_edit['id'] ?></h2>
            <form method="POST">
                <input type="hidden" name="id" value="<?= $data_edit['id'] ?>">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kembali</label>
                    <input type="date" name="tanggal_kembali"
                           value="<?= htmlspecialchars($data_edit['tanggal_kembali']) ?>"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <?php
                        $statuses = ['dipinjam','terlambat','dikembalikan'];
                        foreach ($statuses as $s):
                        ?>
                        <option value="<?= $s ?>" <?= $data_edit['status']==$s ? 'selected' : '' ?>>
                            <?= ucfirst(str_replace('_',' ',$s)) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="flex gap-3">
                    <button type="submit" name="update"
                            class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700">
                        Simpan
                    </button>
                    <a href="data_peminjaman.php"
                       class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm hover:bg-gray-300">
                        Batal
                    </a>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100 text-gray-600">
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
                    ORDER BY peminjaman.id DESC
                ");

                if (mysqli_num_rows($data) == 0) {
                    echo "
                    <tr>
                        <td colspan='9' class='text-center py-6 text-gray-500'>
                            Belum ada peminjaman aktif
                        </td>
                    </tr>";
                }

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
                            <?= ucfirst(str_replace('_',' ',$d['status'])) ?>
                        </td>
                        <td class="p-3 text-center">
                            Rp <?= number_format($d['denda']) ?>
                        </td>
                        <td class="p-3 text-center">
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

    </div>
</div>

</body>
</html>