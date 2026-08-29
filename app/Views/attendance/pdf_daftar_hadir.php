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

$roomName = $room ? $room['nama_ruang'] : 'Semua Ruang';
$sesiName = !empty($selectedSesi) ? "Sesi {$selectedSesi}" : 'Semua Sesi';
$subjectName = $subject ? $subject['nama_mapel'] . " (Kelas " . $subject['kelas'] . ")" : 'Semua Mata Pelajaran';
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
                <p><?= esc($alamatSekolah) ?><?= !empty($settings['telepon']) ? ' Telp. ' . esc($settings['telepon']) : '' ?></p>
            <?php endif; ?>
        </td>
    </tr>
</table>

<div class="doc-title">DAFTAR HADIR PESERTA UJIAN CBT</div>

<table class="info-table">
    <tr>
        <td style="width: 16%;">Nama Ujian</td>
        <td style="width: 34%;">: <strong><?= esc($exam['nama_ujian']) ?></strong></td>
        <td style="width: 16%;">Ruang Ujian</td>
        <td style="width: 34%;">: <strong><?= esc($roomName) ?></strong></td>
    </tr>
    <tr>
        <td>Mata Pelajaran</td>
        <td>: <?= esc($subjectName) ?></td>
        <td>Sesi Ujian</td>
        <td>: <strong><?= esc($sesiName) ?></strong></td>
    </tr>
    <tr>
        <td>Tahun Pelajaran</td>
        <td>: <?= esc($exam['tahun']) ?> (Sem. <?= esc($exam['semester']) ?>)</td>
        <td>Hari / Tanggal</td>
        <td>: <?= date('d F Y') ?></td>
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th style="width: 30px;">No</th>
            <th style="width: 100px;">No. Peserta</th>
            <th>Nama Lengkap Siswa</th>
            <th style="width: 55px;">Kelas</th>
            <th style="width: 70px;">Ruang</th>
            <th style="width: 45px;">Meja</th>
            <th style="width: 55px;">Sesi</th>
            <th style="width: 120px;">Tanda Tangan</th>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1; foreach($participants as $p): ?>
        <tr>
            <td style="text-align: center;"><?= $i ?></td>
            <td style="text-align: center; font-family: monospace; font-weight: bold;"><?= esc($p['nomor_peserta']) ?></td>
            <td><?= esc($p['nama']) ?></td>
            <td style="text-align: center;"><?= esc($p['kelas']) ?></td>
            <td style="text-align: center;"><?= esc($roomsMap[$p['room_id']] ?? '-') ?></td>
            <td style="text-align: center; font-weight: bold;">#<?= esc($p['nomor_meja'] ?: '-') ?></td>
            <td style="text-align: center;">Sesi <?= $p['sesi'] ?: 1 ?></td>
            <td style="height: 24px; vertical-align: middle;">
                <?= $i % 2 == 1 ? $i . ". ............" : "<div style='text-align:right'>" . $i . ". ............</div>" ?>
            </td>
        </tr>
        <?php $i++; endforeach; ?>
        <?php if(empty($participants)): ?>
        <tr>
            <td colspan="8" style="text-align: center; padding: 20px;">Belum ada peserta terdaftar.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<table class="ttd-table">
    <tr>
        <td style="width: 50%;">
            Mengetahui,<br>
            Ketua Panitia Ujian,
            <br><br><br><br>
            <strong><u><?= esc($settings['nama_ketua_panitia'] ?? '...................................') ?></u></strong>
            <?php if(!empty($settings['nip_ketua_panitia'])): ?>
                <br>NIP. <?= esc($settings['nip_ketua_panitia']) ?>
            <?php endif; ?>
        </td>
        <td style="width: 50%;">
            <?= esc($kota) ?>, <?= date('d F Y') ?><br>
            Pengawas Ruang,
            <br><br><br><br>
            <strong><u>( ........................................... )</u></strong>
            <br>NIP. .....................................
        </td>
    </tr>
</table>

</body>
</html>
