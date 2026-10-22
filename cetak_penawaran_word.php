<?php
// cetak_penawaran_word.php
session_start();
if(!isset($_SESSION['login'])){ header("Location: login.php"); exit; }
include 'koneksi.php';

$id  = (int)$_GET['id'];
$pnw = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM penawaran WHERE id='$id'"));
if(!$pnw){ die('Data tidak ditemukan'); }

$detail = mysqli_query($conn,"
    SELECT dp.*, p.deskripsi AS produk_deskripsi
    FROM detail_penawaran dp
    LEFT JOIN produk p ON dp.nama_barang = p.nama_produk
    WHERE dp.penawaran_id='$id'
    ORDER BY dp.id ASC
");

// Data
$nomor      = htmlspecialchars($pnw['nomor_penawaran'] ?? '-');
$judul      = htmlspecialchars($pnw['judul'] ?? '-');
$kepada     = htmlspecialchars($pnw['kepada'] ?? '-');
$total_raw  = (float)($pnw['total'] ?? 0);
$total_fmt  = number_format($total_raw, 0, ',', '.');

$bulan_nama = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$tgl = !empty($pnw['tanggal']) ? strtotime($pnw['tanggal']) : time();
$tanggal_str = date('d', $tgl) . ' ' . $bulan_nama[(int)date('n', $tgl)] . ' ' . date('Y', $tgl);

// Logo
$logo_path = __DIR__ . '/logo.png';
$logo_html = '';
if(file_exists($logo_path)) {
    $logo_b64 = base64_encode(file_get_contents($logo_path));
    $logo_html = "<img src='data:image/png;base64,{$logo_b64}' width='320' style='display:block;margin:0 auto;'>";
} else {
    $logo_html = "<strong style='font-size:26pt;color:#003399;'>WALUYO TEKNIK</strong>";
}

// Isi pengantar surat (ditambah spasi agar kata tidak menempel)
$isi_surat = 'Sehubungan dengan adanya instalasi sistem keamanan di instansi Bapak/Ibu, kami dari '
    . '<b>Waluyo Teknik</b> mengajukan <b>' . $judul . '</b> '
    . 'dengan rincian sebagai berikut:';

// Build rows
$rows_html = '';
$no = 1;
while($d = mysqli_fetch_assoc($detail)) {
    $bg   = ($no % 2 == 0) ? '#d6eaf8' : '#ffffff';
    $hrg  = number_format((float)$d['harga'], 0, ',', '.');
    $sub  = number_format((float)$d['subtotal'], 0, ',', '.');
    $sat  = htmlspecialchars($d['satuan'] ?? 'unit');
    $desk = !empty($d['deskripsi']) ? $d['deskripsi'] : ($d['produk_deskripsi'] ?? '');
    $desk_html = !empty($desk) ? nl2br(htmlspecialchars($desk)) : '-';
    $rows_html .= "
    <tr style='background:{$bg};mso-background-source:auto;'>
        <td style='border:1pt solid #b0cfe0;padding:6pt 5pt;text-align:center;'>{$no}</td>
        <td style='border:1pt solid #b0cfe0;padding:6pt 7pt;font-weight:bold;'>" . htmlspecialchars($d['nama_barang']) . "</td>
        <td style='border:1pt solid #b0cfe0;padding:6pt 7pt;font-size:10.5pt;color:#222;'>{$desk_html}</td>
        <td style='border:1pt solid #b0cfe0;padding:6pt 5pt;text-align:center;'>{$d['qty']}</td>
        <td style='border:1pt solid #b0cfe0;padding:6pt 5pt;text-align:center;'>{$sat}</td>
        <td style='border:1pt solid #b0cfe0;padding:6pt 7pt;text-align:right;'>{$hrg}</td>
        <td style='border:1pt solid #b0cfe0;padding:6pt 7pt;text-align:right;'>{$sub}</td>
    </tr>";
    $no++;
}

// Nama file
$filename = 'Penawaran-' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $pnw['kepada'] ?? $id);

header('Content-Type: application/vnd.ms-word');
header('Content-Disposition: attachment; filename="' . $filename . '.doc"');
header('Cache-Control: no-cache, no-store, must-revalidate');
?>
<html xmlns:o='urn:schemas-microsoft-com:office:office'
      xmlns:w='urn:schemas-microsoft-com:office:word'
      xmlns='http://www.w3.org/TR/REC-html40'>
<head>
<meta charset='UTF-8'>
<title>Surat Penawaran</title>
<!--[if gte mso 9]>
<xml>
  <w:WordDocument>
    <w:View>Print</w:View>
    <w:Zoom>100</w:Zoom>
    <w:DoNotOptimizeForBrowser/>
  </w:WordDocument>
