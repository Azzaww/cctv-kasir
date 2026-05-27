<!-- transaksi.php -->
<?php
session_start();
if(!isset($_SESSION['login'])){ header("Location: login.php"); exit; }
include 'koneksi.php';
include '_style.php';

$produk = mysqli_query($conn,"SELECT * FROM produk ORDER BY nama_produk ASC");
$produk_arr = [];
while($p=mysqli_fetch_assoc($produk)) $produk_arr[] = $p;
$produk_json = json_encode($produk_arr);

// List transaksi
$list_trx = mysqli_query($conn,"SELECT * FROM transaksi ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Transaksi – Waluyo Teknik CCTV</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style><?= getSharedCSS() ?>

@keyframes slideUp {
    from { opacity:0; transform:translateY(20px); }
    to   { opacity:1; transform:translateY(0); }
}

.two-col {
    display: grid;
    grid-template-columns: 1fr 1.2fr;
    gap: 24px;
    align-items: start;
}
@media(max-width:1100px){ .two-col { grid-template-columns:1fr; } }

.card { animation: slideUp 0.4s ease both; }
.card:nth-child(2) { animation-delay:.1s; }

.trx-items { display: flex; flex-direction: column; gap: 10px; }

.trx-row {
    display: grid;
    grid-template-columns: 1fr 80px 90px auto;
    gap: 10px;
    align-items: center;
    padding: 12px;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: 10px;
    transition: border-color 0.2s;
}
.trx-row:hover { border-color: rgba(59,130,246,0.3); }

.row-num {
    font-size: 11px;
    font-weight: 700;
    color: var(--text3);
    width: 20px;
    text-align: center;
}

.subtotal-cell {
    font-family: "DM Mono", monospace;
    font-size: 12px;
    color: var(--green);
    font-weight: 600;
    text-align: right;
}

.total-bar {
    background: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(59,130,246,0.05));
    border: 1px solid rgba(59,130,246,0.2);
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 16px;
}

.total-label { font-weight: 700; font-size: 14px; color: var(--text2); }
.total-value { font-family: "DM Mono", monospace; font-size: 22px; font-weight: 700; color: var(--accent2); }

.btn-remove {
    background: none;
    border: 1px solid rgba(239,68,68,0.3);
    color: #f87171;
    border-radius: 6px;
    width: 30px; height: 30px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px;
}
.btn-remove:hover { background: rgba(239,68,68,0.1); border-color: var(--red); }

.btn-add-row {
    width: 100%;
    padding: 10px;
    background: transparent;
    border: 1px dashed var(--border);
    color: var(--text3);
    border-radius: 10px;
    cursor: pointer;
    font-family: "Plus Jakarta Sans", sans-serif;
    font-size: 13px;
    transition: all 0.2s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    margin-top: 10px;
}
.btn-add-row:hover { border-color: var(--accent); color: var(--accent2); background: rgba(59,130,246,0.05); }

.list-trx-table { font-size: 13px; }
.list-trx-table .badge-blue { cursor: default; }
</style>
</head>
<body>

<?php renderSidebar('transaksi'); ?>

