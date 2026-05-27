<!-- dashboard.php -->
<?php
session_start();
if(!isset($_SESSION['login'])){ header("Location: login.php"); exit; }
include 'koneksi.php';
include '_style.php';

// Stats queries
$total_produk = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM produk"));
$total_trx    = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM transaksi"));
$total_sales  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COALESCE(SUM(total),0) AS s FROM transaksi"))['s'];
$recent_trx   = mysqli_query($conn,"SELECT * FROM transaksi ORDER BY id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard – Waluyo Teknik CCTV</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style><?= getSharedCSS() ?>

.stat-cards { display: grid; grid-template-columns: repeat(3,1fr); gap: 20px; margin-bottom: 28px; }
@media(max-width:900px){ .stat-cards { grid-template-columns:1fr; } }

@keyframes slideUp {
    from { opacity:0; transform:translateY(20px); }
    to   { opacity:1; transform:translateY(0); }
}
.stat-card { animation: slideUp 0.5s ease both; }
.stat-card:nth-child(2) { animation-delay:.1s; }
.stat-card:nth-child(3) { animation-delay:.2s; }
.card { animation: slideUp 0.5s ease .3s both; }
</style>
</head>
<body>

<?php renderSidebar('dashboard'); ?>

<div class="main">
    <div class="topbar">
        <div class="topbar-breadcrumb">
            <i class="fa-solid fa-house" style="color:var(--text3);font-size:12px;"></i>
            <span style="color:var(--text3);">/</span>
            <span class="current">Dashboard</span>
        </div>
        <div style="font-size:12px;color:var(--text3);">Selamat datang, <strong style="color:var(--text2);">Admin</strong></div>
    </div>

    <div class="content">
        <div class="page-title">Dashboard</div>
        <div class="page-subtitle">Ringkasan data sistem kasir CCTV hari ini</div>

        <div class="stat-cards">
            <div class="stat-card blue">
                <div class="stat-icon"><i class="fa-solid fa-box-open"></i></div>
                <div class="stat-value"><?= $total_produk ?></div>
                <div class="stat-label">Total Produk</div>
            </div>
            <div class="stat-card green">
                <div class="stat-icon"><i class="fa-solid fa-receipt"></i></div>
                <div class="stat-value"><?= $total_trx ?></div>
                <div class="stat-label">Total Transaksi</div>
            </div>
            <div class="stat-card yellow">
                <div class="stat-icon"><i class="fa-solid fa-coins"></i></div>
                <div class="stat-value" style="font-size:20px;">Rp <?= number_format($total_sales,0,',','.') ?></div>
                <div class="stat-label">Total Penjualan</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5><i class="fa-solid fa-clock-rotate-left" style="color:var(--accent);margin-right:8px;"></i>Transaksi Terakhir</h5>
                <a href="transaksi.php" class="btn btn-ghost btn-sm">Lihat Semua <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Transaksi</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(mysqli_num_rows($recent_trx)==0): ?>
                        <tr><td colspan="6" style="text-align:center;color:var(--text3);padding:32px;">Belum ada transaksi</td></tr>
                    <?php else: $no=1; while($t=mysqli_fetch_assoc($recent_trx)): ?>
                        <tr>
                            <td style="color:var(--text3);"><?= $no++ ?></td>
                            <td><span class="badge badge-blue"><?= htmlspecialchars($t['kode_transaksi'] ?? 'TRX-'.$t['id']) ?></span></td>
                            <td class="money"><?= date('d-m-Y H:i', strtotime($t['tanggal'] ?? 'now')) ?></td>
                            <td><?= htmlspecialchars($t['pelanggan'] ?? 'Umum') ?></td>
                            <td class="money" style="color:var(--green);font-weight:600;">Rp <?= number_format($t['total'],0,',','.') ?></td>
                            <td>
                                <a href="detail.php?id=<?= $t['id'] ?>" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= getClockScript() ?>
</body>
</html>
