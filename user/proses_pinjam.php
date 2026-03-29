<?php
session_start();
include '../koneksi.php';
include '../config/log.php'; 
include '../config/auto_log.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'peminjam') {
    header("Location: ../login.php");
    exit;
}

$user_id     = (int)$_SESSION['user_id'];
$alat_id     = (int)$_POST['alat_id'];
$jumlah      = (int)$_POST['jumlah'];
$tgl_kembali = $_POST['tanggal_kembali'];

if ($jumlah <= 0) {
    die("Jumlah tidak valid.");
}

if (empty($tgl_kembali)) {
    die("Tanggal kembali harus diisi.");
}

if (strtotime($tgl_kembali) <= strtotime('today')) {
    die("Tanggal kembali harus lebih dari hari ini.");
}

$cek_stok = mysqli_query($conn, "SELECT stok FROM alat WHERE id = $alat_id");
$alat     = mysqli_fetch_assoc($cek_stok);

if (!$alat) {
    die("Alat tidak ditemukan.");
}

if ($alat['stok'] < $jumlah) {
    die("Stok alat tidak mencukupi. Stok tersedia: " . $alat['stok']);
}

$stmt = mysqli_prepare($conn, "
    INSERT INTO peminjaman (user_id, alat_id, jumlah, tanggal_pinjam, tanggal_kembali, status, denda)
    VALUES (?, ?, ?, CURDATE(), ?, 'menunggu', 0)
");

simpanLog($conn,
"peminjam",
"Meminjam alat ID ".$alat_id."jumlah".$jumlah
);

mysqli_stmt_bind_param($stmt, "iiis", $user_id, $alat_id, $jumlah, $tgl_kembali);
$insert = mysqli_stmt_execute($stmt);

if (!$insert) {
    die("Gagal meminjam: " . mysqli_stmt_error($stmt));
}

header("Location: peminjaman_saya.php");
exit;
?>