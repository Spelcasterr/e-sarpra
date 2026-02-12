<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");

    if (!$query) {
        die("Query error: " . mysqli_error($conn));
    }

    $user = mysqli_fetch_assoc($query);

    if ($user && password_verify($password, $user['password'])) {

        // SESSION YANG KONSISTEN DENGAN ADMIN.PHP
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['login'] = true;

        // Redirect sesuai role
        if ($user['role'] === 'admin') {
            header("Location: admin/admin.php");
        } elseif ($user['role'] === 'petugas') {
            header("Location: petugas/petugas.php");
        } else {
            header("Location: user/peminjam.php");
        }

        exit;

    } else {
        echo "<script>
                alert('Email atau password salah');
                window.location='login.php';
              </script>";
        exit;
    }

} else {
    header("Location: login.php");
    exit;
}
?>
