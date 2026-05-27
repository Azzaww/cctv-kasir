<!-- detail.php -->
<?php
session_start();
if(!isset($_SESSION['login'])){ header("Location: login.php"); exit; }
include 'koneksi.php';
include '_style.php';

$id = (int)$_GET['id'];

$trx = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM transaksi WHERE id='$id'"));
$detail = mysqli_query($conn,"
SELECT d.*, p.nama_produk, p.satuan
FROM detail_transaksi d
JOIN produk p ON d.produk_id = p.id
WHERE d.transaksi_id='$id'
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Detail Transaksi – Waluyo Teknik CCTV</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style><?= getSharedCSS() ?>

@keyframes slideUp {
    from { opacity:0; transform:translateY(20px); }
    to   { opacity:1; transform:translateY(0); }
}
.card { animation: slideUp 0.4s ease both; }

.info-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
@media(max-width:900px){ .info-grid { grid-template-columns: repeat(2, 1fr); } }
@media(max-width:700px){ .info-grid { grid-template-columns:1fr; } }

.info-item {
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 16px;
}

.info-item label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--text3);
    display: block;
    margin-bottom: 6px;
}

.info-item span {
    font-size: 14px;
    font-weight: 600;
    color: var(--text);
}

.total-footer {
    background: linear-gradient(135deg, rgba(59,130,246,0.08), transparent);
    border: 1px solid rgba(59,130,246,0.15);
    border-radius: 12px;
    padding: 20px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 8px;
}

.action-bar {
    display: flex;
    gap: 12px;
    margin-top: 24px;
}
</style>
</head>
<body>

<?php renderSidebar('transaksi'); ?>

<div class="main">
    <div class="topbar">
        <div class="topbar-breadcrumb">
            <i class="fa-solid fa-house" style="color:var(--text3);font-size:12px;"></i>
            <span style="color:var(--text3);">/</span>
            <a href="transaksi.php" style="color:var(--text3);text-decoration:none;">Transaksi</a>
            <span style="color:var(--text3);">/</span>
            <span class="current">Detail</span>
        </div>
    </div>

    <div class="content">
        <div class="flex-between mb-4">
            <div>
                <div class="page-title">Detail Transaksi</div>
                <div class="page-subtitle"><?= htmlspecialchars($trx['kode_transaksi'] ?? 'TRX-'.$id) ?></div>
            </div>
            <span class="badge badge-blue" style="font-size:13px;padding:6px 14px;">
                <i class="fa-solid fa-circle-check"></i> Selesai
            </span>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <label>ID Transaksi</label>
                <span class="money"><?= htmlspecialchars($trx['kode_transaksi'] ?? 'TRX-'.$id) ?></span>
            </div>
            <div class="info-item">
                <label>Tanggal</label>
                <span class="money"><?= isset($trx['tanggal']) ? date('d-m-Y H:i', strtotime($trx['tanggal'])) : '-' ?></span>
            </div>
            <div class="info-item">
                <label>Pelanggan</label>
                <span><?= htmlspecialchars($trx['pelanggan'] ?? 'Umum') ?></span>
            </div>
            <div class="info-item">
                <label>Termin</label>
                <span><?= ($trx['termin'] > 0) ? $trx['termin'] . ' Hari' : '<span style="color:var(--text3);">—</span>' ?></span>
            </div>
            <div class="info-item">
                <label>Jatuh Tempo</label>
                <span><?= !empty($trx['jatuh_tempo']) ? date('d-m-Y', strtotime($trx['jatuh_tempo'])) : '<span style="color:var(--text3);">—</span>' ?></span>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5><i class="fa-solid fa-list" style="color:var(--accent);margin-right:8px;"></i>Item Transaksi</h5>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Kemasan</th>
                            <th>Harga</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $no=1; while($d=mysqli_fetch_assoc($detail)): ?>
                        <tr>
                            <td style="color:var(--text3);"><?= $no++ ?></td>
                            <td style="font-weight:500;"><?= htmlspecialchars($d['nama_produk']) ?></td>
                            <td class="money" style="text-align:center;"><?= $d['qty'] ?></td>
                            <td><span class="satuan-badge" style="display:inline-block;padding:2px 10px;background:rgba(99,102,241,0.12);color:#a5b4fc;border-radius:20px;font-size:11px;font-weight:600;"><?= htmlspecialchars($d['satuan'] ?? 'UNIT') ?></span></td>
                            <td class="money">Rp <?= number_format($d['harga'],0,',','.') ?></td>
                            <td class="money" style="color:var(--green);font-weight:600;">Rp <?= number_format($d['subtotal'],0,',','.') ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <div style="padding:0 16px 16px;">
                <div class="total-footer">
                    <div style="font-weight:700;color:var(--text2);font-size:14px;">Total Keseluruhan</div>
                    <div style="font-family:'DM Mono',monospace;font-size:22px;font-weight:700;color:var(--accent2);">
                        Rp <?= number_format($trx['total'],0,',','.') ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="action-bar">
            <a href="transaksi.php" class="btn btn-ghost">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <a href="cetak_pdf.php?id=<?= $id ?>" class="btn btn-danger">
                <i class="fa-solid fa-file-pdf"></i> Download PDF
            </a>
        </div>
    </div>
</div>

<?= getClockScript() ?>
</body>
</html>
