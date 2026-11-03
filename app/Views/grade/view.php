<?php echo view('layout/header', ['title' => $title]); ?>

<?php
$totalSiswa = count($grades);
$gradedList = array_values(array_filter($grades, fn($g) => $g['grade'] !== null));
$gradesList = array_map(fn($g) => (float)$g['grade'], $gradedList);
$avgGrade = count($gradesList) > 0 ? array_sum($gradesList) / count($gradesList) : 0;
$maxGrade = count($gradesList) > 0 ? max($gradesList) : 0;
$minGrade = count($gradesList) > 0 ? min($gradesList) : 0;
$tuntas = count(array_filter($gradesList, fn($v) => $v >= 75));
$tidakTuntas = count($gradesList) - $tuntas;
$sedangMengerjakan = count(array_filter($grades, fn($g) => ($g['state'] ?? '') === 'inprogress'));
$belumUjian = count(array_filter($grades, fn($g) => $g['grade'] === null && ($g['state'] ?? '') !== 'inprogress'));
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
<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(150px, 1fr));gap:16px;margin-bottom:24px">
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:16px">
        <div style="font-size:12px;color:var(--muted)">Total Peserta</div>
        <div style="font-size:24px;font-weight:700;color:#fff;margin-top:4px"><?= $totalSiswa ?></div>
    </div>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:16px">
        <div style="font-size:12px;color:var(--muted)">Rata-Rata Nilai</div>
        <div style="font-size:24px;font-weight:700;color:var(--accent);margin-top:4px"><?= count($gradesList) > 0 ? number_format($avgGrade, 2) : '-' ?></div>
    </div>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:16px">
        <div style="font-size:12px;color:var(--muted)">Nilai Tertinggi</div>
        <div style="font-size:24px;font-weight:700;color:var(--success);margin-top:4px"><?= count($gradesList) > 0 ? number_format($maxGrade, 2) : '-' ?></div>
    </div>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:16px">
        <div style="font-size:12px;color:var(--muted)">Nilai Terendah</div>
        <div style="font-size:24px;font-weight:700;color:var(--warning);margin-top:4px"><?= count($gradesList) > 0 ? number_format($minGrade, 2) : '-' ?></div>
    </div>
    <div style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.25);border-radius:10px;padding:16px">
        <div style="font-size:12px;color:var(--success)">Tuntas (&ge; 75)</div>
        <div style="font-size:24px;font-weight:700;color:var(--success);margin-top:4px"><?= $tuntas ?></div>
    </div>
    <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.25);border-radius:10px;padding:16px">
        <div style="font-size:12px;color:var(--danger)">Belum Tuntas</div>
        <div style="font-size:24px;font-weight:700;color:var(--danger);margin-top:4px"><?= $tidakTuntas ?></div>
    </div>
    <div style="background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.25);border-radius:10px;padding:16px">
        <div style="font-size:12px;color:#f59e0b">Mengerjakan / Belum</div>
        <div style="font-size:24px;font-weight:700;color:#f59e0b;margin-top:4px"><?= $sedangMengerjakan ?> / <?= $belumUjian ?></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Daftar Nilai Siswa</span>
        <span class="badge badge-selesai"><?= count($gradedList) ?> / <?= $totalSiswa ?> Selesai Dinilai</span>
    </div>

    <!-- Filter & Pencarian -->
    <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;gap:10px;flex-wrap:wrap;align-items:center">
        <input type="text" id="searchNama" placeholder="&#128269; Cari nama / username..." oninput="applyFilter()"
            style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:7px 12px;color:var(--text);font-size:13px;outline:none;min-width:220px">

        <?php $kelasList = array_unique(array_filter(array_column($grades, 'kelas'))); sort($kelasList); ?>
        <?php if(count($kelasList) > 1): ?>
        <select id="filterKelas" onchange="applyFilter()" style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:7px 12px;color:var(--text);font-size:13px;outline:none">
            <option value="">Semua Kelas</option>
            <?php foreach($kelasList as $k): ?><option value="<?= esc($k) ?>"><?= esc($k) ?></option><?php endforeach; ?>
        </select>
        <?php endif; ?>

        <?php $sesiList = array_unique(array_filter(array_column($grades, 'sesi'))); sort($sesiList); ?>
        <?php if(count($sesiList) > 1): ?>
        <select id="filterSesi" onchange="applyFilter()" style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:7px 12px;color:var(--text);font-size:13px;outline:none">
            <option value="">Semua Sesi</option>
            <?php foreach($sesiList as $s): ?><option value="<?= $s ?>">Sesi <?= $s ?></option><?php endforeach; ?>
        </select>
        <?php endif; ?>

        <select id="filterStatus" onchange="applyFilter()" style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:7px 12px;color:var(--text);font-size:13px;outline:none">
            <option value="">Semua Status</option>
            <option value="tuntas">Tuntas</option>
            <option value="belum_tuntas">Belum Tuntas</option>
            <option value="inprogress">Sedang Mengerjakan</option>
            <option value="not_started">Belum Ujian</option>
        </select>

        <span id="filterCount" style="font-size:12px;color:var(--muted);margin-left:4px"></span>
    </div>

    <div class="table-wrap">
        <table id="gradeTable">
            <thead>
                <tr>
                    <th style="width:50px">No</th>
                    <th>No. Peserta</th>
                    <th>Nama Lengkap</th>
                    <th>Username / NIS</th>
                    <th>Nilai Akhir</th>
                    <th>Status / Keterangan</th>
                    <th>Waktu Submit</th>
                </tr>
            </thead>
            <tbody>
                <?php $r = 1; foreach($grades as $g): ?>
                <?php
                    $statusVal = 'not_started';
                    if ($g['grade'] !== null) {
                        $statusVal = $g['grade'] >= 75 ? 'tuntas' : 'belum_tuntas';
                    } elseif (($g['state'] ?? '') === 'inprogress') {
                        $statusVal = 'inprogress';
                    }
                ?>
                <tr data-nama="<?= strtolower(esc($g['firstname'].' '.$g['lastname'])) ?>"
                    data-username="<?= strtolower(esc($g['username'])) ?>"
                    data-kelas="<?= esc($g['kelas'] ?? '') ?>"
                    data-sesi="<?= $g['sesi'] ?? 1 ?>"
                    data-status="<?= $statusVal ?>">
                    <td class="row-num"><?= $r++ ?></td>
                    <td><span class="badge" style="background:rgba(255,255,255,0.06);font-family:monospace"><?= esc($g['nomor_peserta'] ?? '-') ?></span></td>
                    <td><strong><?= esc($g['firstname'] . ' ' . $g['lastname']) ?></strong></td>
                    <td>
                        <code><?= esc($g['username']) ?></code>
                        <?php if(!empty($g['idnumber']) && $g['idnumber'] !== '-'): ?>
                            <small style="color:var(--muted);margin-left:4px">(<?= esc($g['idnumber']) ?>)</small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($g['grade'] !== null): ?>
                            <strong style="font-size:16px;color:var(--accent)"><?= number_format($g['grade'], 2) ?></strong>
                        <?php elseif(($g['state'] ?? '') === 'inprogress'): ?>
                            <span style="color:#f59e0b;font-size:13px"><i class="fas fa-spinner fa-spin"></i> Proses</span>
                        <?php else: ?>
                            <span style="color:var(--muted)">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($g['grade'] !== null): ?>
                            <?= $g['grade'] >= 75 ? '<span class="badge badge-aktif"><i class="fas fa-check" style="margin-right:4px"></i>TUNTAS</span>' : '<span class="badge badge-absen"><i class="fas fa-times" style="margin-right:4px"></i>BELUM TUNTAS</span>' ?>
                        <?php elseif(($g['state'] ?? '') === 'inprogress'): ?>
                            <span class="badge" style="background:#f59e0b;color:#fff"><i class="fas fa-clock" style="margin-right:4px"></i>SEDANG MENGERJAKAN</span>
                        <?php else: ?>
                            <span class="badge" style="background:#4b5563;color:#9ca3af">BELUM MENGERJAKAN</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <small style="color:var(--muted)"><?= !empty($g['timemodified']) ? date('d M Y H:i:s', $g['timemodified']) : '-' ?></small>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($grades)): ?>
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:var(--muted)">
                        <i class="fas fa-inbox" style="font-size:32px;opacity:0.4;display:block;margin-bottom:8px"></i>
                        Belum ada peserta atau data nilai pada Quiz Moodle ini.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function applyFilter() {
    var search  = (document.getElementById('searchNama')?.value || '').toLowerCase().trim();
    var kelas   = (document.getElementById('filterKelas')?.value || '').toLowerCase();
    var sesi    = document.getElementById('filterSesi')?.value || '';
    var status  = document.getElementById('filterStatus')?.value || '';

    var rows    = document.querySelectorAll('#gradeTable tbody tr[data-nama]');
    var visible = 0;

    rows.forEach(function(row, idx) {
        var nama     = row.dataset.nama || '';
        var username = row.dataset.username || '';
        var rKelas   = (row.dataset.kelas || '').toLowerCase();
        var rSesi    = row.dataset.sesi || '';
        var rStatus  = row.dataset.status || '';

        var ok = true;
        if (search && !nama.includes(search) && !username.includes(search)) ok = false;
        if (kelas  && rKelas !== kelas)   ok = false;
        if (sesi   && rSesi  !== sesi)    ok = false;
        if (status && rStatus !== status) ok = false;

        row.style.display = ok ? '' : 'none';
        if (ok) {
            visible++;
            // Update nomor urut
            var numCell = row.querySelector('.row-num');
            if (numCell) numCell.textContent = visible;
        }
    });

    var fc = document.getElementById('filterCount');
    if (fc) fc.textContent = 'Menampilkan ' + visible + ' dari ' + rows.length + ' peserta';
}
// Jalankan saat halaman load
applyFilter();
</script>

<?php echo view('layout/footer'); ?>
