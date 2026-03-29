<?php
session_start();
include 'koneksi.php';
include 'config/log.php';

$email = $_POST['email'];
$password = $_POST['password'];

$query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
$user = mysqli_fetch_assoc($query);

if ($user && password_verify($password, $user['password'])) {

    $_SESSION['login'] = true;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    simpanLog($conn, "Login", "User berhasil login");

    if ($user['role'] == 'admin') {
        header("Location: admin/admin.php");
    } elseif ($user['role'] == 'petugas') {
        header("Location: petugas/petugas.php");
    } else {
        header("Location: user/peminjam.php");
    }

} else {
    echo "Login gagal";
}