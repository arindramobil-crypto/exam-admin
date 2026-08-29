<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cetak Daftar Hadir - <?= esc($exam['nama_ujian']) ?></title>
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
    max-width: 900px;
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
.btn-success { background: #10b981; color: #fff; }
.btn-success:hover { background: #059669; }
.btn-outline { background: transparent; border: 1px solid #475569; color: #cbd5e1; }
.btn-outline:hover { background: #334155; }

/* Printable Sheet */
.sheet-container {
    max-width: 900px;
    margin: 0 auto;
    background: #fff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 30px 35px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

/* Kop Surat */
.kop-header {
    display: flex;
    align-items: center;
    gap: 16px;
    border-bottom: 2.5px double #0f172a;
    padding-bottom: 12px;
    margin-bottom: 16px;
}
.kop-logo {
    width: 60px;
    height: 60px;
    object-fit: contain;
}
.kop-text {
    flex: 1;
    text-align: center;
}
.kop-text h2 {
    font-size: 15px;
    font-weight: 800;
    text-transform: uppercase;
    color: #0f172a;
    letter-spacing: 0.02em;
}
.kop-text h3 {
    font-size: 12.5px;
    font-weight: 700;
    text-transform: uppercase;
    color: #4338ca;
    margin-top: 2px;
}
.kop-text p {
    font-size: 11px;
    color: #475569;
    margin-top: 2px;
}

.doc-title {
    text-align: center;
    font-size: 14px;
    font-weight: 800;
    text-transform: uppercase;
    text-decoration: underline;
    margin-bottom: 16px;
    letter-spacing: 0.03em;
}

/* Meta Table */
.meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px 24px;
    margin-bottom: 16px;
    font-size: 12px;
}
.meta-row {
    display: flex;
}
.meta-label {
    width: 120px;
    color: #475569;
    font-weight: 500;
}
.meta-val {
    font-weight: 700;
    color: #0f172a;
}

/* Table */
.table-data {
    width: 100%;
    border-collapse: collapse;
    font-size: 11.5px;
    margin-bottom: 24px;
}
.table-data th, .table-data td {
    border: 1px solid #334155;
    padding: 6px 8px;
}
.table-data th {
    background: #f1f5f9;
    color: #0f172a;
    font-weight: 700;
    text-align: center;
}

/* TTD */
.ttd-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-top: 20px;
    font-size: 12px;
    text-align: center;
}
.ttd-box {
    line-height: 1.4;
}
.ttd-space {
    height: 55px;
}
.ttd-name {
    font-weight: 700;
    text-decoration: underline;
    color: #0f172a;
}

/* Print */
@media print {
    body {
        background: #fff;
        padding: 0;
        margin: 0;
    }
    .no-print-toolbar {
        display: none !important;
    }
    .sheet-container {
        max-width: 100%;
        margin: 0;
        padding: 0;
        border: none;
        box-shadow: none;
    }
    .table-data th {
        background: #e2e8f0 !important;
        -webkit-print-color-adjust: exact;
    }
    @page {
        size: A4;
        margin: 12mm 12mm 12mm 12mm;
    }
}
</style>
</head>
<body>

<?php
$namaSekolah = $settings['nama_sekolah'] ?? 'SEKOLAH / MADRASAH';
$alamatSekolah = $settings['alamat_sekolah'] ?? '';
$kota = $settings['kota'] ?? 'Jakarta';
$logoUrl = (!empty($settings['logo']) && file_exists(FCPATH . $settings['logo'])) ? base_url($settings['logo']) : '';

$roomName = $room ? $room['nama_ruang'] : 'Semua Ruang';
$sesiName = $selectedSesi ? "Sesi {$selectedSesi}" : 'Semua Sesi';
$subjectName = $subject ? $subject['nama_mapel'] . " (Kelas " . $subject['kelas'] . ")" : 'Semua Mata Pelajaran';
?>

<div class="no-print-toolbar">
    <div class="toolbar-info">
        <h2><i class="fas fa-check-square" style="color:#818cf8;margin-right:6px"></i> Daftar Hadir: <?= esc($roomName) ?> - <?= esc($sesiName) ?></h2>
        <p><?= esc($exam['nama_ujian']) ?> &bull; Total: <?= count($participants) ?> Peserta</p>
    </div>
    <div class="toolbar-actions">
        <?php
            $pdfUrlParams = http_build_query([
                'subject_id' => $selectedSubjectId ?? 0,
                'room_id'    => $selectedRoomId ?? 0,
                'sesi'       => $selectedSesi ?? 0,
                'export'     => 'pdf',
            ]);
        ?>
        <a href="<?= base_url('attendance/print/' . $exam['id'] . '?' . $pdfUrlParams) ?>" target="_blank" class="btn btn-success">
            <i class="fas fa-file-pdf"></i> Download PDF
        </a>
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print"></i> Cetak Daftar Hadir (Ctrl+P)
        </button>
        <a href="<?= base_url('attendance/' . $exam['id']) ?>" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="sheet-container">
    <!-- Kop Surat -->
    <div class="kop-header">
        <?php if($logoUrl): ?>
            <img src="<?= $logoUrl ?>" class="kop-logo" alt="Logo">
        <?php endif; ?>
        <div class="kop-text">
            <h2><?= esc($namaSekolah) ?></h2>
            <h3>PANITIA PENILAIAN / UJIAN BERBASIS KOMPUTER (CBT)</h3>
            <?php if($alamatSekolah): ?>
                <p><?= esc($alamatSekolah) ?><?= !empty($settings['telepon']) ? ' Telp. ' . esc($settings['telepon']) : '' ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="doc-title">DAFTAR HADIR PESERTA UJIAN CBT</div>

    <!-- Meta Info -->
    <div class="meta-grid">
        <div>
            <div class="meta-row">
                <span class="meta-label">Nama Ujian</span>
                <span style="margin-right:8px">:</span>
                <span class="meta-val"><?= esc($exam['nama_ujian']) ?></span>
            </div>
            <div class="meta-row" style="margin-top:4px">
                <span class="meta-label">Mata Pelajaran</span>
                <span style="margin-right:8px">:</span>
                <span class="meta-val"><?= esc($subjectName) ?></span>
            </div>
            <div class="meta-row" style="margin-top:4px">
                <span class="meta-label">Tahun Pelajaran</span>
                <span style="margin-right:8px">:</span>
                <span class="meta-val"><?= esc($exam['tahun']) ?> (Sem. <?= esc($exam['semester']) ?>)</span>
            </div>
        </div>
        <div>
            <div class="meta-row">
                <span class="meta-label">Ruang Ujian</span>
                <span style="margin-right:8px">:</span>
                <span class="meta-val" style="color:#4338ca"><?= esc($roomName) ?></span>
            </div>
            <div class="meta-row" style="margin-top:4px">
                <span class="meta-label">Sesi Ujian</span>
                <span style="margin-right:8px">:</span>
                <span class="meta-val" style="color:#059669"><?= esc($sesiName) ?></span>
            </div>
            <div class="meta-row" style="margin-top:4px">
                <span class="meta-label">Hari / Tanggal</span>
                <span style="margin-right:8px">:</span>
                <span class="meta-val"><?= date('d F Y') ?></span>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Hadir -->
    <table class="table-data">
        <thead>
            <tr>
                <th style="width:35px">No</th>
                <th style="width:110px">No. Peserta</th>
                <th>Nama Lengkap Siswa</th>
                <th style="width:65px">Kelas</th>
                <th style="width:75px">Ruang</th>
                <th style="width:50px">Meja</th>
                <th style="width:60px">Sesi</th>
                <th style="width:140px">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; foreach($participants as $p): ?>
            <tr>
                <td style="text-align:center"><?= $i ?></td>
                <td style="text-align:center;font-family:'JetBrains Mono',monospace;font-weight:700"><?= esc($p['nomor_peserta']) ?></td>
                <td><strong><?= esc($p['nama']) ?></strong></td>
                <td style="text-align:center"><?= esc($p['kelas']) ?></td>
                <td style="text-align:center"><?= esc($roomsMap[$p['room_id']] ?? '-') ?></td>
                <td style="text-align:center;font-weight:700">#<?= esc($p['nomor_meja'] ?: '-') ?></td>
                <td style="text-align:center">Sesi <?= $p['sesi'] ?: 1 ?></td>
                <td style="height:28px;vertical-align:middle">
                    <?= $i % 2 == 1 ? $i . ". ............" : "<div style='text-align:right'>" . $i . ". ............</div>" ?>
                </td>
            </tr>
            <?php $i++; endforeach; ?>
            <?php if(empty($participants)): ?>
            <tr>
                <td colspan="8" style="text-align:center;padding:30px;color:#64748b">
                    Tidak ada data peserta untuk kriteria Ruang & Sesi ini.
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <div class="ttd-grid">
        <div class="ttd-box">
            <div>Mengetahui,</div>
            <div>Ketua Panitia Ujian,</div>
            <div class="ttd-space"></div>
            <div class="ttd-name"><?= esc($settings['nama_ketua_panitia'] ?? '...................................') ?></div>
            <?php if(!empty($settings['nip_ketua_panitia'])): ?>
                <div style="font-size:11px;color:#475569">NIP. <?= esc($settings['nip_ketua_panitia']) ?></div>
            <?php endif; ?>
        </div>
        <div class="ttd-box">
            <div><?= esc($kota) ?>, <?= date('d F Y') ?></div>
            <div>Pengawas Ruang,</div>
            <div class="ttd-space"></div>
            <div class="ttd-name">( ........................................... )</div>
            <div style="font-size:11px;color:#475569">NIP. .....................................</div>
        </div>
    </div>
</div>

</body>
</html>
