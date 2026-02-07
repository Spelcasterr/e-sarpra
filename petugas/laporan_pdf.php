<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../login.php");
    exit;
}

require '../vendor/autoload.php';
use Dompdf\Dompdf;

/* ======================
   AMBIL DATA
====================== */
$data = mysqli_query($conn, "
    SELECT u.username, a.nama_alat, p.jumlah, p.status, 
           p.tanggal_pinjam, p.tanggal_kembali
    FROM peminjaman p
    JOIN users u ON p.user_id = u.id
    JOIN alat a ON p.alat_id = a.id
    ORDER BY p.id DESC
");

/* ======================
   HTML LAPORAN
====================== */
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial; font-size: 12px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #eee; }
    </style>
</head>
<body>

<h2>LAPORAN TRANSAKSI PEMINJAMAN & PENGEMBALIAN</h2>

<table>
<tr>
    <th>No</th>
    <th>User</th>
    <th>Barang</th>
    <th>Jumlah</th>
    <th>Status</th>
    <th>Tgl Pinjam</th>
    <th>Tgl Kembali</th>
</tr>
';

$no = 1;
while ($d = mysqli_fetch_assoc($data)) {
    $html .= '
    <tr>
        <td>'.$no++.'</td>
        <td>'.$d['username'].'</td>
        <td>'.$d['nama_alat'].'</td>
        <td>'.$d['jumlah'].'</td>
        <td>'.ucfirst($d['status']).'</td>
        <td>'.$d['tanggal_pinjam'].'</td>
        <td>'.$d['tanggal_kembali'].'</td>
    </tr>
    ';
}

$html .= '
</table>

<p style="margin-top:15px">
    Dicetak pada: '.date('d-m-Y H:i:s').'
</p>

</body>
</html>
';

/* ======================
   GENERATE PDF
====================== */
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream(
    "laporan_transaksi.pdf",
    ["Attachment" => false]
);
