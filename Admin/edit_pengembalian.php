<?php
session_start();
include '../koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = $_GET['id'];
$query = mysqli_query($conn,"SELECT * FROM pengembalian WHERE id='$id'");
$data = mysqli_fetch_assoc($query);
?>

<h3>Edit Pengembalian</h3>

<form action="update_pengembalian.php" method="post">
<input type="hidden" name="id" value="<?= $data['id'] ?>">

<label>Tanggal Kembali</label><br>
<input type="date" name="tanggal_kembali" value="<?= $data['tanggal_kembali'] ?>" required>

<br><br>

<label>Denda</label><br>
<input type="number" name="denda" value="<?= $data['denda'] ?>">

<br><br>

<button type="submit">Update</button>
</form>
