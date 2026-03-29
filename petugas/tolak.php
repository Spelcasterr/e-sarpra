<?php
session_start();
include '../koneksi.php';
include '../config/log.php'; 
include '../config/auto_log.php';

if ($_SESSION['role'] !== 'petugas') {
    exit;
}

$id = (int)$_GET['id'];

mysqli_query($conn, "
    UPDATE peminjaman SET status='ditolak'
    WHERE id=$id
");

simpanLog($conn,
"Penolakan",
"Menolak peminjaman ID ".$id
);

header("Location: peminjaman.php");
