<?php
session_start();
include '../koneksi.php';
include '../config/log.php'; //

if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$username = $_POST['username'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role = $_POST['role'];

mysqli_query($conn, "INSERT INTO users (username, email, password, role)
VALUES ('$username', '$email', '$password', '$role')");

simpanLog($conn,
    'CRUD User',
    'Menambah user ' . $username
);


header("Location: admin.php");
