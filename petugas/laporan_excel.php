<?php
session_start();
include '../koneksi.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=laporan_transaksi.xls");

$data = mysqli_query($conn, "
    SELECT u.username, a.nama_alat, p.jumlah, p.status, p.tanggal_pinjam, p.tanggal_kembali
    FROM peminjaman p
    JOIN users u ON p.user_id = u.id
    JOIN alat a ON p.alat_id = a.id
");
?>

<table border="1">
<tr>
    <th>User</th>
    <th>Barang</th>
    <th>Jumlah</th>
    <th>Status</th>
    <th>Tgl Pinjam</th>
    <th>Tgl Kembali</th>
</tr>
<?php while($d = mysqli_fetch_assoc($data)){ ?>
<tr>
    <td><?= $d['username'] ?></td>
    <td><?= $d['nama_alat'] ?></td>
    <td><?= $d['jumlah'] ?></td>
    <td><?= $d['status'] ?></td>
    <td><?= $d['tanggal_pinjam'] ?></td>
    <td><?= $d['tanggal_kembali'] ?></td>
</tr>
<?php } ?>
</table>
