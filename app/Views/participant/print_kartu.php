<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cetak Kartu Peserta - <?= esc($exam['nama_ujian']) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">
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
.btn-primary { background: #6366f1; color: #fff; }
.btn-primary:hover { background: #4f46e5; }
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
    gap: 16px;
}

.kartu-item {
    background: #fff;
    border: 1.5px solid #1e293b;
    border-radius: 8px;
    padding: 12px 14px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    position: relative;
    page-break-inside: avoid;
    break-inside: avoid;
}

/* Header Kop Kartu */
.kop-container {
    display: flex;
    align-items: center;
    gap: 10px;
    border-bottom: 2px double #0f172a;
    padding-bottom: 6px;
    margin-bottom: 10px;
}
.kop-logo {
    width: 44px;
    height: 44px;
    object-fit: contain;
    flex-shrink: 0;
}
.kop-text {
    flex: 1;
    text-align: center;
}
.kop-text h2 {
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    color: #0f172a;
    letter-spacing: 0.02em;
    line-height: 1.2;
}
.kop-text h3 {
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    color: #4338ca;
    margin-top: 1px;
}
.kop-text p {
    font-size: 10px;
    color: #475569;
    margin-top: 1px;
    font-weight: 600;
}

.kartu-body {
    display: flex;
    gap: 12px;
    align-items: flex-start;
}

.student-info {
    flex: 1;
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
    word-break: break-word;
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

.photo-box {
    width: 65px;
    height: 85px;
    border: 1px dashed #94a3b8;
    background: #f8fafc;
    border-radius: 4px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-size: 9px;
    color: #94a3b8;
    flex-shrink: 0;
    text-align: center;
}
.photo-box i {
    font-size: 20px;
    margin-bottom: 2px;
    color: #cbd5e1;
}

.kartu-footer {
    border-top: 1px dashed #cbd5e1;
    margin-top: 8px;
    padding-top: 6px;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    font-size: 9.5px;
    color: #64748b;
}
.signature-box {
    text-align: center;
    width: 150px;
    line-height: 1.25;
}
.signature-space {
    height: 28px;
}
.signature-name {
    font-weight: 700;
    color: #0f172a;
    text-decoration: underline;
}
.signature-nip {
    font-size: 8.5px;
    color: #475569;
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
    .kartu-item {
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
$kota = $settings['kota'] ?? '';
$isKepsek = ($settings['ttd_kartu_jabatan'] ?? '') === 'Kepala Sekolah';
$jabatanTtd = $isKepsek ? 'Kepala Sekolah' : 'Ketua Panitia Ujian';
$namaPejabat = $isKepsek ? ($settings['nama_kepala_sekolah'] ?? '...................................') : ($settings['nama_ketua_panitia'] ?? '...................................');
$nipPejabat = $isKepsek ? ($settings['nip_kepala_sekolah'] ?? '') : ($settings['nip_ketua_panitia'] ?? '');
?>

<div class="no-print-toolbar">
    <div class="toolbar-info">
        <h2><i class="fas fa-id-card" style="color:#818cf8;margin-right:6px"></i> Kartu Peserta: <?= esc($exam['nama_ujian']) ?> (<?= esc($namaSekolah) ?>)</h2>
        <p>Total: <?= count($participants) ?> Peserta &bull; TTD: <?= esc($jabatanTtd) ?> (<?= esc($namaPejabat) ?>)</p>
    </div>
    <div class="toolbar-actions">
        <a href="<?= base_url('setting') ?>" class="btn btn-outline" title="Ubah Nama Sekolah, Logo, atau TTD">
            <i class="fas fa-cog"></i> Atur Kop & TTD
        </a>
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print"></i> Cetak Kartu (Ctrl+P)
        </button>
        <a href="<?= base_url('participant/' . $exam['id']) ?>" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="page-container">
    <?php if(empty($participants)): ?>
        <div style="background:#fff;border-radius:12px;padding:40px;text-align:center;color:#64748b">
            <i class="fas fa-users-slash" style="font-size:40px;opacity:0.4;margin-bottom:12px;display:block"></i>
            <h3>Belum Ada Data Peserta</h3>
            <p style="margin-top:6px;font-size:13px">Silakan impor peserta dari Moodle atau Excel terlebih dahulu di halaman Kelola Peserta.</p>
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
            <div class="kartu-item">
                <div class="kop-container">
                    <?php if($logoUrl): ?>
                        <img src="<?= $logoUrl ?>" class="kop-logo" alt="Logo">
                    <?php endif; ?>
                    <div class="kop-text">
                        <h2><?= esc($namaSekolah) ?></h2>
                        <h3>KARTU PESERTA UJIAN CBT</h3>
                        <p><?= esc($exam['nama_ujian']) ?> &bull; TP. <?= esc($exam['tahun']) ?></p>
                    </div>
                </div>

                <div class="kartu-body">
                    <div class="student-info">
                        <div class="info-row">
                            <span class="info-label">No. Peserta</span>
                            <span class="info-separator">:</span>
                            <span class="info-value" style="font-size:12.5px;color:#4338ca"><?= esc($p['nomor_peserta']) ?></span>
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
                            <span class="info-label">Ruang / Meja</span>
                            <span class="info-separator">:</span>
                            <span class="info-value"><?= esc($roomName) ?> / Meja #<?= $p['nomor_meja'] ?: '-' ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Sesi Ujian</span>
                            <span class="info-separator">:</span>
                            <span class="info-value">Sesi <?= $p['sesi'] ?: 1 ?></span>
                        </div>
                        <?php if(!empty($p['password'])): ?>
                        <div class="info-row" style="margin-top:3px">
                            <span class="info-label">Password CBT</span>
                            <span class="info-separator">:</span>
                            <span class="info-value"><span class="password-box"><?= esc($p['password']) ?></span></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="photo-box">
                        <i class="fas fa-user"></i>
                        <span>FOTO<br>2 x 3</span>
                    </div>
                </div>

                <div class="kartu-footer">
                    <span>* Harap dibawa saat ujian</span>
                    <div class="signature-box">
                        <div><?= !empty($kota) ? esc($kota) . ', ' : '' ?><?= esc($jabatanTtd) ?></div>
                        <div class="signature-space"></div>
                        <div class="signature-name"><?= esc($namaPejabat) ?></div>
                        <?php if(!empty($nipPejabat)): ?>
                            <div class="signature-nip">NIP. <?= esc($nipPejabat) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
