<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$data = mysqli_query($conn, "
    SELECT l.*, u.username
    FROM log_aktivitas l
    LEFT JOIN users u ON l.user_id = u.id
    ORDER BY l.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Log Aktivitas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<div class="max-w-7xl mx-auto p-6">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Log Aktivitas Sistem</h1>
        <a href="admin.php"
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            Kembali
        </a>
    </div>

    <!-- Card -->
    <div class="bg-white shadow rounded-lg overflow-hidden">

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-gray-700">
                <thead class="bg-gray-200 text-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">User</th>
                        <th class="px-4 py-3 text-left">Role</th>
                        <th class="px-4 py-3 text-left">Aktivitas</th>
                        <th class="px-4 py-3 text-left">Deskripsi</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y">

                <?php
                $no = 1;
                if (mysqli_num_rows($data) == 0):
                ?>
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                            Belum ada aktivitas
                        </td>
                    </tr>
                <?php
                endif;

                while ($d = mysqli_fetch_assoc($data)):
                ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3"><?= $no++ ?></td>
                        <td class="px-4 py-3"><?= $d['username'] ?? '-' ?></td>
                        <td class="px-4 py-3 capitalize"><?= $d['role'] ?></td>
                        <td class="px-4 py-3 font-medium"><?= $d['aktivitas'] ?></td>
                        <td class="px-4 py-3 text-gray-600"><?= $d['deskripsi'] ?></td>
                        <td class="px-4 py-3 text-gray-500">
                            <?= date('d-m-Y H:i', strtotime($d['created_at'])) ?>
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
