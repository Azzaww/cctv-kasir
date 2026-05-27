<?php

include 'koneksi.php';

$pelanggan   = mysqli_real_escape_string($conn, $_POST['pelanggan'] ?? '');
$termin      = isset($_POST['termin']) && $_POST['termin'] !== '' ? (int)$_POST['termin'] : 0;
$jatuh_tempo = isset($_POST['jatuh_tempo']) && $_POST['jatuh_tempo'] !== '' 
               ? "'" . mysqli_real_escape_string($conn, $_POST['jatuh_tempo']) . "'" 
               : "NULL";

mysqli_query($conn,"
INSERT INTO transaksi(pelanggan, total, termin, jatuh_tempo)
VALUES('$pelanggan', 0, '$termin', $jatuh_tempo)
");

$transaksi_id = mysqli_insert_id($conn);

$total = 0;

foreach($_POST['produk'] as $key => $produk_id){

    $qty = $_POST['qty'][$key];

    if($produk_id != ""){

        $produk = mysqli_fetch_array(mysqli_query($conn,"
        SELECT * FROM produk WHERE id='$produk_id'
        "));

        $harga = $produk['harga'];

        $subtotal = $harga * $qty;

        $total += $subtotal;

        mysqli_query($conn,"
        INSERT INTO detail_transaksi(
            transaksi_id,
            produk_id,
            qty,
            harga,
            subtotal
        ) VALUES(
            '$transaksi_id',
            '$produk_id',
            '$qty',
            '$harga',
            '$subtotal'
        )
        ");
    }
}

mysqli_query($conn,"
UPDATE transaksi 
SET total='$total'
WHERE id='$transaksi_id'
");

header("Location: detail.php?id=$transaksi_id");

?>