<?php echo view('layout/header', ['title' => $title]); ?>

<div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
        <h2 style="font-size:1.25rem;font-weight:700;color:#fff"><i class="fas fa-desktop" style="color:var(--accent);margin-right:8px"></i> Monitoring Live: <?= esc($exam['nama_ujian']) ?></h2>
        <p style="font-size:13px;color:var(--muted)">
            <?= date('d M Y', strtotime($exam['tgl_mulai'])) ?> s/d <?= date('d M Y', strtotime($exam['tgl_selesai'])) ?>
        </p>
    </div>
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
        <!-- Refresh interval selector -->
        <div style="display:flex;align-items:center;gap:6px;background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:6px 10px">
            <i class="fas fa-sync-alt" style="color:var(--muted);font-size:12px" id="refreshIcon"></i>
            <select id="refreshInterval" style="background:transparent;border:none;color:var(--text);font-size:13px;outline:none;cursor:pointer">
                <option value="0">Tanpa Auto-Refresh</option>
                <option value="10">Setiap 10 detik</option>
                <option value="30" selected>Setiap 30 detik</option>
                <option value="60">Setiap 1 menit</option>
            </select>
        </div>
        <!-- Countdown display -->
        <div id="refreshCountdown" style="font-size:13px;color:var(--muted);min-width:80px;text-align:center"></div>
        <button class="btn btn-outline" onclick="window.location.reload()"><i class="fas fa-sync-alt"></i> Refresh</button>
        <a href="<?= base_url('exam') ?>" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
</div>

<?php if(empty($monitoring)): ?>
    <div class="card">
        <div class="card-body" style="text-align:center;padding:50px;color:var(--muted)">
            <i class="fas fa-tasks" style="font-size:48px;opacity:0.3;margin-bottom:16px;display:block"></i>
            <h4 style="color:#fff;margin-bottom:8px">Belum Ada Mapel Tertaut Quiz Moodle</h4>
            <p>Silakan tautkan mata pelajaran ke Quiz Moodle pada menu <strong>Manajemen Ujian &rsaquo; Mapel</strong>.</p>
            <a href="<?= base_url('exam/subjects/'.$exam['id']) ?>" class="btn btn-primary" style="margin-top:16px">
                <i class="fas fa-link"></i> Kelola Mapel
            </a>
        </div>
    </div>
