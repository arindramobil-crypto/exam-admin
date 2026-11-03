<?php echo view('layout/header', ['title' => $title]); ?>

<!-- Header & Quick Actions -->
<div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
        <h2 style="font-size:1.25rem;font-weight:700;color:#fff">
            <i class="fas fa-chart-bar" style="color:var(--accent);margin-right:8px"></i> Nilai Ujian: <?= esc($exam['nama_ujian']) ?>
        </h2>
        <p style="font-size:13px;color:var(--muted)">
            Tahun: <?= esc($exam['tahun']) ?> &bull; Semester: <?= esc($exam['semester']) ?> &bull; <?= count($subjects) ?> Mata Pelajaran
        </p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        <a href="<?= base_url('grade/summary/'.$exam['id']) ?>" class="btn btn-primary">
            <i class="fas fa-table"></i> Rekap Gabungan Semua Mapel
        </a>
        <a href="<?= base_url('grade/export-summary-excel/'.$exam['id']) ?>" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Export Excel Semua Mapel
        </a>
        <a href="<?= base_url('exam') ?>" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Daftar Mata Pelajaran Ujian</span>
        <span class="badge badge-draft"><?= count($subjects) ?> Mapel Terdaftar</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:50px">No</th>
                    <th>Mata Pelajaran</th>
                    <th>Kelas</th>
                    <th>Durasi</th>
                    <th>Status Moodle Quiz</th>
                    <th>Aksi Unduh &amp; Rekap</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach($subjects as $s): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><strong><?= esc($s['nama_mapel']) ?></strong></td>
                    <td><span class="badge badge-draft"><?= esc($s['kelas']) ?></span></td>
                    <td><?= $s['durasi_menit'] ?? 90 ?> Menit</td>
                    <td>
                        <?php if($s['moodle_quiz_id']): ?>
                            <span class="badge badge-aktif"><i class="fas fa-check-circle" style="margin-right:4px"></i> Terhubung (Quiz #<?= $s['moodle_quiz_id'] ?>)</span>
                        <?php else: ?>
                            <span class="badge badge-draft"><i class="fas fa-times-circle" style="margin-right:4px"></i> Belum Dihubungkan</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap">
                            <?php if($s['moodle_quiz_id']): ?>
                                <a href="<?= base_url('grade/view/'.$exam['id'].'/'.$s['id']) ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Lihat Nilai
                                </a>
                                <a href="<?= base_url('grade/export-excel/'.$exam['id'].'/'.$s['id']) ?>" class="btn btn-sm btn-success">
                                    <i class="fas fa-file-excel"></i> Excel
                                </a>
                                <a href="<?= base_url('grade/export-pdf/'.$exam['id'].'/'.$s['id']) ?>" target="_blank" class="btn btn-sm btn-danger">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>
                            <?php else: ?>
                                <a href="<?= base_url('exam/subjects/'.$exam['id']) ?>" class="btn btn-sm btn-outline" style="font-size:11px">
                                    <i class="fas fa-link"></i> Hubungkan Quiz
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($subjects)): ?>
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:var(--muted)">
                        Belum ada mata pelajaran pada ujian ini. <a href="<?= base_url('exam/subjects/'.$exam['id']) ?>" style="color:var(--accent)">Tambah Mapel</a>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php echo view('layout/footer'); ?>
