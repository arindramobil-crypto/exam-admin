<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: sans-serif; font-size: 9pt; color: #111; margin: 0; padding: 0; }
.card-table { width: 100%; border-collapse: collapse; }
.card-td { width: 50%; vertical-align: top; padding: 6px; }
.kartu-box {
    border: 1.5px solid #1e293b;
    border-radius: 8px;
    padding: 10px;
    background: #fff;
    height: 250px;
    box-sizing: border-box;
}
.header {
    text-align: center;
    border-bottom: 2px double #000;
    padding-bottom: 4px;
    margin-bottom: 8px;
}
.header h3 { margin: 0; font-size: 11pt; text-transform: uppercase; font-weight: bold; }
.header h4 { margin: 2px 0 0 0; font-size: 8.5pt; color: #334155; font-weight: normal; }
.content-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; }
.content-table td { padding: 2px 0; border: none; vertical-align: top; }
.lbl { width: 75px; color: #475569; }
.val { font-weight: bold; }
.foto-box {
    width: 65px;
    height: 80px;
    border: 1px dashed #64748b;
    text-align: center;
    font-size: 8pt;
    color: #94a3b8;
    background: #f8fafc;
    padding-top: 28px;
    box-sizing: border-box;
}
.ttd-box {
    margin-top: 8px;
    font-size: 7.5pt;
    text-align: center;
    float: right;
    width: 140px;
}
.clear { clear: both; }
.page-break { page-break-after: always; }
</style>
</head>
<body>

<?php
$chunks = array_chunk($participants, 6);
?>

<?php foreach($chunks as $chunkIdx => $pageParticipants): ?>
<table class="card-table">
    <?php 
    $rows = array_chunk($pageParticipants, 2);
    foreach($rows as $row): 
    ?>
    <tr>
        <?php foreach($row as $p): ?>
        <?php 
            $roomName = '-';
            if (!empty($p['room_id']) && !empty($rooms)) {
                foreach($rooms as $r) {
                    if ($r['id'] == $p['room_id']) { $roomName = $r['nama_ruang']; break; }
                }
            }
        ?>
        <td class="card-td">
            <div class="kartu-box">
                <div class="header">
                    <h3>KARTU PESERTA UJIAN CBT</h3>
                    <h4><?= esc($exam['nama_ujian']) ?> &bull; TP. <?= esc($exam['tahun']) ?></h4>
                </div>

                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="vertical-align: top; width: 72%;">
                            <table class="content-table">
                                <tr>
                                    <td class="lbl">No. Peserta</td>
                                    <td style="width:5px">:</td>
                                    <td class="val"><strong style="font-size:9.5pt"><?= esc($p['nomor_peserta']) ?></strong></td>
                                </tr>
                                <tr>
                                    <td class="lbl">Nama Siswa</td>
                                    <td>:</td>
                                    <td class="val"><?= esc($p['nama']) ?></td>
                                </tr>
                                <tr>
                                    <td class="lbl">NIS / NISN</td>
                                    <td>:</td>
                                    <td><?= esc($p['nis']) ?> <?= !empty($p['nisn']) ? '/ '.esc($p['nisn']) : '' ?></td>
                                </tr>
                                <tr>
                                    <td class="lbl">Kelas</td>
                                    <td>:</td>
                                    <td><?= esc($p['kelas']) ?></td>
                                </tr>
                                <tr>
                                    <td class="lbl">Ruang / Meja</td>
                                    <td>:</td>
                                    <td><strong><?= esc($roomName) ?></strong> / Meja #<?= $p['nomor_meja'] ?: '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="lbl">Sesi Ujian</td>
                                    <td>:</td>
                                    <td><span style="background:#e2e8f0;padding:1px 6px;border-radius:3px;font-weight:bold">Sesi <?= $p['sesi'] ?? 1 ?></span></td>
                                </tr>
                                <?php if(!empty($p['password'])): ?>
                                <tr>
                                    <td class="lbl">Password</td>
                                    <td>:</td>
                                    <td><strong style="font-family:monospace;letter-spacing:1px;color:#1e293b"><?= esc($p['password']) ?></strong></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </td>
                        <td style="vertical-align: top; width: 28%; text-align: right;">
                            <div class="foto-box">FOTO<br>2 x 3</div>
                        </td>
                    </tr>
                </table>

                <div class="ttd-box">
                    <span>Ketua Panitia Ujian,</span>
                    <br><br><br>
                    <span><strong>( ................................... )</strong></span>
                </div>
                <div class="clear"></div>
            </div>
        </td>
        <?php endforeach; ?>
        <?php if(count($row) === 1): ?>
            <td class="card-td"></td>
        <?php endif; ?>
    </tr>
    <?php endforeach; ?>
</table>

<?php if($chunkIdx < count($chunks) - 1): ?>
    <div class="page-break"></div>
<?php endif; ?>

<?php endforeach; ?>

</body>
</html>
