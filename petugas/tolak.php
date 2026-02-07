<?php
session_start();
include '../koneksi.php';

if ($_SESSION['role'] !== 'petugas') {
    exit;
}

$id = (int)$_GET['id'];

mysqli_query($conn, "
    UPDATE peminjaman SET status='ditolak'
    WHERE id=$id
");

header("Location: peminjaman.php");
