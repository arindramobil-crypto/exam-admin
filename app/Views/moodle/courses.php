<?php echo view('layout/header', ['title' => $title]); ?>

<div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between">
    <div>
        <h2 style="font-size:1.25rem;font-weight:700;color:#fff">Eksplorasi Kursus & Quiz Moodle</h2>
        <p style="font-size:13px;color:var(--muted)">Daftar seluruh kursus, aktivitas quiz, dan peserta yang ada di database Moodle</p>
    </div>
    <div style="display:flex;gap:10px">
        <a href="<?= base_url('moodle') ?>" class="btn btn-outline"><i class="fas fa-sliders-h"></i> Pengaturan Moodle</a>
        <a href="<?= base_url('exam') ?>" class="btn btn-primary"><i class="fas fa-clipboard-list"></i> Buat Ujian</a>
    </div>
</div>

<?php if(!$status['success']): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i> <?= esc($status['message']) ?>
    </div>
<?php else: ?>
    <?php if(empty($courses)): ?>
        <div class="card">
            <div class="card-body" style="text-align:center;padding:50px;color:var(--muted)">
                <i class="fas fa-box-open" style="font-size:48px;opacity:0.3;margin-bottom:16px;display:block"></i>
                <h4 style="color:#fff;margin-bottom:8px">Belum Ada Kursus di Moodle</h4>
                <p>Silakan buat kursus dan quiz terlebih dahulu di aplikasi Moodle Anda.</p>
            </div>
        </div>
    <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:20px">
            <?php foreach($courses as $c): ?>
                <div class="card">
                    <div class="card-header" style="background:var(--surface2)">
                        <div>
                            <span style="font-weight:700;font-size:15px;color:#fff"><?= esc($c['fullname']) ?></span>
                            <span style="font-size:12px;color:var(--muted);margin-left:8px">(Kode: <?= esc($c['shortname']) ?> | ID Kursus: <?= $c['id'] ?>)</span>
                        </div>
                        <div>
                            <span class="badge badge-aktif"><i class="fas fa-users" style="margin-right:4px"></i> <?= $c['student_count'] ?> Siswa Terdaftar</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 style="color:var(--muted);font-size:12px;text-transform:uppercase;margin-bottom:12px">Daftar Quiz di Kursus Ini:</h6>
                        <?php if(empty($c['quizzes'])): ?>
                            <p style="color:var(--muted);font-size:13px;font-style:italic">Belum ada aktivitas quiz di kursus ini.</p>
                        <?php else: ?>
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nama Quiz</th>
                                            <th>Waktu Buka</th>
                                            <th>Waktu Tutup</th>
                                            <th>Durasi</th>
                                            <th>Nilai Maksimal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($c['quizzes'] as $q): ?>
                                        <tr>
                                            <td><strong>#<?= $q['id'] ?></strong></td>
                                            <td><strong style="color:var(--accent)"><?= esc($q['name']) ?></strong></td>
                                            <td><?= $q['timeopen'] ? date('d M Y H:i', $q['timeopen']) : '<span class="badge badge-draft">Tanpa Batas</span>' ?></td>
                                            <td><?= $q['timeclose'] ? date('d M Y H:i', $q['timeclose']) : '<span class="badge badge-draft">Tanpa Batas</span>' ?></td>
                                            <td><?= $q['timelimit'] ? ($q['timelimit'] / 60) . ' menit' : 'Tidak dibatasi' ?></td>
                                            <td><span class="badge badge-selesai"><?= round($q['grade'], 2) ?></span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php echo view('layout/footer'); ?>
