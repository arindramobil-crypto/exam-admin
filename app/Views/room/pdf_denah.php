<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Denah Tempat Duduk - <?= esc($room['nama_ruang']) ?></title>
<style>
body {
    font-family: Arial, sans-serif;
    color: #000;
    font-size: 10pt;
}
.kop-table {
    width: 100%;
    border-bottom: 2px solid #000;
    padding-bottom: 8px;
    margin-bottom: 12px;
}
.kop-title {
    text-align: center;
}
.kop-title h1 {
    font-size: 14pt;
    font-weight: bold;
    margin: 0;
    text-transform: uppercase;
}
.kop-title h2 {
    font-size: 12pt;
    font-weight: bold;
    margin: 2px 0;
}
.kop-title p {
    font-size: 8.5pt;
    color: #333;
    margin: 0;
}
.header-box {
    text-align: center;
    margin-bottom: 12px;
}
.header-box h3 {
    font-size: 12pt;
    font-weight: bold;
    text-decoration: underline;
    text-transform: uppercase;
    margin: 0 0 3px 0;
}
.header-box p {
    font-size: 9.5pt;
    font-weight: bold;
    margin: 0;
}
.info-table {
    width: 100%;
    background-color: #f1f5f9;
    border: 1px solid #94a3b8;
    margin-bottom: 14px;
    font-size: 8.5pt;
    font-weight: bold;
}
.info-table td {
    padding: 5px 8px;
}
.front-table {
    width: 100%;
    margin-bottom: 14px;
}
.door-box {
    width: 120px;
    border: 1.5px dashed #64748b;
    padding: 6px;
    text-align: center;
    font-weight: bold;
    font-size: 8pt;
    background-color: #f8fafc;
}
.blackboard {
    border: 1px solid #000;
    background-color: #1e293b;
    color: #fff;
    padding: 6px;
    text-align: center;
    font-weight: bold;
    font-size: 9pt;
    letter-spacing: 2px;
}
.supervisor-box {
    width: 170px;
    border: 2px solid #000;
    padding: 6px;
    text-align: center;
    font-weight: bold;
    font-size: 8.5pt;
    background-color: #f8fafc;
}
.grid-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 8px;
    margin-bottom: 16px;
}
.desk-cell {
    width: 25%;
    border: 1.5px solid #000;
    padding: 6px 8px;
    vertical-align: top;
    height: 52px;
    background-color: #fff;
}
.desk-cell.empty {
    border: 1.5px dashed #cbd5e1;
    background-color: #f8fafc;
    text-align: center;
    vertical-align: middle;
    color: #94a3b8;
}
.desk-title {
    font-size: 8.5pt;
    font-weight: bold;
    color: #1e1b4b;
    border-bottom: 1px dotted #ccc;
    padding-bottom: 2px;
    margin-bottom: 3px;
}
.desk-name {
    font-size: 8.5pt;
    font-weight: bold;
    color: #000;
    white-space: nowrap;
    overflow: hidden;
}
.desk-sub {
    font-size: 7.5pt;
    color: #333;
    font-family: monospace;
}
.sig-table {
    width: 100%;
    margin-top: 15px;
    font-size: 9pt;
}
.sig-box {
    text-align: center;
    width: 35%;
}
</style>
</head>
<body>

<table class="kop-table">
    <tr>
        <?php if(!empty($settings['logo']) && file_exists(FCPATH . $settings['logo'])): ?>
            <td style="width:70px">
                <img src="<?= FCPATH . $settings['logo'] ?>" style="width:60px;height:60px" />
            </td>
        <?php endif; ?>
        <td class="kop-title">
            <h1><?= esc($settings['nama_sekolah'] ?? 'PANITIA UJIAN BERBASIS KOMPUTER') ?></h1>
            <h2>DENAH TEMPAT DUDUK PESERTA UJIAN (CBT)</h2>
            <p>
                <?= esc($settings['alamat_sekolah'] ?? 'Alamat Sekolah') ?>
                <?php if(!empty($settings['telepon'])): ?> | Telp: <?= esc($settings['telepon']) ?><?php endif; ?>
                <?php if(!empty($settings['email'])): ?> | Email: <?= esc($settings['email']) ?><?php endif; ?>
            </p>
        </td>
    </tr>
