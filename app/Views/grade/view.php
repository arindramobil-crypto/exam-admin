<?php echo view('layout/header', ['title' => $title]); ?>

<?php
$totalSiswa = count($grades);
$gradesList = array_map(fn($g) => (float)$g['grade'], $grades);
$avgGrade = $totalSiswa > 0 ? array_sum($gradesList) / $totalSiswa : 0;
$maxGrade = $totalSiswa > 0 ? max($gradesList) : 0;
$minGrade = $totalSiswa > 0 ? min($gradesList) : 0;
$tuntas = count(array_filter($gradesList, fn($v) => $v >= 75));
$tidakTuntas = $totalSiswa - $tuntas;
?>

<div style="margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
    <div>
        <h2 style="font-size:1.25rem;font-weight:700;color:#fff"><i class="fas fa-chart-bar" style="color:var(--accent);margin-right:8px"></i> Rekap Nilai: <?= esc($subject['nama_mapel']) ?></h2>
        <p style="font-size:13px;color:var(--muted)">
            <?= esc($exam['nama_ujian']) ?> | Kelas <?= esc($subject['kelas']) ?> | Quiz Moodle #<?= $subject['moodle_quiz_id'] ?>
        </p>
    </div>
    <div style="display:flex;gap:8px">
        <a href="<?= base_url('grade/'.$exam['id']) ?>" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Kembali</a>
        <a href="<?= base_url('grade/export-excel/'.$exam['id'].'/'.$subject['id']) ?>" class="btn btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
        <a href="<?= base_url('grade/export-pdf/'.$exam['id'].'/'.$subject['id']) ?>" target="_blank" class="btn btn-danger"><i class="fas fa-file-pdf"></i> Export PDF</a>
    </div>
</div>

<!-- Stats Card Grid -->
<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(160px, 1fr));gap:16px;margin-bottom:24px">
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:16px">
        <div style="font-size:12px;color:var(--muted)">Total Peserta</div>
        <div style="font-size:24px;font-weight:700;color:#fff;margin-top:4px"><?= $totalSiswa ?></div>
    </div>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:16px">
        <div style="font-size:12px;color:var(--muted)">Rata-Rata Nilai</div>
        <div style="font-size:24px;font-weight:700;color:var(--accent);margin-top:4px"><?= number_format($avgGrade, 2) ?></div>
    </div>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:16px">
        <div style="font-size:12px;color:var(--muted)">Nilai Tertinggi</div>
        <div style="font-size:24px;font-weight:700;color:var(--success);margin-top:4px"><?= number_format($maxGrade, 2) ?></div>
    </div>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:16px">
        <div style="font-size:12px;color:var(--muted)">Nilai Terendah</div>
        <div style="font-size:24px;font-weight:700;color:var(--warning);margin-top:4px"><?= number_format($minGrade, 2) ?></div>
    </div>
    <div style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.25);border-radius:10px;padding:16px">
        <div style="font-size:12px;color:var(--success)">Tuntas (≥ 75)</div>
        <div style="font-size:24px;font-weight:700;color:var(--success);margin-top:4px"><?= $tuntas ?></div>
    </div>
    <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.25);border-radius:10px;padding:16px">
        <div style="font-size:12px;color:var(--danger)">Belum Tuntas (&lt; 75)</div>
        <div style="font-size:24px;font-weight:700;color:var(--danger);margin-top:4px"><?= $tidakTuntas ?></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Daftar Nilai Siswa</span>
        <span class="badge badge-selesai"><?= $totalSiswa ?> Siswa</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:60px">No</th>
                    <th>Nama Lengkap</th>
                    <th>Username / NIS</th>
                    <th>Nilai Akhir Moodle</th>
                    <th>Keterangan</th>
                    <th>Waktu Submit</th>
                </tr>
            </thead>
            <tbody>
                <?php $r = 1; foreach($grades as $g): ?>
                <tr>
                    <td><?= $r++ ?></td>
                    <td><strong><?= esc($g['firstname'] . ' ' . $g['lastname']) ?></strong></td>
                    <td>
                        <code><?= esc($g['username']) ?></code>
                        <?php if(!empty($g['idnumber'])): ?>
                            <small style="color:var(--muted);margin-left:4px">(<?= esc($g['idnumber']) ?>)</small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong style="font-size:16px;color:var(--accent)"><?= number_format($g['grade'], 2) ?></strong>
                    </td>
                    <td>
                        <?= $g['grade'] >= 75 ? '<span class="badge badge-aktif"><i class="fas fa-check" style="margin-right:4px"></i> TUNTAS</span>' : '<span class="badge badge-absen"><i class="fas fa-times" style="margin-right:4px"></i> BELUM TUNTAS</span>' ?>
                    </td>
                    <td>
                        <small style="color:var(--muted)"><?= !empty($g['timemodified']) ? date('d M Y H:i:s', $g['timemodified']) : '-' ?></small>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($grades)): ?>
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:var(--muted)">
                        <i class="fas fa-inbox" style="font-size:32px;opacity:0.4;display:block;margin-bottom:8px"></i>
                        Belum ada data nilai pada Quiz Moodle ini.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php echo view('layout/footer'); ?>
