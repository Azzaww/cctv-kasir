<?php
// _style.php - Shared CSS & sidebar component
// Include this file for shared styles

function renderSidebar($activePage = '') {
    $pages = [
        'dashboard' => ['icon' => 'fa-solid fa-house', 'label' => 'Dashboard', 'href' => 'dashboard.php'],
        'produk'    => ['icon' => 'fa-box-open', 'label' => 'Data Produk', 'href' => 'produk.php'],
        'transaksi'  => ['icon' => 'fa-receipt',       'label' => 'Transaksi',  'href' => 'transaksi.php'],
        'penawaran'  => ['icon' => 'fa-file-contract', 'label' => 'Penawaran',  'href' => 'penawaran.php'],
    ];
    echo '<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>';
    echo '<aside class="sidebar" id="appSidebar">';
    echo '<div class="sidebar-brand">';
    echo '<img src="logo_waluyo.png" alt="Waluyo Teknik" class="sidebar-logo">';
    echo '<button class="sidebar-close-btn" onclick="toggleSidebar()" aria-label="Tutup Menu" type="button"><i class="fa-solid fa-xmark"></i></button>';
    echo '</div>';
    echo '<nav class="sidebar-nav">';
    foreach ($pages as $key => $page) {
        $activeClass = ($activePage === $key) ? ' active' : '';
        echo "<a href=\"{$page['href']}\" class=\"nav-item{$activeClass}\"><i class=\"fa-solid {$page['icon']}\"></i><span>{$page['label']}</span></a>";
    }
    echo '<a href="logout.php" class="nav-item logout"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a>';
    echo '</nav>';
    echo '<div class="sidebar-footer"><div class="sidebar-time" id="clock"></div></div>';
    echo '</aside>';
}

