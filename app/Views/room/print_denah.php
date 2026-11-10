<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Denah Tempat Duduk - <?= esc($room['nama_ruang']) ?> (Sesi <?= $sesi ?>)</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'Inter', sans-serif;
    background: #0f172a;
    color: #0f172a;
    padding: 20px;
    font-size: 12px;
}

/* Toolbar navigasi non-print */
.no-print-toolbar {
    max-width: 1050px;
    margin: 0 auto 20px auto;
    background: #1e293b;
    border: 1px solid #334155;
    color: #fff;
    padding: 12px 20px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.4);
}
.toolbar-title h2 { font-size: 15px; font-weight: 700; color: #fff; }
.toolbar-title p { font-size: 12px; color: #94a3b8; margin-top: 2px; }
.toolbar-controls { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.toolbar-controls select {
    background: #334155;
    border: 1px solid #475569;
    color: #fff;
    padding: 7px 12px;
    border-radius: 8px;
    font-size: 12.5px;
    outline: none;
    cursor: pointer;
}
.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    text-decoration: none;
    transition: 0.15s;
}
.btn-primary { background: #6366f1; color: #fff; }
.btn-primary:hover { background: #4f46e5; }
.btn-success { background: #10b981; color: #fff; }
.btn-success:hover { background: #059669; }
.btn-outline { background: transparent; border: 1px solid #475569; color: #cbd5e1; }
.btn-outline:hover { background: #334155; }

/* Kertas Cetak Lembar Denah */
.sheet {
    background: #ffffff;
    max-width: 1050px;
    margin: 0 auto;
    padding: 25px 30px;
    border-radius: 10px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.5);
    color: #0f172a;
}

/* Kop Surat */
.kop {
    display: flex;
    align-items: center;
    gap: 18px;
    border-bottom: 2.5px solid #000;
    padding-bottom: 12px;
    margin-bottom: 16px;
}
.kop-logo {
    width: 65px;
    height: 65px;
    object-fit: contain;
}
.kop-text { flex: 1; text-align: center; }
.kop-text h1 { font-size: 16px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #000; margin-bottom: 2px; }
.kop-text h2 { font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 3px; }
.kop-text p { font-size: 11px; color: #475569; line-height: 1.35; }

/* Judul Denah */
.denah-header {
    text-align: center;
    margin-bottom: 18px;
}
.denah-header h3 {
    font-size: 15px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #0f172a;
    text-decoration: underline;
    margin-bottom: 4px;
}
.denah-header p {
    font-size: 12px;
    font-weight: 600;
    color: #334155;
}

/* Info Bar Ruang */
.info-bar {
    display: flex;
    justify-content: space-between;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 6px 14px;
    font-size: 11.5px;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 16px;
}

/* Bagian Depan Kelas */
.front-area {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 20px;
    padding: 0 10px;
}
.door-box {
    border: 2px dashed #94a3b8;
    background: #f1f5f9;
    border-radius: 6px;
    padding: 6px 14px;
    font-weight: 800;
    font-size: 10.5px;
    letter-spacing: 0.05em;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 6px;
}
.blackboard-box {
    flex: 1;
    max-width: 380px;
    background: #0f172a;
    color: #fff;
    text-align: center;
    padding: 6px 12px;
    border-radius: 4px;
    font-weight: 700;
    font-size: 11px;
    letter-spacing: 0.15em;
    text-transform: uppercase;
}
.supervisor-table {
    border: 2px solid #0f172a;
    background: #f8fafc;
    border-radius: 6px;
    padding: 8px 18px;
    text-align: center;
    font-weight: 800;
    font-size: 11px;
    letter-spacing: 0.05em;
    color: #0f172a;
    box-shadow: 2px 2px 0px rgba(0,0,0,0.1);
}

/* Grid Meja Peserta (4 Kolom Standard) */
.desks-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 24px;
}
.desk-card {
    border: 1.5px solid #0f172a;
    border-radius: 8px;
    padding: 8px 10px;
    background: #ffffff;
    display: flex;
    flex-direction: column;
    min-height: 82px;
    box-shadow: 2px 2px 0px rgba(0,0,0,0.06);
    position: relative;
}
.desk-card.empty {
    border: 1.5px dashed #cbd5e1;
    background: #f8fafc;
    color: #94a3b8;
    align-items: center;
    justify-content: center;
}
.desk-badge {
    position: absolute;
    top: 6px;
    right: 8px;
    background: #0f172a;
    color: #fff;
    font-family: 'JetBrains Mono', monospace;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
}
.desk-num {
    font-size: 11px;
    font-weight: 800;
    color: #4338ca;
    text-transform: uppercase;
    margin-bottom: 3px;
}
.desk-name {
    font-size: 11.5px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.25;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 2px;
}
.desk-nopes {
    font-family: 'JetBrains Mono', monospace;
    font-size: 10px;
    color: #334155;
    font-weight: 600;
}
.desk-class {
    font-size: 10px;
    color: #64748b;
    margin-top: auto;
    font-weight: 500;
}

/* Tanda Tangan */
.signature-section {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
    page-break-inside: avoid;
}
.sig-box {
    width: 250px;
    text-align: center;
    font-size: 11.5px;
    line-height: 1.4;
}
.sig-space {
    height: 55px;
}
.sig-name {
    font-weight: 700;
    text-decoration: underline;
}

/* Print CSS */
@media print {
    body {
        background: #fff;
        padding: 0;
    }
    .no-print-toolbar {
        display: none !important;
    }
    .sheet {
        box-shadow: none;
        padding: 0;
        max-width: 100%;
        border-radius: 0;
    }
    @page {
        size: A4 landscape;
        margin: 10mm 12mm 10mm 12mm;
    }
}
</style>
</head>
<body>

<!-- Toolbar Navigasi -->
<div class="no-print-toolbar">
    <div class="toolbar-title">
        <h2><i class="fas fa-th-large" style="color:#6366f1;margin-right:6px"></i> Denah Tempat Duduk: <?= esc($room['nama_ruang']) ?></h2>
        <p><?= esc($exam['nama_ujian']) ?> &bull; Sesi <?= $sesi ?> &bull; Total <?= count($participants) ?> Peserta Terdaftar</p>
    </div>
    <div class="toolbar-controls">
        <!-- Switch Ruangan -->
        <select onchange="location.href='<?= base_url('room/denah/'.$exam['id']) ?>/' + this.value + '?sesi=<?= $sesi ?>'">
            <?php foreach($rooms as $r): ?>
                <option value="<?= $r['id'] ?>" <?= $r['id'] == $room['id'] ? 'selected' : '' ?>>
                    Ruang: <?= esc($r['nama_ruang']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Switch Sesi -->
        <select onchange="location.href='<?= base_url('room/denah/'.$exam['id'].'/'.$room['id']) ?>?sesi=' + this.value">
            <?php foreach($availableSessions as $s): ?>
                <option value="<?= $s ?>" <?= $s == $sesi ? 'selected' : '' ?>>
                    Sesi <?= $s ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Cetak Denah
        </button>
        <a href="<?= base_url('room/denah/'.$exam['id'].'/'.$room['id'].'?sesi='.$sesi.'&export=pdf') ?>" class="btn btn-success">
            <i class="fas fa-file-pdf"></i> Download PDF
        </a>
        <a href="<?= base_url('exam') ?>" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<!-- Lembar Kertas Denah -->
<div class="sheet">
    <!-- Kop Surat -->
    <div class="kop">
        <?php if(!empty($settings['logo']) && file_exists(FCPATH . $settings['logo'])): ?>
            <img src="<?= base_url($settings['logo']) ?>" class="kop-logo" alt="Logo">
        <?php else: ?>
            <div style="width:65px;height:65px;border-radius:10px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:24px;color:#6366f1">
                <i class="fas fa-school"></i>
            </div>
        <?php endif; ?>
        <div class="kop-text">
            <h1><?= esc($settings['nama_sekolah'] ?? 'PANITIA UJIAN BERBASIS KOMPUTER') ?></h1>
            <h2>DENAH TEMPAT DUDUK PESERTA UJIAN (CBT)</h2>
            <p>
                <?= esc($settings['alamat_sekolah'] ?? 'Alamat Sekolah / Madrasah') ?>
                <?php if(!empty($settings['telepon'])): ?> &bull; Telp: <?= esc($settings['telepon']) ?><?php endif; ?>
                <?php if(!empty($settings['email'])): ?> &bull; Email: <?= esc($settings['email']) ?><?php endif; ?>
            </p>
        </div>
    </div>

    <!-- Header Denah -->
    <div class="denah-header">
        <h3>TATA LETAK TEMPAT DUDUK PESERTA RUANG UJIAN</h3>
        <p><?= esc($exam['nama_ujian']) ?> &mdash; TAHUN AJARAN <?= esc($exam['tahun']) ?></p>
    </div>

    <!-- Info Bar -->
    <div class="info-bar">
        <span><strong>RUANG:</strong> <?= strtoupper(esc($room['nama_ruang'])) ?> (<?= esc($room['gedung'] ?? 'Gedung Utama') ?> / Lt. <?= esc($room['lantai'] ?? 1) ?>)</span>
        <span><strong>SESI:</strong> <?= $sesi ?></span>
        <span><strong>KAPASITAS:</strong> <?= esc($room['kapasitas']) ?> Kursi</span>
        <span><strong>TERISI:</strong> <?= count($participants) ?> Siswa</span>
        <span><strong>TANGGAL:</strong> <?= date('d M Y') ?></span>
    </div>

    <!-- Area Depan: Meja Pengawas, Papan Tulis, Pintu -->
    <div class="front-area">
        <div class="door-box">
            <i class="fas fa-door-open"></i> PINTU MASUK
        </div>
        <div class="blackboard-box">
            [ PAPAN TULIS / LAYAR DEPAN ]
        </div>
        <div class="supervisor-table">
            <i class="fas fa-user-shield" style="margin-right:4px"></i> MEJA PENGAWAS &amp; PROKTOR
        </div>
    </div>

    <!-- Grid Meja Siswa (4 Kolom) -->
    <?php
    $kapasitas = max((int)$room['kapasitas'], count($participants));
    $desksByMeja = [];
    foreach($participants as $p) {
        $noMeja = (int)$p['nomor_meja'];
        if ($noMeja > 0) {
            $desksByMeja[$noMeja] = $p;
        }
    }
    ?>

    <div class="desks-grid">
        <?php for($m = 1; $m <= $kapasitas; $m++): ?>
            <?php if(isset($desksByMeja[$m])): ?>
                <?php $p = $desksByMeja[$m]; ?>
                <div class="desk-card">
                    <span class="desk-badge">M-<?= sprintf('%02d', $m) ?></span>
                    <div class="desk-num">MEJA <?= sprintf('%02d', $m) ?></div>
                    <div class="desk-name" title="<?= esc($p['nama']) ?>"><?= esc($p['nama']) ?></div>
                    <div class="desk-nopes"><?= esc($p['nomor_peserta']) ?></div>
                    <div class="desk-class">Kelas: <?= esc($p['kelas']) ?> &bull; NIS: <?= esc($p['nis'] ?: '-') ?></div>
                </div>
            <?php else: ?>
                <div class="desk-card empty">
                    <span class="desk-badge" style="background:#cbd5e1;color:#64748b">M-<?= sprintf('%02d', $m) ?></span>
                    <span style="font-weight:700;font-size:11px;color:#94a3b8">MEJA <?= sprintf('%02d', $m) ?></span>
                    <small style="font-size:10px;color:#cbd5e1">[ KOSONG ]</small>
                </div>
            <?php endif; ?>
        <?php endfor; ?>
    </div>

    <!-- Bagian Tanda Tangan -->
    <div class="signature-section">
        <div class="sig-box">
            <p>Pengawas Ruang Ujian,</p>
            <div class="sig-space"></div>
            <p class="sig-name">( .................................................... )</p>
            <p>NIP. .................................................</p>
        </div>
        <div class="sig-box">
            <p><?= esc($settings['kota'] ?? 'Kota') ?>, <?= date('d F Y') ?></p>
            <p>Ketua Panitia Ujian,</p>
            <div class="sig-space"></div>
            <p class="sig-name"><?= esc($settings['nama_ketua_panitia'] ?? '( .................................................... )') ?></p>
            <p>NIP. <?= esc($settings['nip_ketua_panitia'] ?? '.................................................') ?></p>
        </div>
    </div>
</div>

</body>
</html>
