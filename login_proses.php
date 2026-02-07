<?php
session_start();
include 'koneksi.php';

$email = $_POST['email'];
$password = $_POST['password'];

$query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
$user = mysqli_fetch_assoc($query);


if ($user && password_verify($password, $user['password'])) {
    $_SESSION['login'] = true;
    $_SESSION['id'] = $user['id'];
    $_SESSION['role'] = $user['role'];

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
