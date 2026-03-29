<?php
function tambah_log($conn, $user_id, $role, $aktivitas, $keterangan) {
    $stmt = $conn->prepare("INSERT INTO log_aktivitas (user_id, role, aktivitas, keterangan) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $role, $aktivitas, $keterangan);
    $stmt->execute();
}
?>