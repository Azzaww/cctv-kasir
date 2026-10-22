<?php
session_start();
if(!isset($_SESSION['login'])){ header("Location: login.php"); exit; }
include 'koneksi.php';

if(isset($_POST['edit_id']) && !empty($_POST['edit_id'])) {
    $id        = (int)$_POST['edit_id'];
    $nama      = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $harga     = (int)$_POST['harga'];
    $satuan    = mysqli_real_escape_string($conn, $_POST['satuan']);
    $deskripsi = isset($_POST['deskripsi']) ? mysqli_real_escape_string($conn, trim($_POST['deskripsi'])) : '';
    mysqli_query($conn,"UPDATE produk SET nama_produk='$nama', harga=$harga, satuan='$satuan', deskripsi='$deskripsi' WHERE id=$id");
} else {
    $nama      = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $harga     = (int)$_POST['harga'];
    $satuan    = mysqli_real_escape_string($conn, $_POST['satuan']);
    $deskripsi = isset($_POST['deskripsi']) ? mysqli_real_escape_string($conn, trim($_POST['deskripsi'])) : '';
    mysqli_query($conn,"INSERT INTO produk (nama_produk, harga, satuan, deskripsi) VALUES ('$nama', $harga, '$satuan', '$deskripsi')");
}

header("Location: produk.php?saved=1");
?>
