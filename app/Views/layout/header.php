<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= $title ?? 'CBT Admin' ?> &mdash; CBT Admin Panel</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{
    --sidebar:#0f172a;
    --sidebar-hover:#1e293b;
    --accent:#6366f1;
    --accent2:#8b5cf6;
    --bg:#0f172a;
    --surface:#1e293b;
    --surface2:#334155;
    --text:#e2e8f0;
    --muted:#94a3b8;
    --border:#334155;
    --success:#10b981;
    --warning:#f59e0b;
    --danger:#ef4444;
}
body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);display:flex;min-height:100vh;overflow-x:hidden}

/* Sidebar */
.sidebar{
    width:260px;
    background:var(--sidebar);
    border-right:1px solid var(--border);
    display:flex;
    flex-direction:column;
    position:fixed;
    height:100vh;
    z-index:100;
    transition:transform .25s ease-in-out;
}
.sidebar-brand{padding:20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px}
.brand-icon{width:40px;height:40px;background:linear-gradient(135deg,var(--accent),var(--accent2));border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;color:#fff;flex-shrink:0}
.brand-text h2{font-size:15px;font-weight:700;color:#fff}
.brand-text p{font-size:11px;color:var(--muted);margin-top:1px}
.sidebar-nav{flex:1;padding:16px 12px;overflow-y:auto}
.nav-section{font-size:10px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;padding:8px 8px 4px;margin-top:8px}
.nav-item{display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;color:var(--muted);text-decoration:none;font-size:13.5px;font-weight:500;transition:all .15s;margin-bottom:2px}
.nav-item:hover,.nav-item.active{background:var(--sidebar-hover);color:#fff}
.nav-item.active{background:rgba(99,102,241,.15);color:var(--accent)}
.nav-item .icon{width:18px;text-align:center;font-size:14px}
.sidebar-footer{padding:16px 20px;border-top:1px solid var(--border)}
.user-info{display:flex;align-items:center;gap:10px}
.avatar{width:36px;height:36px;background:linear-gradient(135deg,var(--accent),var(--accent2));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;color:#fff;font-weight:600;flex-shrink:0}
.user-name{font-size:13px;font-weight:600;color:#fff}
.user-role{font-size:11px;color:var(--muted)}
.logout-btn{margin-left:auto;background:none;border:none;color:var(--muted);cursor:pointer;padding:6px;border-radius:6px;transition:.15s}
.logout-btn:hover{color:var(--danger);background:rgba(239,68,68,.1)}

/* Mobile Backdrop */
.sidebar-backdrop{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.6);
    backdrop-filter:blur(3px);
    z-index:90;
    transition:opacity .25s ease;
}

/* Main Content Area */
.main{margin-left:260px;flex:1;display:flex;flex-direction:column;min-width:0;width:calc(100% - 260px)}
.topbar{height:60px;background:var(--sidebar);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 24px;gap:16px;position:sticky;top:0;z-index:80}
.topbar h1{font-size:16px;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.topbar-actions{margin-left:auto;display:flex;gap:8px;align-items:center}
.mobile-toggle{display:none;background:none;border:1px solid var(--border);color:var(--text);padding:8px 10px;border-radius:8px;cursor:pointer;font-size:14px}
.mobile-toggle:hover{background:var(--surface2)}
.content{padding:24px;flex:1}

/* Cards & Layout */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px}
.stat-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:20px;position:relative;overflow:hidden}
.stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--accent),var(--accent2))}
.stat-label{font-size:12px;color:var(--muted);font-weight:500;margin-bottom:8px}
.stat-value{font-size:26px;font-weight:700;color:#fff;line-height:1}
.stat-icon{position:absolute;right:16px;top:50%;transform:translateY(-50%);font-size:32px;opacity:.08;color:var(--accent)}
.card{background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden}
.card-header{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px}
.card-title{font-size:15px;font-weight:600;color:#fff}
.card-body{padding:20px}

/* Table */
.table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}
table{width:100%;border-collapse:collapse;font-size:13.5px}
thead th{padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid var(--border);white-space:nowrap}
tbody td{padding:11px 14px;border-bottom:1px solid rgba(255,255,255,.03);color:var(--text)}
tbody tr:hover{background:rgba(255,255,255,.02)}

/* Badges */
.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:100px;font-size:11px;font-weight:600;white-space:nowrap}
.badge-draft{background:rgba(148,163,184,.15);color:var(--muted)}
.badge-aktif{background:rgba(16,185,129,.15);color:var(--success)}
.badge-selesai{background:rgba(99,102,241,.15);color:var(--accent)}
.badge-hadir{background:rgba(16,185,129,.15);color:var(--success)}
.badge-absen{background:rgba(239,68,68,.15);color:var(--danger)}

