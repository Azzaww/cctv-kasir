<?php
session_start();
if(!isset($_SESSION['login'])){ header("Location: login.php"); exit; }
include 'koneksi.php';
include '_style.php';

// Ambil daftar produk untuk dropdown
$produk = mysqli_query($conn,"SELECT * FROM produk ORDER BY nama_produk ASC");
$produk_arr = [];
while($p = mysqli_fetch_assoc($produk)) $produk_arr[] = $p;
$produk_json = json_encode($produk_arr);

// List penawaran
$list_penawaran = mysqli_query($conn,"SELECT * FROM penawaran ORDER BY id DESC");

// Generate nomor penawaran otomatis
$bulan_romawi = ['','I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
$bln = (int)date('n');
$thn = date('Y');
$count_row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*)+1 AS next FROM penawaran WHERE YEAR(created_at)=YEAR(NOW())"));
$next_num = str_pad($count_row['next'], 3, '0', STR_PAD_LEFT);
$nomor_otomatis = "{$next_num}/SPH/WT/{$bulan_romawi[$bln]}/{$thn}";
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Penawaran – Waluyo Teknik CCTV</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style><?= getSharedCSS() ?>
@keyframes slideUp {
    from { opacity:0; transform:translateY(20px); }
    to   { opacity:1; transform:translateY(0); }
}
@keyframes fadeIn { from { opacity:0; } to { opacity:1; } }

.card { animation: slideUp 0.4s ease both; }

.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(4px);
    z-index: 1000;
    align-items: flex-start;
    justify-content: center;
    padding: 24px 16px;
    overflow-y: auto;
}
.modal-overlay.active { display: flex; animation: fadeIn 0.2s ease; }

.modal-box {
    background: var(--surface);
    border-radius: 20px;
    border: 1px solid var(--border);
    width: 100%;
    max-width: 820px;
    margin: auto;
    box-shadow: 0 24px 64px rgba(0,0,0,0.25);
    animation: slideUp 0.3s ease both;
}
.modal-header {
    padding: 24px 28px;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
}
.modal-header h4 {
    font-size: 17px; font-weight: 800; color: var(--text);
    display: flex; align-items: center; gap: 10px;
}
.modal-header h4 i { color: var(--accent); }
.modal-close {
    width: 36px; height: 36px; border-radius: 8px;
    border: 1px solid var(--border); background: transparent;
    cursor: pointer; color: var(--text3); font-size: 16px;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.2s;
}
.modal-close:hover { background: rgba(239,68,68,0.1); color: var(--red); border-color: var(--red); }
.modal-body { padding: 28px; }
.modal-footer {
    padding: 20px 28px;
    border-top: 1px solid var(--border);
    display: flex; gap: 12px; justify-content: flex-end;
}

/* Preview template surat */
.surat-preview {
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 14px 16px;
    font-size: 12.5px;
    line-height: 1.8;
    color: var(--text2);
    margin-bottom: 6px;
}
.surat-preview .hl {
    display: inline-block;
    background: rgba(59,130,246,0.12);
    color: var(--accent2);
    font-weight: 700;
    border-radius: 4px;
    padding: 0 4px;
    min-width: 30px;
    font-style: italic;
}

