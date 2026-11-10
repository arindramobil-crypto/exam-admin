<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Berita Acara - <?= esc($subject['nama_mapel']) ?> (<?= esc($room['nama_ruang']) ?>)</title>
<style>
body {
    font-family: Arial, sans-serif;
    font-size: 10.5pt;
    line-height: 1.5;
    color: #111;
    margin: 0;
    padding: 0;
}
.kop-table {
    width: 100%;
    border-collapse: collapse;
    border-bottom: 2.5px double #000;
    padding-bottom: 8px;
    margin-bottom: 16px;
}
.kop-table td {
    border: none;
    padding: 0;
    vertical-align: middle;
}
.kop-logo {
    width: 60px;
    height: 60px;
}
.kop-title h2 {
    margin: 0;
    font-size: 13.5pt;
    text-transform: uppercase;
    font-weight: bold;
    letter-spacing: 0.04em;
}
.kop-title h3 {
    margin: 2px 0 0 0;
    font-size: 11pt;
    text-transform: uppercase;
}
.kop-title p {
    margin: 2px 0 0 0;
    font-size: 8.5pt;
    color: #333;
}

.doc-title {
    text-align: center;
    font-weight: bold;
    font-size: 12.5pt;
    margin-bottom: 16px;
    text-decoration: underline;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.content p {
    margin-bottom: 10px;
    text-align: justify;
}
.content table {
    width: 100%;
    border-collapse: collapse;
    margin: 12px 0;
}
.content td {
    padding: 4px 6px;
    vertical-align: top;
    border: none;
    font-size: 10pt;
}

.ttd-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 30px;
}
.ttd-table td {
    border: none;
    padding: 0;
    vertical-align: top;
    text-align: center;
    font-size: 10pt;
}
</style>
</head>
<body>

<?php
$namaSekolah   = $settings['nama_sekolah'] ?? 'PANITIA UJIAN BERBASIS KOMPUTER (CBT)';
$alamatSekolah = $settings['alamat_sekolah'] ?? '';
$kota          = $settings['kota'] ?? 'Kota';
$logoPath      = (!empty($settings['logo']) && file_exists(FCPATH . $settings['logo'])) ? FCPATH . $settings['logo'] : '';

$hadir = (int)($ba['jml_peserta_hadir'] ?? 0);
$absen = (int)($ba['jml_peserta_absen'] ?? 0);
$total = $hadir + $absen;
?>

<!-- Kop Surat Resmi -->
<table class="kop-table">
    <tr>
        <?php if($logoPath): ?>
            <td style="width: 70px; text-align: left;">
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
    </tr>
</table>

<div class="doc-title">BERITA ACARA PELAKSANAAN UJIAN CBT</div>

<div class="content">
    <p>
        Pada hari ini, tanggal <strong><?= date('d F Y') ?></strong>, telah diselenggarakan Ujian <strong><?= esc($exam['nama_ujian']) ?></strong> Tahun Pelajaran <strong><?= esc($exam['tahun']) ?></strong> (Semester <?= esc($exam['semester']) ?>), dengan rincian pelaksanaan sebagai berikut:
    </p>

    <table>
        <tr>
            <td style="width: 5%;">1.</td>
            <td style="width: 38%;">Mata Pelajaran / Tingkat</td>
            <td style="width: 3%;">:</td>
            <td style="font-weight: bold;"><?= esc($subject['nama_mapel']) ?> (Kelas <?= esc($subject['kelas']) ?>)</td>
        </tr>
        <tr>
            <td>2.</td>
            <td>Ruang Ujian</td>
            <td>:</td>
            <td style="font-weight: bold;"><?= esc($room['nama_ruang']) ?> (<?= esc($room['gedung'] ?: 'Gedung Utama') ?> / Lt. <?= esc($room['lantai'] ?: 1) ?>)</td>
        </tr>
        <tr>
            <td>3.</td>
            <td>Waktu Pelaksanaan</td>
            <td>:</td>
            <td>Pukul <strong><?= esc($ba['waktu_mulai'] ?: ($subject['jam_mulai'] ?: '07:30')) ?></strong> s/d <strong><?= esc($ba['waktu_selesai'] ?: ($subject['jam_selesai'] ?: '09:00')) ?></strong></td>
        </tr>
        <tr>
            <td>4.</td>
            <td>Jumlah Butir Soal</td>
            <td>:</td>
            <td><strong><?= (int)($ba['jml_soal'] ?: 40) ?></strong> butir soal CBT</td>
        </tr>
        <tr>
            <td>5.</td>
            <td>Jumlah Peserta Seharusnya</td>
            <td>:</td>
            <td><strong><?= $total ?></strong> orang</td>
        </tr>
        <tr>
            <td>6.</td>
            <td>Jumlah Peserta yang Hadir</td>
            <td>:</td>
            <td style="color: #047857; font-weight: bold;"><?= $hadir ?> orang</td>
        </tr>
        <tr>
            <td>7.</td>
            <td>Jumlah Peserta Tidak Hadir</td>
            <td>:</td>
            <td style="color: #b91c1c; font-weight: bold;"><?= $absen ?> orang</td>
        </tr>
        <tr>
            <td>8.</td>
            <td>Catatan Kejadian Selama Ujian</td>
            <td>:</td>
            <td><?= nl2br(esc($ba['catatan'])) ?: 'Ujian berlangsung dengan tertib, aman, dan lancar tanpa kendala teknis yang berarti.' ?></td>
        </tr>
    </table>

    <p style="margin-top: 14px;">
        Demikian Berita Acara Pelaksanaan Ujian ini dibuat dengan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya.
    </p>
</div>

<table class="ttd-table">
    <tr>
        <td style="width: 50%;">
            Proktor / Teknisi Ujian,<br><br><br><br><br>
            <strong><u><?= esc($ba['nama_pengawas2'] ?: '................................................') ?></u></strong><br>
            NIP. ................................................
        </td>
        <td style="width: 50%;">
            <?= esc($kota) ?>, <?= date('d F Y') ?><br>
            Pengawas Ruang Ujian,<br><br><br><br><br>
            <strong><u><?= esc($ba['nama_pengawas1'] ?: '................................................') ?></u></strong><br>
            NIP. ................................................
        </td>
    </tr>
</table>

</body>
</html>
