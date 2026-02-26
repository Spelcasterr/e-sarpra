<?php
session_start();


include '../koneksi.php';

/* =====================
   CEK LOGIN USER
===================== */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'peminjam') {
    header("Location: ../login.php");
    exit;
}

/* =====================
   AMBIL DATA
===================== */
$user_id   = $_SESSION['id']; // pastikan login_proses menyimpan ini
$alat_id   = (int)$_POST['alat_id'];
$jumlah    = (int)$_POST['jumlah'];
$tgl_kembali = $_POST['tanggal_kembali'];

/* =====================
   VALIDASI
===================== */
if ($jumlah <= 0) {
    die("Jumlah tidak valid");
}

if (empty($tgl_kembali)) {
    die("Tanggal kembali harus diisi");
}

/* =====================
   INSERT PEMINJAMAN
===================== */
$query = "
    INSERT INTO peminjaman 
    (user_id, alat_id, jumlah, tanggal_pinjam, tanggal_kembali, status, denda)
    VALUES 
    ($user_id, $alat_id, $jumlah, CURDATE(), '$tgl_kembali', 'menunggu', 0)
";

$insert = mysqli_query($conn, $query);

if (!$insert) {
    die("Gagal meminjam: " . mysqli_error($conn));
}

/* =====================
   REDIRECT
===================== */
header("Location: peminjaman_saya.php");
exit;
?>
