<?php
session_start();
include '../koneksi.php';

if ($_SESSION['role'] !== 'peminjam') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['id'];
$alat_id = (int)$_POST['alat_id'];
$jumlah = (int)$_POST['jumlah'];
$tanggal_kembali = $_POST['tanggal_kembali'];

mysqli_query($conn, "
    INSERT INTO peminjaman 
    (user_id, alat_id, jumlah, tanggal_pinjam, tanggal_kembali)
    VALUES
    ($user_id, $alat_id, $jumlah, CURDATE(), '$tanggal_kembali')
");

header("Location: peminjam.php");