<div class="main">
    <div class="topbar">
        <div class="topbar-breadcrumb">
            <i class="fa-solid fa-house" style="color:var(--text3);font-size:12px;"></i>
            <span style="color:var(--text3);">/</span>
            <span class="current">Transaksi</span>
        </div>
    </div>

    <div class="content">
        <div class="page-title">Transaksi</div>
        <div class="page-subtitle">Buat dan kelola transaksi penjualan</div>

        <div class="two-col">
            <!-- FORM TAMBAH -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fa-solid fa-plus-circle" style="color:var(--accent);margin-right:8px;"></i>Tambah Transaksi</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="simpan.php" id="formTrx">
                        <div class="form-group">
                            <label class="form-label">Nama Pelanggan</label>
                            <input type="text" name="pelanggan" class="form-control" placeholder="Umum / Nama pelanggan">
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                            <div class="form-group">
                                <label class="form-label">Termin (Hari) <span style="color:var(--text3);font-weight:400;font-size:11px;">(opsional)</span></label>
                                <input type="number" name="termin" id="terminInput" class="form-control" placeholder="cth: 30" min="0" oninput="hitungJatuhTempo()">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Jatuh Tempo <span style="color:var(--text3);font-weight:400;font-size:11px;">(opsional)</span></label>
                                <input type="date" name="jatuh_tempo" id="jatuhTempoInput" class="form-control">
                            </div>
                        </div>

                        <div class="form-label" style="margin-bottom:10px;">Item Produk</div>
                        <div class="trx-items" id="trxItems"></div>
                        <button type="button" class="btn-add-row" onclick="addRow()">
                            <i class="fa-solid fa-plus"></i> Tambah Baris
                        </button>

                        <div class="total-bar mt-4">
                            <div class="total-label">Total</div>
                            <div class="total-value" id="totalDisplay">Rp 0</div>
                        </div>

                        <button type="submit" class="btn btn-success" style="width:100%;margin-top:16px;padding:13px;justify-content:center;font-size:14px;">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Transaksi
                        </button>
                    </form>
                </div>
            </div>

            <!-- LIST TRANSAKSI -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fa-solid fa-list" style="color:var(--accent);margin-right:8px;"></i>Riwayat Transaksi</h5>
                </div>
                <div class="table-wrap">
                    <table class="list-trx-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if(mysqli_num_rows($list_trx)==0): ?>
                            <tr><td colspan="5" style="text-align:center;color:var(--text3);padding:32px;">Belum ada transaksi</td></tr>
                        <?php else: $no=1; while($t=mysqli_fetch_assoc($list_trx)): ?>
                            <tr>
                                <td style="color:var(--text3);"><?= $no++ ?></td>
                                <td><span class="badge badge-blue"><?= htmlspecialchars($t['kode_transaksi'] ?? 'TRX-'.$t['id']) ?></span></td>
                                <td><?= htmlspecialchars($t['pelanggan'] ?? 'Umum') ?></td>
                                <td class="money" style="color:var(--green);">Rp <?= number_format($t['total'],0,',','.') ?></td>
                                <td>
                                    <a href="detail.php?id=<?= $t['id'] ?>" class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-eye"></i>
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
</div>

<script>
const produkData = <?= $produk_json ?>;
let rowCount = 0;

function formatRp(n) {
    return 'Rp ' + Number(n).toLocaleString('id-ID');
}

function addRow() {
    rowCount++;
    const idx = rowCount;
    const div = document.createElement('div');
    div.className = 'trx-row';
    div.id = 'row_' + idx;

    let opts = '<option value="">-- Pilih --</option>';
    produkData.forEach(p => {
        opts += `<option value="${p.id}" data-harga="${p.harga}">${p.nama_produk}</option>`;
    });

    div.innerHTML = `
        <select name="produk[]" class="form-control" onchange="calcTotal()" style="padding:8px 10px;font-size:12px;">${opts}</select>
        <input type="number" name="qty[]" class="form-control" placeholder="Qty" min="1" value="1" oninput="calcTotal()" style="padding:8px 10px;font-size:12px;text-align:center;">
        <div class="subtotal-cell" id="sub_${idx}">Rp 0</div>
        <button type="button" class="btn-remove" onclick="removeRow(${idx})"><i class="fa-solid fa-xmark"></i></button>
    `;
    document.getElementById('trxItems').appendChild(div);
    calcTotal();
}

function removeRow(idx) {
    const el = document.getElementById('row_' + idx);
    if(el) el.remove();
    calcTotal();
}

function calcTotal() {
    let total = 0;
    document.querySelectorAll('.trx-row').forEach((row, i) => {
        const sel = row.querySelector('select');
        const qty = row.querySelector('input[type=number]');
        const subEl = row.querySelectorAll('div')[0];
        if(!sel || !qty) return;
        const opt = sel.options[sel.selectedIndex];
        const harga = opt ? parseInt(opt.getAttribute('data-harga') || 0) : 0;
        const q = parseInt(qty.value) || 0;
        const sub = harga * q;
        total += sub;
        if(subEl) subEl.textContent = formatRp(sub);
    });
    document.getElementById('totalDisplay').textContent = formatRp(total);
}

// start with 3 rows
addRow(); addRow(); addRow();

function hitungJatuhTempo() {
    const termin = parseInt(document.getElementById('terminInput').value);
    const jatuhTempoInput = document.getElementById('jatuhTempoInput');
    if (!isNaN(termin) && termin > 0) {
        const today = new Date();
        today.setDate(today.getDate() + termin);
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        jatuhTempoInput.value = `${yyyy}-${mm}-${dd}`;
    } else {
        jatuhTempoInput.value = '';
    }
}
</script>
<?= getClockScript() ?>
</body>
</html>
