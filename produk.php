<!-- produk.php -->
<?php
session_start();
if(!isset($_SESSION['login'])){ header("Location: login.php"); exit; }
include 'koneksi.php';
include '_style.php';

// Handle hapus
if(isset($_GET['hapus'])){
    $id = (int)$_GET['hapus'];
    mysqli_query($conn,"DELETE FROM produk WHERE id=$id");
    header("Location: produk.php?deleted=1"); exit;
}

$data = mysqli_query($conn,"SELECT * FROM produk ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Data Produk – Waluyo Teknik CCTV</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<!-- Modal untuk tambah/edit produk -->
<style><?= getSharedCSS() ?>

@keyframes slideUp {
    from { opacity:0; transform:translateY(20px); }
    to   { opacity:1; transform:translateY(0); }
}
.card { animation: slideUp 0.4s ease both; }

.satuan-badge {
    display: inline-block;
    padding: 2px 10px;
    background: rgba(99,102,241,0.12);
    color: #a5b4fc;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.05em;
}

.price-cell { font-family: "DM Mono", monospace; font-size: 13px; color: var(--green); font-weight: 500; }

/* Modal */
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.6);
    z-index: 200;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(4px);
}
.modal-overlay.show { display: flex; }
.modal-box {
    background: var(--bg2);
    border: 1px solid var(--border);
    border-radius: 20px;
    width: 480px;
    max-width: 95vw;
    box-shadow: 0 32px 64px rgba(0,0,0,0.6);
    animation: slideUp 0.3s ease;
}
.modal-header {
    padding: 24px 28px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.modal-header h5 { font-size: 16px; font-weight: 700; }
.modal-close {
    background: none;
    border: none;
    color: var(--text3);
    font-size: 18px;
    cursor: pointer;
    padding: 4px;
    transition: color 0.2s;
}
.modal-close:hover { color: var(--text); }
.modal-body { padding: 28px; }
.modal-footer { padding: 0 28px 28px; display: flex; gap: 10px; justify-content: flex-end; }

.alert-success {
    background: rgba(16,185,129,0.1);
    border: 1px solid rgba(16,185,129,0.2);
    color: #6ee7b7;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 13px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}
</style>
</head>
<body>

<?php renderSidebar('produk'); ?>

<div class="main">
    <div class="topbar">
        <div class="topbar-breadcrumb">
            <i class="fa-solid fa-house" style="color:var(--text3);font-size:12px;"></i>
            <span style="color:var(--text3);">/</span>
            <span class="current">Data Produk</span>
        </div>
    </div>

    <div class="content">
        <div class="flex-between mb-4">
            <div>
                <div class="page-title">Data Produk</div>
                <div class="page-subtitle">Kelola daftar produk dan layanan CCTV</div>
            </div>
            <button class="btn btn-primary" onclick="document.getElementById('modalTambah').classList.add('show')">
                <i class="fa-solid fa-plus"></i> Tambah Produk
            </button>
        </div>

        <?php if(isset($_GET['deleted'])): ?>
        <div class="alert-success"><i class="fa-solid fa-circle-check"></i> Produk berhasil dihapus.</div>
        <?php endif; ?>
        <?php if(isset($_GET['saved'])): ?>
        <div class="alert-success"><i class="fa-solid fa-circle-check"></i> Produk berhasil disimpan.</div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h5><i class="fa-solid fa-list" style="color:var(--accent);margin-right:8px;"></i>Daftar Produk</h5>
                <span class="text-muted"><?= mysqli_num_rows($data) ?> item</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Satuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(mysqli_num_rows($data)==0): ?>
                        <tr><td colspan="5" style="text-align:center;color:var(--text3);padding:40px;">Belum ada produk. <a href="#" onclick="document.getElementById('modalTambah').classList.add('show')" style="color:var(--accent);">Tambah sekarang</a></td></tr>
                    <?php else: $no=1; while($d=mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td style="color:var(--text3);width:50px;"><?= $no++ ?></td>
                            <td style="font-weight:500;"><?= htmlspecialchars($d['nama_produk']) ?></td>
                            <td class="price-cell">Rp <?= number_format($d['harga'],0,',','.') ?></td>
                            <td><span class="satuan-badge"><?= htmlspecialchars($d['satuan']) ?></span></td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick="editProduk(<?= $d['id'] ?>, '<?= addslashes($d['nama_produk']) ?>', <?= $d['harga'] ?>, '<?= $d['satuan'] ?>')">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <a href="produk.php?hapus=<?= $d['id'] ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Hapus produk ini?')">
                                    <i class="fa-solid fa-trash"></i>
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

<!-- Modal Tambah -->
<div class="modal-overlay" id="modalTambah">
    <div class="modal-box">
        <div class="modal-header">
            <h5><i class="fa-solid fa-plus" style="color:var(--accent);margin-right:8px;"></i>Tambah Produk</h5>
            <button class="modal-close" onclick="document.getElementById('modalTambah').classList.remove('show')">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form method="POST" action="simpan_produk.php">
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">Nama Produk</label>
                <input type="text" name="nama_produk" class="form-control" placeholder="Contoh: IP HIKVISION 2MP" required>
            </div>
            <div class="form-group">
                <label class="form-label">Harga (Rp)</label>
                <input type="number" name="harga" class="form-control" placeholder="0" required>
            </div>
            <div class="form-group">
                <label class="form-label">Satuan</label>
                <select name="satuan" class="form-control">
                    <option value="UNIT">UNIT</option>
                    <option value="PAKET">PAKET</option>
                    <option value="METER">METER</option>
                    <option value="KAMERA">KAMERA</option>
                    <option value="PCS">PCS</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="document.getElementById('modalTambah').classList.remove('show')">Batal</button>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
        </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal-overlay" id="modalEdit">
    <div class="modal-box">
        <div class="modal-header">
            <h5><i class="fa-solid fa-pen" style="color:var(--yellow);margin-right:8px;"></i>Edit Produk</h5>
            <button class="modal-close" onclick="document.getElementById('modalEdit').classList.remove('show')">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form method="POST" action="simpan_produk.php">
        <input type="hidden" name="edit_id" id="edit_id">
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">Nama Produk</label>
                <input type="text" name="nama_produk" id="edit_nama" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Harga (Rp)</label>
                <input type="number" name="harga" id="edit_harga" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Satuan</label>
                <select name="satuan" id="edit_satuan" class="form-control">
                    <option value="UNIT">UNIT</option>
                    <option value="PAKET">PAKET</option>
                    <option value="METER">METER</option>
                    <option value="KAMERA">KAMERA</option>
                    <option value="PCS">PCS</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="document.getElementById('modalEdit').classList.remove('show')">Batal</button>
            <button type="submit" class="btn btn-warning"><i class="fa-solid fa-floppy-disk"></i> Update</button>
        </div>
        </form>
    </div>
</div>

<script>
function editProduk(id, nama, harga, satuan) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_harga').value = harga;
    document.getElementById('edit_satuan').value = satuan;
    document.getElementById('modalEdit').classList.add('show');
}

// Close modal on backdrop click
document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', function(e) {
        if(e.target === this) this.classList.remove('show');
    });
});
</script>
<?= getClockScript() ?>
</body>
</html>
