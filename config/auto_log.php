<?php
if (!isset($_SESSION['user_id'])) return;

include_once 'log.php';

$halaman = basename($_SERVER['PHP_SELF']);

$aktivitas = "Akses Halaman";
$deskripsi = "Membuka halaman ".$halaman;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aktivitas = "Submit Form";
    $deskripsi = "Mengirim data di ".$halaman;
}

simpanLog($conn, $aktivitas, $deskripsi);