<?php
session_start();
include '../koneksi.php';
include '../config/log.php'; //

if ($_SESSION['role'] !== 'petugas') {
    exit;
}

$id = (int)$_GET['id'];

$p = mysqli_query($conn, "
    SELECT p.*, a.id AS alat_id 
    FROM peminjaman p
    JOIN alat a ON p.alat_id = a.id
    WHERE p.id = $id 
      AND p.status = 'menunggu_pengembalian'
");

$data = mysqli_fetch_assoc($p);
if (!$data) {
    die("Data tidak valid");
}

mysqli_begin_transaction($conn);

try {

    mysqli_query($conn, "
        UPDATE alat 
        SET stok = stok + {$data['jumlah']}
        WHERE id = {$data['alat_id']}
    ");

    mysqli_query($conn, "
        UPDATE peminjaman 
        SET status = 'dikembalikan'
        WHERE id = $id
    ");

    simpanLog($conn,
    'Pengembalian',
    'Menyetujui pengembalian ' . $nama_alat
);


    mysqli_commit($conn);
    header("Location: petugas.php");

} catch (Exception $e) {
    mysqli_rollback($conn);
    die("Gagal memproses pengembalian");
}
