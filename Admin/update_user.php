<?php
session_start();
include '../koneksi.php';

// Cek login & role — FIX: file lama tidak ada cek ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: data_user.php");
    exit;
}

$id       = (int) $_POST['id'];
$username = mysqli_real_escape_string($conn, $_POST['username']);
$email    = mysqli_real_escape_string($conn, $_POST['email']);
$role     = mysqli_real_escape_string($conn, $_POST['role']);
$password = $_POST['password'];

/* =============================================
   GUNAKAN TRANSAKSI
   Elemen UKK: 1.16 (commit/rollback)
============================================= */
mysqli_begin_transaction($conn);

try {

    if ($password != "") {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, password=?, role=? WHERE id=?");
        $stmt->bind_param("ssssi", $username, $email, $hash, $role, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, role=? WHERE id=?");
        $stmt->bind_param("sssi", $username, $email, $role, $id);
    }

    if (!$stmt->execute()) {
        throw new Exception("Gagal update user: " . $stmt->error);
    }

    $stmt->close();

    mysqli_commit($conn);

    header("Location: data_user.php");
    exit;

} catch (Exception $e) {

    mysqli_rollback($conn);

    header("Location: data_user.php?error=" . urlencode($e->getMessage()));
    exit;
}