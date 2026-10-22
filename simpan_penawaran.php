<?php
// simpan_penawaran.php
include 'koneksi.php';

$nomor    = mysqli_real_escape_string($conn, $_POST['nomor_penawaran'] ?? '');
$judul    = mysqli_real_escape_string($conn, $_POST['judul'] ?? '');
$kepada   = mysqli_real_escape_string($conn, $_POST['kepada'] ?? '');
$tanggal  = mysqli_real_escape_string($conn, $_POST['tanggal'] ?? date('Y-m-d'));

// Insert header penawaran (tanpa field instansi & keterangan, isi surat dibuat otomatis dari judul)
mysqli_query($conn,"
INSERT INTO penawaran(nomor_penawaran, judul, kepada, tanggal, total)
VALUES('$nomor','$judul','$kepada','$tanggal',0)
");

$penawaran_id = mysqli_insert_id($conn);
$total = 0;

if(isset($_POST['nama_barang']) && is_array($_POST['nama_barang'])) {
    foreach($_POST['nama_barang'] as $key => $nama_barang) {
        $nama      = mysqli_real_escape_string($conn, trim($nama_barang));
        $qty       = (int)($_POST['qty'][$key] ?? 1);
        $satuan    = mysqli_real_escape_string($conn, $_POST['satuan'][$key] ?? 'unit');
        $harga     = (float)($_POST['harga'][$key] ?? 0);
        $deskripsi = mysqli_real_escape_string($conn, trim($_POST['deskripsi'][$key] ?? ''));

        if($nama === '' || $qty <= 0) continue;

        if($deskripsi === '') {
            $p_check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT deskripsi FROM produk WHERE nama_produk='$nama' LIMIT 1"));
            if($p_check && !empty($p_check['deskripsi'])) {
                $deskripsi = mysqli_real_escape_string($conn, $p_check['deskripsi']);
            }
        }

        $subtotal = $qty * $harga;
        $total += $subtotal;

        mysqli_query($conn,"
        INSERT INTO detail_penawaran(penawaran_id, nama_barang, deskripsi, qty, satuan, harga, subtotal)
        VALUES('$penawaran_id','$nama','$deskripsi','$qty','$satuan','$harga','$subtotal')
        ");
    }
}

// Update total
mysqli_query($conn,"UPDATE penawaran SET total='$total' WHERE id='$penawaran_id'");

header("Location: detail_penawaran.php?id=$penawaran_id");
exit;
?>
