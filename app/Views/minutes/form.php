<?php echo view('layout/header', ['title' => $title]); ?>
<div class="card" style="max-width:700px"><div class="card-header"><span class="card-title"><?= $title ?></span></div>
<div class="card-body">
<form action="<?= base_url('minutes/save') ?>" method="POST">
<?= csrf_field() ?>
<input type="hidden" name="exam_id" value="<?= $exam['id'] ?>">
<input type="hidden" name="subject_id" value="<?= $subject['id'] ?>">
<input type="hidden" name="room_id" value="<?= $room['id'] ?>">
<div style="background:rgba(99,102,241,0.05);padding:15px;border-radius:8px;margin-bottom:20px;border:1px solid rgba(99,102,241,0.2)">
<h4 style="margin:0 0 10px 0;color:var(--accent)">Informasi Ujian</h4>
<p style="margin:0 0 5px 0"><strong>Mapel:</strong> <?= esc($subject['nama_mapel']) ?></p>
<p style="margin:0 0 5px 0"><strong>Ruang:</strong> <?= esc($room['nama_ruang']) ?></p>
<p style="margin:0"><strong>Peserta Hadir:</strong> <?= $hadir ?> | <strong>Absen:</strong> <?= $absen ?></p>
</div>
<div class="form-group"><label class="form-label">Nama Pengawas</label><input type="text" name="pengawas" class="form-control" value="<?= $existing ? esc($existing['pengawas']) : '' ?>" required></div>
<div class="form-group"><label class="form-label">Proktor</label><input type="text" name="proktor" class="form-control" value="<?= $existing ? esc($existing['proktor']) : '' ?>" required></div>
<div class="form-group"><label class="form-label">Catatan Selama Ujian (Kejadian Penting)</label><textarea name="catatan" class="form-control" rows="5" placeholder="Tuliskan kejadian penting selama ujian berlangsung (misal: peserta atas nama A sakit, dll)"><?= $existing ? esc($existing['catatan']) : '' ?></textarea></div>
<div style="margin-top:20px"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Berita Acara</button> <a href="<?= base_url('minutes/'.$exam['id']) ?>" class="btn btn-outline">Batal</a></div>
</form></div></div>
<?php echo view('layout/footer'); ?>
