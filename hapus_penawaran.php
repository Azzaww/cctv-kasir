<?php
// hapus_penawaran.php
session_start();
if(!isset($_SESSION['login'])){ header("Location: login.php"); exit; }
include 'koneksi.php';

$id = (int)$_POST['id'];
if($id > 0) {
    mysqli_query($conn,"DELETE FROM detail_penawaran WHERE penawaran_id='$id'");
    mysqli_query($conn,"DELETE FROM penawaran WHERE id='$id'");
}
header("Location: penawaran.php");
exit;
?>
