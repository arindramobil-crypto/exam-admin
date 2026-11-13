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
    <!-- Ringkasan global pengerjaan & Kiosk Anti-Cheat -->
    <?php
        $totalGlobal   = array_sum(array_column($monitoring, 'total'));
        $selesaiGlobal = array_sum(array_column($monitoring, 'selesai'));
        $belumGlobal   = array_sum(array_column($monitoring, 'belum'));
        $pctGlobal     = $totalGlobal > 0 ? round(($selesaiGlobal / $totalGlobal) * 100) : 0;
    ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:20px">
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

    <!-- Kiosk Anti-Cheat Live Stats & Proctor PIN -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-bottom:24px">
        <div class="stat-card" style="border:1px solid rgba(239,68,68,0.3);background:rgba(239,68,68,0.03)">
            <div class="stat-label" style="color:#f87171">Pelanggaran Tab/Aplikasi</div>
            <div class="stat-value" style="color:#ef4444"><?= $totalViolations ?></div>
            <i class="stat-icon fas fa-exclamation-triangle" style="color:#ef4444"></i>
        </div>
        <div class="stat-card" style="border:1px solid rgba(239,68,68,0.4);background:rgba(239,68,68,0.06)">
            <div class="stat-label" style="color:#f87171">Siswa Terkunci Saat Ini</div>
            <div class="stat-value" style="color:#ef4444"><?= $totalLocked ?></div>
            <i class="stat-icon fas fa-lock" style="color:#ef4444"></i>
        </div>
        <div class="stat-card" style="border:1px solid rgba(56,189,248,0.3);background:rgba(56,189,248,0.03)">
            <div class="stat-label" style="color:#38bdf8">Dibuka Kembali Pengawas</div>
            <div class="stat-value" style="color:#38bdf8"><?= $totalUnlocked ?></div>
            <i class="stat-icon fas fa-unlock-alt" style="color:#38bdf8"></i>
        </div>
        <div class="stat-card" style="border:1px solid rgba(16,185,129,0.4);background:rgba(16,185,129,0.05);position:relative">
            <div style="display:flex;justify-content:space-between;align-items:center">
                <div class="stat-label" style="color:#10b981;margin-bottom:0">PIN Pengawas (Buka Kunci)</div>
                <button type="button" onclick="openProctorPinModal()" class="btn btn-sm" style="padding:2px 8px;font-size:11px;background:rgba(16,185,129,0.2);color:#10b981;border:1px solid rgba(16,185,129,0.4);border-radius:6px;cursor:pointer">
                    <i class="fas fa-edit"></i> Ubah PIN
                </button>
            </div>
            <div class="stat-value" style="color:#10b981;font-family:'Courier New',monospace;letter-spacing:2px;font-size:24px;margin-top:6px">
                <?= esc($proctorPin) ?>
            </div>
            <i class="stat-icon fas fa-key" style="color:#10b981"></i>
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
                                        <th>Status Quiz</th>
                                        <th>Status Kiosk</th>
                                        <th>Waktu Mulai</th>
                                        <th>Waktu Selesai</th>
                                        <th>Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($m['attempts'] as $att): ?>
                                    <?php
                                        $uid = (int)$att['user_id'];
                                        $st  = $violationStats[$uid] ?? null;
                                    ?>
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
                                        <td>
                                            <?php if(!empty($st) && !empty($st['is_locked'])): ?>
                                                <div style="display:flex;flex-direction:column;gap:4px;align-items:flex-start">
                                                    <span class="badge" style="background:#ef4444;color:#fff;font-weight:700;box-shadow:0 0 10px rgba(239,68,68,0.6)">
                                                        <i class="fas fa-lock"></i> TERKUNCI (3x)
                                                    </span>
                                                    <button type="button" class="btn btn-sm" style="background:#10b981;color:#fff;border:none;padding:3px 8px;font-size:11px;font-weight:600;border-radius:6px;cursor:pointer" onclick="remoteUnlockStudent(<?= (int)$sub['moodle_quiz_id'] ?>, <?= $uid ?>, '<?= esc($att['firstname'] . ' ' . $att['lastname']) ?>')">
                                                        <i class="fas fa-key"></i> Buka Kunci
                                                    </button>
                                                </div>
                                            <?php elseif(!empty($st) && !empty($st['is_unlocked'])): ?>
                                                <span class="badge" style="background:rgba(56,189,248,.18);color:#38bdf8">
                                                    <i class="fas fa-unlock"></i> Dibuka Proktor (2/3)
                                                </span>
                                            <?php elseif(!empty($st) && !empty($st['total_violations'])): ?>
                                                <span class="badge" style="background:rgba(245,158,11,.18);color:#f59e0b">
                                                    <i class="fas fa-exclamation-triangle"></i> <?= $st['total_violations'] ?>x Peringatan
                                                </span>
                                            <?php else: ?>
                                                <span class="badge" style="background:rgba(16,185,129,.15);color:#10b981">
                                                    <i class="fas fa-shield-alt"></i> Aman (0)
                                                </span>
                                            <?php endif; ?>
                                            <?php if(!empty($st['last_device'])): ?>
                                                <small style="color:var(--muted);display:block;margin-top:2px;font-size:10px">
                                                    <?= esc($st['last_device']) ?>
                                                </small>
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

    <!-- Real-time Violation Audit Log Card -->
    <div class="card" style="margin-top:24px;border:1px solid rgba(239,68,68,0.3)">
        <div class="card-header" style="background:rgba(239,68,68,0.06);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px">
            <div>
                <span class="card-title" style="color:#f87171">
                    <i class="fas fa-history" style="margin-right:6px"></i> Log Audit Pelanggaran Kiosk Real-Time (Tab-Switch)
                </span>
                <span class="badge badge-draft" style="margin-left:8px"><?= count($recentViolations) ?> Aktivitas Terakhir</span>
            </div>
            <div style="font-size:12px;color:var(--muted)">
                PIN Buka Kunci Pengawas: <strong style="color:#10b981;font-family:monospace;letter-spacing:1px"><?= esc($proctorPin) ?></strong>
            </div>
        </div>
        <div class="card-body">
            <?php if(empty($recentViolations)): ?>
                <div style="text-align:center;padding:30px;color:var(--muted)">
                    <i class="fas fa-shield-alt" style="font-size:36px;color:#10b981;opacity:0.4;margin-bottom:10px;display:block"></i>
                    Belum ada pelanggaran tab atau aplikasi yang tercatat pada sesi ujian ini.
                </div>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Siswa</th>
                                <th>Username</th>
                                <th>Mapel / Quiz</th>
                                <th>Kejadian</th>
                                <th>Detail Pelanggaran</th>
                                <th>Perangkat</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recentViolations as $v): ?>
                            <tr>
                                <td><small style="color:var(--muted)"><?= date('H:i:s (d/m)', $v['timecreated']) ?></small></td>
                                <td><strong><?= esc($v['firstname'] . ' ' . $v['lastname']) ?></strong></td>
                                <td><code><?= esc($v['username']) ?></code></td>
                                <td><small><?= esc($v['quiz_name'] ?: 'Quiz #' . $v['quizid']) ?></small></td>
                                <td>
                                    <?php if($v['action'] === 'locked'): ?>
                                        <span class="badge" style="background:#ef4444;color:#fff;font-weight:700">
                                            <i class="fas fa-lock"></i> TERKUNCI (3x)
                                        </span>
                                    <?php elseif($v['action'] === 'unlocked'): ?>
                                        <span class="badge" style="background:#0284c7;color:#fff;font-weight:700">
                                            <i class="fas fa-unlock"></i> DIBUKA PENGAWAS
                                        </span>
                                    <?php elseif($v['action'] === 'warning_2'): ?>
                                        <span class="badge" style="background:rgba(245,158,11,0.2);color:#f59e0b">
                                            Peringatan 2/3
                                        </span>
                                    <?php else: ?>
                                        <span class="badge" style="background:rgba(245,158,11,0.15);color:#fbbf24">
                                            Peringatan 1/3
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><small><?= esc($v['reason']) ?></small></td>
                                <td>
                                    <span class="badge badge-draft" style="font-size:11px">
                                        <?= strpos($v['device_type'] ?? '', 'HP') !== false ? '📱 ' : '💻 ' ?><?= esc($v['device_type'] ?? '-') ?>
                                    </span>
                                </td>
                                <td><small style="color:var(--muted)"><?= esc($v['ip_address']) ?></small></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
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

