<?php
function simpanLog($conn, $aktivitas, $deskripsi) {
    if (!isset($_SESSION['id'])) return;

    $user_id = $_SESSION['id'];
    $role    = $_SESSION['role'];

    mysqli_query($conn, "
        INSERT INTO log_aktivitas (user_id, role, aktivitas, deskripsi)
        VALUES ('$user_id', '$role', '$aktivitas', '$deskripsi')
    ");
}
