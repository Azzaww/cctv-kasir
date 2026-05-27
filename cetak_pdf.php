<?php
// Coba beberapa kemungkinan path vendor
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
} else {
    die('Error: vendor/autoload.php tidak ditemukan. Jalankan "composer install" di folder project, atau copy folder vendor ke ' . __DIR__);
}
use Dompdf\Dompdf;
include 'koneksi.php';

$id = (int)$_GET['id'];

$trx = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM transaksi WHERE id='$id'"));
$detail = mysqli_query($conn,"
SELECT d.*, p.nama_produk, p.satuan
FROM detail_transaksi d
JOIN produk p ON d.produk_id = p.id
WHERE d.transaksi_id='$id'
");

$kode        = htmlspecialchars($trx['kode_transaksi'] ?? 'TRX-'.$id);
$pelanggan   = htmlspecialchars($trx['pelanggan'] ?? 'Umum');
$tanggal_raw = isset($trx['tanggal']) ? $trx['tanggal'] : date('Y-m-d');
$tanggal     = date('d/m/Y', strtotime($tanggal_raw));
$total_raw   = $trx['total'] ?? 0;
$total       = number_format($total_raw, 0, ',', '.');
$diskon_raw  = $trx['diskon'] ?? 0;
$diskon      = number_format($diskon_raw, 0, ',', '.');
$subtotal    = number_format($total_raw + $diskon_raw, 0, ',', '.');
$termin_raw  = $trx['termin'] ?? 0;
$termin_str  = ($termin_raw > 0) ? $termin_raw . ' Hari' : '-';
$jt_raw      = $trx['jatuh_tempo'] ?? '';
$jatuh_tempo_str = !empty($jt_raw) ? date('d/m/Y', strtotime($jt_raw)) : '-';

// Build logo base64 dari file yang ada di folder yang sama
$logo_path = __DIR__ . '/logo.png';
$logo_b64  = '';
if (file_exists($logo_path)) {
    $logo_b64 = base64_encode(file_get_contents($logo_path));
}
$logo_src = $logo_b64 ? 'data:image/png;base64,' . $logo_b64 : '';

$rows = '';
$no   = 1;
while ($d = mysqli_fetch_assoc($detail)) {
    $bg   = ($no % 2 == 0) ? "#e8f4fc" : "#ffffff";
    $qty  = $d['qty'];
    $sat  = htmlspecialchars($d['satuan'] ?? 'UNIT');
    $hrg  = number_format($d['harga'], 0, ',', '.');
    $sub  = number_format($d['subtotal'], 0, ',', '.');
    $rows .= "
    <tr style='background:{$bg};'>
        <td style='text-align:center; padding:7px 8px; border:1px solid #b0cfe0;'>{$no}</td>
        <td style='padding:7px 8px; border:1px solid #b0cfe0;'>" . htmlspecialchars($d['nama_produk']) . "</td>
        <td style='text-align:center; padding:7px 8px; border:1px solid #b0cfe0;'>{$qty}</td>
        <td style='text-align:center; padding:7px 8px; border:1px solid #b0cfe0;'>{$sat}</td>
        <td style='text-align:right; padding:7px 8px; border:1px solid #b0cfe0;'>{$hrg}</td>
        <td style='text-align:center; padding:7px 8px; border:1px solid #b0cfe0;'></td>
        <td style='text-align:right; padding:7px 8px; border:1px solid #b0cfe0;'>{$sub}</td>
    </tr>";
    $no++;
}

$html = "
<!DOCTYPE html>
<html>
<head>
<meta charset='UTF-8'>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 11px;
    color: #1a1a1a;
    background: white;
    padding: 28px 32px;
}

/* ===== HEADER ===== */
.header-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 0;
}
.header-table td { vertical-align: middle; padding: 0; }
.logo-cell { width: 300px; }
.logo-cell img { width: 300px; height: auto; margin-left: 30px }
.title-cell { text-align: right; }
.invoice-title {
    font-size: 16px;
    font-weight: 900;
    margin-right: 60px;
    letter-spacing: 0.08em;
    color: #1a1a1a;
    text-decoration: underline;
    text-transform: uppercase;
}
.header-divider {
    border: none;
    border-top: 2px solid #1a1a1a;
    margin: 8px 0 10px 0;
}

