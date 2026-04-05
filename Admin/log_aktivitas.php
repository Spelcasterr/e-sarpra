<?php
session_start();
include '../koneksi.php';

// Proteksi halaman
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Session aman
$username = $_SESSION['username'] ?? 'Admin';
$role     = $_SESSION['role'] ?? 'admin';
$initial  = strtoupper(substr($username, 0, 1));

// Query data
$data = mysqli_query($conn, "
    SELECT * FROM log_aktivitas
    ORDER BY created_at DESC
");

// Debug query
if (!$data) {
    die("Query error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Log Aktivitas</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="flex h-screen overflow-hidden">

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

    <a href="log_aktivitas.php" class="flex items-center gap-3 bg-white text-[#1565C0] px-4 py-2.5 rounded-xl font-sora font-semibold shadow-sm">
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
    <div class="flex-1 p-8 overflow-y-auto">

        <h1 class="text-2xl font-bold mb-6">
            Log Aktivitas Sistem
        </h1>

        <div class="bg-white rounded-lg shadow overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-3">No</th>
                        <th class="p-3">User</th>
                        <th class="p-3">Role</th>
                        <th class="p-3">Aktivitas</th>
                        <th class="p-3">Deskripsi</th>
                        <th class="p-3">Tanggal</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                <?php
                $no = 1;

                if ($data && mysqli_num_rows($data) > 0):
                    while ($d = mysqli_fetch_assoc($data)):
                ?>

                    <tr class="hover:bg-gray-50 text-center">
                        <td class="p-3"><?= $no++ ?></td>
                        <td class="p-3"><?= htmlspecialchars($d['username'] ?? '') ?></td>
                        <td class="p-3"><?= htmlspecialchars($d['role'] ?? '') ?></td>
                        <td class="p-3 font-medium"><?= htmlspecialchars($d['aktivitas'] ?? '') ?></td>
                        <td class="p-3"><?= htmlspecialchars($d['deskripsi'] ?? '') ?></td>
                        <td class="p-3">
                            <?= !empty($d['created_at']) ? date('d-m-Y H:i', strtotime($d['created_at'])) : '-' ?>
                        </td>
                    </tr>

                <?php 
                    endwhile;
                else:
                ?>

                    <tr>
                        <td colspan="6" class="p-6 text-center text-gray-500">
                            Belum ada aktivitas
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>