// ─── Buka Kunci Siswa Jarak Jauh (Remote Unlock) ──────────────────────────────
function remoteUnlockStudent(quizId, userId, studentName) {
    if (!confirm('Apakah Anda yakin ingin membuka kunci ujian untuk siswa:\n' + studentName + '?\n\nSiswa akan diberikan 1 kesempatan toleransi terakhir.')) {
        return;
    }

    var formData = new FormData();
    formData.append('quiz_id', quizId);
    formData.append('user_id', userId);
    formData.append('student_name', studentName);

    fetch('<?= base_url('dashboard/remote-unlock/' . $exam['id']) ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data && data.success) {
            alert(data.message || 'Kunci ujian berhasil dibuka!');
            window.location.reload();
        } else {
            alert('Gagal: ' + ((data && data.message) ? data.message : 'Terjadi kesalahan sistem.'));
        }
    })
    .catch(function(err) {
        alert('Terjadi kesalahan jaringan saat membuka kunci ujian.');
    });
}

function openProctorPinModal() {
    var m = document.getElementById('modalProctorPin');
    if (m) m.style.display = 'flex';
}
function closeProctorPinModal() {
    var m = document.getElementById('modalProctorPin');
    if (m) m.style.display = 'none';
}
</script>

<!-- Modal Ubah PIN Pengawas -->
<div id="modalProctorPin" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.65);z-index:99999;align-items:center;justify-content:center;padding:16px">
    <div style="background:var(--card-bg, #1e293b);border:1px solid var(--border, #334155);border-radius:14px;padding:24px;max-width:420px;width:100%;box-shadow:0 20px 50px rgba(0,0,0,0.6)">
        <h4 style="margin:0 0 10px;color:#fff;display:flex;align-items:center;gap:8px;font-size:17px">
            <i class="fas fa-key" style="color:#10b981"></i> Ubah PIN Buka Kunci Pengawas
        </h4>
        <p style="color:var(--muted, #94a3b8);font-size:13px;margin:0 0 18px;line-height:1.5">
            PIN ini digunakan oleh Pengawas Ruangan untuk membuka lembar ujian siswa yang terkunci akibat membuka tab/aplikasi lain. Berbeda dengan token soal.
        </p>
        <form action="<?= base_url('token/save-proctor-pin/'.$exam['id']) ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group" style="margin-bottom:18px">
                <label style="display:block;font-size:12px;font-weight:600;color:var(--muted);margin-bottom:6px">PIN Pengawas Baru (Min. 4 Karakter)</label>
                <input type="text" name="proctor_pin" value="<?= esc($proctorPin) ?>" class="form-control" style="font-family:'Courier New',monospace;font-weight:700;letter-spacing:2px;text-transform:uppercase;font-size:16px" required autofocus>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px">
                <button type="button" onclick="closeProctorPinModal()" class="btn btn-sm btn-outline">Batal</button>
                <button type="submit" class="btn btn-sm btn-success" style="background:#10b981;border:none"><i class="fas fa-save"></i> Simpan PIN Baru</button>
            </div>
        </form>
    </div>
</div>

<?php echo view('layout/footer'); ?>
