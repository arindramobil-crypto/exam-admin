<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: serif; font-size: 11pt; line-height: 1.6; color: #111; margin: 0; padding: 0; }
.kop-table { width: 100%; border-collapse: collapse; border-bottom: 2.5px double #000; padding-bottom: 8px; margin-bottom: 14px; }
.kop-table td { border: none; padding: 0; vertical-align: middle; }
.kop-logo { width: 55px; height: 55px; }
.kop-title h2 { font-family: sans-serif; margin: 0; font-size: 13pt; text-transform: uppercase; font-weight: bold; }
.kop-title h3 { font-family: sans-serif; margin: 2px 0 0 0; font-size: 11pt; text-transform: uppercase; }
.kop-title p { font-family: sans-serif; margin: 2px 0 0 0; font-size: 8.5pt; color: #333; }

.doc-title { text-align: center; font-weight: bold; font-size: 12pt; margin-bottom: 14px; text-decoration: underline; text-transform: uppercase; }

.content p { margin-bottom: 10px; text-align: justify; }
.content table { width: 100%; border-collapse: collapse; margin: 10px 0; }
.content td { padding: 4px 6px; vertical-align: top; border: none; }

.ttd-table { width: 100%; border-collapse: collapse; margin-top: 35px; }
.ttd-table td { border: none; padding: 0; vertical-align: top; text-align: center; }
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

<div class="doc-title">BERITA ACARA PELAKSANAAN UJIAN CBT</div>

<div class="content">
    <p>
        Pada hari ini, tanggal <strong><?= date('d F Y') ?></strong>, telah diselenggarakan Ujian <strong><?= esc($exam['nama_ujian']) ?></strong> Tahun Pelajaran <strong><?= esc($exam['tahun']) ?></strong> untuk Mata Pelajaran <strong><?= esc($subject['nama_mapel']) ?> (Kelas <?= esc($subject['kelas']) ?>)</strong> di Ruang <strong><?= esc($room['nama_ruang']) ?></strong> dengan rincian pelaksanaan sebagai berikut:
    </p>

    <table>
        <tr>
            <td style="width: 5%;">1.</td>
            <td style="width: 40%;">Jumlah Peserta Seharusnya</td>
            <td style="width: 4%;">:</td>
            <td style="font-weight: bold;"><?= (int)$ba['hadir_count'] + (int)$ba['absen_count'] ?> orang</td>
        </tr>
        <tr>
            <td>2.</td>
            <td>Jumlah Peserta yang Hadir</td>
            <td>:</td>
            <td style="font-weight: bold;"><?= (int)$ba['hadir_count'] ?> orang</td>
        </tr>
        <tr>
            <td>3.</td>
            <td>Jumlah Peserta yang Tidak Hadir</td>
            <td>:</td>
            <td style="font-weight: bold;"><?= (int)$ba['absen_count'] ?> orang</td>
        </tr>
        <tr>
            <td>4.</td>
            <td>Catatan Kejadian Selama Ujian</td>
            <td>:</td>
            <td><?= nl2br(esc($ba['catatan'])) ?: 'Nihil / Ujian berjalan tertib, aman, dan lancar.' ?></td>
        </tr>
    </table>

    <p style="margin-top: 15px;">
        Demikian Berita Acara Pelaksanaan Ujian ini dibuat dengan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya.
    </p>
</div>

<table class="ttd-table">
    <tr>
        <td style="width: 50%;">
            Proktor Ujian,<br><br><br><br><br>
            <strong><u><?= esc($ba['proktor'] ?: '..........................................') ?></u></strong>
        </td>
        <td style="width: 50%;">
            <?= esc($kota) ?>, <?= date('d F Y') ?><br>
            Pengawas Ruang,<br><br><br><br><br>
            <strong><u><?= esc($ba['pengawas'] ?: '..........................................') ?></u></strong>
        </td>
    </tr>
</table>

</body>
</html>
