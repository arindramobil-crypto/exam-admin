<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Laporan Analisis Butir Soal - <?= esc($subject['nama_mapel']) ?></title>
<style>
body {
    font-family: Arial, sans-serif;
    font-size: 9pt;
    line-height: 1.4;
    color: #111;
    margin: 0;
    padding: 0;
}
.kop-table {
    width: 100%;
    border-collapse: collapse;
    border-bottom: 2.5px double #000;
    padding-bottom: 6px;
    margin-bottom: 12px;
}
.kop-table td {
    border: none;
    padding: 0;
    vertical-align: middle;
}
.kop-logo {
    width: 55px;
    height: 55px;
}
.kop-title h2 {
    margin: 0;
    font-size: 13pt;
    text-transform: uppercase;
    font-weight: bold;
    letter-spacing: 0.03em;
}
.kop-title h3 {
    margin: 2px 0 0 0;
    font-size: 10pt;
    text-transform: uppercase;
}
.kop-title p {
    margin: 2px 0 0 0;
    font-size: 8pt;
    color: #333;
}

.doc-title {
    text-align: center;
    font-weight: bold;
    font-size: 11pt;
    margin-bottom: 10px;
    text-decoration: underline;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.meta-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 12px;
    font-size: 8.5pt;
}
.meta-table td {
    border: none;
    padding: 2px 4px;
    vertical-align: top;
}

/* Stat Cards Table */
.summary-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 14px;
    font-size: 8pt;
}
.summary-table th {
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    padding: 4px 6px;
    text-align: center;
    font-weight: bold;
}
.summary-table td {
    border: 1px solid #cbd5e1;
    padding: 5px 6px;
    text-align: center;
}

/* Main Table */
.item-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 16px;
    font-size: 8pt;
}
.item-table th {
    background: #1e293b;
    color: #ffffff;
    border: 1px solid #475569;
    padding: 5px 4px;
    text-align: center;
    font-weight: bold;
}
.item-table td {
    border: 1px solid #cbd5e1;
    padding: 4px 5px;
    vertical-align: middle;
}
.item-table tr:nth-child(even) td {
    background: #f8fafc;
}

