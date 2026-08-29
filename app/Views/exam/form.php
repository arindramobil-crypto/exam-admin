<?php echo view('layout/header', ['title' => $title]); ?>
<div class="card" style="max-width:600px"><div class="card-header"><span class="card-title"><?= $title ?></span></div>
<div class="card-body">
<form action="<?= base_url('exam/' . ($exam ? 'update/' . $exam['id'] : 'store')) ?>" method="POST">
<?= csrf_field() ?>
<div class="form-group"><label class="form-label">Nama Ujian</label><input type="text" name="nama_ujian" class="form-control" value="<?= $exam ? esc($exam['nama_ujian']) : '' ?>" required></div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:15px">
<div class="form-group"><label class="form-label">Tahun Ajaran</label><input type="text" name="tahun" class="form-control" placeholder="Misal: 2026/2027" value="<?= $exam ? esc($exam['tahun']) : '' ?>" required></div>
<div class="form-group"><label class="form-label">Semester</label><select name="semester" class="form-control" required><option value="1" <?= ($exam && $exam['semester']==1)?'selected':'' ?>>Ganjil (1)</option><option value="2" <?= ($exam && $exam['semester']==2)?'selected':'' ?>>Genap (2)</option></select></div>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:15px">
<div class="form-group"><label class="form-label">Tanggal Mulai</label><input type="date" name="tgl_mulai" class="form-control" value="<?= $exam ? $exam['tgl_mulai'] : '' ?>" required></div>
<div class="form-group"><label class="form-label">Tanggal Selesai</label><input type="date" name="tgl_selesai" class="form-control" value="<?= $exam ? $exam['tgl_selesai'] : '' ?>" required></div>
</div>
<?php if($exam): ?>
<div class="form-group"><label class="form-label">Status</label><select name="status" class="form-control" required>
<option value="draft" <?= $exam['status']=='draft'?'selected':'' ?>>Draft</option>
<option value="aktif" <?= $exam['status']=='aktif'?'selected':'' ?>>Aktif</option>
<option value="selesai" <?= $exam['status']=='selesai'?'selected':'' ?>>Selesai</option>
</select></div>
<?php endif ?>
<div class="form-group"><label class="form-label">Keterangan Tambahan</label><textarea name="keterangan" class="form-control" rows="3"><?= $exam ? esc($exam['keterangan']) : '' ?></textarea></div>
<div style="margin-top:20px"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button> <a href="<?= base_url('exam') ?>" class="btn btn-outline">Batal</a></div>
</form></div></div>
<?php echo view('layout/footer'); ?>
