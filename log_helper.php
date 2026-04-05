<?php
function tambah_log($conn, $user_id, $username, $role, $aktivitas, $deskripsi) {
    $stmt = $conn->prepare("INSERT INTO log_aktivitas (user_id, username, role, aktivitas, deskripsi) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $username, $role, $aktivitas, $deskripsi);
    $stmt->execute();
}
?>