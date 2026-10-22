<?php
// detail_penawaran.php
session_start();
if(!isset($_SESSION['login'])){ header("Location: login.php"); exit; }
include 'koneksi.php';
include '_style.php';

$id = (int)$_GET['id'];
$pnw = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM penawaran WHERE id='$id'"));
if(!$pnw){ header("Location: penawaran.php"); exit; }

$detail = mysqli_query($conn,"
    SELECT dp.*, p.deskripsi AS produk_deskripsi
    FROM detail_penawaran dp
    LEFT JOIN produk p ON dp.nama_barang = p.nama_produk
    WHERE dp.penawaran_id='$id'
    ORDER BY dp.id ASC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Detail Penawaran – Waluyo Teknik CCTV</title>
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
    gap: 16px; margin-bottom: 24px;
}
@media(max-width:900px){ .info-grid { grid-template-columns: repeat(2,1fr); } }
@media(max-width:700px){
    .info-grid { grid-template-columns: 1fr !important; }
    .info-item-full { grid-column: span 1 !important; }
    .total-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
        padding: 14px 16px;
    }
    .action-bar {
        flex-direction: column;
        gap: 8px;
    }
    .action-bar .btn {
        width: 100%;
        justify-content: center;
    }
}

.info-item {
    background: var(--bg); border: 1px solid var(--border);
    border-radius: 12px; padding: 16px;
}
.info-item label {
    font-size: 10px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.1em;
    color: var(--text3); display: block; margin-bottom: 6px;
}
.info-item span { font-size: 14px; font-weight: 600; color: var(--text); }

.surat-preview-box {
    background: var(--bg); border: 1px solid var(--border);
    border-left: 3px solid var(--accent);
    border-radius: 10px; padding: 16px 20px;
    margin-bottom: 24px;
    font-size: 13px; line-height: 1.8; color: var(--text2);
    font-style: italic;
}
.surat-preview-box strong { color: var(--text); font-style: normal; }
.surat-preview-box .judul-hl { color: var(--accent2); font-weight: 700; font-style: normal; }

.total-footer {
    background: linear-gradient(135deg, rgba(59,130,246,0.08), transparent);
    border: 1px solid rgba(59,130,246,0.15);
    border-radius: 12px; padding: 20px 24px;
    display: flex; align-items: center; justify-content: space-between;
    margin-top: 8px;
}
.action-bar { display: flex; gap: 12px; margin-top: 24px; flex-wrap: wrap; }

