<?php

function simpanLog($conn, $aktivitas, $deskripsi)
{
    if (!isset($_SESSION['user_id'])) return;

    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $role = $_SESSION['role'];

    mysqli_query($conn,"
        INSERT INTO log_aktivitas
        (user_id, username, role, aktivitas, deskripsi)
        VALUES
        ('$user_id','$username','$role','$aktivitas','$deskripsi')
    ");
}