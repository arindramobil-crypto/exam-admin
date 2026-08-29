<?php echo view('layout/header', ['title' => $title]); ?>

<?php
$items = $analysisData['items'] ?? [];
$totalSoal = count($items);
$mudah = count(array_filter($items, fn($x) => $x['category'] === 'Mudah'));
$sedang = count(array_filter($items, fn($x) => $x['category'] === 'Sedang'));
$sukar = count(array_filter($items, fn($x) => $x['category'] === 'Sukar'));
?>

<div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
        <h2 style="font-size:1.25rem;font-weight:700;color:#fff"><i class="fas fa-chart-pie" style="color:var(--accent);margin-right:8px"></i> Analisis Butir Soal: <?= esc($subject['nama_mapel']) ?></h2>
        <p style="font-size:13px;color:var(--muted)"><?= esc($exam['nama_ujian']) ?> | Kelas <?= esc($subject['kelas']) ?> | Quiz Moodle #<?= $subject['moodle_quiz_id'] ?></p>
    </div>
    <div style="display:flex;gap:8px">
        <a href="<?= base_url('analysis/export-excel/'.$exam['id'].'/'.$subject['id']) ?>" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Export Analisis (Excel)
        </a>
        <a href="<?= base_url('analysis/'.$exam['id']) ?>" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
</div>

<!-- Stat Cards -->
<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(160px, 1fr));gap:16px;margin-bottom:24px">
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:16px">
        <div style="font-size:12px;color:var(--muted)">Total Butir Soal</div>
        <div style="font-size:24px;font-weight:700;color:#fff;margin-top:4px"><?= $totalSoal ?></div>
    </div>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:16px">
        <div style="font-size:12px;color:var(--muted)">Siswa Selesai Tes</div>
        <div style="font-size:24px;font-weight:700;color:var(--accent);margin-top:4px"><?= $analysisData['total_attempts'] ?? 0 ?></div>
    </div>
    <div style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.25);border-radius:10px;padding:16px">
        <div style="font-size:12px;color:var(--success)">Kategori Mudah</div>
        <div style="font-size:24px;font-weight:700;color:var(--success);margin-top:4px"><?= $mudah ?></div>
    </div>
    <div style="background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.25);border-radius:10px;padding:16px">
        <div style="font-size:12px;color:var(--warning)">Kategori Sedang</div>
        <div style="font-size:24px;font-weight:700;color:var(--warning);margin-top:4px"><?= $sedang ?></div>
    </div>
    <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.25);border-radius:10px;padding:16px">
        <div style="font-size:12px;color:var(--danger)">Kategori Sukar</div>
        <div style="font-size:24px;font-weight:700;color:var(--danger);margin-top:4px"><?= $sukar ?></div>
    </div>
</div>

<!-- Tabel Butir Soal -->
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-list"></i> Rincian Tingkat Kesukaran Butir Soal</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:70px">No. Slot</th>
                    <th>Nama / Pertanyaan</th>
                    <th>Tipe Soal</th>
                    <th>Bobot Skor</th>
                    <th>Tingkat Kesukaran (Index)</th>
                    <th>Status Kategori</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $it): ?>
                <tr>
                    <td><strong>#<?= $it['slot'] ?></strong></td>
                    <td><strong><?= esc($it['name']) ?></strong></td>
                    <td><span class="badge badge-draft"><?= esc($it['qtype']) ?></span></td>
                    <td><?= $it['maxmark'] ?></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px">
                            <span style="font-family:monospace;font-weight:bold"><?= number_format($it['facility_index'], 2) ?></span>
                            <div class="progress" style="width:80px;height:6px">
                                <div class="progress-bar" style="width:<?= $it['facility_index'] * 100 ?>%"></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php if($it['category'] === 'Mudah'): ?>
                            <span class="badge badge-aktif"><i class="fas fa-check" style="margin-right:4px"></i> Mudah</span>
                        <?php elseif($it['category'] === 'Sedang'): ?>
                            <span class="badge badge-warning" style="background:rgba(245,158,11,0.15);color:var(--warning)">
                                <i class="fas fa-minus" style="margin-right:4px"></i> Sedang
                            </span>
                        <?php else: ?>
                            <span class="badge badge-absen"><i class="fas fa-exclamation" style="margin-right:4px"></i> Sukar</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($items)): ?>
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:var(--muted)">
                        <i class="fas fa-folder-open" style="font-size:36px;opacity:0.3;margin-bottom:8px;display:block"></i>
                        Belum ada data butir soal di Quiz Moodle ini.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php echo view('layout/footer'); ?>
