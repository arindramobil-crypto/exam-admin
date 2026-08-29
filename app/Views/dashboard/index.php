<?php echo view('layout/header', ['title' => $title]); ?>

<!-- Moodle Status Banner -->
<?php if(!empty($moodle_status)): ?>
    <div style="background:<?= $moodle_status['success'] ? 'linear-gradient(135deg, rgba(99,102,241,0.12), rgba(139,92,246,0.06))' : 'rgba(239,68,68,0.1)' ?>;border:1px solid <?= $moodle_status['success'] ? 'rgba(99,102,241,0.25)' : 'rgba(239,68,68,0.25)' ?>;border-radius:12px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div style="display:flex;align-items:center;gap:14px">
            <div style="width:40px;height:40px;border-radius:10px;background:<?= $moodle_status['success'] ? 'var(--accent)' : 'var(--danger)' ?>;display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div>
                <div style="font-weight:700;font-size:14px;color:#fff;display:flex;align-items:center;gap:8px">
                    Integrasi Database Moodle
                    <?php if($moodle_status['success']): ?>
                        <span class="badge badge-aktif"><i class="fas fa-check-circle" style="margin-right:4px"></i> Terhubung</span>
                    <?php else: ?>
                        <span class="badge badge-absen"><i class="fas fa-times-circle" style="margin-right:4px"></i> Terputus</span>
                    <?php endif; ?>
                </div>
                <div style="font-size:12px;color:var(--muted);margin-top:2px">
                    <?php if($moodle_status['success']): ?>
                        Tersambung ke database <code><?= esc($moodle_status['database']) ?></code> &bull; <?= $moodle_status['stats']['courses'] ?? 0 ?> Kursus &bull; <?= $moodle_status['stats']['quizzes'] ?? 0 ?> Quiz &bull; <?= $moodle_status['stats']['users'] ?? 0 ?> Siswa
                    <?php else: ?>
                        Koneksi ke Moodle gagal: <?= esc($moodle_status['message']) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div style="display:flex;gap:8px">
            <a href="<?= base_url('moodle') ?>" class="btn btn-sm btn-outline"><i class="fas fa-cog"></i> Pengaturan Moodle</a>
            <?php if($moodle_status['success']): ?>
                <a href="<?= base_url('moodle/courses') ?>" class="btn btn-sm btn-primary"><i class="fas fa-book-open"></i> Eksplorasi Kursus</a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Ujian</div>
        <div class="stat-value"><?= $total_exam ?></div>
        <i class="stat-icon fas fa-clipboard-list"></i>
    </div>
    <div class="stat-card">
        <div class="stat-label">Ujian Aktif</div>
        <div class="stat-value" style="color:var(--success)"><?= $exam_aktif ?></div>
        <i class="stat-icon fas fa-play-circle"></i>
    </div>
    <div class="stat-card">
        <div class="stat-label">Ujian Selesai</div>
        <div class="stat-value" style="color:var(--accent)"><?= $exam_selesai ?></div>
        <i class="stat-icon fas fa-check-circle"></i>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Peserta</div>
        <div class="stat-value"><?= $total_peserta ?></div>
        <i class="stat-icon fas fa-users"></i>
    </div>
    <div class="stat-card">
        <div class="stat-label">Ruang Ujian</div>
        <div class="stat-value"><?= $total_ruang ?></div>
        <i class="stat-icon fas fa-door-open"></i>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Ujian Terbaru</span>
        <div style="display:flex;gap:8px">
            <a href="<?= base_url('exam/create') ?>" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Buat Ujian Baru</a>
            <a href="<?= base_url('exam') ?>" class="btn btn-sm btn-outline">Lihat Semua</a>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama Ujian</th>
                    <th>Tahun / Semester</th>
                    <th>Periode Tanggal</th>
                    <th>Status</th>
                    <th>Aksi & Monitoring</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($exams_recent as $e): ?>
                <tr>
                    <td><strong><?= esc($e['nama_ujian']) ?></strong></td>
                    <td><?= esc($e['tahun']) ?> / Sem <?= esc($e['semester']) ?></td>
                    <td><?= date('d M Y', strtotime($e['tgl_mulai'])) ?> - <?= date('d M Y', strtotime($e['tgl_selesai'])) ?></td>
                    <td>
                        <?php if($e['status'] === 'aktif'): ?>
                            <span class="badge badge-aktif"><i class="fas fa-play" style="margin-right:4px"></i> Aktif</span>
                        <?php elseif($e['status'] === 'selesai'): ?>
                            <span class="badge badge-selesai"><i class="fas fa-check" style="margin-right:4px"></i> Selesai</span>
                        <?php else: ?>
                            <span class="badge badge-draft"><i class="fas fa-file" style="margin-right:4px"></i> Draft</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap">
                            <a href="<?= base_url('exam/subjects/'.$e['id']) ?>" class="btn btn-sm btn-outline" title="Kelola Mapel"><i class="fas fa-list"></i> Mapel</a>
                            <a href="<?= base_url('participant/'.$e['id']) ?>" class="btn btn-sm btn-primary" title="Kelola Peserta"><i class="fas fa-users"></i> Peserta</a>
                            <a href="<?= base_url('dashboard/monitoring/'.$e['id']) ?>" class="btn btn-sm btn-warning" title="Live Monitoring Moodle"><i class="fas fa-desktop"></i> Monitor</a>
                            <a href="<?= base_url('grade/'.$e['id']) ?>" class="btn btn-sm btn-success" title="Rekap Nilai"><i class="fas fa-chart-bar"></i> Nilai</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($exams_recent)): ?>
                <tr>
                    <td colspan="5" style="text-align:center;padding:40px;color:var(--muted)">
                        <i class="fas fa-clipboard" style="font-size:32px;opacity:0.3;margin-bottom:8px;display:block"></i>
                        Belum ada data ujian. <a href="<?= base_url('exam/create') ?>" style="color:var(--accent);font-weight:600">Buat ujian pertama</a>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php echo view('layout/footer'); ?>
