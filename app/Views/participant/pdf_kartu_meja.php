<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Kartu Meja Peserta Ujian</title>
<style>
body { font-family: sans-serif; font-size: 9pt; color: #000; margin: 0; padding: 0; }
.card-grid { width: 100%; border-collapse: collapse; }
.card-cell { width: 50%; vertical-align: top; padding: 8px; }
.card-box {
    border: 2px solid #1e293b;
    border-radius: 8px;
    padding: 10px 12px;
    background: #fff;
    height: 190px;
    position: relative;
    box-sizing: border-box;
}
.card-header {
    text-align: center;
    border-bottom: 1.5px solid #334155;
    padding-bottom: 6px;
    margin-bottom: 8px;
}
.card-header h3 { margin: 0; font-size: 10pt; font-weight: bold; text-transform: uppercase; }
.card-header p { margin: 2px 0 0 0; font-size: 7.5pt; color: #475569; }
.nomor-meja-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #0f172a;
    color: #fff;
    font-size: 14pt;
    font-weight: bold;
    padding: 4px 10px;
    border-radius: 6px;
}
.info-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; }
.info-table td { padding: 3px 0; border: none; vertical-align: top; }
.info-label { width: 85px; color: #475569; font-weight: 500; }
.info-val { font-weight: bold; color: #0f172a; }
.card-footer {
    border-top: 1px dashed #cbd5e1;
    margin-top: 8px;
    padding-top: 4px;
    display: flex;
    justify-content: space-between;
    font-size: 7.5pt;
    color: #64748b;
}
.page-break { page-break-after: always; }
</style>
</head>
<body>

<?php
$chunks = array_chunk($participants, 6);
?>

<?php foreach($chunks as $chunkIdx => $pageParticipants): ?>
<table class="card-grid">
    <?php 
    $rows = array_chunk($pageParticipants, 2);
    foreach($rows as $row): 
    ?>
    <tr>
        <?php foreach($row as $p): ?>
        <?php 
            $roomName = '-';
            if (!empty($p['room_id'])) {
                foreach($rooms as $r) {
                    if ($r['id'] == $p['room_id']) { $roomName = $r['nama_ruang']; break; }
                }
            }
        ?>
        <td class="card-cell">
            <div class="card-box">
                <div class="card-header">
                    <h3>KARTU MEJA UJIAN CBT</h3>
                    <p><?= esc($exam['nama_ujian']) ?> &bull; TP. <?= esc($exam['tahun']) ?></p>
                </div>

                <div class="nomor-meja-badge">
                    #<?= $p['nomor_meja'] ?: '-' ?>
                </div>

                <table class="info-table">
                    <tr>
                        <td class="info-label">No. Peserta</td>
                        <td class="info-val">: <?= esc($p['nomor_peserta']) ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">Nama Siswa</td>
                        <td class="info-val">: <?= esc($p['nama']) ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">NIS / NISN</td>
                        <td class="info-val">: <?= esc($p['nis']) ?> <?= !empty($p['nisn']) ? '/ '.esc($p['nisn']) : '' ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">Kelas</td>
                        <td class="info-val">: <?= esc($p['kelas']) ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">Ruang / Sesi</td>
                        <td class="info-val">: <?= esc($roomName) ?> / Sesi <?= $p['sesi'] ?? 1 ?></td>
                    </tr>
                    <?php if(!empty($p['password'])): ?>
                    <tr>
                        <td class="info-label">Password CBT</td>
                        <td class="info-val">: <span style="font-family:monospace;letter-spacing:1px"><?= esc($p['password']) ?></span></td>
                    </tr>
                    <?php endif; ?>
                </table>

                <div class="card-footer">
                    <span>Tempelkan pada sudut kanan atas meja peserta.</span>
                </div>
            </div>
        </td>
        <?php endforeach; ?>
        <?php if(count($row) === 1): ?>
            <td class="card-cell"></td>
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
