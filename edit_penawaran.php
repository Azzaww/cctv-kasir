<?php
// edit_penawaran.php
session_start();
if(!isset($_SESSION['login'])){ header("Location: login.php"); exit; }
include 'koneksi.php';
include '_style.php';

$id = (int)$_GET['id'];
$pnw = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM penawaran WHERE id='$id'"));
if(!$pnw){ header("Location: penawaran.php"); exit; }

$detail_rows = [];
$detail_res = mysqli_query($conn,"
    SELECT dp.*, COALESCE(NULLIF(dp.deskripsi, ''), p.deskripsi, '') AS deskripsi_final
    FROM detail_penawaran dp
    LEFT JOIN produk p ON dp.nama_barang = p.nama_produk
    WHERE dp.penawaran_id='$id'
    ORDER BY dp.id ASC
");
while($d = mysqli_fetch_assoc($detail_res)) $detail_rows[] = $d;

$produk = mysqli_query($conn,"SELECT * FROM produk ORDER BY nama_produk ASC");
$produk_arr = [];
while($p = mysqli_fetch_assoc($produk)) $produk_arr[] = $p;
$produk_json = json_encode($produk_arr);
$detail_json = json_encode($detail_rows);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit Penawaran – Waluyo Teknik CCTV</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style><?= getSharedCSS() ?>
@keyframes slideUp {
    from { opacity:0; transform:translateY(20px); }
    to   { opacity:1; transform:translateY(0); }
}
.card { animation: slideUp 0.4s ease both; }

/* Preview template */
.surat-preview {
    background: var(--bg);
    border: 1px solid var(--border);
    border-left: 3px solid var(--accent);
    border-radius: 10px; padding: 14px 16px;
    font-size: 12.5px; line-height: 1.8; color: var(--text2); margin-bottom: 6px;
}
.surat-preview .hl {
    display: inline-block;
    background: rgba(59,130,246,0.12); color: var(--accent2);
    font-weight: 700; border-radius: 4px; padding: 0 4px;
    min-width: 30px; font-style: italic;
}

/* Item rows */
.item-row {
    display: grid;
    grid-template-columns: 1fr 70px 90px 110px auto;
    gap: 10px; align-items: start; padding: 12px;
    background: var(--bg); border: 1px solid var(--border);
    border-radius: 10px; transition: border-color 0.2s; margin-bottom: 8px;
}
.item-row:hover { border-color: rgba(59,130,246,0.3); }

.subtotal-cell {
    font-family: "DM Mono", monospace;
    font-size: 11px; color: var(--green); font-weight: 600; text-align: right;
}
.btn-remove {
    background: none; border: 1px solid rgba(239,68,68,0.3);
    color: #f87171; border-radius: 6px; width: 30px; height: 30px;
    cursor: pointer; transition: all 0.2s;
    display: flex; align-items: center; justify-content: center; font-size: 12px;
    margin-top: 2px;
}
.btn-remove:hover { background: rgba(239,68,68,0.1); border-color: var(--red); }

.btn-add-row {
    width: 100%; padding: 10px; background: transparent;
    border: 1px dashed var(--border); color: var(--text3);
    border-radius: 10px; cursor: pointer;
    font-family: "Plus Jakarta Sans", sans-serif; font-size: 13px;
    transition: all 0.2s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    margin-top: 4px;
}
.btn-add-row:hover { border-color: var(--accent); color: var(--accent2); background: rgba(59,130,246,0.05); }

.total-bar {
    background: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(59,130,246,0.05));
    border: 1px solid rgba(59,130,246,0.2);
    border-radius: 12px; padding: 18px 20px;
    display: flex; align-items: center; justify-content: space-between; margin-top: 16px;
}
.total-label { font-weight: 700; font-size: 14px; color: var(--text2); }
.total-value { font-family: "DM Mono", monospace; font-size: 22px; font-weight: 700; color: var(--accent2); }

.section-title {
    font-size: 13px; font-weight: 700; color: var(--text2);
    margin: 20px 0 12px 0;
    display: flex; align-items: center; gap: 8px;
    padding-bottom: 8px; border-bottom: 1px solid var(--border);
}
.section-title i { color: var(--accent); }
.item-row-header {
    display: grid;
    grid-template-columns: 1fr 70px 90px 110px 30px;
    gap: 10px; padding: 0 12px; margin-bottom: 4px;
}
.col-header {
    font-size: 10px; font-weight: 700; color: var(--text3);
    text-transform: uppercase; letter-spacing: 0.06em;
}
.action-bar { display: flex; gap: 12px; margin-top: 24px; flex-wrap: wrap; }

.item-col-details { display: contents; }

