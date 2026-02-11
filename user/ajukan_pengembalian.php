<?php
session_start();
include '../koneksi.php';

if ($_SESSION['role'] !== 'peminjam') {
    exit;
}

$id = (int)$_GET['id'];
$user_id = $_SESSION['id'];

mysqli_query($conn, "
    UPDATE peminjaman 
    SET status = 'menunggu_pengembalian'
    WHERE id = $id 
    AND user_id = $user_id 
    AND status IN ('disetujui', 'terlambat')
");

header("Location: peminjaman_saya.php");
exit;
