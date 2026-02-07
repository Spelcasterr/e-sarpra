<?php
include '../koneksi.php';

$id = $_POST['id'];
$username = $_POST['username'];
$email = $_POST['email'];
$role = $_POST['role'];
$password = $_POST['password'];

if($password != ""){

    $hash = password_hash($password, PASSWORD_DEFAULT);

    mysqli_query($conn,"UPDATE users SET
        username='$username',
        email='$email',
        password='$hash',
        role='$role'
        WHERE id='$id'
    ");

}else{

    mysqli_query($conn,"UPDATE users SET
        username='$username',
        email='$email',
        role='$role'
        WHERE id='$id'
    ");
}

header("Location: data_user.php");
exit;
