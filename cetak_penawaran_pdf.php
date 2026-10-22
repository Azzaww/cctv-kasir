<?php
// cetak_penawaran_pdf.php
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
} else {
    die('Error: vendor/autoload.php tidak ditemukan. Jalankan "composer install".');
}
use Dompdf\Dompdf;
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

// Helper
$nomor      = htmlspecialchars($pnw['nomor_penawaran'] ?? '-');
$judul      = htmlspecialchars($pnw['judul'] ?? '-');
$kepada     = htmlspecialchars($pnw['kepada'] ?? '-');
$total_raw  = (float)($pnw['total'] ?? 0);
$total_fmt  = number_format($total_raw, 0, ',', '.');

// Tanggal format Indonesia panjang
$bulan_nama = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$tgl = !empty($pnw['tanggal']) ? strtotime($pnw['tanggal']) : time();
$tanggal_str = date('d', $tgl) . ' ' . $bulan_nama[(int)date('n', $tgl)] . ' ' . date('Y', $tgl);

// Logo base64
$logo_path = __DIR__ . '/logo.png';
$logo_b64  = '';
if (file_exists($logo_path)) {
    $logo_b64 = base64_encode(file_get_contents($logo_path));
}
$logo_src = $logo_b64 ? 'data:image/png;base64,' . $logo_b64 : '';

// Build rows barang
$rows_html = '';
$no = 1;
while($d = mysqli_fetch_assoc($detail)) {
    $bg   = ($no % 2 == 0) ? '#e8f4fc' : '#ffffff';
    $hrg  = number_format((float)$d['harga'], 0, ',', '.');
    $sub  = number_format((float)$d['subtotal'], 0, ',', '.');
    $sat  = htmlspecialchars($d['satuan'] ?? 'unit');
    $desk = !empty($d['deskripsi']) ? $d['deskripsi'] : ($d['produk_deskripsi'] ?? '');
    $desk_html = !empty($desk) ? nl2br(htmlspecialchars($desk)) : '-';
    $rows_html .= "
    <tr style='background:{$bg};'>
        <td style='text-align:center;padding:8px 6px;border:1px solid #b0cfe0;'>{$no}</td>
        <td style='padding:8px 8px;border:1px solid #b0cfe0;font-weight:bold;'>" . htmlspecialchars($d['nama_barang']) . "</td>
        <td style='padding:8px 8px;border:1px solid #b0cfe0;color:#222;font-size:11px;'>{$desk_html}</td>
        <td style='text-align:center;padding:8px 6px;border:1px solid #b0cfe0;'>{$d['qty']}</td>
        <td style='text-align:center;padding:8px 6px;border:1px solid #b0cfe0;'>{$sat}</td>
        <td style='text-align:right;padding:8px 8px;border:1px solid #b0cfe0;'>{$hrg}</td>
        <td style='text-align:right;padding:8px 8px;border:1px solid #b0cfe0;'>{$sub}</td>
    </tr>";
    $no++;
}

// Isi pengantar surat (ditambah spasi agar kata tidak menempel)
$isi_surat = 'Sehubungan dengan adanya instalasi sistem keamanan di instansi Bapak/Ibu, kami dari '
    . '<strong>Waluyo Teknik</strong> mengajukan <strong>' . $judul . '</strong> '
    . 'dengan rincian sebagai berikut:';

$html = "
<!DOCTYPE html>
<html>
<head>
<meta charset='UTF-8'>
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family: 'Times New Roman', Times, serif; }
body {
    font-family: 'Times New Roman', Times, serif;
    font-size: 13px;
    color: #1a1a1a;
    background: white;
    padding: 28px 36px;
}

