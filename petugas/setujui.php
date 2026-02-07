<?php
session_start();
include '../koneksi.php';
include '../config/log.php'; //

/* ======================
   CEK AKSES
====================== */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../login.php");
    exit;
}

/* ======================
   CEK PARAMETER
====================== */
if (!isset($_GET['id'])) {
    header("Location: petugas.php");
    exit;
}

$id = (int) $_GET['id'];
$petugas_id = (int) $_SESSION['id'];

/* ======================
   AMBIL DATA PEMINJAMAN
====================== */
$p = mysqli_query($conn, "
    SELECT p.*, a.stok 
    FROM peminjaman p
    JOIN alat a ON p.alat_id = a.id
    WHERE p.id = $id
");

$data = mysqli_fetch_assoc($p);

if (!$data) {
    die("Data peminjaman tidak ditemukan");
}

if ($data['status'] !== 'menunggu') {
    die("Peminjaman sudah diproses");
}

$alat_id = $data['alat_id'];
$jumlah  = $data['jumlah'];
$stok    = $data['stok'];

if ($stok < $jumlah) {
    die("Stok tidak mencukupi");
}

/* ======================
   TRANSAKSI
====================== */
mysqli_begin_transaction($conn);

/* Kurangi stok */
$q1 = mysqli_query($conn, "
    UPDATE alat 
    SET stok = stok - $jumlah 
    WHERE id = $alat_id
");

/* Update peminjaman */
$q2 = mysqli_query($conn, "
    UPDATE peminjaman 
    SET status = 'disetujui',
        petugas_id = $petugas_id
    WHERE id = $id
");

simpanLog($conn,
    'Peminjaman',
    'Menyetujui peminjaman' . $nama_alat
);


if ($q1 && $q2) {
    mysqli_commit($conn);
    header("Location: petugas.php");
    exit;
} else {
    mysqli_rollback($conn);
    die("Gagal menyetujui peminjaman");
}
