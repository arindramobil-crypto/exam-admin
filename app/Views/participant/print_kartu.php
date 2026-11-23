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
    padding: 16px 20px;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
}
.toolbar-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid #334155;
}
.toolbar-info h2 { font-size: 15px; font-weight: 700; }
.toolbar-info p { font-size: 12px; color: #94a3b8; margin-top: 3px; }
.toolbar-actions { display: flex; gap: 10px; }
.toolbar-filter {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 12px;
}
.filter-group {
    display: flex;
    align-items: center;
    gap: 6px;
}
.filter-label {
    font-size: 11.5px;
    color: #94a3b8;
    font-weight: 600;
}
.filter-select {
    background: #0f172a;
    color: #fff;
    border: 1px solid #475569;
    border-radius: 6px;
    padding: 6px 10px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    outline: none;
    transition: 0.15s;
}
.filter-select:focus {
    border-color: #6366f1;
}
.filter-check {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #cbd5e1;
    cursor: pointer;
    user-select: none;
    background: #0f172a;
    padding: 5px 10px;
    border-radius: 6px;
    border: 1px solid #475569;
}
.filter-check input {
    cursor: pointer;
}
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

.class-divider {
    background: #e2e8f0;
    color: #1e293b;
    padding: 8px 14px;
    border-radius: 6px;
    font-size: 12.5px;
    font-weight: 800;
    margin: 18px 0 10px 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-left: 4px solid #6366f1;
}

/* Card Grid Layout */
.card-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.kartu-item {
    background: #fff;
    border: 1.5px solid #0f172a;
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
    margin-top: 10px;
    padding-top: 8px;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    font-size: 9.5px;
    color: #64748b;
}
.kartu-footer-note {
    font-size: 8.5px;
    color: #94a3b8;
    line-height: 1.4;
    font-style: italic;
}
.signature-box {
    text-align: center;
    min-width: 190px;
    max-width: 240px;
    line-height: 1.25;
    flex-shrink: 0;
}
.signature-date {
    font-size: 9.5px;
    color: #0f172a;
    white-space: nowrap;
}
.signature-title {
    font-size: 9.5px;
    color: #0f172a;
    font-weight: 600;
    white-space: nowrap;
    margin-top: 1px;
}
.signature-space {
    height: 30px;
}
.signature-name {
    font-size: 9.5px;
    font-weight: 700;
    color: #0f172a;
    text-decoration: underline;
    white-space: nowrap;
}
.signature-nip {
    font-size: 8.5px;
    color: #475569;
    white-space: nowrap;
    margin-top: 1px;
}

/* Print Stylesheet */
@media print {
    body {
        background: #fff;
        padding: 0;
        margin: 0;
    }
    .no-print-toolbar, .no-print {
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
    .class-print-group {
        page-break-after: always;
        break-after: page;
        margin-bottom: 0;
    }
    .class-print-group:last-child {
        page-break-after: auto;
        break-after: auto;
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
$kota = !empty($settings['kota']) ? $settings['kota'] : 'Mojokerto';
$isKepsek = ($settings['ttd_kartu_jabatan'] ?? '') === 'Kepala Sekolah';
$jabatanTtd = $isKepsek ? 'Kepala Sekolah' : 'Ketua Panitia Ujian';
$namaPejabat = $isKepsek ? ($settings['nama_kepala_sekolah'] ?? '...................................') : ($settings['nama_ketua_panitia'] ?? '...................................');
$nipPejabat = $isKepsek ? ($settings['nip_kepala_sekolah'] ?? '') : ($settings['nip_ketua_panitia'] ?? '');

// Format tanggal tanda tangan: "Mojokerto,    Nopember 2026"
$bulanIndo = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'Nopember', 12 => 'Desember'
];
$bulanTtd = $bulanIndo[(int)date('n')] ?? 'Nopember';
$tahunTtd = !empty($exam['tahun']) ? $exam['tahun'] : date('Y');
$kotaTanggal = esc($kota) . ', &nbsp;&nbsp;&nbsp;&nbsp;' . $bulanTtd . ' ' . $tahunTtd;

$roomsMap = [];
if (!empty($rooms)) {
    foreach ($rooms as $r) {
        $roomsMap[$r['id']] = $r['nama_ruang'];
    }
}
?>

<div class="no-print-toolbar">
    <div class="toolbar-top">
        <div class="toolbar-info">
            <h2><i class="fas fa-id-card" style="color:#818cf8;margin-right:6px"></i> Kartu Peserta: <?= esc($exam['nama_ujian']) ?> (<?= esc($namaSekolah) ?>)</h2>
            <p>
                Menampilkan: <strong><?= count($participants) ?> Peserta</strong>
                <?= !empty($filterKelas) ? ' &bull; Kelas: <span style="color:#a5b4fc;font-weight:700">' . esc($filterKelas) . '</span>' : '' ?>
                <?= (!empty($filterRoom) && isset($roomsMap[$filterRoom])) ? ' &bull; Ruang: <span style="color:#38bdf8;font-weight:700">' . esc($roomsMap[$filterRoom]) . '</span>' : '' ?>
                <?= !empty($filterSesi) ? ' &bull; Sesi <span style="color:#f472b6;font-weight:700">' . esc($filterSesi) . '</span>' : '' ?>
                &bull; TTD: <?= esc($jabatanTtd) ?> (<?= esc($namaPejabat) ?>)
            </p>
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

    <!-- Toolbar Filter & Sorting Interaktif -->
    <form method="GET" action="" class="toolbar-filter">
        <div class="filter-group">
            <label class="filter-label"><i class="fas fa-users" style="color:#818cf8"></i> Kelas:</label>
            <select name="kelas" class="filter-select" onchange="this.form.submit()">
                <option value="">-- Semua Kelas (<?= count($allKelas) ?>) --</option>
                <?php foreach($allKelas as $k): ?>
                    <option value="<?= esc($k) ?>" <?= ($filterKelas === $k) ? 'selected' : '' ?>><?= esc($k) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label"><i class="fas fa-door-open" style="color:#38bdf8"></i> Ruang:</label>
            <select name="room_id" class="filter-select" onchange="this.form.submit()">
                <option value="">-- Semua Ruang --</option>
                <?php foreach($rooms as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= ((string)$filterRoom === (string)$r['id']) ? 'selected' : '' ?>><?= esc($r['nama_ruang']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label"><i class="fas fa-clock" style="color:#f472b6"></i> Sesi:</label>
            <select name="sesi" class="filter-select" onchange="this.form.submit()">
                <option value="">-- Semua Sesi --</option>
                <option value="1" <?= ($filterSesi == '1') ? 'selected' : '' ?>>Sesi 1</option>
                <option value="2" <?= ($filterSesi == '2') ? 'selected' : '' ?>>Sesi 2</option>
                <option value="3" <?= ($filterSesi == '3') ? 'selected' : '' ?>>Sesi 3</option>
                <option value="4" <?= ($filterSesi == '4') ? 'selected' : '' ?>>Sesi 4</option>
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label"><i class="fas fa-sort-amount-down" style="color:#34d399"></i> Urutkan:</label>
            <select name="sort" class="filter-select" onchange="this.form.submit()">
                <option value="kelas_nama" <?= ($sortBy === 'kelas_nama') ? 'selected' : '' ?>>Per Kelas &rarr; Nama Siswa (A-Z)</option>
                <option value="kelas_meja" <?= ($sortBy === 'kelas_meja') ? 'selected' : '' ?>>Per Kelas &rarr; No. Meja</option>
                <option value="kelas_nomor" <?= ($sortBy === 'kelas_nomor') ? 'selected' : '' ?>>Per Kelas &rarr; No. Peserta</option>
                <option value="room_meja" <?= ($sortBy === 'room_meja') ? 'selected' : '' ?>>Per Ruangan &rarr; No. Meja</option>
                <option value="nama" <?= ($sortBy === 'nama') ? 'selected' : '' ?>>Nama Siswa (A-Z)</option>
                <option value="nomor_peserta" <?= ($sortBy === 'nomor_peserta') ? 'selected' : '' ?>>No. Peserta</option>
            </select>
        </div>

        <label class="filter-check" title="Kertas A4 otomatis ganti lembar baru setiap ganti kelas">
            <input type="checkbox" name="break" value="1" <?= $pageBreak ? 'checked' : '' ?> onchange="this.form.submit()">
            <span>Pisahkan Halaman per Kelas</span>
        </label>

        <?php if (!empty($filterKelas) || !empty($filterRoom) || !empty($filterSesi) || $sortBy !== 'kelas_nama' || $pageBreak): ?>
            <a href="<?= base_url('participant/print-kartu/' . $exam['id']) ?>" class="btn btn-outline" style="padding:4px 10px;font-size:11px;color:#f87171;border-color:rgba(239,68,68,0.4);margin-left:auto">
                <i class="fas fa-undo"></i> Reset Filter
            </a>
        <?php endif; ?>
    </form>
</div>

<div class="page-container">
    <?php if(empty($participants)): ?>
        <div style="background:#fff;border-radius:12px;padding:40px;text-align:center;color:#64748b">
            <i class="fas fa-users-slash" style="font-size:40px;opacity:0.4;margin-bottom:12px;display:block"></i>
            <h3>Tidak Ada Peserta yang Sesuai Filter</h3>
            <p style="margin-top:6px;font-size:13px">Coba ubah pilihan filter Kelas, Ruang, atau Sesi di atas.</p>
        </div>
    <?php else: ?>
        <?php
        if ($pageBreak) {
            $grouped = [];
            foreach ($participants as $p) {
                $k = $p['kelas'] ?: 'Tanpa Kelas';
                $grouped[$k][] = $p;
            }
        } else {
            $grouped = ['all' => $participants];
        }
        ?>

        <?php foreach ($grouped as $groupKey => $pList): ?>
            <div class="<?= $pageBreak ? 'class-print-group' : '' ?>">
                <?php if ($pageBreak): ?>
                    <div class="class-divider no-print">
                        <span><i class="fas fa-users" style="color:#6366f1"></i> KELAS: <?= esc($groupKey) ?> (<?= count($pList) ?> Peserta)</span>
                    </div>
                <?php endif; ?>

                <div class="card-grid">
                    <?php foreach($pList as $p): ?>
                    <?php 
                        $roomName = (!empty($p['room_id']) && isset($roomsMap[$p['room_id']])) 
                            ? $roomsMap[$p['room_id']] 
                            : 'Belum Diset';
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
                                <?php
                                    $cbtUser = strtolower(preg_replace('/[^a-zA-Z0-9._-]/', '', $p['nis'] ?: $p['nomor_peserta']));
                                ?>
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
                                <div class="info-row" style="background:#e0e7ff;padding:1px 5px;border-radius:4px;margin:2px 0;border:1px solid #c7d2fe">
                                    <span class="info-label" style="font-weight:700;color:#3730a3">Username</span>
                                    <span class="info-separator">:</span>
                                    <span class="info-value" style="font-family:'JetBrains Mono',monospace;font-size:11.5px;color:#1e1b4b"><?= esc($cbtUser) ?></span>
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
                            <div class="kartu-footer-note">
                                * Harap dibawa saat ujian
                            </div>
                            <div class="signature-box">
                                <div class="signature-date"><?= $kotaTanggal ?></div>
                                <div class="signature-title"><?= esc($jabatanTtd) ?></div>
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
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