/* ===== KEPADA ===== */
.kepada-section { margin-bottom: 10px; }
.kepada-label { font-size: 12px; font-weight: 700; color: #1a1a1a; margin-right: 250px;}
.kepada-value { font-size: 14px; font-weight: 900; font-style: bold; color: #1a1a1a; margin-top: 2px; text-transform: uppercase; margin-right: 200px;}

/* ===== META TABLE ===== */
.meta-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 14px;
    border: 1.5px solid #000;
}
.meta-table td {
    padding: 5px 8px;
    border: 1px solid #000;
    font-size: 10.5px;
}
.meta-table .meta-label { font-weight: 700; background: #ffffff; }
.meta-table .meta-value { font-weight: 700; }
.meta-table .nota-cell { font-weight: 900; font-size: 11px; }

/* ===== ITEMS TABLE ===== */
table.items {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 10px;
    font-size: 10.5px;
}
table.items thead tr {
    background: #00aadd;
    color: #ffffff;
}
table.items thead th {
    padding: 7px 8px;
    text-align: center;
    font-weight: 700;
    font-size: 10.5px;
    border: 1px solid #0088bb;
}
table.items tbody td { padding: 7px 8px; border: 1px solid #b0cfe0; }
table.items tfoot td {
    border: 1px solid #b0cfe0;
    padding: 7px 8px;
}

/* ===== SUMMARY ===== */
.summary-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 14px;
}
.summary-table td { padding: 2px 8px; font-size: 10.5px; }
.summary-right { text-align: right; width: 120px; }
.grand-total-row td {
    font-size: 13px;
    font-weight: 900;
    padding-top: 5px;
    padding-bottom: 5px;
}

/* ===== KETERANGAN ===== */
.keterangan-box {
    margin-bottom: 14px;
    padding-top: 6px;
}
.keterangan-label { font-weight: 700; font-size: 10.5px; margin-bottom: 2px; }
.keterangan-text { font-size: 10px; color: #444; font-style: italic; }

/* ===== PAYMENT ===== */
.payment-box {
    margin-bottom: 18px;
    font-size: 10.5px;
}
.payment-label { font-weight: 700; }

/* ===== SIGNATURE ===== */
.sig-table {
    width: 100%;
    border-collapse: collapse;
}
.sig-table td {
    text-align: center;
    width: 50%;
    padding: 0 20px;
    font-size: 10.5px;
    vertical-align: bottom;
}
.sig-space { height: 52px; }
.sig-name { display: inline-block; min-width: 130px; padding-top: 2px; font-weight: 700; }
</style>
</head>
<body>

<!-- HEADER -->
<table class='header-table'>
  <tr>
    <td class='logo-cell'>
      " . ($logo_src ? "<img src='{$logo_src}' alt='Waluyo Teknik Logo'>" : "<span style='font-size:18px;font-weight:900;color:#003366;'>WALUYO TEKNIK</span>") . "
    </td>
    <td class='title-cell'>
      <div class='invoice-title'>Invoice Penjualan</div>
      <br>
      <div class='kepada-label'>Kepada : </div>
      <div class='kepada-value'>{$pelanggan}</div>
    </td>
  </tr>
</table>

<br>


<!-- META TABLE (Tanggal / Termin / Jatuh Tempo / No. Nota / Total) -->
<table class='meta-table'>
  <tr>
    <td class='meta-label' style='width:18%;'>Tanggal</td>
    <td class='meta-label' style='width:16%;'>Termin</td>
    <td class='meta-label' style='width:18%;'>Jatuh Tempo</td>
    <td class='nota-cell' style='width:48%;' colspan='2'>No. Nota : {$kode}</td>
  </tr>
  <tr>
    <td class='meta-value'>{$tanggal}</td>
    <td class='meta-value'>{$termin_str}</td>
    <td class='meta-value'>{$jatuh_tempo_str}</td>
    <td class='meta-label' style='width:10%;'>Total :</td>
    <td class='meta-value' style='text-align:right; width:38%;'>{$total}</td>
  </tr>
</table>

<!-- ITEMS TABLE -->
<table class='items'>
  <thead>
    <tr>
      <th style='width:32px;'>No</th>
      <th style='text-align:left;'>Barang</th>
      <th style='width:45px;'>Qty</th>
      <th style='width:50px;'>Unit</th>
      <th style='width:80px;'>Harga</th>
      <th style='width:50px;'>Disc</th>
      <th style='width:80px;'>Subtotal</th>
    </tr>
  </thead>
  <tbody>
    {$rows}
  </tbody>
</table>

<!-- SUMMARY + KETERANGAN side by side -->
<table style='width:100%; border-collapse:collapse; margin-bottom:14px;'>
  <tr>
    <td style='width:55%; vertical-align:top; padding-right:20px;'>
      <div class='keterangan-box'>
        <div class='keterangan-label'>Keterangan:</div>
        <div class='keterangan-text'>Barang yang sudah dibeli tidak dapat ditukar / dikembalikan</div>
      </div>
      <div class='payment-box'>
        <div class='payment-label'>Pembayaran Transfer ke Rekening</div>
        <div><strong>BCA 8161825824 A/N ZAINAL ARIFIN</strong></div>
      </div>
    </td>
    <td style='width:45%; vertical-align:top; padding-left:20px;'>
      <table style='width:100%; border-collapse:collapse; font-size:10.5px;'>
        <tr>
          <td>Sub Total Rp.</td>
          <td style='text-align:right; font-weight:600;'>{$subtotal}</td>
        </tr>
        <tr>
          <td>Diskon Rp.</td>
          <td style='text-align:right; font-weight:600;'>{$diskon}</td>
        </tr>
       <br>
        <tr>
          <td style='font-size:13px; font-weight:900;'><strong>GRAND TOTAL Rp.</strong></td>
          <td style='text-align:right; font-size:13px; font-weight:900;'><strong>{$total}</strong></td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<hr style='border:none;border-top:1.5px solid #1a1a1a;margin-bottom:18px;'>


<!-- SIGNATURE -->
<table class='sig-table'>
  <tr>
    <td>
      <div>Hormat Kami</div>
      <br>
      <br>
      <div class='sig-space'></div>
      <span class='sig-name'>(Zainal Arifin)</span>
    </td>
    <td>
      <div>Penerima</div>
      <br>
      <br>
      <div class='sig-space'></div>
      <span class='sig-name'>({$pelanggan})</span>
    </td>
  </tr>
</table>

</body>
</html>
";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("invoice-{$kode}.pdf");
?>
