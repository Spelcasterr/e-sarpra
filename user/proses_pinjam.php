<?php
session_start();
include '../koneksi.php';
include '../log_helper.php';

// Validasi login
if (
    !isset($_SESSION['login']) ||
    $_SESSION['login'] !== true ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'peminjam' ||
    !isset($_SESSION['user_id'])
) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: daftar_alat.php");
    exit;
}

$user_id     = (int) $_SESSION['user_id'];
$alat_id     = (int) $_POST['alat_id'];
$jumlah      = (int) $_POST['jumlah'];
$tgl_pinjam  = date('Y-m-d');
$tgl_kembali = $_POST['tanggal_kembali'];

// Validasi input dasar
if ($alat_id === 0 || $jumlah < 1 || empty($tgl_kembali)) {
    header("Location: daftar_alat.php?error=Input+tidak+valid");
    exit;
}

/* =============================================
   TRANSAKSI — elemen UKK 1.16
============================================= */
mysqli_begin_transaction($conn);

try {

    // 1. Cek stok alat (server-side)
    $cek = $conn->prepare("SELECT stok FROM alat WHERE id = ? FOR UPDATE");
    $cek->bind_param("i", $alat_id);
    $cek->execute();
    $cek->bind_result($stok);
    $cek->fetch();
    $cek->close();

    if ($stok === null) {
        throw new Exception("Alat tidak ditemukan");
    }

    if ($stok < $jumlah) {
        throw new Exception("Stok tidak mencukupi. Stok tersedia: $stok unit");
    }


    // 3. Simpan peminjaman
    $ins = $conn->prepare("
        INSERT INTO peminjaman (user_id, alat_id, jumlah, tanggal_pinjam, tanggal_kembali, status, denda)
        VALUES (?, ?, ?, ?, ?, 'menunggu', 0)
    ");
    $ins->bind_param("iiiss", $user_id, $alat_id, $jumlah, $tgl_pinjam, $tgl_kembali);
    $ins->execute();
    $ins->close();

    // 4. Catat log
    tambah_log(
        $conn,
        $user_id,
        $_SESSION['username'],
        $_SESSION['role'],
        'Peminjaman',
        'Meminjam alat ID ' . $alat_id . ' sebanyak ' . $jumlah . ' unit'
    );

    // 5. Commit — semua berhasil
    mysqli_commit($conn);

    header("Location: peminjaman_saya.php?sukses=1");
    exit;

} catch (Exception $e) {

    // Rollback jika ada error
    mysqli_rollback($conn);

    header("Location: pinjam.php?id=" . $alat_id . "&error=" . urlencode($e->getMessage()));
    exit;
}