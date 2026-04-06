<?php
session_start();
include '../koneksi.php';

// Cek login & role — FIX: file lama tidak ada cek ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: data_peminjaman.php");
    exit;
}

$id  = (int) $_POST['id'];
$tgl = mysqli_real_escape_string($conn, $_POST['tanggal_kembali']);
$denda = (int) $_POST['denda'];

mysqli_begin_transaction($conn);

try {

    // Panggil stored procedure pengembalian
    $sql  = "CALL sp_proses_kembali(?, @hasil, @denda)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // Ambil output
    $result = $conn->query("SELECT @hasil AS hasil, @denda AS denda");
    $row    = $result->fetch_assoc();

    if ($row['hasil'] !== 'BERHASIL') {
        throw new Exception($row['hasil']);
    }

    mysqli_commit($conn);

    header("Location: data_peminjaman.php");
    exit;

} catch (Exception $e) {

    mysqli_rollback($conn);

    header("Location: data_peminjaman.php?error=" . urlencode($e->getMessage()));
    exit;
}