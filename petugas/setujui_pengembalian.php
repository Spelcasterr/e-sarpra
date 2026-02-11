<?php
session_start();
include '../koneksi.php';
include '../config/log.php';

if ($_SESSION['role'] !== 'petugas') {
    exit;
}

$id = (int)$_GET['id'];

$p = mysqli_query($conn, "
    SELECT p.*, a.id AS alat_id, a.nama_alat 
    FROM peminjaman p
    JOIN alat a ON p.alat_id = a.id
    WHERE p.id = $id 
      AND p.status = 'menunggu_pengembalian'
");

$data = mysqli_fetch_assoc($p);
if (!$data) {
    die("Data tidak valid");
}

/* =========================
   HITUNG DENDA TERBARU
========================= */
$today = date('Y-m-d');
$denda = 0;

if ($today > $data['tanggal_kembali']) {
    $hari_telat = (strtotime($today) - strtotime($data['tanggal_kembali'])) / 86400;
    $denda = $hari_telat * 5000;
}

/* =========================
   TAMPILKAN INFO DENDA
========================= */
if ($denda > 0) {
    echo "<h3 style='color:red'>
            Peminjam terlambat dan harus membayar denda sebesar 
            Rp " . number_format($denda) . "
          </h3><hr>";
} else {
    echo "<h3 style='color:green'>
            Tidak ada denda
          </h3><hr>";
}

/* =========================
   PROSES SETUJUI
========================= */
mysqli_begin_transaction($conn);

try {

    mysqli_query($conn, "
        UPDATE alat 
        SET stok = stok + {$data['jumlah']}
        WHERE id = {$data['alat_id']}
    ");

    mysqli_query($conn, "
        UPDATE peminjaman 
        SET status = 'dikembalikan',
            denda = $denda
        WHERE id = $id
    ");

    simpanLog($conn,
        'Pengembalian',
        'Menyetujui pengembalian ' . $data['nama_alat']
    );

    mysqli_commit($conn);
    header("Location: petugas.php");

} catch (Exception $e) {
    mysqli_rollback($conn);
    die("Gagal memproses pengembalian");
}
