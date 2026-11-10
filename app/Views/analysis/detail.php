<?php echo view('layout/header', ['title' => $title]); ?>

<?php
$items           = $analysisData['items'] ?? [];
$totalSoal       = count($items);
$totalAttempts   = $analysisData['total_attempts'] ?? 0;
$avgFacility     = $analysisData['avg_facility'] ?? 0;
$avgDiscrim      = $analysisData['avg_discrimination'] ?? 0;
$mudah           = $analysisData['easy_count'] ?? 0;
$sedang          = $analysisData['medium_count'] ?? 0;
$sukar           = $analysisData['hard_count'] ?? 0;
$diterima        = $analysisData['accepted_count'] ?? 0;
$revisi          = $analysisData['revised_count'] ?? 0;
$ditolak         = $analysisData['rejected_count'] ?? 0;
$isSimulated     = $analysisData['is_simulated'] ?? false;
?>

<div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
            <h2 style="font-size:1.25rem;font-weight:700;color:#fff;margin:0">
                <i class="fas fa-chart-pie" style="color:var(--accent);margin-right:8px"></i> Analisis Butir Soal: <?= esc($subject['nama_mapel']) ?>
            </h2>
            <?php if($isSimulated): ?>
                <span class="badge" style="background:rgba(245,158,11,0.15);color:var(--warning);border:1px solid rgba(245,158,11,0.3)">
                    <i class="fas fa-info-circle" style="margin-right:4px"></i> Mode Simulasi (Belum ada soal/peserta di Moodle)
                </span>
            <?php else: ?>
                <span class="badge badge-aktif">
                    <i class="fas fa-database" style="margin-right:4px"></i> Data Nyata Moodle (<?= $totalAttempts ?> Siswa)
                </span>
            <?php endif; ?>
        </div>
        <p style="font-size:13px;color:var(--muted);margin-top:4px;margin-bottom:0">
            <?= esc($exam['nama_ujian']) ?> | Kelas <?= esc($subject['kelas']) ?> | Quiz Moodle #<?= $subject['moodle_quiz_id'] ?>
        </p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        <a href="<?= base_url('analysis/export-pdf/'.$exam['id'].'/'.$subject['id']) ?>" target="_blank" class="btn btn-danger" style="background:#dc2626;border-color:#b91c1c">
            <i class="fas fa-file-pdf"></i> Download PDF
        </a>
        <a href="<?= base_url('analysis/export-excel/'.$exam['id'].'/'.$subject['id']) ?>" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Export Excel
        </a>
        <a href="<?= base_url('analysis/'.$exam['id']) ?>" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:14px;margin-bottom:20px">
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:16px">
        <div style="font-size:12px;color:var(--muted)">Total Butir Soal</div>
        <div style="font-size:24px;font-weight:700;color:#fff;margin-top:4px"><?= $totalSoal ?></div>
        <div style="font-size:11px;color:var(--muted);margin-top:2px">Bobot Quiz Moodle</div>
    </div>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:16px">
        <div style="font-size:12px;color:var(--muted)">Siswa Selesai Tes</div>
        <div style="font-size:24px;font-weight:700;color:var(--accent);margin-top:4px"><?= $totalAttempts ?></div>
        <div style="font-size:11px;color:var(--muted);margin-top:2px">Selesai dikerjakan</div>
    </div>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:16px">
        <div style="font-size:12px;color:var(--muted)">Rata-rata Kesukaran (P)</div>
        <div style="font-size:24px;font-weight:700;color:#38bdf8;margin-top:4px"><?= number_format($avgFacility, 2) ?></div>
        <div style="font-size:11px;color:var(--muted);margin-top:2px">Skala 0.00 &ndash; 1.00</div>
    </div>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:16px">
        <div style="font-size:12px;color:var(--muted)">Rata-rata Daya Pembeda (D)</div>
        <div style="font-size:24px;font-weight:700;color:#a855f7;margin-top:4px"><?= number_format($avgDiscrim, 2) ?></div>
        <div style="font-size:11px;color:var(--muted);margin-top:2px">Kelompok 27% Atas &ndash; Bawah</div>
    </div>
    <div style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.25);border-radius:10px;padding:16px">
        <div style="font-size:12px;color:var(--success)">Butir Diterima (Baik)</div>
        <div style="font-size:24px;font-weight:700;color:var(--success);margin-top:4px"><?= $diterima ?></div>
        <div style="font-size:11px;color:var(--muted);margin-top:2px">D &ge; 0.30</div>
    </div>
    <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.25);border-radius:10px;padding:16px">
        <div style="font-size:12px;color:var(--danger)">Perlu Revisi / Ditolak</div>
        <div style="font-size:24px;font-weight:700;color:var(--danger);margin-top:4px"><?= $revisi + $ditolak ?></div>
        <div style="font-size:11px;color:var(--muted);margin-top:2px"><?= $revisi ?> Revisi, <?= $ditolak ?> Ditolak</div>
    </div>
