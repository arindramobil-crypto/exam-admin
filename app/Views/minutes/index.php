<?php echo view('layout/header', ['title' => $title]); ?>

<!-- Header & Quick Actions -->
<div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
        <h2 style="font-size:1.25rem;font-weight:700;color:#fff">
            <i class="fas fa-file-alt" style="color:var(--accent);margin-right:8px"></i> Berita Acara Pelaksanaan Ujian
        </h2>
        <p style="font-size:13px;color:var(--muted)">
            <?= esc($exam['nama_ujian']) ?> &bull; Tahun: <?= esc($exam['tahun']) ?> &bull; Semester <?= esc($exam['semester']) ?>
        </p>
    </div>
    <div>
        <a href="<?= base_url('exam') ?>" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Kembali ke Ujian
        </a>
    </div>
</div>

<!-- Selector Form Buat/Edit Berita Acara Baru -->
<div class="card" style="margin-bottom:24px;border:1px solid rgba(99,102,241,0.3)">
    <div class="card-header" style="background:rgba(99,102,241,0.06)">
        <span class="card-title" style="font-size:13.5px">
            <i class="fas fa-edit" style="color:var(--accent)"></i> Input atau Buka Berita Acara Berdasarkan Mapel &amp; Ruangan
        </span>
    </div>
    <div class="card-body" style="padding:18px">
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)) 160px;gap:14px;align-items:flex-end">
            <div class="form-group" style="margin:0">
                <label class="form-label">Pilih Mata Pelajaran *</label>
                <select id="subjSelect" class="form-control">
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    <?php foreach($subjects as $s): ?>
                        <option value="<?= $s['id'] ?>">
                            <?= esc($s['nama_mapel']) ?> (Kelas <?= esc($s['kelas']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin:0">
                <label class="form-label">Pilih Ruang Ujian *</label>
                <select id="roomSelect" class="form-control">
                    <option value="">-- Pilih Ruang Ujian --</option>
                    <?php foreach($rooms as $r): ?>
                        <option value="<?= $r['id'] ?>">
                            <?= esc($r['nama_ruang']) ?> (Kapasitas: <?= $r['kapasitas'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="button" class="btn btn-primary" onclick="buatBa()" style="width:100%;justify-content:center;padding:10px">
                <i class="fas fa-pen-alt"></i> Buka Berita Acara
            </button>
        </div>
    </div>
</div>

<script>
function buatBa() {
    var s = document.getElementById('subjSelect').value;
    var r = document.getElementById('roomSelect').value;
    if (!s || !r) {
        showToast('Pilih Mata Pelajaran dan Ruang Ujian terlebih dahulu!', 'warning');
        return;
    }
    location.href = '<?= base_url('minutes/form/' . $exam['id']) ?>/' + s + '/' + r;
}
</script>

<!-- Tabel Berita Acara yang Tersimpan -->
<div class="card">
    <div class="card-header">
        <span class="card-title">Daftar Berita Acara Tersimpan</span>
        <span class="badge badge-draft"><?= count($minutes) ?> Dokumen</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:50px">No</th>
                    <th>Mata Pelajaran</th>
                    <th>Ruangan</th>
                    <th>Waktu Ujian</th>
                    <th>Kehadiran Peserta</th>
                    <th>Pengawas &amp; Proktor</th>
                    <th>Status</th>
                    <th style="text-align:right">Aksi &amp; Cetak</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach($minutes as $m): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td>
                        <strong><?= esc($m['nama_mapel'] ?? '-') ?></strong>
                        <small style="color:var(--muted);display:block">Kelas <?= esc($m['kelas'] ?? '-') ?></small>
                    </td>
                    <td>
                        <span class="badge badge-draft" style="font-size:12px">
                            <i class="fas fa-door-open" style="margin-right:4px"></i> <?= esc($m['nama_ruang'] ?? '-') ?>
                        </span>
                    </td>
                    <td>
                        <small>
                            <?= esc($m['waktu_mulai'] ? date('H:i', strtotime($m['waktu_mulai'])) : '07:30') ?> s/d
                            <?= esc($m['waktu_selesai'] ? date('H:i', strtotime($m['waktu_selesai'])) : '09:00') ?>
                        </small>
                    </td>
                    <td>
                        <span class="badge badge-aktif" title="Hadir"><i class="fas fa-check" style="margin-right:3px"></i> <?= (int)$m['jml_peserta_hadir'] ?> Hadir</span>
                        <?php if((int)$m['jml_peserta_absen'] > 0): ?>
                            <span class="badge badge-absen" title="Tidak Hadir" style="margin-left:3px"><i class="fas fa-times" style="margin-right:3px"></i> <?= (int)$m['jml_peserta_absen'] ?> Absen</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="font-size:12.5px;color:#fff">
                            <i class="fas fa-user-shield" style="color:var(--accent);margin-right:4px;font-size:11px"></i>
                            <?= esc($m['nama_pengawas1'] ?: '-') ?>
                        </div>
                        <?php if(!empty($m['nama_pengawas2'])): ?>
                            <small style="color:var(--muted)">
                                <i class="fas fa-laptop" style="color:var(--warning);margin-right:4px;font-size:10px"></i>
                                <?= esc($m['nama_pengawas2']) ?>
                            </small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($m['status'] === 'final'): ?>
                            <span class="badge badge-aktif"><i class="fas fa-check-double" style="margin-right:3px"></i> Final</span>
                        <?php else: ?>
                            <span class="badge badge-draft"><i class="fas fa-edit" style="margin-right:3px"></i> Draft</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:right">
                        <div style="display:inline-flex;gap:6px">
                            <a href="<?= base_url('minutes/form/'.$exam['id'].'/'.$m['subject_id'].'/'.$m['room_id']) ?>" class="btn btn-sm btn-outline" title="Edit Data">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="<?= base_url('minutes/print/'.$m['id']) ?>" target="_blank" class="btn btn-sm btn-success" title="Cetak PDF">
                                <i class="fas fa-print"></i> Cetak PDF
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($minutes)): ?>
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:var(--muted)">
                        <i class="fas fa-file-invoice" style="font-size:32px;opacity:0.3;margin-bottom:8px;display:block"></i>
                        Belum ada dokumen Berita Acara yang dibuat. Silakan pilih Mapel dan Ruang di atas.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php echo view('layout/footer'); ?>