/* Mobile responsiveness */
@media (max-width: 680px) {
    .form-grid-2 {
        grid-template-columns: 1fr !important;
        gap: 10px !important;
    }
    .item-row-header {
        display: none !important;
    }
    .item-row {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 12px;
        position: relative;
    }
    .item-col-main {
        width: 100%;
        padding-right: 36px;
    }
    .item-col-details {
        display: grid !important;
        grid-template-columns: 65px 75px 1fr !important;
        gap: 8px !important;
        align-items: start !important;
        width: 100% !important;
    }
    .btn-remove {
        position: absolute;
        top: 10px;
        right: 10px;
        margin-top: 0;
    }
    .total-bar {
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
        padding: 14px 16px;
    }
    .total-value {
        font-size: 19px;
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
            <a href="detail_penawaran.php?id=<?= $id ?>" style="color:var(--text3);text-decoration:none;">Detail</a>
            <span style="color:var(--text3);">/</span>
            <span class="current">Edit</span>
        </div>
    </div>

    <div class="content">
        <div class="flex-between mb-4">
            <div>
                <div class="page-title">Edit Penawaran</div>
                <div class="page-subtitle"><?= htmlspecialchars($pnw['nomor_penawaran'] ?? 'SPH-'.$id) ?></div>
            </div>
            <a href="detail_penawaran.php?id=<?= $id ?>" class="btn btn-ghost">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5><i class="fa-solid fa-pen-to-square" style="color:var(--yellow);margin-right:8px;"></i>Form Edit Penawaran</h5>
            </div>
            <div style="padding: 24px 28px;">
                <form method="POST" action="update_penawaran.php" id="formEdit">
                    <input type="hidden" name="id" value="<?= $id ?>">

                    <div class="form-grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                        <div class="form-group">
                            <label class="form-label">Nomor Penawaran</label>
                            <input type="text" name="nomor_penawaran" class="form-control"
                                   value="<?= htmlspecialchars($pnw['nomor_penawaran'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control"
                                   value="<?= htmlspecialchars($pnw['tanggal'] ?? date('Y-m-d')) ?>" required>
                        </div>
                    </div>

                    <!-- Perihal -->
                    <div class="form-group">
                        <label class="form-label">Perihal / Judul Penawaran</label>
                        <input type="text" name="judul" id="inputJudul" class="form-control"
                               value="<?= htmlspecialchars($pnw['judul'] ?? '') ?>"
                               oninput="updatePreview()" required>
                    </div>

                    <!-- Kepada -->
                    <div class="form-group">
                        <label class="form-label">Kepada (Penerima)</label>
                        <input type="text" name="kepada" class="form-control"
                               value="<?= htmlspecialchars($pnw['kepada'] ?? '') ?>" required>
                    </div>

                    <!-- Preview isi surat otomatis -->
                    <div class="form-group">
                        <label class="form-label">Preview Isi Pengantar Surat <span style="color:var(--text3);font-weight:400;font-size:11px;">(otomatis)</span></label>
                        <div class="surat-preview">
                            Dengan hormat,<br>
                            Sehubungan dengan adanya instalasi sistem keamanan di instansi Bapak/Ibu, kami dari
                            <strong>Waluyo Teknik</strong> mengajukan penawaran harga
                            <span class="hl" id="previewJudul"><?= htmlspecialchars($pnw['judul'] ?? '...') ?></span>
                            dengan rincian sebagai berikut:
                        </div>
                        <div style="font-size:11px;color:var(--text3);margin-top:4px;"><i class="fa-solid fa-circle-info"></i> Otomatis mengikuti judul penawaran</div>
                    </div>

                    <!-- Daftar Barang -->
                    <div class="section-title">
                        <i class="fa-solid fa-boxes-stacked"></i> Daftar Barang yang Ditawarkan
                    </div>

                    <div class="item-row-header">
                        <div class="col-header">Nama Barang & Deskripsi</div>
                        <div class="col-header">Qty</div>
                        <div class="col-header">Satuan</div>
                        <div class="col-header" style="text-align:right;">Harga</div>
                        <div></div>
                    </div>

                    <div id="itemList"></div>

                    <button type="button" class="btn-add-row" onclick="addItemRow()">
                        <i class="fa-solid fa-plus"></i> Tambah Baris Barang
                    </button>

                    <div class="total-bar">
                        <div class="total-label"><i class="fa-solid fa-calculator" style="margin-right:8px;color:var(--accent);"></i>Total Penawaran</div>
                        <div class="total-value" id="totalDisplay">Rp 0</div>
                    </div>

                </form>
            </div>
        </div>

        <div class="action-bar">
            <a href="detail_penawaran.php?id=<?= $id ?>" class="btn btn-ghost">
                <i class="fa-solid fa-arrow-left"></i> Batal
            </a>
            <button type="submit" form="formEdit" class="btn btn-success">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
            </button>
        </div>
    </div>
</div>

<script>
const produkData = <?= $produk_json ?>;
const existingRows = <?= $detail_json ?>;
let rowCount = 0;

function updatePreview() {
    const val = document.getElementById('inputJudul').value.trim();
    document.getElementById('previewJudul').textContent = val || '...';
}

function formatRp(n) {
    return 'Rp ' + Number(n).toLocaleString('id-ID');
}

function escapeHtml(str) {
    if(!str) return '';
    return String(str).replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

function addItemRow(namaBarang='', qty=1, satuan='unit', harga=0, deskripsi='') {
    rowCount++;
    const idx = rowCount;
    const div = document.createElement('div');
    div.className = 'item-row';
    div.id = 'item_' + idx;

    let opts = '<option value="">-- Pilih Produk --</option>';
    produkData.forEach(p => {
        const sel = p.nama_produk === namaBarang ? ' selected' : '';
        const deskAttr = escapeHtml(p.deskripsi || '');
        const satAttr  = escapeHtml(p.satuan || 'unit');
        opts += `<option value="${p.nama_produk}" data-harga="${p.harga}" data-satuan="${satAttr}" data-deskripsi="${deskAttr}"${sel}>${p.nama_produk}</option>`;
    });

    const sub = qty * harga;
    div.innerHTML = `
        <div class="item-col-main">
            <select class="form-control" onchange="onSelectProduk(${idx},this)" style="padding:7px 10px;font-size:12px;margin-bottom:5px;">
                ${opts}
            </select>
            <input type="text" name="nama_barang[]" class="item-nama form-control"
                   value="${escapeHtml(namaBarang)}"
                   placeholder="atau ketik nama barang..." oninput="calcTotal()"
                   style="padding:7px 10px;font-size:12px;margin-bottom:5px;">
            <input type="text" name="deskripsi[]" class="item-deskripsi form-control"
                   value="${escapeHtml(deskripsi)}"
                   placeholder="Deskripsi barang (opsional)..."
                   style="padding:7px 10px;font-size:12px;">
        </div>
        <div class="item-col-details">
            <input type="number" name="qty[]" class="form-control" placeholder="Qty" min="1" value="${qty}"
                   oninput="calcTotal()" style="padding:7px 10px;font-size:12px;text-align:center;">
            <input type="text" name="satuan[]" class="form-control" value="${satuan}"
                   style="padding:7px 10px;font-size:12px;text-align:center;">
            <div>
                <input type="number" name="harga[]" class="item-harga form-control"
                       value="${harga}" min="0" oninput="calcTotal()"
                       style="padding:7px 10px;font-size:12px;text-align:right;">
                <div class="subtotal-cell" id="sub_${idx}" style="margin-top:4px;">${formatRp(sub)}</div>
            </div>
        </div>
        <button type="button" class="btn-remove" onclick="removeItemRow(${idx})" title="Hapus baris">
            <i class="fa-solid fa-xmark"></i>
        </button>
    `;
    document.getElementById('itemList').appendChild(div);
    calcTotal();
}

function onSelectProduk(idx, sel) {
    const row = document.getElementById('item_' + idx);
    const opt = sel.options[sel.selectedIndex];
    if(opt && opt.value) {
        row.querySelector('.item-nama').value = opt.value;
        row.querySelector('.item-harga').value = opt.getAttribute('data-harga') || 0;
        if(opt.getAttribute('data-satuan')) {
            const satEl = row.querySelector('input[name="satuan[]"]');
            if(satEl) satEl.value = opt.getAttribute('data-satuan');
        }
        const deskEl = row.querySelector('.item-deskripsi');
        if(deskEl) deskEl.value = opt.getAttribute('data-deskripsi') || '';
    }
    calcTotal();
}

function removeItemRow(idx) {
    const el = document.getElementById('item_' + idx);
    if(el) el.remove();
    calcTotal();
}

function calcTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach((row) => {
        const qtyEl = row.querySelector('input[name="qty[]"]');
        const hrgEl = row.querySelector('.item-harga');
        const subEl = row.querySelector('.subtotal-cell');
        if(!qtyEl || !hrgEl) return;
        const q = parseInt(qtyEl.value) || 0;
        const h = parseFloat(hrgEl.value) || 0;
        const sub = q * h;
        total += sub;
        if(subEl) subEl.textContent = formatRp(sub);
    });
    document.getElementById('totalDisplay').textContent = formatRp(total);
}

// Load existing rows
if(existingRows.length > 0) {
    existingRows.forEach(r => {
        addItemRow(r.nama_barang, r.qty, r.satuan, r.harga, r.deskripsi_final || r.deskripsi || '');
    });
} else {
    addItemRow(); addItemRow(); addItemRow();
}
</script>
<?= getClockScript() ?>
</body>
</html>
