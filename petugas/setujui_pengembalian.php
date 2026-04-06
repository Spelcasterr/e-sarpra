<?php
session_start();
include '../koneksi.php';
include '../config/log.php';
include '../config/auto_log.php';

/* ======================
   CEK AKSES
====================== */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../login.php");
    exit;
}

/* ======================
   CEK PARAMETER
====================== */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: pengembalian.php");
    exit;
}

$id = (int) $_GET['id'];

/* ======================
   AMBIL DATA PEMINJAMAN
====================== */
$p = mysqli_query($conn, "
    SELECT p.*, a.id AS alat_id, a.nama_alat 
    FROM peminjaman p
    JOIN alat a ON p.alat_id = a.id
    WHERE p.id = $id 
      AND p.status = 'menunggu_pengembalian'
");

$data = mysqli_fetch_assoc($p);

if (!$data) {
    die("Data tidak valid atau status bukan 'menunggu_pengembalian'.");
}

//function untuk hitung denda
$q_denda = mysqli_query($conn, "
    SELECT fn_hitung_denda('{$data['tanggal_kembali']}') AS denda
");

$row_denda = mysqli_fetch_assoc($q_denda);
$denda     = (int) $row_denda['denda'];

/* ======================
   TAMPILKAN INFO DENDA
====================== */
if ($denda > 0) {
    echo "<h3 style='color:red'>
            Peminjam terlambat. Denda: Rp " . number_format($denda, 0, ',', '.') . "
          </h3><hr>";
} else {
    echo "<h3 style='color:green'>
            Tidak ada denda. Pengembalian tepat waktu.
          </h3><hr>";
}

/* ======================
   PROSES SETUJUI PENGEMBALIAN
   Menggunakan transaksi (commit/rollback)
   — Elemen UKK: Commit & Rollback
====================== */
mysqli_begin_transaction($conn);

try {

    

    // 2. Update status peminjaman + simpan denda hasil function
    mysqli_query($conn, "
        UPDATE peminjaman 
        SET status = 'dikembalikan',
            denda  = $denda
        WHERE id   = $id
    ");

    // 3. Catat log aktivitas
    simpanLog(
        $conn,
        'Pengembalian',
        'Menyetujui pengembalian alat: ' . $data['nama_alat']
          . ($denda > 0 ? ' | Denda: Rp ' . number_format($denda, 0, ',', '.') : ' | Tanpa denda')
    );

    mysqli_commit($conn);

    header("Location: pengembalian.php");
    exit;

} catch (Exception $e) {

    mysqli_rollback($conn);
    die("Gagal memproses pengembalian: " . $e->getMessage());
}