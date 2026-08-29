<?php echo view('layout/header', ['title' => $title]); ?>
<div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between">
    <div>
        <h2 style="font-size:1.25rem;font-weight:700;color:#fff"><?= esc($exam['nama_ujian']) ?></h2>
        <p style="font-size:13px;color:var(--muted)">Tahun: <?= esc($exam['tahun']) ?> | Semester: <?= esc($exam['semester']) ?></p>
    </div>
    <a href="<?= base_url('exam') ?>" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Kembali ke Ujian</a>
</div>

<div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap">
    <div class="card" style="flex:1;min-width:320px">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-book-open"></i> Daftar Mata Pelajaran</span>
            <span class="badge badge-selesai"><?= count($subjects) ?> Mapel</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th>Koneksi Quiz Moodle</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($subjects as $s): ?>
                <tr>
                    <td>
                        <strong><?= esc($s['nama_mapel']) ?></strong>
                        <?php if(!empty($s['kode_mapel'])): ?>
                            <br><small style="color:var(--muted)"><?= esc($s['kode_mapel']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge badge-draft"><?= esc($s['kelas']) ?></span></td>
                    <td>
                        <?php if($s['moodle_quiz_id']): ?>
                            <span class="badge badge-aktif" title="Quiz ID: <?= $s['moodle_quiz_id'] ?>, Course ID: <?= $s['moodle_course_id'] ?>">
                                <i class="fas fa-check-circle" style="margin-right:4px"></i> Terhubung (Quiz #<?= $s['moodle_quiz_id'] ?>)
                            </span>
                        <?php else: ?>
                            <span class="badge badge-draft">
                                <i class="fas fa-exclamation-triangle" style="margin-right:4px"></i> Belum Terkait
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <a href="<?= base_url('participant/'.$exam['id']) ?>" class="btn btn-sm btn-outline" title="Kelola Peserta">
                                <i class="fas fa-users"></i>
                            </a>
                            <a href="<?= base_url('exam/subjects/delete/'.$s['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus mapel ini?')" title="Hapus Mapel">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($subjects)): ?>
                    <tr>
                        <td colspan="4" style="text-align:center;padding:30px;color:var(--muted)">
                            <i class="fas fa-inbox" style="font-size:24px;margin-bottom:8px;display:block;opacity:0.5"></i>
                            Belum ada mata pelajaran. Silakan tambahkan pada form di samping.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card" style="width:380px;min-width:320px">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-plus-circle"></i> Tambah Mapel</span>
        </div>
        <div class="card-body">
            <form action="<?= base_url('exam/subjects/'.$exam['id'].'/add') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label class="form-label">Nama Mata Pelajaran <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="nama_mapel" class="form-control" placeholder="Contoh: Matematika Wajib" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kelas / Tingkat <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="kelas" class="form-control" placeholder="Contoh: 10 IPA, 11 IPS, dll" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kode Mapel (Opsional)</label>
                    <input type="text" name="kode_mapel" class="form-control" placeholder="Contoh: MTK-10">
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-plug" style="color:var(--accent)"></i> Tautkan ke Quiz Moodle</label>
                    <select name="moodle_quiz_id" class="form-control" id="moodle_quiz_select">
                        <option value="">-- Pilih Quiz dari Moodle (Opsional) --</option>
                        <?php foreach($quizzes as $q): ?>
                            <option value="<?= $q['id'] ?>" data-course="<?= $q['course_id'] ?>">
                                <?= esc($q['name']) ?> (Kursus: <?= esc($q['course_name']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small style="color:var(--muted);font-size:11px;margin-top:4px;display:block">
                        <?php if(empty($quizzes)): ?>
                            <span style="color:var(--warning)"><i class="fas fa-info-circle"></i> Tidak ada Quiz Moodle aktif atau belum dibuat di Moodle.</span>
                        <?php else: ?>
                            Pilih Quiz untuk sinkronisasi peserta otomatis dan penarikan nilai.
                        <?php endif; ?>
                    </small>
                </div>
                <input type="hidden" name="moodle_course_id" id="course_id_input">
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
                    <i class="fas fa-plus"></i> Simpan Mata Pelajaran
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('moodle_quiz_select').addEventListener('change', function() {
    var selected = this.options[this.selectedIndex];
    var courseId = selected.getAttribute('data-course') || '';
    document.getElementById('course_id_input').value = courseId;
});
</script>
<?php echo view('layout/footer'); ?>