function getSharedCSS() {
    return '
    @import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap");

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
        --bg: #f4f6fb;
        --bg2: #ffffff;
        --surface: #ffffff;
        --surface2: #f1f5f9;
        --border: rgba(15,23,42,0.08);
        --accent: #3b82f6;
        --accent2: #2563eb;
        --accent-glow: rgba(59,130,246,0.25);
        --green: #10b981;
        --yellow: #f59e0b;
        --red: #ef4444;
        --text: #1e293b;
        --text2: #475569;
        --text3: #94a3b8;
        --sidebar-w: 260px;
        --radius: 12px;
        --radius-lg: 16px;
        --shadow: 0 4px 24px rgba(15,23,42,0.08);
    }

    html, body {
        height: 100%;
        background: var(--bg);
        color: var(--text);
        font-family: "Plus Jakarta Sans", sans-serif;
        font-size: 14px;
        line-height: 1.6;
    }

    /* ── SIDEBAR ── */
    .sidebar {
        position: fixed;
        top: 0; left: 0;
        width: var(--sidebar-w);
        height: 100vh;
        background: var(--bg2);
        border-right: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        z-index: 100;
        transition: transform 0.3s ease;
    }

    .sidebar-brand {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px 16px;
        border-bottom: 1px solid var(--border);
        position: relative;
    }

    .sidebar-logo {
        max-width: 100%;
        height: auto;
    }

    .sidebar-close-btn {
        display: none;
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--surface2);
        color: var(--text2);
        cursor: pointer;
        align-items: center;
        justify-content: center;
        font-size: 15px;
    }

    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,0.45);
        backdrop-filter: blur(2px);
        z-index: 99;
        transition: opacity 0.25s ease;
    }

    .sidebar-toggle {
        display: none;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--text);
        cursor: pointer;
        font-size: 15px;
        transition: background 0.2s, border-color 0.2s;
        margin-right: 4px;
        flex-shrink: 0;
    }
    .sidebar-toggle:hover {
        background: var(--surface2);
        border-color: var(--accent);
    }

    .sidebar-nav {
        flex: 1;
        padding: 16px 12px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 14px;
        border-radius: var(--radius);
        color: var(--text2);
        text-decoration: none;
        font-weight: 500;
        font-size: 13.5px;
        transition: all 0.2s ease;
        position: relative;
    }

    .nav-item i { width: 18px; text-align: center; font-size: 15px; }

    .nav-item:hover {
        background: var(--surface2);
        color: var(--text);
    }

    .nav-item.active {
        background: linear-gradient(135deg, rgba(59,130,246,0.2), rgba(59,130,246,0.1));
        color: var(--accent2);
        border: 1px solid rgba(59,130,246,0.2);
    }

    .nav-item.active i { color: var(--accent); }

    .nav-item.logout {
        margin-top: auto;
        color: var(--text3);
    }
    .nav-item.logout:hover { background: rgba(239,68,68,0.1); color: var(--red); }

    .sidebar-footer {
        padding: 16px 20px;
        border-top: 1px solid var(--border);
    }

    .sidebar-time {
        font-family: "DM Mono", monospace;
        font-size: 12px;
        color: var(--text3);
        letter-spacing: 0.05em;
    }

    /* ── MAIN CONTENT ── */
    .main {
        margin-left: var(--sidebar-w);
        min-height: 100vh;
        background: var(--bg);
    }

    .topbar {
        background: var(--bg2);
        border-bottom: 1px solid var(--border);
        padding: 0 32px;
        height: 64px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 50;
    }

    .topbar-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text3);
        font-size: 13px;
    }

    .topbar-breadcrumb .current { color: var(--text); font-weight: 600; }

    .content {
        padding: 32px;
    }

    .page-title {
        font-size: 24px;
        font-weight: 800;
        color: var(--text);
        margin-bottom: 8px;
        letter-spacing: -0.02em;
    }

    .page-subtitle {
        color: var(--text3);
        font-size: 13px;
        margin-bottom: 28px;
    }

    /* ── CARDS ── */
    .card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-header h5 {
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
    }

    .card-body { padding: 24px; }

    /* ── STAT CARDS ── */
    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 24px;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 32px rgba(15,23,42,0.12);
    }

    .stat-card::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
    }

    .stat-card.blue::before { background: linear-gradient(90deg, var(--accent), var(--accent2)); }
    .stat-card.green::before { background: linear-gradient(90deg, #10b981, #34d399); }
    .stat-card.yellow::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }

    .stat-icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 16px;
    }

    .stat-card.blue .stat-icon { background: rgba(59,130,246,0.15); color: var(--accent); }
    .stat-card.green .stat-icon { background: rgba(16,185,129,0.15); color: #10b981; }
    .stat-card.yellow .stat-icon { background: rgba(245,158,11,0.15); color: #f59e0b; }

    .stat-value {
        font-size: 28px;
        font-weight: 800;
        letter-spacing: -0.03em;
        color: var(--text);
        line-height: 1;
        margin-bottom: 6px;
    }

    .stat-label {
        font-size: 12px;
        font-weight: 500;
        color: var(--text3);
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    /* ── TABLE ── */
    .table-wrap {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead tr {
        background: var(--surface2);
    }

    thead th {
        padding: 12px 16px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text3);
        text-align: left;
        white-space: nowrap;
    }

    tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background 0.15s;
    }

    tbody tr:last-child { border-bottom: none; }

    tbody tr:hover { background: rgba(15,23,42,0.025); }

    tbody td {
        padding: 14px 16px;
        font-size: 13.5px;
        color: var(--text);
        vertical-align: middle;
    }

    /* ── BUTTONS ── */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 18px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        font-family: "Plus Jakarta Sans", sans-serif;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .btn-primary {
        background: var(--accent);
        color: white;
        box-shadow: 0 4px 12px var(--accent-glow);
    }
    .btn-primary:hover { background: #2563eb; transform: translateY(-1px); box-shadow: 0 6px 20px var(--accent-glow); }

    .btn-success { background: var(--green); color: white; }
    .btn-success:hover { background: #059669; transform: translateY(-1px); }

    .btn-danger { background: var(--red); color: white; }
    .btn-danger:hover { background: #dc2626; }

    .btn-warning { background: var(--yellow); color: #1a1a1a; }
    .btn-warning:hover { background: #d97706; }

    .btn-ghost {
        background: transparent;
        color: var(--text2);
        border: 1px solid var(--border);
    }
    .btn-ghost:hover { background: var(--surface2); color: var(--text); }

    .btn-sm { padding: 6px 12px; font-size: 12px; gap: 5px; }

    /* ── FORM ── */
    .form-group { margin-bottom: 18px; }

    .form-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: var(--text2);
        margin-bottom: 7px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .form-control {
        width: 100%;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 13.5px;
        font-family: "Plus Jakarta Sans", sans-serif;
        color: var(--text);
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px var(--accent-glow);
    }

    select.form-control option { background: var(--surface); }

    /* ── BADGE ── */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge-blue { background: rgba(59,130,246,0.15); color: var(--accent2); }

    /* ── GRID ── */
    .grid { display: grid; gap: 20px; }
    .grid-3 { grid-template-columns: repeat(3, 1fr); }
    .grid-2 { grid-template-columns: repeat(2, 1fr); }

    /* ── RESPONSIVE / MOBILE CSS ── */
    @media (max-width: 850px) {
        :root {
            --sidebar-w: 260px;
        }

        .sidebar {
            transform: translateX(-100%);
            box-shadow: 0 0 35px rgba(0,0,0,0.3);
            z-index: 1000;
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .sidebar-overlay.active {
            display: block;
        }

        .sidebar-close-btn {
            display: flex;
        }

        .sidebar-toggle {
            display: inline-flex;
        }

        .main {
            margin-left: 0 !important;
            width: 100% !important;
            min-width: 0 !important;
        }

        .topbar {
            padding: 0 16px;
            height: 56px;
        }

        .topbar-breadcrumb {
            font-size: 12px;
            gap: 6px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .content {
            padding: 16px 14px !important;
        }

        .page-title {
            font-size: 20px;
            margin-bottom: 4px;
        }

        .page-subtitle {
            font-size: 12px;
            margin-bottom: 18px;
        }

        .card-header {
            padding: 14px 16px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .card-body {
            padding: 16px 14px;
        }

        .grid-3, .grid-2 {
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
            display: block;
            border-radius: 0 0 var(--radius-lg) var(--radius-lg);
        }

        .table-wrap table {
            min-width: 540px;
        }

        .btn {
            padding: 8px 14px;
            font-size: 12.5px;
        }

        .form-control {
            font-size: 14px;
        }

        .row {
            flex-direction: column;
            gap: 14px;
        }

        .col-4 {
            width: 100%;
            min-width: 0;
        }

        .flex-between {
            flex-wrap: wrap;
            gap: 12px;
        }
    }

    /* ── UTILITIES ── */
    .mt-4 { margin-top: 24px; }
    .mb-4 { margin-bottom: 24px; }
    .flex { display: flex; }
    .flex-between { display: flex; align-items: center; justify-content: space-between; }
    .text-muted { color: var(--text3); font-size: 12px; }
    .money { font-family: "DM Mono", monospace; font-size: 13px; }

    .row { display: flex; gap: 20px; flex-wrap: wrap; }
    .col-4 { flex: 1; min-width: 200px; }
    ';
}

function getClockScript() {
    return '
    <script>
    function toggleSidebar() {
        const sb = document.getElementById("appSidebar") || document.querySelector(".sidebar");
        const ov = document.getElementById("sidebarOverlay");
        if(sb) sb.classList.toggle("active");
        if(ov) ov.classList.toggle("active");
    }

    // Auto-insert hamburger menu button into topbar if not present
    document.addEventListener("DOMContentLoaded", function() {
        const topbar = document.querySelector(".topbar");
        const breadcrumb = document.querySelector(".topbar-breadcrumb");
        if(topbar && !document.querySelector(".sidebar-toggle")) {
            const btn = document.createElement("button");
            btn.className = "sidebar-toggle";
            btn.setAttribute("type", "button");
            btn.setAttribute("aria-label", "Buka Menu");
            btn.innerHTML = \'<i class="fa-solid fa-bars"></i>\';
            btn.onclick = toggleSidebar;
            if(breadcrumb) {
                breadcrumb.parentNode.insertBefore(btn, breadcrumb);
            } else {
                topbar.insertBefore(btn, topbar.firstChild);
            }
        }
        
        // Auto-close sidebar on mobile when nav item clicked
        document.querySelectorAll(".sidebar-nav .nav-item").forEach(function(item) {
            item.addEventListener("click", function() {
                if(window.innerWidth <= 850) {
                    toggleSidebar();
                }
            });
        });
    });

    function updateClock() {
        const now = new Date();
        const t = now.toLocaleTimeString("id-ID", {hour:"2-digit",minute:"2-digit",second:"2-digit"});
        const d = now.toLocaleDateString("id-ID",{day:"2-digit",month:"short",year:"numeric"});
        const el = document.getElementById("clock");
        if(el) el.innerHTML = d + "<br>" + t;
    }
    updateClock();
    setInterval(updateClock, 1000);
    </script>
    ';
}
?>