/* Buttons */
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:8px;font-size:13px;font-weight:500;font-family:'Inter',sans-serif;cursor:pointer;border:none;transition:all .15s;text-decoration:none;position:relative}
.btn-primary{background:linear-gradient(135deg,var(--accent),var(--accent2));color:#fff;box-shadow:0 2px 8px rgba(99,102,241,.25)}
.btn-primary:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(99,102,241,.35)}
.btn-sm{padding:5px 10px;font-size:12px}
.btn-outline{background:transparent;border:1px solid var(--border);color:var(--text)}
.btn-outline:hover{background:var(--surface2)}
.btn-danger{background:rgba(239,68,68,.15);color:var(--danger);border:1px solid rgba(239,68,68,.2)}
.btn-danger:hover{background:rgba(239,68,68,.25)}
.btn-success{background:rgba(16,185,129,.15);color:var(--success);border:1px solid rgba(16,185,129,.2)}
.btn-warning{background:rgba(245,158,11,.15);color:var(--warning);border:1px solid rgba(245,158,11,.2)}

/* Button Loading State */
.btn.is-loading{
    pointer-events:none;
    opacity:0.75;
    cursor:not-allowed;
}

/* Forms */
.form-group{margin-bottom:18px}
.form-label{display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:6px}
.form-control{width:100%;background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:9px 12px;color:var(--text);font-size:13.5px;font-family:'Inter',sans-serif;outline:none;transition:.15s}
.form-control:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(99,102,241,.1)}
.form-control option{background:var(--surface)}

/* Progress Bars */
.progress{height:8px;background:var(--surface2);border-radius:100px;overflow:hidden}
.progress-bar{height:100%;background:linear-gradient(90deg,var(--accent),var(--accent2));border-radius:100px;transition:width .5s ease}

