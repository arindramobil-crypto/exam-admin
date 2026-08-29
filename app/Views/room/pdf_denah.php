<!DOCTYPE html><html><head><style>
body { font-family: sans-serif; text-align: center; }
.meja { width: 140px; height: 70px; border: 2px solid #000; display: inline-block; margin: 10px; padding: 5px; box-sizing: border-box; font-size: 11px; }
.meja strong { display: block; font-size: 14px; margin-bottom: 5px; }
.pengawas { border: 2px dashed #000; padding: 10px; margin: 20px auto; width: 200px; font-weight: bold; }
</style></head><body>
<h2>DENAH TEMPAT DUDUK - <?= esc($room['nama_ruang']) ?></h2>
<h3><?= esc($exam['nama_ujian']) ?></h3>
<div class="pengawas">MEJA PENGAWAS</div>
<div style="margin-top: 30px;">
<?php foreach($participants as $p): ?>
<div class="meja"><strong>Meja <?= $p['nomor_meja'] ?></strong><?= $p['nomor_peserta'] ?><br><?= substr($p['nama'], 0, 15) ?></div>
<?php endforeach ?>
</div></body></html>
