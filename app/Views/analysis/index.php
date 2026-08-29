<?php echo view('layout/header', ['title' => $title]); ?>

<div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
        <h2 style="font-size:1.25rem;font-weight:700;color:#fff"><i class="fas fa-microscope" style="color:var(--accent);margin-right:8px"></i> Analisis Butir Soal Ujian</h2>
        <p style="font-size:13px;color:var(--muted)"><?= esc($exam['nama_ujian']) ?> (Tahun: <?= esc($exam['tahun']) ?>)</p>
    </div>
    <a href="<?= base_url('exam') ?>" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-book"></i> Pilih Mata Pelajaran untuk Analisis Butir Soal</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Mata Pelajaran</th>
                    <th>Kelas</th>
                    <th>Quiz Moodle</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($subjects as $s): ?>
                <tr>
                    <td><strong><?= esc($s['nama_mapel']) ?></strong></td>
                    <td><span class="badge badge-draft"><?= esc($s['kelas']) ?></span></td>
                    <td>
                        <?php if($s['moodle_quiz_id']): ?>
                            <span class="badge badge-aktif"><i class="fas fa-check-circle" style="margin-right:4px"></i> Terhubung (Quiz #<?= $s['moodle_quiz_id'] ?>)</span>
                        <?php else: ?>
                            <span class="badge badge-draft">Belum Terhubung</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($s['moodle_quiz_id']): ?>
                            <a href="<?= base_url('analysis/detail/'.$exam['id'].'/'.$s['id']) ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-chart-pie"></i> Lihat Analisis Butir Soal
                            </a>
                        <?php else: ?>
                            <span style="color:var(--muted);font-size:12px">Tautkan Quiz Moodle terlebih dahulu</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($subjects)): ?>
                <tr>
                    <td colspan="4" style="text-align:center;padding:40px;color:var(--muted)">Belum ada mata pelajaran pada ujian ini.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php echo view('layout/footer'); ?>
