<?php
session_start();
include '../koneksi.php';

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

// Validasi parameter id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID tidak valid.");
}

$id = (int) $_GET['id'];
$user_id = $_SESSION['user_id'];

// Gunakan prepared statement (WAJIB)
$stmt = $conn->prepare("
    UPDATE peminjaman
    SET status = 'menunggu_pengembalian'
    WHERE id = ?
    AND user_id = ?
    AND status IN ('disetujui', 'terlambat')
");

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    header("Location: peminjaman_saya.php");
    exit;
} else {
    die("Gagal mengajukan pengembalian atau status tidak valid.");
}