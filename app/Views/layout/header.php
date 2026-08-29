<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= $title ?? 'CBT Admin' ?> — CBT Admin Panel</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{--sidebar:#0f172a;--sidebar-hover:#1e293b;--accent:#6366f1;--accent2:#8b5cf6;--bg:#0f172a;--surface:#1e293b;--surface2:#334155;--text:#e2e8f0;--muted:#94a3b8;--border:#334155;--success:#10b981;--warning:#f59e0b;--danger:#ef4444}
body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);display:flex;min-height:100vh}
/* Sidebar */
.sidebar{width:260px;background:var(--sidebar);border-right:1px solid var(--border);display:flex;flex-direction:column;position:fixed;height:100vh;z-index:100}
.sidebar-brand{padding:24px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px}
.brand-icon{width:40px;height:40px;background:linear-gradient(135deg,var(--accent),var(--accent2));border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;color:#fff;flex-shrink:0}
.brand-text h2{font-size:15px;font-weight:700;color:#fff}
.brand-text p{font-size:11px;color:var(--muted);margin-top:1px}
.sidebar-nav{flex:1;padding:16px 12px;overflow-y:auto}
.nav-section{font-size:10px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;padding:8px 8px 4px;margin-top:8px}
.nav-item{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;color:var(--muted);text-decoration:none;font-size:13.5px;font-weight:500;transition:all .15s;margin-bottom:2px}
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
/* Main */
.main{margin-left:260px;flex:1;display:flex;flex-direction:column}
.topbar{height:60px;background:var(--sidebar);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 24px;gap:16px}
.topbar h1{font-size:16px;font-weight:600;color:#fff}
.topbar-actions{margin-left:auto;display:flex;gap:8px}
.content{padding:24px;flex:1}
/* Cards */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:24px}
.stat-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:20px;position:relative;overflow:hidden}
.stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--accent),var(--accent2))}
.stat-label{font-size:12px;color:var(--muted);font-weight:500;margin-bottom:8px}
.stat-value{font-size:28px;font-weight:700;color:#fff;line-height:1}
.stat-icon{position:absolute;right:16px;top:50%;transform:translateY(-50%);font-size:32px;opacity:.08;color:var(--accent)}
.card{background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden}
.card-header{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.card-title{font-size:15px;font-weight:600;color:#fff}
.card-body{padding:20px}
/* Table */
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:13.5px}
thead th{padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid var(--border)}
tbody td{padding:11px 14px;border-bottom:1px solid rgba(255,255,255,.03);color:var(--text)}
tbody tr:hover{background:rgba(255,255,255,.02)}
/* Badges */
.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:100px;font-size:11px;font-weight:600}
.badge-draft{background:rgba(148,163,184,.15);color:var(--muted)}
.badge-aktif{background:rgba(16,185,129,.15);color:var(--success)}
.badge-selesai{background:rgba(99,102,241,.15);color:var(--accent)}
.badge-hadir{background:rgba(16,185,129,.15);color:var(--success)}
.badge-absen{background:rgba(239,68,68,.15);color:var(--danger)}
/* Buttons */
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:8px;font-size:13px;font-weight:500;font-family:'Inter',sans-serif;cursor:pointer;border:none;transition:all .15s;text-decoration:none}
.btn-primary{background:linear-gradient(135deg,var(--accent),var(--accent2));color:#fff;box-shadow:0 2px 8px rgba(99,102,241,.25)}
.btn-primary:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(99,102,241,.35)}
.btn-sm{padding:5px 10px;font-size:12px}
.btn-outline{background:transparent;border:1px solid var(--border);color:var(--text)}
.btn-outline:hover{background:var(--surface2)}
.btn-danger{background:rgba(239,68,68,.15);color:var(--danger);border:1px solid rgba(239,68,68,.2)}
.btn-danger:hover{background:rgba(239,68,68,.25)}
.btn-success{background:rgba(16,185,129,.15);color:var(--success);border:1px solid rgba(16,185,129,.2)}
.btn-warning{background:rgba(245,158,11,.15);color:var(--warning);border:1px solid rgba(245,158,11,.2)}
/* Forms */
.form-group{margin-bottom:18px}
.form-label{display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:6px}
.form-control{width:100%;background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:9px 12px;color:var(--text);font-size:13.5px;font-family:'Inter',sans-serif;outline:none;transition:.15s}
.form-control:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(99,102,241,.1)}
.form-control option{background:var(--surface)}
/* Alerts */
.alert{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:8px}
.alert-success{background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.25);color:#6ee7b7}
.alert-danger{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.25);color:#fca5a5}
/* Progress bars */
.progress{height:8px;background:var(--surface2);border-radius:100px;overflow:hidden}
.progress-bar{height:100%;background:linear-gradient(90deg,var(--accent),var(--accent2));border-radius:100px;transition:width .5s ease}
</style>
</head>
<body>
<aside class="sidebar">
<div class="sidebar-brand">
<div class="brand-icon"><i class="fas fa-graduation-cap"></i></div>
<div class="brand-text"><h2>CBT Admin</h2><p>Manajemen Ujian</p></div>
</div>
<nav class="sidebar-nav">
<div class="nav-section">Utama</div>
<a href="<?= base_url('dashboard') ?>" class="nav-item <?= (uri_string() === 'dashboard') ? 'active' : '' ?>"><span class="icon"><i class="fas fa-home"></i></span> Dashboard</a>
<a href="<?= base_url('exam') ?>" class="nav-item <?= str_starts_with(uri_string(),'exam') ? 'active' : '' ?>"><span class="icon"><i class="fas fa-clipboard-list"></i></span> Manajemen Ujian</a>
<div class="nav-section">Persiapan</div>
<a href="<?= base_url('room') ?>" class="nav-item <?= str_starts_with(uri_string(),'room') ? 'active' : '' ?>"><span class="icon"><i class="fas fa-door-open"></i></span> Ruang Ujian</a>
<div class="nav-section">Pelaksanaan CBT</div>
<a href="<?= base_url('exam') ?>" class="nav-item <?= str_starts_with(uri_string(),'participant') ? 'active' : '' ?>"><span class="icon"><i class="fas fa-users"></i></span> Peserta & Kartu</a>
<a href="<?= base_url('exam') ?>" class="nav-item <?= str_starts_with(uri_string(),'token') ? 'active' : '' ?>"><span class="icon"><i class="fas fa-key"></i></span> Rilis Token CBT</a>
<a href="<?= base_url('exam') ?>" class="nav-item <?= str_starts_with(uri_string(),'supervisor') ? 'active' : '' ?>"><span class="icon"><i class="fas fa-user-shield"></i></span> Pengawas & Proktor</a>
<a href="<?= base_url('attendance') ?>" class="nav-item <?= str_starts_with(uri_string(),'attendance') ? 'active' : '' ?>"><span class="icon"><i class="fas fa-check-square"></i></span> Daftar Hadir</a>
<a href="<?= base_url('exam') ?>" class="nav-item <?= str_starts_with(uri_string(),'minutes') ? 'active' : '' ?>"><span class="icon"><i class="fas fa-file-alt"></i></span> Berita Acara</a>
<div class="nav-section">Pelaporan & Analisis</div>
<a href="<?= base_url('exam') ?>" class="nav-item <?= str_starts_with(uri_string(),'grade') ? 'active' : '' ?>"><span class="icon"><i class="fas fa-chart-bar"></i></span> Download Nilai</a>
<a href="<?= base_url('exam') ?>" class="nav-item <?= str_starts_with(uri_string(),'analysis') ? 'active' : '' ?>"><span class="icon"><i class="fas fa-chart-pie"></i></span> Analisis Butir Soal</a>
<div class="nav-section">Integrasi & Pengaturan</div>
<a href="<?= base_url('setting') ?>" class="nav-item <?= (uri_string() === 'setting') ? 'active' : '' ?>"><span class="icon"><i class="fas fa-school"></i></span> Pengaturan Sekolah</a>
<a href="<?= base_url('moodle') ?>" class="nav-item <?= (uri_string() === 'moodle') ? 'active' : '' ?>"><span class="icon"><i class="fas fa-network-wired"></i></span> Koneksi Moodle</a>
<a href="<?= base_url('moodle/courses') ?>" class="nav-item <?= (uri_string() === 'moodle/courses') ? 'active' : '' ?>"><span class="icon"><i class="fas fa-graduation-cap"></i></span> Kursus & Quiz</a>
</nav>
<div class="sidebar-footer">
<div class="user-info">
<div class="avatar"><?= strtoupper(substr(session()->get('admin_nama') ?? 'A', 0, 1)) ?></div>
<div><div class="user-name"><?= session()->get('admin_nama') ?></div><div class="user-role"><?= ucfirst(session()->get('admin_role')) ?></div></div>
<a href="<?= base_url('auth/logout') ?>" class="logout-btn" title="Keluar"><i class="fas fa-sign-out-alt"></i></a>
</div>
</div>
</aside>
<div class="main">
<div class="topbar"><h1><?= $title ?? 'Dashboard' ?></h1><div class="topbar-actions"></div></div>
<div class="content">
<?php if(session()->getFlashdata('success')): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?></div>
<?php endif ?>
<?php if(session()->getFlashdata('error')): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?></div>
<?php endif ?>