/* Modern Floating Toast Notifications */
.toast-container{
    position:fixed;
    top:20px;
    right:20px;
    z-index:1100;
    display:flex;
    flex-direction:column;
    gap:10px;
    max-width:380px;
    width:calc(100% - 40px);
    pointer-events:none;
}
.toast-item{
    pointer-events:auto;
    background:var(--surface);
    border:1px solid var(--border);
    border-radius:10px;
    padding:14px 16px;
    box-shadow:0 10px 30px rgba(0,0,0,0.5);
    display:flex;
    align-items:center;
    gap:12px;
    color:#fff;
    font-size:13px;
    transition:all .3s cubic-bezier(0.16, 1, 0.3, 1);
    transform:translateX(0);
    opacity:1;
}
.toast-item.hiding{
    opacity:0;
    transform:translateX(50px);
}
.toast-success{border-left:4px solid var(--success)}
.toast-danger{border-left:4px solid var(--danger)}
.toast-warning{border-left:4px solid var(--warning)}
.toast-icon{font-size:18px;flex-shrink:0}
.toast-text{flex:1;line-height:1.4}
.toast-close{background:none;border:none;color:var(--muted);cursor:pointer;padding:4px;font-size:14px;margin-left:auto}
.toast-close:hover{color:#fff}

/* Confirm Dialog Modal */
.confirm-overlay{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.65);
    backdrop-filter:blur(4px);
    z-index:1200;
    align-items:center;
    justify-content:center;
    padding:16px;
}
.confirm-overlay.show{display:flex}
.confirm-box{
    background:var(--surface);
    border:1px solid var(--border);
    border-radius:14px;
    padding:24px;
    max-width:400px;
    width:100%;
    box-shadow:0 20px 60px rgba(0,0,0,.6);
    animation:modalPop .2s ease-out;
}
@keyframes modalPop{
    from{transform:scale(0.92);opacity:0}
    to{transform:scale(1);opacity:1}
}
.confirm-title{font-size:16px;font-weight:700;color:#fff;margin-bottom:8px;display:flex;align-items:center;gap:8px}
.confirm-msg{font-size:13.5px;color:var(--muted);margin-bottom:20px;line-height:1.5}
.confirm-actions{display:flex;gap:10px;justify-content:flex-end}

/* Responsive CSS for Tablet & Mobile */
@media (max-width: 768px){
    .sidebar{
        transform:translateX(-100%);
        z-index:200;
        box-shadow:0 0 30px rgba(0,0,0,0.8);
    }
    .sidebar.show{
        transform:translateX(0);
    }
    .sidebar-backdrop.show{
        display:block;
    }
    .main{
        margin-left:0;
        width:100%;
    }
    .mobile-toggle{
        display:inline-flex;
        align-items:center;
        justify-content:center;
    }
    .topbar{
        padding:0 16px;
    }
    .content{
        padding:16px;
    }
    .stats-grid{
        grid-template-columns:1fr;
    }
}
</style>
</head>
<body>

<!-- Mobile Sidebar Backdrop -->
<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

<!-- Floating Toast Notification Container -->
<div class="toast-container" id="toastContainer"></div>

<aside class="sidebar" id="sidebarNav">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="fas fa-graduation-cap"></i></div>
        <div class="brand-text">
            <h2>CBT Admin</h2>
            <p>Manajemen Ujian</p>
        </div>
        <button class="mobile-toggle" onclick="toggleSidebar()" style="margin-left:auto;border:none" title="Tutup Menu">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section">Utama</div>
        <a href="<?= base_url('dashboard') ?>" class="nav-item <?= (uri_string() === 'dashboard') ? 'active' : '' ?>">
            <span class="icon"><i class="fas fa-home"></i></span> Dashboard
        </a>
        <a href="<?= base_url('exam') ?>" class="nav-item <?= str_starts_with(uri_string(),'exam') ? 'active' : '' ?>">
            <span class="icon"><i class="fas fa-clipboard-list"></i></span> Manajemen Ujian
        </a>

        <div class="nav-section">Persiapan</div>
        <a href="<?= base_url('room') ?>" class="nav-item <?= str_starts_with(uri_string(),'room') ? 'active' : '' ?>">
            <span class="icon"><i class="fas fa-door-open"></i></span> Ruang Ujian
        </a>

        <div class="nav-section">Pelaksanaan CBT</div>
        <a href="<?= base_url('exam') ?>" class="nav-item <?= str_starts_with(uri_string(),'participant') ? 'active' : '' ?>">
            <span class="icon"><i class="fas fa-users"></i></span> Peserta &amp; Kartu
        </a>
        <a href="<?= base_url('exam') ?>" class="nav-item <?= str_starts_with(uri_string(),'token') ? 'active' : '' ?>">
            <span class="icon"><i class="fas fa-key"></i></span> Rilis Token CBT
        </a>
        <a href="<?= base_url('exam') ?>" class="nav-item <?= str_starts_with(uri_string(),'supervisor') ? 'active' : '' ?>">
            <span class="icon"><i class="fas fa-user-shield"></i></span> Pengawas &amp; Proktor
        </a>
        <a href="<?= base_url('attendance') ?>" class="nav-item <?= str_starts_with(uri_string(),'attendance') ? 'active' : '' ?>">
            <span class="icon"><i class="fas fa-check-square"></i></span> Daftar Hadir
        </a>
        <a href="<?= base_url('exam') ?>" class="nav-item <?= str_starts_with(uri_string(),'minutes') ? 'active' : '' ?>">
            <span class="icon"><i class="fas fa-file-alt"></i></span> Berita Acara
        </a>

        <div class="nav-section">Pelaporan &amp; Analisis</div>
        <a href="<?= base_url('exam') ?>" class="nav-item <?= str_starts_with(uri_string(),'grade') ? 'active' : '' ?>">
            <span class="icon"><i class="fas fa-chart-bar"></i></span> Download Nilai
        </a>
        <a href="<?= base_url('exam') ?>" class="nav-item <?= str_starts_with(uri_string(),'analysis') ? 'active' : '' ?>">
            <span class="icon"><i class="fas fa-chart-pie"></i></span> Analisis Butir Soal
        </a>

        <div class="nav-section">Integrasi &amp; Pengaturan</div>
        <a href="<?= base_url('setting') ?>" class="nav-item <?= (uri_string() === 'setting') ? 'active' : '' ?>">
            <span class="icon"><i class="fas fa-school"></i></span> Pengaturan Sekolah
        </a>
        <a href="<?= base_url('user') ?>" class="nav-item <?= str_starts_with(uri_string(),'user') ? 'active' : '' ?>">
            <span class="icon"><i class="fas fa-users-cog"></i></span> Manajemen Admin
        </a>
        <a href="<?= base_url('moodle') ?>" class="nav-item <?= str_starts_with(uri_string(),'moodle') ? 'active' : '' ?>">
            <span class="icon"><i class="fas fa-network-wired"></i></span> Koneksi Moodle
        </a>
        <a href="<?= base_url('moodle/courses') ?>" class="nav-item <?= (uri_string() === 'moodle/courses') ? 'active' : '' ?>">
            <span class="icon"><i class="fas fa-graduation-cap"></i></span> Kursus &amp; Quiz
        </a>
    </nav>
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="avatar"><?= strtoupper(substr(session()->get('admin_nama') ?? 'A', 0, 1)) ?></div>
            <div>
                <div class="user-name"><?= esc(session()->get('admin_nama')) ?></div>
                <div class="user-role"><?= ucfirst(session()->get('admin_role')) ?></div>
            </div>
            <a href="<?= base_url('auth/logout') ?>" class="logout-btn" title="Keluar"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>
</aside>

<div class="main">
    <div class="topbar">
        <button class="mobile-toggle" onclick="toggleSidebar()" aria-label="Toggle Navigation">
            <i class="fas fa-bars"></i>
        </button>
        <h1><?= $title ?? 'Dashboard' ?></h1>
        <div class="topbar-actions"></div>
    </div>

    <div class="content">

<!-- Confirm Dialog Modal -->
<div class="confirm-overlay" id="confirmOverlay">
    <div class="confirm-box">
        <div class="confirm-title" id="confirmTitle"><i class="fas fa-exclamation-triangle" style="color:var(--danger)"></i> Konfirmasi</div>
        <div class="confirm-msg" id="confirmMsg">Apakah Anda yakin ingin melanjutkan tindakan ini?</div>
        <div class="confirm-actions">
            <button class="btn btn-outline" onclick="closeConfirm()">Batal</button>
            <a href="#" id="confirmBtn" class="btn btn-danger">Ya, Lanjutkan</a>
        </div>
    </div>
</div>

<script>
// ─── Responsive Sidebar Mobile Toggle ────────────────────────────────────────
function toggleSidebar() {
    var sidebar  = document.getElementById('sidebarNav');
    var backdrop = document.getElementById('sidebarBackdrop');
    if (!sidebar) return;

    var isOpen = sidebar.classList.toggle('show');
    if (backdrop) {
        if (isOpen) {
            backdrop.classList.add('show');
        } else {
            backdrop.classList.remove('show');
        }
    }
}

// ─── Reusable Confirm Dialog ──────────────────────────────────────────────────
function showConfirm(url, title, msg, btnClass) {
    document.getElementById('confirmTitle').innerHTML = '<i class="fas fa-exclamation-triangle" style="color:var(--danger)"></i> ' + (title || 'Konfirmasi');
    document.getElementById('confirmMsg').textContent = msg || 'Apakah Anda yakin ingin melanjutkan?';
    var btn = document.getElementById('confirmBtn');
    btn.href = url;
    btn.className = 'btn ' + (btnClass || 'btn-danger');
    btn.innerHTML = '<i class="fas fa-check"></i> Ya, Lanjutkan';
    document.getElementById('confirmOverlay').classList.add('show');
}
function closeConfirm() {
    document.getElementById('confirmOverlay').classList.remove('show');
}
document.getElementById('confirmOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeConfirm();
});