/* HEADER */
.header-wrap { width:100%; text-align:center; margin-bottom:6px; font-family: 'Times New Roman', Times, serif; }
.header-wrap img { width:340px; height:auto; display:inline-block; }
.header-divider { border:none; border-top:2.5px solid #003399; margin:6px 0 14px 0; }

/* NOMOR / PERIHAL / TANGGAL */
.surat-meta { margin-bottom: 16px; font-size: 13px; line-height: 2; font-family: 'Times New Roman', Times, serif; }
.surat-meta td { padding: 0 4px 0 0; font-family: 'Times New Roman', Times, serif; }
.surat-meta .lbl { font-weight:700; white-space:nowrap; }
.surat-meta .sep { padding: 0 6px; }

/* KEPADA */
.kepada-box { margin-bottom: 16px; font-size: 13px; font-family: 'Times New Roman', Times, serif; }
.kepada-label { font-weight: 700; margin-bottom: 2px; }
.kepada-value { font-size:14px; font-weight:700; }
.kepada-sub { color:#333; margin-top:2px; }

/* ISI SURAT */
.surat-p1 { margin-bottom: 4px; font-size: 13px; font-family: 'Times New Roman', Times, serif; }
.surat-p2 { margin-bottom: 16px; font-size: 13px; line-height: 1.7; font-family: 'Times New Roman', Times, serif; }

/* TABLE BARANG */
.tbl-title { font-family: 'Times New Roman', Times, serif; font-size:13.5px; font-weight:900; margin-bottom:8px; }
table.items {
    width: 100%; border-collapse: collapse;
    margin-bottom: 14px; font-size: 12px;
    font-family: 'Times New Roman', Times, serif;
}
table.items thead tr { background: #00aadd; color: white; }
table.items thead th {
    padding: 8px 6px; text-align:center;
    font-weight:700; border:1px solid #0088bb; font-size:12px;
    font-family: 'Times New Roman', Times, serif;
}
table.items tbody td {
    font-family: 'Times New Roman', Times, serif;
}
table.items tfoot td {
    padding:8px 8px; border:1px solid #b0cfe0;
    font-weight:900; font-size:12.5px;
    font-family: 'Times New Roman', Times, serif;
}

/* NOTE */
.note-box {
    font-family: 'Times New Roman', Times, serif;
    font-size: 11px; color: #555;
    font-style: italic; margin-bottom: 16px;
    border-top: 1px solid #ddd; padding-top: 6px;
}
.note-box strong { color: #cc0000; }

/* PENUTUP */
.penutup { font-family: 'Times New Roman', Times, serif; font-size: 13px; line-height: 1.75; margin-bottom: 20px; }

/* TTD */
.ttd-table { width: 100%; border-collapse:collapse; margin-top: 12px; font-family: 'Times New Roman', Times, serif; }
.ttd-table td { text-align:right; padding:0; font-size: 13px; font-family: 'Times New Roman', Times, serif; }
.ttd-space { height: 56px; }
.ttd-nama { font-weight: 900; font-size: 14px; font-family: 'Times New Roman', Times, serif; }
</style>
</head>
<body>

<!-- HEADER / KOP SURAT -->
<table style='width:100%;border-collapse:collapse;margin-bottom:6px;'>
  <tr>
    <td style='text-align:center;vertical-align:middle;padding:0;'>
      " . ($logo_src ? "<img src='{$logo_src}' alt='Waluyo Teknik' style='width:340px;height:auto;'>" : "<span style='font-size:28px;font-weight:900;color:#003399;'>WALUYO TEKNIK</span>") . "
    </td>
  </tr>
</table>
<hr class='header-divider'>

<!-- NOMOR / PERIHAL / TANGGAL -->
<table class='surat-meta'>
  <tr>
    <td class='lbl'>Nomor</td>
    <td class='sep'>:</td>
    <td>{$nomor}</td>
  </tr>
  <tr>
    <td class='lbl'>Perihal</td>
    <td class='sep'>:</td>
    <td><strong>{$judul}</strong></td>
  </tr>
  <tr>
    <td class='lbl'>Tanggal</td>
    <td class='sep'>:</td>
    <td>{$tanggal_str}</td>
  </tr>
</table>

<br>

<!-- KEPADA -->
<div class='kepada-box'>
  <div class='kepada-label'>Kepada Yth.</div>
  <div class='kepada-value'>Bapak/Ibu {$kepada}</div>
  <div class='kepada-sub'>di Tempat</div>
</div>

<div class='surat-p1'>Dengan hormat,</div>
<div class='surat-p2'>{$isi_surat}</div>

<!-- TABLE BARANG -->
<div class='tbl-title'>Daftar Rincian Harga Penawaran</div>
<table class='items'>
  <thead>
    <tr>
      <th style='width:25px;'>No</th>
      <th style='text-align:left;width:150px;'>Barang</th>
      <th style='text-align:left;'>Deskripsi</th>
      <th style='width:35px;'>Qty</th>
      <th style='width:45px;'>Unit</th>
      <th style='width:80px;'>Harga</th>
      <th style='width:85px;'>Sub Total</th>
    </tr>
  </thead>
  <tbody>
    {$rows_html}
  </tbody>
  <tfoot>
    <tr>
      <td colspan='6' style='text-align:right;border:1px solid #b0cfe0;font-weight:900;background:#f0f7ff;'>Total</td>
      <td style='text-align:right;border:1px solid #b0cfe0;font-weight:900;background:#f0f7ff;color:#003399;'>{$total_fmt}</td>
    </tr>
  </tfoot>
</table>

<!-- NOTE -->
<div class='note-box'>
  <strong>Note : Harga tidak tetap, Bisa berubah suatu saat tanpa pemberitahuan</strong>
</div>

<!-- PENUTUP -->
<div class='penutup'>
  Demikian surat penawaran harga ini kami sampaikan. Besar harapan kami untuk dapat
  bekerja sama dengan pihak Bapak/Ibu. Atas perhatian dan kesempatan yang diberikan, kami ucapkan
  terima kasih.
</div>

<!-- TTD -->
<table class='ttd-table'>
  <tr>
    <td>
      <div>Hormat Kami,</div>
      <div class='ttd-space'></div>
      <div class='ttd-nama'>Waluyo Teknik</div>
      <div>( Zainal Arifin )</div>
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
$filename = 'Penawaran-' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $pnw['kepada'] ?? $id);
$dompdf->stream("{$filename}.pdf", ['Attachment' => true]);
?>