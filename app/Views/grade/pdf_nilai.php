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
            <h3>REKAPITULASI NILAI UJIAN CBT</h3>
            <?php if($alamatSekolah): ?>
                <p><?= esc($alamatSekolah) ?><?= !empty($settings['telepon']) ? ' Telp. ' . esc($settings['telepon']) : '' ?></p>
            <?php endif; ?>
        </td>
    </tr>
</table>

<div class="doc-title">DAFTAR NILAI PESERTA UJIAN</div>

<table class="info-table">
    <tr>
        <td style="width: 16%;">Nama Ujian</td>
        <td style="width: 34%;">: <strong><?= esc($exam['nama_ujian']) ?></strong></td>
        <td style="width: 16%;">Kelas</td>
        <td style="width: 34%;">: <strong><?= esc($subject['kelas']) ?></strong></td>
    </tr>
    <tr>
        <td>Mata Pelajaran</td>
        <td>: <strong><?= esc($subject['nama_mapel']) ?></strong></td>
        <td>Tahun Pelajaran</td>
        <td>: <?= esc($exam['tahun']) ?> (Sem. <?= esc($exam['semester']) ?>)</td>
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th style="width: 30px;">No</th>
            <th>Nama Lengkap Siswa</th>
            <th style="width: 100px;">Username</th>
            <th style="width: 100px;">NIS / NISN</th>
            <th style="width: 70px;">Nilai</th>
            <th style="width: 90px;">Status</th>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1; foreach($grades as $g): ?>
        <tr>
            <td style="text-align: center;"><?= $i++ ?></td>
            <td><strong><?= esc($g['firstname'] . ' ' . $g['lastname']) ?></strong></td>
            <td style="text-align: center; font-family: monospace;"><?= esc($g['username']) ?></td>
            <td style="text-align: center;"><?= esc($g['idnumber'] ?: '-') ?></td>
            <td style="text-align: center; font-weight: bold;"><?= number_format($g['grade'], 2) ?></td>
            <td style="text-align: center;">
                <?= $g['grade'] >= 75 ? '<span style="color:#059669;font-weight:bold">TUNTAS</span>' : '<span style="color:#dc2626">BELUM TUNTAS</span>' ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($grades)): ?>
        <tr>
            <td colspan="6" style="text-align: center; padding: 20px;">Belum ada data nilai dari Moodle.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<table class="ttd-table">
    <tr>
        <td style="width: 50%;">
            Mengetahui,<br>
            Kepala Sekolah,
            <br><br><br><br>
            <strong><u><?= esc($settings['nama_kepala_sekolah'] ?? '...................................') ?></u></strong>
            <?php if(!empty($settings['nip_kepala_sekolah'])): ?>
                <br>NIP. <?= esc($settings['nip_kepala_sekolah']) ?>
            <?php endif; ?>
        </td>
        <td style="width: 50%;">
            <?= esc($kota) ?>, <?= date('d F Y') ?><br>
            Guru Mata Pelajaran,
            <br><br><br><br>
            <strong><u>( ........................................... )</u></strong>
            <br>NIP. .....................................
        </td>
    </tr>
</table>

</body>
</html>