<?php else: ?>
    <!-- Ringkasan global semua mapel -->
    <?php
        $totalGlobal   = array_sum(array_column($monitoring, 'total'));
        $selesaiGlobal = array_sum(array_column($monitoring, 'selesai'));
        $belumGlobal   = array_sum(array_column($monitoring, 'belum'));
        $pctGlobal     = $totalGlobal > 0 ? round(($selesaiGlobal / $totalGlobal) * 100) : 0;
    ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:24px">
        <div class="stat-card">
            <div class="stat-label">Total Pengerjaan</div>
            <div class="stat-value"><?= $totalGlobal ?></div>
            <i class="stat-icon fas fa-users"></i>
        </div>
        <div class="stat-card" style="--accent-c:var(--success)">
            <div class="stat-label" style="color:var(--success)">Sudah Selesai</div>
            <div class="stat-value" style="color:var(--success)"><?= $selesaiGlobal ?></div>
            <i class="stat-icon fas fa-check-circle" style="color:var(--success)"></i>
        </div>
        <div class="stat-card">
            <div class="stat-label" style="color:var(--warning)">Sedang Mengerjakan</div>
            <div class="stat-value" style="color:var(--warning)"><?= $belumGlobal ?></div>
            <i class="stat-icon fas fa-pen" style="color:var(--warning)"></i>
        </div>
        <div class="stat-card">
            <div class="stat-label">Penyelesaian Global</div>
            <div class="stat-value" style="color:var(--accent)"><?= $pctGlobal ?>%</div>
            <i class="stat-icon fas fa-chart-pie"></i>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:24px">
        <?php foreach($monitoring as $m): ?>
            <?php
                $sub    = $m['subject'];
                $total  = $m['total'];
                $selesai = $m['selesai'];
                $belum  = $m['belum'];
                $pct    = $total > 0 ? round(($selesai / $total) * 100) : 0;
                $barColor = $pct >= 80 ? 'var(--success)' : ($pct >= 40 ? 'var(--warning)' : 'var(--danger)');
            ?>
            <div class="card">
                <div class="card-header" style="background:var(--surface2)">
                    <div>
                        <span style="font-weight:700;font-size:16px;color:#fff"><?= esc($sub['nama_mapel']) ?></span>
                        <span class="badge badge-draft" style="margin-left:8px">Kelas: <?= esc($sub['kelas']) ?></span>
                        <span class="badge badge-aktif" style="margin-left:4px">Quiz #<?= $sub['moodle_quiz_id'] ?></span>
                    </div>
                    <div style="display:flex;gap:8px">
                        <a href="<?= base_url('grade/view/'.$exam['id'].'/'.$sub['id']) ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-chart-bar"></i> Rekap Nilai
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Stats -->
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:18px">
                        <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:14px">
                            <div style="font-size:11px;color:var(--muted)">Memulai Ujian</div>
                            <div style="font-size:22px;font-weight:700;color:#fff;margin-top:4px"><?= $total ?></div>
                        </div>
                        <div style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.25);border-radius:10px;padding:14px">
                            <div style="font-size:11px;color:var(--success)">Selesai (Submit)</div>
                            <div style="font-size:22px;font-weight:700;color:var(--success);margin-top:4px"><?= $selesai ?></div>
                        </div>
                        <div style="background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.25);border-radius:10px;padding:14px">
                            <div style="font-size:11px;color:var(--warning)">Sedang Mengerjakan</div>
                            <div style="font-size:22px;font-weight:700;color:var(--warning);margin-top:4px"><?= $belum ?></div>
                        </div>
                    </div>

                    <!-- Progress Bar berwarna dinamis -->
                    <div style="margin-bottom:20px">
                        <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--muted);margin-bottom:6px">
                            <span>Tingkat Penyelesaian</span>
                            <span><strong><?= $pct ?>%</strong> (<?= $selesai ?> dari <?= $total ?> siswa)</span>
                        </div>
                        <div class="progress" style="height:10px">
                            <div class="progress-bar" style="width:<?= $pct ?>%;background:<?= $barColor ?>"></div>
                        </div>
                    </div>

                    <!-- Tabel attempts -->
                    <h6 style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px">Aktivitas Siswa di Moodle</h6>
                    <?php if(empty($m['attempts'])): ?>
                        <p style="color:var(--muted);font-size:13px;font-style:italic">Belum ada siswa yang memulai quiz ini di Moodle.</p>
                    <?php else: ?>
                        <!-- Filter tabel per status -->
                        <div style="display:flex;gap:8px;margin-bottom:12px;flex-wrap:wrap">
                            <button onclick="filterTable('<?= $sub['id'] ?>', 'all')" class="btn btn-sm btn-outline filter-btn active" data-quiz="<?= $sub['id'] ?>" data-filter="all">Semua (<?= $total ?>)</button>
                            <button onclick="filterTable('<?= $sub['id'] ?>', 'inprogress')" class="btn btn-sm btn-outline filter-btn" data-quiz="<?= $sub['id'] ?>" data-filter="inprogress" style="color:var(--warning);border-color:rgba(245,158,11,.3)">
                                <i class="fas fa-spinner"></i> Mengerjakan (<?= $belum ?>)
                            </button>
                            <button onclick="filterTable('<?= $sub['id'] ?>', 'finished')" class="btn btn-sm btn-outline filter-btn" data-quiz="<?= $sub['id'] ?>" data-filter="finished" style="color:var(--success);border-color:rgba(16,185,129,.3)">
                                <i class="fas fa-check"></i> Selesai (<?= $selesai ?>)
                            </button>
                        </div>
                        <div class="table-wrap">
                            <table id="table-<?= $sub['id'] ?>">
                                <thead>
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <th>Username</th>
                                        <th>Status</th>
                                        <th>Waktu Mulai</th>
                                        <th>Waktu Selesai</th>
                                        <th>Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($m['attempts'] as $att): ?>
                                    <tr data-state="<?= esc($att['state']) ?>">
                                        <td><strong><?= esc($att['firstname'] . ' ' . $att['lastname']) ?></strong></td>
                                        <td>
                                            <code><?= esc($att['username']) ?></code>
                                            <?php if(!empty($att['idnumber'])): ?>
                                                <small style="color:var(--muted);margin-left:4px">(<?= esc($att['idnumber']) ?>)</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($att['state'] === 'finished'): ?>
                                                <span class="badge badge-aktif"><i class="fas fa-check-circle" style="margin-right:4px"></i>Selesai</span>
                                            <?php elseif($att['state'] === 'inprogress'): ?>
                                                <span class="badge" style="background:rgba(245,158,11,.15);color:var(--warning)"><i class="fas fa-spinner fa-spin" style="margin-right:4px"></i>Mengerjakan</span>
                                            <?php else: ?>
                                                <span class="badge badge-draft"><?= esc(ucfirst($att['state'])) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><small><?= $att['timestart'] ? date('H:i:s', $att['timestart']) : '-' ?></small></td>
                                        <td>
                                            <?php if($att['timefinish']): ?>
                                                <small style="color:var(--success)"><?= date('H:i:s', $att['timefinish']) ?></small>
                                            <?php else: ?>
                                                <small style="color:var(--muted)">Belum selesai</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(isset($att['sumgrades']) && $att['sumgrades'] !== null): ?>
                                                <strong style="color:var(--accent)"><?= round($att['sumgrades'], 1) ?></strong>
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
// ─── Auto-refresh dengan countdown ───────────────────────────────────────────
var refreshTimer = null;
var countdownTimer = null;
var remaining = 0;

function startRefresh(seconds) {
    clearInterval(refreshTimer);
    clearInterval(countdownTimer);
    var cd = document.getElementById('refreshCountdown');
    var icon = document.getElementById('refreshIcon');

    if (seconds === 0) {
        cd.textContent = '';
        icon.classList.remove('fa-spin');
        return;
    }

    icon.classList.add('fa-spin');
    remaining = seconds;

    countdownTimer = setInterval(function() {
        remaining--;
        cd.textContent = 'Refresh dalam ' + remaining + 's';
        if (remaining <= 0) {
            clearInterval(countdownTimer);
            window.location.reload();
        }
    }, 1000);
}

document.getElementById('refreshInterval').addEventListener('change', function() {
    startRefresh(parseInt(this.value));
});
// Default: 30 detik
startRefresh(30);

// ─── Filter tabel per status ─────────────────────────────────────────────────
function filterTable(quizId, filter) {
    var table = document.getElementById('table-' + quizId);
    if (!table) return;

    // Update tombol aktif
    document.querySelectorAll('.filter-btn[data-quiz="' + quizId + '"]').forEach(function(btn) {
        btn.style.opacity = btn.dataset.filter === filter ? '1' : '0.5';
        btn.style.fontWeight = btn.dataset.filter === filter ? '700' : '400';
    });

    // Tampilkan/sembunyikan baris
    table.querySelectorAll('tbody tr').forEach(function(row) {
        var state = row.getAttribute('data-state');
        if (filter === 'all' || state === filter) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>

<?php echo view('layout/footer'); ?>
