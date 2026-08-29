<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cetak Kartu Meja - <?= esc($exam['nama_ujian']) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'Inter', sans-serif;
    background: #f1f5f9;
    color: #0f172a;
    padding: 20px;
}

/* Toolbar */
.no-print-toolbar {
    max-width: 960px;
    margin: 0 auto 20px auto;
    background: #1e293b;
    color: #fff;
    padding: 14px 20px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
}
.toolbar-info h2 { font-size: 15px; font-weight: 700; }
.toolbar-info p { font-size: 12px; color: #94a3b8; margin-top: 2px; }
.toolbar-actions { display: flex; gap: 10px; }
.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    text-decoration: none;
    transition: 0.15s;
}
.btn-primary { background: #f59e0b; color: #000; }
.btn-primary:hover { background: #d97706; }
.btn-outline { background: transparent; border: 1px solid #475569; color: #cbd5e1; }
.btn-outline:hover { background: #334155; }

/* Print Page Container */
.page-container {
    max-width: 960px;
    margin: 0 auto;
}

/* Card Grid Layout */
.card-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
}

.meja-item {
    background: #fff;
    border: 1.5px solid #0f172a;
    border-radius: 8px;
    padding: 12px 14px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    position: relative;
    page-break-inside: avoid;
    break-inside: avoid;
}

.meja-header {
    border-bottom: 2px solid #0f172a;
    padding-bottom: 6px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
    padding-right: 80px; /* space for table badge */
}
.meja-logo {
    width: 32px;
    height: 32px;
    object-fit: contain;
    flex-shrink: 0;
}
.meja-header-text h3 {
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    color: #0f172a;
    line-height: 1.2;
}
.meja-header-text p {
    font-size: 10px;
    color: #475569;
    margin-top: 1px;
}

.nomor-meja-badge {
    position: absolute;
    top: 10px;
    right: 12px;
    background: #0f172a;
    color: #fff;
    padding: 5px 12px;
    border-radius: 6px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 16px;
    font-weight: 900;
}

.info-row {
    display: flex;
    font-size: 11.5px;
    margin-bottom: 4px;
    line-height: 1.35;
}
.info-label {
    width: 85px;
    color: #475569;
    font-weight: 500;
    flex-shrink: 0;
}
.info-separator {
    margin-right: 6px;
    color: #64748b;
}
.info-value {
    font-weight: 700;
    color: #0f172a;
}
.password-box {
    font-family: 'JetBrains Mono', monospace;
    background: #f1f5f9;
    padding: 1px 6px;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    color: #0f172a;
    letter-spacing: 1px;
    font-size: 11px;
}

.meja-footer {
    border-top: 1px dashed #cbd5e1;
    margin-top: 8px;
    padding-top: 5px;
    display: flex;
    justify-content: space-between;
    font-size: 9.5px;
    color: #64748b;
}

/* Print Stylesheet */
@media print {
    body {
        background: #fff;
        padding: 0;
        margin: 0;
    }
    .no-print-toolbar {
        display: none !important;
    }
    .page-container {
        max-width: 100%;
        margin: 0;
    }
    .card-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    .meja-item {
        box-shadow: none;
        border: 1.5px solid #000;
        page-break-inside: avoid;
        break-inside: avoid;
    }
    @page {
        size: A4;
        margin: 8mm;
    }
}
</style>
</head>
<body>

<?php
$namaSekolah = $settings['nama_sekolah'] ?? 'SEKOLAH / MADRASAH';
$logoUrl = (!empty($settings['logo']) && file_exists(FCPATH . $settings['logo'])) ? base_url($settings['logo']) : '';
?>

<div class="no-print-toolbar">
    <div class="toolbar-info">
        <h2><i class="fas fa-tags" style="color:#f59e0b;margin-right:6px"></i> Kartu Meja: <?= esc($exam['nama_ujian']) ?> (<?= esc($namaSekolah) ?>)</h2>
        <p>Total: <?= count($participants) ?> Siswa &bull; Format 6 Kartu per A4</p>
    </div>
    <div class="toolbar-actions">
        <a href="<?= base_url('setting') ?>" class="btn btn-outline" title="Ubah Nama Sekolah atau Logo">
            <i class="fas fa-cog"></i> Atur Kop
        </a>
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print"></i> Cetak Kartu Meja (Ctrl+P)
        </button>
        <a href="<?= base_url('participant/' . $exam['id']) ?>" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="page-container">
    <?php if(empty($participants)): ?>
        <div style="background:#fff;border-radius:12px;padding:40px;text-align:center;color:#64748b">
            <i class="fas fa-tags" style="font-size:40px;opacity:0.4;margin-bottom:12px;display:block"></i>
            <h3>Belum Ada Data Peserta</h3>
            <p style="margin-top:6px;font-size:13px">Silakan impor peserta terlebih dahulu.</p>
        </div>
    <?php else: ?>
        <div class="card-grid">
            <?php foreach($participants as $p): ?>
            <?php 
                $roomName = 'Belum Diset';
                if (!empty($p['room_id']) && !empty($rooms)) {
                    foreach($rooms as $r) {
                        if ($r['id'] == $p['room_id']) { $roomName = $r['nama_ruang']; break; }
                    }
                }
            ?>
            <div class="meja-item">
                <div class="meja-header">
                    <?php if($logoUrl): ?>
                        <img src="<?= $logoUrl ?>" class="meja-logo" alt="Logo">
                    <?php endif; ?>
                    <div class="meja-header-text">
                        <h3><?= esc($namaSekolah) ?></h3>
                        <p>KARTU MEJA CBT &bull; <?= esc($exam['nama_ujian']) ?></p>
                    </div>
                </div>

                <div class="nomor-meja-badge">
                    #<?= $p['nomor_meja'] ?: '-' ?>
                </div>

                <div style="padding-top:2px">
                    <div class="info-row">
                        <span class="info-label">No. Peserta</span>
                        <span class="info-separator">:</span>
                        <span class="info-value" style="color:#4338ca"><?= esc($p['nomor_peserta']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nama Siswa</span>
                        <span class="info-separator">:</span>
                        <span class="info-value"><?= esc($p['nama']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">NIS / NISN</span>
                        <span class="info-separator">:</span>
                        <span class="info-value" style="font-weight:normal"><?= esc($p['nis'] ?: '-') ?> <?= !empty($p['nisn']) ? '/ ' . esc($p['nisn']) : '' ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Kelas</span>
                        <span class="info-separator">:</span>
                        <span class="info-value"><?= esc($p['kelas']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Ruang / Sesi</span>
                        <span class="info-separator">:</span>
                        <span class="info-value"><?= esc($roomName) ?> / Sesi <?= $p['sesi'] ?: 1 ?></span>
                    </div>
                    <?php if(!empty($p['password'])): ?>
                    <div class="info-row" style="margin-top:3px">
                        <span class="info-label">Password CBT</span>
                        <span class="info-separator">:</span>
                        <span class="info-value"><span class="password-box"><?= esc($p['password']) ?></span></span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="meja-footer">
                    <span>Tempelkan pada sudut kanan atas meja.</span>
                    <span>TP. <?= esc($exam['tahun']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