/* Item rows */
.item-row {
    display: grid;
    grid-template-columns: 1fr 70px 90px 110px auto;
    gap: 10px;
    align-items: start;
    padding: 12px;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: 10px;
    transition: border-color 0.2s;
    margin-bottom: 8px;
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

.badge-penawaran { background: rgba(245,158,11,0.15); color: #d97706; }

.page-header-row {
    display: flex; align-items: flex-start;
    justify-content: space-between; margin-bottom: 28px; gap: 16px; flex-wrap: wrap;
}
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

.item-col-details { display: contents; }

/* Mobile responsiveness */
@media (max-width: 680px) {
    .page-header-row {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
        margin-bottom: 20px;
    }
    #btn-buat-penawaran {
        width: 100%;
        justify-content: center;
    }
    .modal-overlay {
        padding: 8px;
    }
    .modal-box {
        border-radius: 14px;
        max-width: 100% !important;
        margin: 4px auto;
    }
    .modal-header {
        padding: 16px 18px;
    }
    .modal-body {
        padding: 16px 14px;
    }
    .modal-footer {
        padding: 14px 16px;
        flex-direction: column-reverse;
        gap: 8px;
    }
    .modal-footer .btn {
        width: 100%;
        justify-content: center;
    }
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
            <span class="current">Penawaran</span>
        </div>
    </div>

    <div class="content">
        <div class="page-header-row">
            <div>
                <div class="page-title">Penawaran Harga</div>
                <div class="page-subtitle">Buat dan kelola surat penawaran harga untuk pelanggan</div>
            </div>
            <button class="btn btn-primary" onclick="openModal()" id="btn-buat-penawaran">
                <i class="fa-solid fa-plus"></i> Buat Penawaran Baru
            </button>
        </div>

        <!-- LIST PENAWARAN -->
        <div class="card">
            <div class="card-header">
                <h5><i class="fa-solid fa-list" style="color:var(--accent);margin-right:8px;"></i>Daftar Penawaran</h5>
                <span style="font-size:12px;color:var(--text3);"><?= mysqli_num_rows($list_penawaran) ?> penawaran</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor</th>
                            <th>Perihal</th>
                            <th>Kepada</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(mysqli_num_rows($list_penawaran)==0): ?>
                        <tr><td colspan="7" style="text-align:center;color:var(--text3);padding:52px 16px;">
                            <i class="fa-solid fa-file-circle-xmark" style="font-size:36px;margin-bottom:12px;display:block;color:var(--border);"></i>
                            Belum ada penawaran dibuat
                        </td></tr>
                    <?php else: $no=1; while($pnw=mysqli_fetch_assoc($list_penawaran)): ?>
                        <tr>
                            <td style="color:var(--text3);"><?= $no++ ?></td>
                            <td><span class="badge badge-penawaran"><?= htmlspecialchars($pnw['nomor_penawaran'] ?? '-') ?></span></td>
                            <td style="font-weight:500;max-width:240px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($pnw['judul'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($pnw['kepada'] ?? '-') ?></td>
                            <td class="money"><?= !empty($pnw['tanggal']) ? date('d/m/Y', strtotime($pnw['tanggal'])) : '-' ?></td>
                            <td class="money" style="color:var(--green);font-weight:600;">Rp <?= number_format($pnw['total'],0,',','.') ?></td>
                            <td>
                                <div style="display:flex;gap:6px;">
                                    <a href="detail_penawaran.php?id=<?= $pnw['id'] ?>" class="btn btn-primary btn-sm" title="Lihat Detail">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="edit_penawaran.php?id=<?= $pnw['id'] ?>" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <button onclick="konfirmasiHapus(<?= $pnw['id'] ?>, '<?= htmlspecialchars(addslashes($pnw['nomor_penawaran'])) ?>')"
                                            class="btn btn-danger btn-sm" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL BUAT PENAWARAN -->
<div class="modal-overlay" id="modalPenawaran">
    <div class="modal-box">
        <div class="modal-header">
            <h4><i class="fa-solid fa-file-signature"></i> Buat Penawaran Baru</h4>
            <button class="modal-close" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <form method="POST" action="simpan_penawaran.php" id="formPenawaran">

                <div class="form-grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group">
                        <label class="form-label">Nomor Penawaran</label>
                        <input type="text" name="nomor_penawaran" class="form-control"
                               value="<?= htmlspecialchars($nomor_otomatis) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <!-- Perihal -->
                <div class="form-group">
                    <label class="form-label">Perihal / Judul Penawaran</label>
                    <input type="text" name="judul" id="inputJudul" class="form-control"
                           placeholder="cth: PENAWARAN INSTALASI DAN SETTING KAMERA EZVIZ"
                           oninput="updatePreview()" required>
                </div>

                <!-- Kepada (1 field saja) -->
                <div class="form-group">
                    <label class="form-label">Kepada (Penerima)</label>
                    <input type="text" name="kepada" class="form-control"
                           placeholder="cth: Bapak/Ibu Pimpinan Universitas Islam Malang" required>
                </div>

                <!-- Preview isi surat otomatis -->
                <div class="form-group">
                    <label class="form-label">Preview Isi Pengantar Surat <span style="color:var(--text3);font-weight:400;font-size:11px;">(otomatis)</span></label>
                    <div class="surat-preview">
                        Dengan hormat,<br>
                        Sehubungan dengan adanya instalasi sistem keamanan di instansi Bapak/Ibu, kami dari
                        <strong>Waluyo Teknik</strong> mengajukan penawaran harga
                        <span class="hl" id="previewJudul">...</span>
                        dengan rincian sebagai berikut:
                    </div>
                    <div style="font-size:11px;color:var(--text3);margin-top:4px;"><i class="fa-solid fa-circle-info"></i> Isi surat akan otomatis mengikuti judul penawaran yang diisi di atas</div>
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
        <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="closeModal()">
                <i class="fa-solid fa-xmark"></i> Batal
            </button>
            <button type="submit" form="formPenawaran" class="btn btn-success">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Penawaran
            </button>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI HAPUS -->
<div class="modal-overlay" id="modalHapus">
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-header">
            <h4><i class="fa-solid fa-triangle-exclamation" style="color:var(--red);"></i> Konfirmasi Hapus</h4>
            <button class="modal-close" onclick="closeHapus()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <p style="color:var(--text2);line-height:1.7;">
                Apakah Anda yakin ingin menghapus penawaran<br>
                <strong id="hapusNomor" style="color:var(--text);"></strong>?<br>
                <span style="color:var(--red);font-size:12px;"><i class="fa-solid fa-circle-exclamation"></i> Data yang dihapus tidak dapat dikembalikan.</span>
            </p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="closeHapus()">Batal</button>
            <form id="formHapus" method="POST" action="hapus_penawaran.php" style="margin:0;">
                <input type="hidden" name="id" id="hapusId">
                <button type="submit" class="btn btn-danger">
                    <i class="fa-solid fa-trash"></i> Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>

<script>
const produkData = <?= $produk_json ?>;
let rowCount = 0;

function openModal() {
    document.getElementById('modalPenawaran').classList.add('active');
    document.body.style.overflow = 'hidden';
    if(rowCount === 0) { addItemRow(); addItemRow(); addItemRow(); }
}
function closeModal() {
    document.getElementById('modalPenawaran').classList.remove('active');
    document.body.style.overflow = '';
}
document.getElementById('modalPenawaran').addEventListener('click', function(e) {
    if(e.target === this) closeModal();
});

function konfirmasiHapus(id, nomor) {
    document.getElementById('hapusId').value = id;
    document.getElementById('hapusNomor').textContent = nomor;
    document.getElementById('modalHapus').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeHapus() {
    document.getElementById('modalHapus').classList.remove('active');
    document.body.style.overflow = '';
}
document.getElementById('modalHapus').addEventListener('click', function(e) {
    if(e.target === this) closeHapus();
});

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

function addItemRow() {
    rowCount++;
    const idx = rowCount;
    const div = document.createElement('div');
    div.className = 'item-row';
    div.id = 'item_' + idx;

    let opts = '<option value="">-- Pilih Produk --</option>';
    produkData.forEach(p => {
        const deskAttr = escapeHtml(p.deskripsi || '');
        const satAttr  = escapeHtml(p.satuan || 'unit');
        opts += `<option value="${p.nama_produk}" data-harga="${p.harga}" data-satuan="${satAttr}" data-deskripsi="${deskAttr}">${p.nama_produk}</option>`;
    });

    div.innerHTML = `
        <div class="item-col-main">
            <select class="form-control" onchange="onSelectProduk(${idx},this)" style="padding:7px 10px;font-size:12px;margin-bottom:5px;">
                ${opts}
            </select>
            <input type="text" name="nama_barang[]" class="item-nama form-control"
                   placeholder="atau ketik nama barang..." oninput="calcTotal()"
                   style="padding:7px 10px;font-size:12px;margin-bottom:5px;">
            <input type="text" name="deskripsi[]" class="item-deskripsi form-control"
                   placeholder="Deskripsi barang (opsional)..."
                   style="padding:7px 10px;font-size:12px;">
        </div>
        <div class="item-col-details">
            <input type="number" name="qty[]" class="form-control" placeholder="Qty" min="1" value="1"
                   oninput="calcTotal()" style="padding:7px 10px;font-size:12px;text-align:center;">
            <input type="text" name="satuan[]" class="form-control" placeholder="unit" value="unit"
                   style="padding:7px 10px;font-size:12px;text-align:center;">
            <div>
                <input type="number" name="harga[]" class="item-harga form-control"
                       placeholder="0" min="0" oninput="calcTotal()"
                       style="padding:7px 10px;font-size:12px;text-align:right;">
                <div class="subtotal-cell" id="sub_${idx}" style="margin-top:4px;">Rp 0</div>
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
</script>
<?= getClockScript() ?>
</body>
</html>