</xml>
<![endif]-->
<style>
  * {
    font-family: "Times New Roman", Times, serif;
  }
  body {
    font-family: "Times New Roman", Times, serif;
    font-size: 12pt;
    color: #1a1a1a;
    margin: 0;
    padding: 0;
    line-height: 1.6;
  }
  @page {
    margin: 2cm 2.5cm 2cm 2.5cm;
    size: A4;
  }
  table { border-collapse: collapse; width: 100%; font-family: "Times New Roman", Times, serif; }
  td, th, p, span, div, strong, b { font-family: "Times New Roman", Times, serif; }
  hr.divider { border:none; border-top:2.5pt solid #003399; margin:6pt 0 14pt 0; }
  
  .lbl { font-weight:bold; white-space:nowrap; font-size:12pt; }
  .perihal-bold { font-weight:bold; font-size:12pt; }
  
  th.items-th {
    background:#00aadd; color:white;
    padding:7pt 6pt; text-align:center;
    font-size:11pt; border:1pt solid #0088bb;
    font-family: "Times New Roman", Times, serif;
  }
  .total-row td {
    font-weight:900; background:#e8f4fc;
    border:1pt solid #b0cfe0; padding:7pt 8pt;
    font-size:11pt;
    font-family: "Times New Roman", Times, serif;
  }
  .note { font-size:10pt; color:#cc0000; font-style:italic; font-family: "Times New Roman", Times, serif; }
  .ttd-col { text-align:right; font-size:11.5pt; font-family: "Times New Roman", Times, serif; }
</style>
</head>
<body>

<!-- KOP SURAT -->
<table style='width:100%;margin-bottom:6pt;'>
  <tr>
    <td style='text-align:center;padding:0;' align='center'>
      <?= $logo_html ?>
    </td>
  </tr>
</table>

<hr class='divider'>

<!-- NOMOR / PERIHAL / TANGGAL -->
<table style='width:auto;margin-bottom:12pt;font-size:12pt;'>
  <tr>
    <td class='lbl' style='padding:2pt 6pt 2pt 0;'>Nomor</td>
    <td style='padding:2pt 6pt;'>:</td>
    <td style='padding:2pt 0;'><?= $nomor ?></td>
  </tr>
  <tr>
    <td class='lbl' style='padding:2pt 6pt 2pt 0;'>Perihal</td>
    <td style='padding:2pt 6pt;'>:</td>
    <td class='perihal-bold' style='padding:2pt 0;'><?= $judul ?></td>
  </tr>
  <tr>
    <td class='lbl' style='padding:2pt 6pt 2pt 0;'>Tanggal</td>
    <td style='padding:2pt 6pt;'>:</td>
    <td style='padding:2pt 0;'><?= $tanggal_str ?></td>
  </tr>
</table>

<br>

<!-- KEPADA -->
<p style='margin-bottom:2pt;font-size:12pt;'>Kepada Yth.</p>
<p style='font-weight:bold;font-size:13pt;margin-bottom:2pt;'>Bapak/Ibu <?= $kepada ?></p>
<p style='margin-bottom:14pt;font-size:12pt;'>di Tempat</p>

<!-- ISI SURAT (Jarak rapat antara "Dengan hormat," dan isi) -->
<p style='margin-bottom:4pt;font-size:12pt;'>Dengan hormat,</p>
<p style='margin-bottom:16pt;line-height:1.7;font-size:12pt;'><?= $isi_surat ?></p>

<!-- JUDUL TABLE -->
<p style='font-weight:bold;font-size:12pt;margin-bottom:6pt;'>Daftar Rincian Harga Penawaran</p>

<!-- TABLE BARANG -->
<table style='width:100%;border-collapse:collapse;margin-bottom:14pt;font-size:11pt;'>
  <thead>
    <tr>
      <th class='items-th' style='width:25pt;'>No</th>
      <th class='items-th' style='text-align:left;width:130pt;'>Barang</th>
      <th class='items-th' style='text-align:left;'>Deskripsi</th>
      <th class='items-th' style='width:35pt;'>Qty</th>
      <th class='items-th' style='width:40pt;'>Unit</th>
      <th class='items-th' style='width:80pt;'>Harga</th>
      <th class='items-th' style='width:85pt;'>Sub Total</th>
    </tr>
  </thead>
  <tbody>
    <?= $rows_html ?>
  </tbody>
  <tfoot>
    <tr class='total-row'>
      <td colspan='6' style='text-align:right;border:1pt solid #b0cfe0;font-weight:bold;padding:7pt 8pt;background:#e8f4fc;font-size:11pt;'>Total</td>
      <td style='text-align:right;border:1pt solid #b0cfe0;font-weight:bold;padding:7pt 8pt;background:#e8f4fc;color:#003399;font-size:11.5pt;'><?= $total_fmt ?></td>
    </tr>
  </tfoot>
</table>

<!-- NOTE -->
<p class='note' style='border-top:1pt solid #ddd;padding-top:6pt;margin-bottom:16pt;'>
  <b>Note : Harga tidak tetap, Bisa berubah suatu saat tanpa pemberitahuan</b>
</p>

<!-- PENUTUP -->
<p style='margin-bottom:6pt;line-height:1.75;font-size:12pt;'>
  Demikian surat penawaran harga ini kami sampaikan. Besar harapan kami untuk dapat
  bekerja sama dengan pihak Bapak/Ibu. Atas perhatian dan kesempatan yang diberikan, kami ucapkan
  terima kasih.
</p>

<br><br>

<!-- TTD -->
<table style='width:100%;'>
  <tr>
    <td style='width:55%;'>&nbsp;</td>
    <td class='ttd-col'>
      <p style='font-size:12pt;margin-bottom:0;'>Hormat Kami,</p>
      <br><br><br><br>
      <p style='font-weight:bold;font-size:13pt;margin-bottom:2pt;'>Waluyo Teknik</p>
      <p style='font-size:12pt;'>( Zainal Arifin )</p>
    </td>
  </tr>
</table>

</body>
</html>