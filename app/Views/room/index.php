<?php echo view('layout/header', ['title' => $title]); ?>

<!-- Header & Quick Actions -->
<div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
        <h2 style="font-size:1.25rem;font-weight:700;color:#fff">
            <i class="fas fa-door-open" style="color:var(--accent);margin-right:8px"></i> Manajemen Ruangan &amp; Denah Ujian
        </h2>
        <p style="font-size:13px;color:var(--muted)">Kelola master data ruang ujian, kapasitas tempat duduk, dan cetak denah layout meja per sesi</p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        <?php if(!empty($exams)): ?>
            <button onclick="document.getElementById('modalPilihDenah').classList.add('show')" class="btn btn-outline" style="border-color:rgba(99,102,241,0.4);color:var(--accent)">
                <i class="fas fa-th-large"></i> Cetak Denah Tempat Duduk
            </button>
        <?php endif; ?>
        <a href="<?= base_url('room/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Ruang Baru
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Daftar Ruangan Terdaftar</span>
        <span class="badge badge-draft"><?= count($rooms) ?> Ruangan</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:50px">No</th>
                    <th>Nama Ruangan</th>
                    <th>Gedung</th>
                    <th>Lantai</th>
                    <th>Kapasitas</th>
                    <th>Keterangan</th>
                    <th style="text-align:right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $i=1; foreach($rooms as $r): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><strong><?= esc($r['nama_ruang']) ?></strong></td>
                    <td><?= esc($r['gedung'] ?: 'Gedung Utama') ?></td>
                    <td>Lantai <?= esc($r['lantai'] ?: 1) ?></td>
                    <td><span class="badge badge-selesai"><?= esc($r['kapasitas']) ?> Kursi</span></td>
                    <td><small style="color:var(--muted)"><?= esc($r['keterangan'] ?: '-') ?></small></td>
                    <td style="text-align:right">
                        <div style="display:inline-flex;gap:6px">
                            <?php if(!empty($exams)): ?>
                                <a href="<?= base_url('room/denah/'.$exams[0]['id'].'/'.$r['id']) ?>" class="btn btn-sm btn-outline" style="color:var(--accent);border-color:rgba(99,102,241,0.3)" title="Lihat Denah">
                                    <i class="fas fa-th-large"></i> Denah
                                </a>
                            <?php endif; ?>
                            <a href="<?= base_url('room/edit/'.$r['id']) ?>" class="btn btn-sm btn-outline" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= base_url('room/delete/'.$r['id']) ?>" class="btn btn-sm btn-danger"
                               onclick="event.preventDefault(); showConfirm('<?= base_url('room/delete/'.$r['id']) ?>', 'Hapus Ruangan', 'Yakin ingin menghapus ruangan <?= esc(addslashes($r['nama_ruang'])) ?>?', 'btn-danger')" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($rooms)): ?>
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:var(--muted)">
                        <i class="fas fa-door-closed" style="font-size:32px;opacity:0.3;margin-bottom:8px;display:block"></i>
                        Belum ada ruangan ujian yang terdaftar. Silakan tambahkan ruang baru.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Dialog Pilih Ujian & Ruang untuk Cetak Denah -->
<?php if(!empty($exams)): ?>
<div class="confirm-overlay" id="modalPilihDenah">
    <div class="confirm-box" style="max-width:440px">
        <div class="confirm-title"><i class="fas fa-th-large" style="color:var(--accent)"></i> Pilih Ujian &amp; Ruangan</div>
        <p class="confirm-msg" style="margin-bottom:16px">Pilih paket ujian dan ruangan yang ingin dilihat denah tata letak tempat duduknya:</p>
        <div class="form-group">
            <label class="form-label">Paket Ujian</label>
            <select id="pilihExamId" class="form-control">
                <?php foreach($exams as $e): ?>
                    <option value="<?= $e['id'] ?>"><?= esc($e['nama_ujian']) ?> (<?= esc($e['tahun']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Ruangan</label>
            <select id="pilihRoomId" class="form-control">
                <?php foreach($rooms as $r): ?>
                    <option value="<?= $r['id'] ?>"><?= esc($r['nama_ruang']) ?> (Kapasitas: <?= $r['kapasitas'] ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="confirm-actions" style="margin-top:20px">
            <button class="btn btn-outline" onclick="document.getElementById('modalPilihDenah').classList.remove('show')">Batal</button>
            <button class="btn btn-primary" onclick="bukaDenah()">
                <i class="fas fa-arrow-right"></i> Buka Denah
            </button>
        </div>
    </div>
</div>
<script>
function bukaDenah() {
    var examId = document.getElementById('pilihExamId').value;
    var roomId = document.getElementById('pilihRoomId').value;
    location.href = '<?= base_url('room/denah') ?>/' + examId + '/' + roomId;
}
</script>
<?php endif; ?>

<?php echo view('layout/footer'); ?>