.badge-penawaran { background: rgba(245,158,11,0.15); color: #d97706; }

/* MODAL HAPUS */
@keyframes fadeIn { from{opacity:0;}to{opacity:1;} }
.modal-overlay {
    display:none; position:fixed; inset:0;
    background:rgba(0,0,0,0.5); backdrop-filter:blur(4px);
    z-index:1000; align-items:center; justify-content:center; padding:16px;
}
.modal-overlay.active { display:flex; animation:fadeIn 0.2s ease; }
.modal-box {
    background:var(--surface); border-radius:20px;
    border:1px solid var(--border); width:100%; max-width:420px;
    box-shadow:0 24px 64px rgba(0,0,0,0.25);
}
.modal-header {
    padding:20px 24px; border-bottom:1px solid var(--border);
    display:flex; align-items:center; justify-content:space-between;
}
.modal-header h4 { font-size:16px; font-weight:800; display:flex; align-items:center; gap:10px; }
.modal-close {
    width:34px;height:34px;border-radius:8px;border:1px solid var(--border);
    background:transparent;cursor:pointer;color:var(--text3);font-size:15px;
    display:flex;align-items:center;justify-content:center;transition:all 0.2s;
}
.modal-close:hover { background:rgba(239,68,68,0.1);color:var(--red);border-color:var(--red); }
.modal-body { padding:20px 24px; }
.modal-footer { padding:16px 24px; border-top:1px solid var(--border); display:flex;gap:10px;justify-content:flex-end; }
</style>
</head>
<body>

<?php renderSidebar('penawaran'); ?>

<div class="main">
    <div class="topbar">
        <div class="topbar-breadcrumb">
            <i class="fa-solid fa-file-contract" style="color:var(--text3);font-size:12px;"></i>
            <span style="color:var(--text3);">/</span>
            <a href="penawaran.php" style="color:var(--text3);text-decoration:none;">Penawaran</a>
            <span style="color:var(--text3);">/</span>
            <span class="current">Detail</span>
        </div>
    </div>

    <div class="content">
        <div class="flex-between mb-4">
            <div>
                <div class="page-title">Detail Penawaran</div>
                <div class="page-subtitle"><?= htmlspecialchars($pnw['nomor_penawaran'] ?? 'SPH-'.$id) ?></div>
            </div>
            <span class="badge badge-penawaran" style="font-size:13px;padding:6px 14px;">
                <i class="fa-solid fa-file-contract"></i> Penawaran
            </span>
        </div>

        <!-- INFO CARDS -->
        <div class="info-grid">
            <div class="info-item">
                <label>Nomor Penawaran</label>
                <span class="money"><?= htmlspecialchars($pnw['nomor_penawaran'] ?? '-') ?></span>
            </div>
            <div class="info-item">
                <label>Tanggal</label>
                <span class="money"><?= !empty($pnw['tanggal']) ? date('d/m/Y', strtotime($pnw['tanggal'])) : '-' ?></span>
            </div>
            <div class="info-item">
                <label>Kepada</label>
                <span><?= htmlspecialchars($pnw['kepada'] ?? '-') ?></span>
            </div>
            <div class="info-item info-item-full" style="grid-column:span 3;">
                <label>Perihal</label>
                <span style="font-size:13px;line-height:1.5;"><?= htmlspecialchars($pnw['judul'] ?? '-') ?></span>
            </div>
        </div>

        <!-- PREVIEW ISI SURAT -->
        <div class="surat-preview-box">
            Dengan hormat,<br>
            Sehubungan dengan adanya instalasi sistem keamanan di instansi Bapak/Ibu, kami dari
            <strong>Waluyo Teknik</strong> mengajukan penawaran harga
            <span class="judul-hl"><?= htmlspecialchars($pnw['judul'] ?? '-') ?></span>
            dengan rincian sebagai berikut:
        </div>

        <!-- TABLE BARANG -->
        <div class="card">
            <div class="card-header">
                <h5><i class="fa-solid fa-boxes-stacked" style="color:var(--accent);margin-right:8px;"></i>Daftar Rincian Harga Penawaran</h5>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Deskripsi</th>
                            <th style="text-align:center;">Qty</th>
                            <th style="text-align:center;">Unit</th>
                            <th style="text-align:right;">Harga</th>
                            <th style="text-align:right;">Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $no=1; while($d=mysqli_fetch_assoc($detail)):
                        $desk = !empty($d['deskripsi']) ? $d['deskripsi'] : ($d['produk_deskripsi'] ?? '');
                    ?>
                        <tr>
                            <td style="color:var(--text3);"><?= $no++ ?></td>
                            <td style="font-weight:500;"><?= htmlspecialchars($d['nama_barang']) ?></td>
                            <td style="color:var(--text2);font-size:12px;"><?= !empty($desk) ? nl2br(htmlspecialchars($desk)) : '<span style="color:var(--text3);">-</span>' ?></td>
                            <td class="money" style="text-align:center;"><?= $d['qty'] ?></td>
                            <td style="text-align:center;">
                                <span style="display:inline-block;padding:2px 10px;background:rgba(99,102,241,0.12);color:#a5b4fc;border-radius:20px;font-size:11px;font-weight:600;">
                                    <?= htmlspecialchars($d['satuan'] ?? 'unit') ?>
                                </span>
                            </td>
                            <td class="money" style="text-align:right;">Rp <?= number_format($d['harga'],0,',','.') ?></td>
                            <td class="money" style="text-align:right;color:var(--green);font-weight:600;">Rp <?= number_format($d['subtotal'],0,',','.') ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <div style="padding:0 16px 16px;">
                <div class="total-footer">
                    <div style="font-weight:700;color:var(--text2);font-size:14px;">Total Penawaran</div>
                    <div style="font-family:'DM Mono',monospace;font-size:22px;font-weight:700;color:var(--accent2);">
                        Rp <?= number_format($pnw['total'],0,',','.') ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="action-bar">
            <a href="penawaran.php" class="btn btn-ghost">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <a href="edit_penawaran.php?id=<?= $id ?>" class="btn btn-warning">
                <i class="fa-solid fa-pen"></i> Edit Penawaran
            </a>
            <a href="cetak_penawaran_pdf.php?id=<?= $id ?>" class="btn btn-danger" target="_blank">
                <i class="fa-solid fa-file-pdf"></i> Download PDF
            </a>
            <a href="cetak_penawaran_word.php?id=<?= $id ?>" class="btn btn-primary">
                <i class="fa-solid fa-file-word"></i> Download Word
            </a>
            <button onclick="bukaHapus()" class="btn btn-danger" style="background:transparent;border:1px solid var(--red);color:var(--red);">
                <i class="fa-solid fa-trash"></i> Hapus
            </button>
        </div>
    </div>
</div>

<!-- MODAL HAPUS -->
<div class="modal-overlay" id="modalHapus">
    <div class="modal-box">
        <div class="modal-header">
            <h4><i class="fa-solid fa-triangle-exclamation" style="color:var(--red);"></i> Konfirmasi Hapus</h4>
            <button class="modal-close" onclick="tutupHapus()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <p style="color:var(--text2);line-height:1.8;">
                Apakah Anda yakin ingin menghapus penawaran<br>
                <strong style="color:var(--text);"><?= htmlspecialchars($pnw['nomor_penawaran'] ?? '-') ?></strong>?<br>
                <span style="color:var(--red);font-size:12px;"><i class="fa-solid fa-circle-exclamation"></i> Data yang dihapus tidak dapat dikembalikan.</span>
            </p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="tutupHapus()">Batal</button>
            <form method="POST" action="hapus_penawaran.php" style="margin:0;">
                <input type="hidden" name="id" value="<?= $id ?>">
                <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash"></i> Ya, Hapus</button>
            </form>
        </div>
    </div>
</div>

<script>
function bukaHapus() {
    document.getElementById('modalHapus').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function tutupHapus() {
    document.getElementById('modalHapus').classList.remove('active');
    document.body.style.overflow = '';
}
document.getElementById('modalHapus').addEventListener('click', function(e) {
    if(e.target === this) tutupHapus();
});
</script>
<?= getClockScript() ?>
</body>
</html>