</div>

<!-- Pedoman Interpretasi Indeks -->
<div style="background:rgba(99,102,241,0.06);border:1px solid rgba(99,102,241,0.2);border-radius:10px;padding:14px 18px;margin-bottom:20px;font-size:12px;color:var(--text)">
    <div style="font-weight:700;color:var(--accent);margin-bottom:6px;display:flex;align-items:center;gap:6px">
        <i class="fas fa-info-circle"></i> Pedoman Standar Evaluasi Butir Soal (Arikunto &amp; Robert Ebel)
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:12px;line-height:1.5">
        <div>
            <strong>Tingkat Kesukaran (P):</strong><br>
            <span style="color:var(--success)">&bull; P &ge; 0.70</span> = Kategori Mudah<br>
            <span style="color:var(--warning)">&bull; 0.30 &le; P &lt; 0.70</span> = Kategori Sedang (Ideal)<br>
            <span style="color:var(--danger)">&bull; P &lt; 0.30</span> = Kategori Sukar
        </div>
        <div>
            <strong>Daya Pembeda (D = P<sub>atas 27%</sub> &minus; P<sub>bawah 27%</sub>):</strong><br>
            <span style="color:var(--success)">&bull; D &ge; 0.40</span> = Sangat Baik (Diterima tanpa revisi)<br>
            <span style="color:#38bdf8">&bull; 0.30 &le; D &lt; 0.40</span> = Baik (Diterima)<br>
            <span style="color:var(--warning)">&bull; 0.20 &le; D &lt; 0.30</span> = Cukup (Perlu perbaikan/revisi)<br>
            <span style="color:var(--danger)">&bull; D &lt; 0.20</span> = Buruk / Ditolak (Harus diganti)
        </div>
    </div>
</div>