.badge-tag {
    display: inline-block;
    padding: 2px 5px;
    border-radius: 3px;
    font-size: 7.5pt;
    font-weight: bold;
}
.tag-success { background: #dcfce7; color: #15803d; }
.tag-warning { background: #fef3c7; color: #b45309; }
.tag-danger  { background: #fee2e2; color: #b91c1c; }
.tag-info    { background: #e0f2fe; color: #0369a1; }

.ttd-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    page-break-inside: avoid;
}
.ttd-table td {
    border: none;
    padding: 0;
    vertical-align: top;
    text-align: center;
    font-size: 8.5pt;
}
</style>
</head>
<body>

<?php
$namaSekolah   = $settings['nama_sekolah'] ?? 'PANITIA UJIAN BERBASIS KOMPUTER (CBT)';
$alamatSekolah = $settings['alamat_sekolah'] ?? '';
$kota          = $settings['kota'] ?? 'Kota';
$logoPath      = (!empty($settings['logo']) && file_exists(FCPATH . $settings['logo'])) ? FCPATH . $settings['logo'] : '';

$items           = $analysisData['items'] ?? [];
$totalSoal       = count($items);
$totalAttempts   = $analysisData['total_attempts'] ?? 0;
$avgFacility     = $analysisData['avg_facility'] ?? 0;
$avgDiscrim      = $analysisData['avg_discrimination'] ?? 0;
$mudah           = $analysisData['easy_count'] ?? 0;
$sedang          = $analysisData['medium_count'] ?? 0;
$sukar           = $analysisData['hard_count'] ?? 0;
$diterima        = $analysisData['accepted_count'] ?? 0;
$revisi          = $analysisData['revised_count'] ?? 0;
$ditolak         = $analysisData['rejected_count'] ?? 0;
?>

<!-- Kop Surat -->
<table class="kop-table">
    <tr>
        <?php if($logoPath): ?>
            <td style="width: 65px; text-align: left;">
                <img src="<?= $logoPath ?>" class="kop-logo">
            </td>
        <?php endif; ?>
        <td class="kop-title" style="text-align: center;">
            <h2><?= esc($namaSekolah) ?></h2>
            <h3>PANITIA PENILAIAN / UJIAN BERBASIS KOMPUTER (CBT)</h3>
            <?php if($alamatSekolah): ?>
                <p>
                    <?= esc($alamatSekolah) ?>
                    <?= !empty($settings['telepon']) ? ' &bull; Telp. ' . esc($settings['telepon']) : '' ?>
                    <?= !empty($settings['email']) ? ' &bull; Email: ' . esc($settings['email']) : '' ?>
                </p>
            <?php endif; ?>
        </td>
        <?php if($logoPath): ?>
            <td style="width: 65px;"></td>
        <?php endif; ?>
    </tr>
</table>

<div class="doc-title">LAPORAN ANALISIS BUTIR SOAL UJIAN CBT</div>

<!-- Metadata Ujian -->
<table class="meta-table">
    <tr>
        <td style="width: 14%;"><strong>Nama Ujian</strong></td>
        <td style="width: 2%;">:</td>
        <td style="width: 34%;"><?= esc($exam['nama_ujian']) ?> (Tahun <?= esc($exam['tahun'] ?? date('Y')) ?>)</td>

        <td style="width: 18%;"><strong>Total Peserta Mengerjakan</strong></td>
        <td style="width: 2%;">:</td>
        <td style="width: 30%;"><?= $totalAttempts ?> Siswa Selesai Tes</td>
    </tr>
    <tr>
        <td><strong>Mata Pelajaran</strong></td>
        <td>:</td>
        <td><?= esc($subject['nama_mapel']) ?> (Kelas <?= esc($subject['kelas']) ?>)</td>

        <td><strong>Jumlah Butir Soal</strong></td>
        <td>:</td>
        <td><?= $totalSoal ?> Butir (Quiz Moodle #<?= $subject['moodle_quiz_id'] ?>)</td>
    </tr>
    <tr>
        <td><strong>Tanggal Export</strong></td>
        <td>:</td>
        <td><?= date('d F Y, H:i') ?> WIB</td>

        <td><strong>Rata-rata Tingkat Kesukaran (P)</strong></td>
        <td>:</td>
        <td><strong><?= number_format($avgFacility, 3) ?></strong> &nbsp;|&nbsp; Rata-rata D: <strong><?= number_format($avgDiscrim, 3) ?></strong></td>
    </tr>
</table>

<!-- Ringkasan Statistik Evaluasi -->
<table class="summary-table">
    <thead>
        <tr>
            <th colspan="3" style="background:#e2e8f0;color:#0f172a">Distribusi Tingkat Kesukaran (P)</th>
            <th colspan="3" style="background:#e2e8f0;color:#0f172a">Distribusi Rekomendasi Butir Soal</th>
        </tr>
        <tr>
            <th style="width:16.6%">Mudah (P &ge; 0.70)</th>
            <th style="width:16.6%">Sedang (0.30 &le; P &lt; 0.70)</th>
            <th style="width:16.6%">Sukar (P &lt; 0.30)</th>
            <th style="width:16.6%">Diterima Baik (D &ge; 0.30)</th>
            <th style="width:16.6%">Perlu Revisi (0.20 &le; D &lt; 0.30)</th>
            <th style="width:16.6%">Ditolak / Diganti (D &lt; 0.20)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong><?= $mudah ?></strong> Soal (<?= $totalSoal > 0 ? round(($mudah/$totalSoal)*100, 1) : 0 ?>%)</td>
            <td><strong><?= $sedang ?></strong> Soal (<?= $totalSoal > 0 ? round(($sedang/$totalSoal)*100, 1) : 0 ?>%)</td>
            <td><strong><?= $sukar ?></strong> Soal (<?= $totalSoal > 0 ? round(($sukar/$totalSoal)*100, 1) : 0 ?>%)</td>
            <td><strong style="color:#15803d"><?= $diterima ?></strong> Soal (<?= $totalSoal > 0 ? round(($diterima/$totalSoal)*100, 1) : 0 ?>%)</td>
            <td><strong style="color:#b45309"><?= $revisi ?></strong> Soal (<?= $totalSoal > 0 ? round(($revisi/$totalSoal)*100, 1) : 0 ?>%)</td>
            <td><strong style="color:#b91c1c"><?= $ditolak ?></strong> Soal (<?= $totalSoal > 0 ? round(($ditolak/$totalSoal)*100, 1) : 0 ?>%)</td>
        </tr>
    </tbody>
</table>

<!-- Rincian Parameter Butir Soal -->
<table class="item-table">
    <thead>
        <tr>
            <th style="width: 30px;">No</th>
            <th style="width: 50px;">Slot</th>
            <th>Nama / Pertanyaan Butir Soal</th>
            <th style="width: 70px;">Tipe</th>
            <th style="width: 45px;">Bobot</th>
            <th style="width: 65px;">Indeks (P)</th>
            <th style="width: 75px;">Kategori (P)</th>
            <th style="width: 65px;">Indeks (D)</th>
            <th style="width: 90px;">Kategori (D)</th>
            <th style="width: 110px;">Status Rekomendasi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($items as $idx => $it): ?>
        <tr>
            <td style="text-align: center;"><?= $idx + 1 ?></td>
            <td style="text-align: center; font-weight: bold;">#<?= $it['slot'] ?></td>
            <td><?= esc($it['name']) ?></td>
            <td style="text-align: center;"><?= esc($it['qtype']) ?></td>
            <td style="text-align: right;"><?= number_format($it['maxmark'], 1) ?></td>
            <td style="text-align: right; font-family: monospace; font-weight: bold;"><?= number_format($it['facility_index'], 2) ?></td>
            <td style="text-align: center;">
                <?php if($it['category'] === 'Mudah'): ?>
                    <span class="badge-tag tag-success">Mudah</span>
                <?php elseif($it['category'] === 'Sedang'): ?>
                    <span class="badge-tag tag-warning">Sedang</span>
                <?php else: ?>
                    <span class="badge-tag tag-danger">Sukar</span>
                <?php endif; ?>
            </td>
            <td style="text-align: right; font-family: monospace; font-weight: bold;">
                <?= ($it['discrimination_index'] >= 0 ? '+' : '') . number_format($it['discrimination_index'], 2) ?>
            </td>
            <td style="text-align: center;"><?= $it['discrimination_category'] ?></td>
            <td style="text-align: center;">
                <?php if(str_contains($it['recommendation'], 'Sangat Baik')): ?>
                    <span class="badge-tag tag-success">Diterima Baik</span>
                <?php elseif(str_contains($it['recommendation'], 'Diterima')): ?>
                    <span class="badge-tag tag-info">Diterima</span>
                <?php elseif(str_contains($it['recommendation'], 'Revisi')): ?>
                    <span class="badge-tag tag-warning">Perlu Revisi</span>
                <?php else: ?>
                    <span class="badge-tag tag-danger">Ditolak</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($items)): ?>
        <tr>
            <td colspan="10" style="text-align: center; padding: 20px;">Tidak ada data butir soal.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Tanda Tangan -->
<table class="ttd-table">
    <tr>
        <td style="width: 45%;">
            Mengetahui,<br>
            Ketua Panitia Ujian CBT
            <br><br><br><br>
            <strong>__________________________</strong><br>
            NIP. ....................................................
        </td>
        <td style="width: 10%;"></td>
        <td style="width: 45%;">
            <?= esc($kota) ?>, <?= date('d F Y') ?><br>
            Guru Pengampu Mata Pelajaran
            <br><br><br><br>
            <strong>__________________________</strong><br>
            NIP. ....................................................
        </td>
    </tr>
</table>

</body>
</html>
