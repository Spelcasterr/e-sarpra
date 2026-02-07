<?php
include '../koneksi.php';

$id = $_POST['id'];
$tgl = $_POST['tanggal_kembali'];
$denda = $_POST['denda'];

mysqli_query($conn,"
UPDATE pengembalian 
SET tanggal_kembali='$tgl', denda='$denda'
WHERE id='$id'
");

header("Location: data_pengembalian.php");
