<?php echo view('layout/header', ['title' => $title]); ?>
<div class="card" style="max-width:500px"><div class="card-header"><span class="card-title"><?= $title ?></span></div>
<div class="card-body">
<form action="<?= base_url('room/' . ($room ? 'update/' . $room['id'] : 'store')) ?>" method="POST">
<?= csrf_field() ?>
<div class="form-group"><label class="form-label">Nama Ruang</label><input type="text" name="nama_ruang" class="form-control" value="<?= $room ? esc($room['nama_ruang']) : '' ?>" required></div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:15px">
<div class="form-group"><label class="form-label">Gedung</label><input type="text" name="gedung" class="form-control" value="<?= $room ? esc($room['gedung']) : '' ?>"></div>
<div class="form-group"><label class="form-label">Lantai</label><input type="text" name="lantai" class="form-control" value="<?= $room ? esc($room['lantai']) : '' ?>"></div>
</div>
<div class="form-group"><label class="form-label">Kapasitas (Jumlah Kursi)</label><input type="number" name="kapasitas" class="form-control" value="<?= $room ? esc($room['kapasitas']) : '' ?>" required></div>
<div class="form-group"><label class="form-label">Keterangan Tambahan</label><textarea name="keterangan" class="form-control" rows="2"><?= $room ? esc($room['keterangan']) : '' ?></textarea></div>
<div style="margin-top:20px"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button> <a href="<?= base_url('room') ?>" class="btn btn-outline">Batal</a></div>
</form></div></div>
<?php echo view('layout/footer'); ?>