<!-- Tabel Rincian Butir Soal -->
<div class="card">
    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between">
        <span class="card-title"><i class="fas fa-list-ol"></i> Rincian Parameter Analisis Tiap Butir Soal</span>
        <span style="font-size:12px;color:var(--muted)">Menampilkan <?= $totalSoal ?> Butir Soal</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:65px;text-align:center">Slot</th>
                    <th>Nama / Kode Soal</th>
                    <th style="width:110px">Tipe</th>
                    <th style="width:80px;text-align:right">Bobot</th>
                    <th style="width:180px">Tingkat Kesukaran (P)</th>
                    <th style="width:160px">Daya Pembeda (D)</th>
                    <th style="width:160px">Rekomendasi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $it): ?>
                <tr>
                    <td style="text-align:center"><strong style="color:var(--accent)">#<?= $it['slot'] ?></strong></td>
                    <td>
                        <strong style="color:#fff"><?= esc($it['name']) ?></strong>
                        <div style="font-size:11px;color:var(--muted)">ID: <?= $it['question_id'] ?></div>
                    </td>
                    <td><span class="badge badge-draft"><?= esc($it['qtype']) ?></span></td>
                    <td style="text-align:right;font-family:monospace"><?= number_format($it['maxmark'], 1) ?></td>
                    <td>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:3px">
                            <span style="font-family:monospace;font-weight:700"><?= number_format($it['facility_index'], 2) ?></span>
                            <?php if($it['category'] === 'Mudah'): ?>
                                <span class="badge badge-aktif" style="font-size:10px;padding:2px 6px">Mudah</span>
                            <?php elseif($it['category'] === 'Sedang'): ?>
                                <span class="badge badge-warning" style="background:rgba(245,158,11,0.15);color:var(--warning);font-size:10px;padding:2px 6px">Sedang</span>
                            <?php else: ?>
                                <span class="badge badge-absen" style="font-size:10px;padding:2px 6px">Sukar</span>
                            <?php endif; ?>
                        </div>
                        <div class="progress" style="width:100%;height:6px;background:rgba(255,255,255,0.08);border-radius:3px">
                            <div class="progress-bar" style="width:<?= min(100, max(0, $it['facility_index'] * 100)) ?>%;background:<?= $it['category'] === 'Mudah' ? 'var(--success)' : ($it['category'] === 'Sedang' ? 'var(--warning)' : 'var(--danger)') ?>"></div>
                        </div>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:6px">
                            <span style="font-family:monospace;font-weight:700;color:<?= $it['discrimination_index'] >= 0.30 ? 'var(--success)' : ($it['discrimination_index'] >= 0.20 ? 'var(--warning)' : 'var(--danger)') ?>">
                                <?= ($it['discrimination_index'] >= 0 ? '+' : '') . number_format($it['discrimination_index'], 2) ?>
                            </span>
                            <span class="badge" style="font-size:10px;padding:2px 6px;
                                <?php if($it['discrimination_category'] === 'Sangat Baik'): ?>
                                    background:rgba(16,185,129,0.15);color:var(--success);border:1px solid rgba(16,185,129,0.3);
                                <?php elseif($it['discrimination_category'] === 'Baik'): ?>
                                    background:rgba(56,189,248,0.15);color:#38bdf8;border:1px solid rgba(56,189,248,0.3);
                                <?php elseif($it['discrimination_category'] === 'Cukup'): ?>
                                    background:rgba(245,158,11,0.15);color:var(--warning);border:1px solid rgba(245,158,11,0.3);
                                <?php else: ?>
                                    background:rgba(239,68,68,0.15);color:var(--danger);border:1px solid rgba(239,68,68,0.3);
                                <?php endif; ?>
                            ">
                                <?= $it['discrimination_category'] ?>
                            </span>
                        </div>
                    </td>
                    <td>
                        <?php if(str_contains($it['recommendation'], 'Sangat Baik')): ?>
                            <span class="badge badge-aktif"><i class="fas fa-check-double" style="margin-right:4px"></i> Diterima Baik</span>
                        <?php elseif(str_contains($it['recommendation'], 'Diterima')): ?>
                            <span class="badge" style="background:rgba(56,189,248,0.15);color:#38bdf8;border:1px solid rgba(56,189,248,0.3)">
                                <i class="fas fa-check" style="margin-right:4px"></i> Diterima
                            </span>
                        <?php elseif(str_contains($it['recommendation'], 'Revisi')): ?>
                            <span class="badge badge-warning" style="background:rgba(245,158,11,0.15);color:var(--warning)">
                                <i class="fas fa-tools" style="margin-right:4px"></i> Direvisi
                            </span>
                        <?php else: ?>
                            <span class="badge badge-absen">
                                <i class="fas fa-times-circle" style="margin-right:4px"></i> Ditolak
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($items)): ?>
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:var(--muted)">
                        <i class="fas fa-folder-open" style="font-size:36px;opacity:0.3;margin-bottom:8px;display:block"></i>
                        Belum ada butir soal pada Quiz Moodle ini.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php echo view('layout/footer'); ?>