</table>

<div class="header-box">
    <h3>TATA LETAK TEMPAT DUDUK RUANG UJIAN</h3>
    <p><?= strtoupper(esc($exam['nama_ujian'])) ?> &mdash; TAHUN AJARAN <?= esc($exam['tahun']) ?></p>
</div>

<table class="info-table">
    <tr>
        <td>RUANG: <?= strtoupper(esc($room['nama_ruang'])) ?> (<?= esc($room['gedung'] ?? 'Gedung Utama') ?> / Lt. <?= esc($room['lantai'] ?? 1) ?>)</td>
        <td>SESI: <?= $sesi ?></td>
        <td>KAPASITAS: <?= esc($room['kapasitas']) ?> Kursi</td>
        <td>TERISI: <?= count($participants) ?> Siswa</td>
        <td>TANGGAL: <?= date('d M Y') ?></td>
    </tr>
</table>

<table class="front-table">
    <tr>
        <td class="door-box">[ PINTU MASUK ]</td>
        <td style="width:20px">&nbsp;</td>
        <td class="blackboard">[ PAPAN TULIS / MONITOR DEPAN ]</td>
        <td style="width:20px">&nbsp;</td>
        <td class="supervisor-box">MEJA PENGAWAS &amp; PROKTOR</td>
    </tr>
</table>

<?php
$kapasitas = max((int)$room['kapasitas'], count($participants));
$desksByMeja = [];
foreach($participants as $p) {
    $noMeja = (int)$p['nomor_meja'];
    if ($noMeja > 0) {
        $desksByMeja[$noMeja] = $p;
    }
}
$cols = 4;
$rowsCount = ceil($kapasitas / $cols);
?>

<table class="grid-table">
    <?php for($r = 0; $r < $rowsCount; $r++): ?>
    <tr>
        <?php for($c = 1; $c <= $cols; $c++): ?>
            <?php $m = ($r * $cols) + $c; ?>
            <?php if($m <= $kapasitas): ?>
                <?php if(isset($desksByMeja[$m])): ?>
                    <?php $p = $desksByMeja[$m]; ?>
                    <td class="desk-cell">
                        <div class="desk-title">MEJA <?= sprintf('%02d', $m) ?></div>
                        <div class="desk-name"><?= esc($p['nama']) ?></div>
                        <div class="desk-sub"><?= esc($p['nomor_peserta']) ?> | <?= esc($p['kelas']) ?></div>
                    </td>
                <?php else: ?>
                    <td class="desk-cell empty">
                        <strong>MEJA <?= sprintf('%02d', $m) ?></strong><br>
                        <span style="font-size:7pt">[ KOSONG ]</span>
                    </td>
                <?php endif; ?>
            <?php else: ?>
                <td style="border:none"></td>
            <?php endif; ?>
        <?php endfor; ?>
    </tr>
    <?php endfor; ?>
</table>

<table class="sig-table">
    <tr>
        <td class="sig-box">
            Pengawas Ruang Ujian,
            <br><br><br><br>
            <strong>( .................................................... )</strong><br>
            NIP. .................................................
        </td>
        <td style="width:30%">&nbsp;</td>
        <td class="sig-box">
            <?= esc($settings['kota'] ?? 'Kota') ?>, <?= date('d F Y') ?><br>
            Ketua Panitia Ujian,
            <br><br><br><br>
            <strong><?= esc($settings['nama_ketua_panitia'] ?? '( .................................................... )') ?></strong><br>
            NIP. <?= esc($settings['nip_ketua_panitia'] ?? '.................................................') ?>
        </td>
    </tr>
</table>

</body>
</html>