// ─── Modern Toast Notification System ─────────────────────────────────────────
function showToast(message, type) {
    var container = document.getElementById('toastContainer');
    if (!container) return;

    type = type || 'success';
    var iconClass = 'fa-check-circle';
    var iconColor = 'var(--success)';

    if (type === 'danger' || type === 'error') {
        type = 'danger';
        iconClass = 'fa-times-circle';
        iconColor = 'var(--danger)';
    } else if (type === 'warning') {
        iconClass = 'fa-exclamation-triangle';
        iconColor = 'var(--warning)';
    }

    var toast = document.createElement('div');
    toast.className = 'toast-item toast-' + type;
    toast.innerHTML = '<i class="fas ' + iconClass + ' toast-icon" style="color:' + iconColor + '"></i>' +
                      '<div class="toast-text">' + message + '</div>' +
                      '<button class="toast-close" onclick="dismissToast(this.parentElement)">&times;</button>';

    container.appendChild(toast);

    // Auto dismiss after 4 seconds
    setTimeout(function() {
        dismissToast(toast);
    }, 4000);
}

function dismissToast(el) {
    if (!el || el.classList.contains('hiding')) return;
    el.classList.add('hiding');
    setTimeout(function() {
        if (el.parentElement) el.parentElement.removeChild(el);
    }, 300);
}

// ─── Global Button Loading States for Forms ───────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('form').forEach(function(form) {
        form.addEventListener('submit', function() {
            var submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn && !form.dataset.noLoading) {
                submitBtn.classList.add('is-loading');
                var originalHtml = submitBtn.innerHTML;
                submitBtn.setAttribute('data-original-html', originalHtml);
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right:6px"></i> Memproses...';
            }
        });
    });

    // Flash messages rendered via Toast
    <?php if(session()->getFlashdata('success')): ?>
        showToast(<?= json_encode(session()->getFlashdata('success')) ?>, 'success');
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
        showToast(<?= json_encode(session()->getFlashdata('error')) ?>, 'danger');
    <?php endif; ?>
    <?php if(session()->getFlashdata('warning')): ?>
        showToast(<?= json_encode(session()->getFlashdata('warning')) ?>, 'warning');
    <?php endif; ?>
});
</script>
