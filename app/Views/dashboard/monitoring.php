<?php echo view('layout/header', ['title' => $title]); ?>

<div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
        <h2 style="font-size:1.25rem;font-weight:700;color:#fff"><i class="fas fa-desktop" style="color:var(--accent);margin-right:8px"></i> Monitoring Live Ujian: <?= esc($exam['nama_ujian']) ?></h2>
        <p style="font-size:13px;color:var(--muted)">
            Pelaksanaan: <?= date('d M Y', strtotime($exam['tgl_mulai'])) ?> s/d <?= date('d M Y', strtotime($exam['tgl_selesai'])) ?>
        </p>
    </div>
    <div style="display:flex;align-items:center;gap:12px">
        <label style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--muted);cursor:pointer">
            <input type="checkbox" id="autoRefreshToggle" checked> Auto-refresh (10s)
        </label>
        <button class="btn btn-outline" onclick="window.location.reload()"><i class="fas fa-sync-alt"></i> Refresh Sekarang</button>
        <a href="<?= base_url('exam') ?>" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
</div>

<?php if(empty($monitoring)): ?>
    <div class="card">
        <div class="card-body" style="text-align:center;padding:50px;color:var(--muted)">
            <i class="fas fa-tasks" style="font-size:48px;opacity:0.3;margin-bottom:16px;display:block"></i>
            <h4 style="color:#fff;margin-bottom:8px">Belum Ada Mapel Tertaut Quiz Moodle</h4>
            <p>Silakan tautkan mata pelajaran ke Quiz Moodle pada menu <strong>Manajemen Ujian > Mapel</strong>.</p>
            <a href="<?= base_url('exam/subjects/'.$exam['id']) ?>" class="btn btn-primary" style="margin-top:16px">
                <i class="fas fa-link"></i> Kelola Mapel
            </a>
        </div>
    </div>
<?php else: ?>
    <div style="display:flex;flex-direction:column;gap:24px">
        <?php foreach($monitoring as $m): ?>
            <?php 
                $sub = $m['subject'];
                $total = $m['total'];
                $selesai = $m['selesai'];
                $belum = $m['belum'];
                $pct = $total > 0 ? round(($selesai / $total) * 100) : 0;
            ?>
            <div class="card">
                <div class="card-header" style="background:var(--surface2)">
                    <div>
                        <span style="font-weight:700;font-size:16px;color:#fff"><?= esc($sub['nama_mapel']) ?></span>
                        <span class="badge badge-draft" style="margin-left:8px">Kelas: <?= esc($sub['kelas']) ?></span>
                        <span class="badge badge-aktif" style="margin-left:4px">Moodle Quiz #<?= $sub['moodle_quiz_id'] ?></span>
                    </div>
                    <div style="display:flex;gap:8px">
                        <a href="<?= base_url('grade/view/'.$exam['id'].'/'.$sub['id']) ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-chart-bar"></i> Rekap Nilai
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Stat Bar -->
                    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:16px;margin-bottom:20px">
                        <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:16px">
                            <div style="font-size:12px;color:var(--muted)">Total Siswa Memulai</div>
                            <div style="font-size:24px;font-weight:700;color:#fff;margin-top:4px"><?= $total ?></div>
                        </div>
                        <div style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.25);border-radius:10px;padding:16px">
                            <div style="font-size:12px;color:var(--success)">Sudah Selesai (Submit)</div>
                            <div style="font-size:24px;font-weight:700;color:var(--success);margin-top:4px"><?= $selesai ?></div>
                        </div>
                        <div style="background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.25);border-radius:10px;padding:16px">
                            <div style="font-size:12px;color:var(--warning)">Sedang Mengerjakan</div>
                            <div style="font-size:24px;font-weight:700;color:var(--warning);margin-top:4px"><?= $belum ?></div>
                        </div>
                    </div>

                    <!-- Progress -->
                    <div style="margin-bottom:24px">
                        <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--muted);margin-bottom:6px">
                            <span>Tingkat Penyelesaian</span>
                            <span><strong><?= $pct ?>%</strong> (<?= $selesai ?> dari <?= $total ?> siswa)</span>
                        </div>
                        <div class="progress" style="height:10px">
                            <div class="progress-bar" style="width:<?= $pct ?>%"></div>
                        </div>
                    </div>

                    <!-- Detail Attempts Table -->
                    <h6 style="color:var(--muted);font-size:12px;text-transform:uppercase;margin-bottom:12px">Aktivitas Siswa Terkini di Quiz Moodle:</h6>
                    <?php if(empty($m['attempts'])): ?>
                        <p style="color:var(--muted);font-size:13px;font-style:italic">Belum ada siswa yang memulai quiz ini di Moodle.</p>
                    <?php else: ?>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <th>Username / NIS</th>
                                        <th>Status Pengerjaan</th>
                                        <th>Waktu Mulai</th>
                                        <th>Waktu Selesai</th>
                                        <th>Nilai Moodle</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($m['attempts'] as $att): ?>
                                    <tr>
                                        <td><strong><?= esc($att['firstname'] . ' ' . $att['lastname']) ?></strong></td>
                                        <td>
                                            <code><?= esc($att['username']) ?></code>
                                            <?php if(!empty($att['idnumber'])): ?>
                                                <small style="color:var(--muted);margin-left:4px">(<?= esc($att['idnumber']) ?>)</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($att['state'] === 'finished'): ?>
                                                <span class="badge badge-aktif"><i class="fas fa-check-circle" style="margin-right:4px"></i> Selesai</span>
                                            <?php elseif($att['state'] === 'inprogress'): ?>
                                                <span class="badge badge-warning" style="background:rgba(245,158,11,0.15);color:var(--warning)">
                                                    <i class="fas fa-spinner fa-spin" style="margin-right:4px"></i> Sedang Mengerjakan
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-draft"><?= esc(ucfirst($att['state'])) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $att['timestart'] ? date('H:i:s (d/m)', $att['timestart']) : '-' ?></td>
                                        <td><?= $att['timefinish'] ? date('H:i:s (d/m)', $att['timefinish']) : '<span style="color:var(--muted)">Belum selesai</span>' ?></td>
                                        <td>
                                            <?php if(isset($att['grade']) && $att['grade'] !== null): ?>
                                                <strong style="color:var(--success);font-size:14px"><?= round($att['grade'], 2) ?></strong>
                                                <?php if(!empty($att['maxgrade'])): ?>
                                                    <small style="color:var(--muted)">/ <?= round($att['maxgrade'], 0) ?></small>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span style="color:var(--muted)">-</span>
                                            <?php endif; ?>
                                        </td>
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

<script>
var intervalId = null;
function setupAutoRefresh() {
    var toggle = document.getElementById('autoRefreshToggle');
    if (toggle && toggle.checked) {
        if (!intervalId) {
            intervalId = setInterval(function() {
                window.location.reload();
            }, 10000);
        }
    } else {
        if (intervalId) {
            clearInterval(intervalId);
            intervalId = null;
        }
    }
}
document.getElementById('autoRefreshToggle').addEventListener('change', setupAutoRefresh);
setupAutoRefresh();
</script>

<?php echo view('layout/footer'); ?>
