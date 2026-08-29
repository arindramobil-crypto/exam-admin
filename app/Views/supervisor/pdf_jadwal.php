<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: sans-serif; font-size: 9.5pt; color: #111; margin: 0; padding: 0; }
.kop-table { width: 100%; border-collapse: collapse; border-bottom: 2.5px double #000; padding-bottom: 8px; margin-bottom: 12px; }
.kop-table td { border: none; padding: 0; vertical-align: middle; }
.kop-logo { width: 55px; height: 55px; }
.kop-title h2 { margin: 0; font-size: 13pt; text-transform: uppercase; font-weight: bold; }
.kop-title h3 { margin: 2px 0 0 0; font-size: 11pt; text-transform: uppercase; }
.kop-title p { margin: 2px 0 0 0; font-size: 8.5pt; color: #333; }

.doc-title { text-align: center; margin: 10px 0 12px 0; font-size: 11pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; }

.info-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 9pt; }
.info-table td { border: none; padding: 2px 4px; vertical-align: top; }

.data-table { width: 100%; border-collapse: collapse; margin-top: 6px; font-size: 9pt; }
.data-table th, .data-table td { border: 1px solid #000; padding: 5px 6px; }
.data-table th { background-color: #f1f5f9; text-align: center; font-weight: bold; }

.ttd-table { width: 100%; border-collapse: collapse; margin-top: 25px; }
.ttd-table td { border: none; padding: 0; vertical-align: top; text-align: center; font-size: 9pt; }
</style>
</head>
<body>

<?php
$namaSekolah = $settings['nama_sekolah'] ?? 'SEKOLAH / MADRASAH';
$alamatSekolah = $settings['alamat_sekolah'] ?? '';
$kota = $settings['kota'] ?? 'Jakarta';
$logoPath = (!empty($settings['logo']) && file_exists(FCPATH . $settings['logo'])) ? FCPATH . $settings['logo'] : '';
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
            <h3>PANITIA PENILAIAN / UJIAN CBT</h3>
            <?php if($alamatSekolah): ?>
                <p><?= esc($alamatSekolah) ?><?= !empty($settings['telepon']) ? ' Telp. ' . esc($settings['telepon']) : '' ?></p>
            <?php endif; ?>
        </td>
    </tr>
</table>

<div class="doc-title">JADWAL TUGAS PENGAWAS & PROKTOR RUANG</div>

<table class="info-table">
    <tr>
        <td style="width: 16%;">Nama Ujian</td>
        <td style="width: 34%;">: <strong><?= esc($exam['nama_ujian']) ?></strong></td>
        <td style="width: 16%;">Tahun Pelajaran</td>
        <td style="width: 34%;">: <?= esc($exam['tahun']) ?> (Sem. <?= esc($exam['semester']) ?>)</td>
    </tr>
    <tr>
        <td>Periode Tanggal</td>
        <td>: <?= date('d M Y', strtotime($exam['tgl_mulai'])) ?> s/d <?= date('d M Y', strtotime($exam['tgl_selesai'])) ?></td>
        <td>Status</td>
        <td>: <?= strtoupper($exam['status']) ?></td>
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th style="width: 30px;">No</th>
            <th>Nama Petugas</th>
            <th style="width: 110px;">NIP / Kontak</th>
            <th style="width: 80px;">Peran</th>
            <th style="width: 100px;">Ruang Ujian</th>
            <th style="width: 70px;">Sesi</th>
            <th style="width: 80px;">Tanggal</th>
            <th style="width: 80px;">Tanda Tangan</th>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1; foreach($supervisors as $s): ?>
        <tr>
            <td style="text-align: center;"><?= $i++ ?></td>
            <td><strong><?= esc($s['nama_pengawas']) ?></strong></td>
            <td style="text-align: center; font-size: 8.5pt;"><?= esc($s['nip'] ?: '-') ?><?= !empty($s['kontak']) ? '<br><small>'.esc($s['kontak']).'</small>' : '' ?></td>
            <td style="text-align: center;"><strong><?= ucfirst(esc($s['peran'])) ?></strong></td>
            <td style="text-align: center;"><?= esc($s['nama_ruang'] ?: 'Semua Ruang') ?></td>
            <td style="text-align: center;">Sesi <?= $s['sesi'] ?: 'Semua' ?></td>
            <td style="text-align: center;"><?= !empty($s['tanggal_tugas']) ? date('d/m/Y', strtotime($s['tanggal_tugas'])) : '-' ?></td>
            <td style="height: 24px;"></td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($supervisors)): ?>
        <tr>
            <td colspan="8" style="text-align: center; padding: 20px;">Belum ada jadwal penugasan pengawas yang dibuat.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<table class="ttd-table">
    <tr>
        <td style="width: 50%;"></td>
        <td style="width: 50%;">
            <?= esc($kota) ?>, <?= date('d F Y') ?><br>
            Kepala Sekolah,
            <br><br><br><br>
            <strong><u><?= esc($settings['nama_kepala_sekolah'] ?? '...................................') ?></u></strong>
            <?php if(!empty($settings['nip_kepala_sekolah'])): ?>
                <br>NIP. <?= esc($settings['nip_kepala_sekolah']) ?>
            <?php endif; ?>
        </td>
    </tr>
</table>

</body>
</html>
