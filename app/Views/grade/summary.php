<?php echo view('layout/header', ['title' => $title]); ?>

<!-- Header & Quick Actions -->
<div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
        <h2 style="font-size:1.25rem;font-weight:700;color:#fff">
            <i class="fas fa-table" style="color:var(--accent);margin-right:8px"></i> Rekap Nilai Gabungan (Semua Mapel)
        </h2>
        <p style="font-size:13px;color:var(--muted)">
            <?= esc($exam['nama_ujian']) ?> &bull; Tahun: <?= esc($exam['tahun']) ?> (Semester <?= esc($exam['semester']) ?>) &bull; <?= count($subjects) ?> Mata Pelajaran
        </p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        <a href="<?= base_url('grade/export-summary-excel/'.$exam['id']) ?>" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Export Excel Rekap
        </a>
        <a href="<?= base_url('grade/'.$exam['id']) ?>" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Kembali ke Mapel
        </a>
    </div>
</div>

<!-- Stats Card Grid -->
<?php
$totalPeserta = count($rows);
$averages = array_filter(array_column($rows, 'average'), fn($v) => is_numeric($v));
$rataGlobal = count($averages) > 0 ? array_sum($averages) / count($averages) : 0;
?>
<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:14px;margin-bottom:20px">
    <div class="stat-card">
        <div class="stat-label">Total Peserta</div>
        <div class="stat-value"><?= $totalPeserta ?></div>
        <i class="stat-icon fas fa-users"></i>
    </div>
    <div class="stat-card">
        <div class="stat-label">Jumlah Mapel</div>
        <div class="stat-value" style="color:var(--accent)"><?= count($subjects) ?></div>
        <i class="stat-icon fas fa-book"></i>
    </div>
    <div class="stat-card">
        <div class="stat-label">Rata-Rata Seluruh Peserta</div>
        <div class="stat-value" style="color:var(--success)"><?= count($averages) > 0 ? number_format($rataGlobal, 2) : '-' ?></div>
        <i class="stat-icon fas fa-chart-line"></i>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Matriks Nilai Seluruh Peserta</span>
        <span class="badge badge-draft" id="summaryCounter">Memuat...</span>
    </div>

    <!-- Filter & Search Toolbar -->
    <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;gap:10px;flex-wrap:wrap;align-items:center">
        <input type="text" id="summarySearch" placeholder="&#128269; Cari Nama atau NIS / No. Peserta..." oninput="filterSummary()"
            style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:7px 12px;color:var(--text);font-size:13px;outline:none;min-width:240px">

        <?php
        $kelasList = array_unique(array_filter(array_map(fn($r) => $r['participant']['kelas'] ?? '', $rows)));
        sort($kelasList);
        ?>
        <?php if(count($kelasList) > 1): ?>
        <select id="summaryKelas" onchange="filterSummary()" style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:7px 12px;color:var(--text);font-size:13px;outline:none">
            <option value="">Semua Kelas</option>
            <?php foreach($kelasList as $k): ?>
                <option value="<?= esc($k) ?>"><?= esc($k) ?></option>
            <?php endforeach; ?>
        </select>
        <?php endif; ?>

        <?php
        $sesiList = array_unique(array_filter(array_map(fn($r) => $r['participant']['sesi'] ?? 1, $rows)));
        sort($sesiList);
        ?>
        <?php if(count($sesiList) > 1): ?>
        <select id="summarySesi" onchange="filterSummary()" style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:7px 12px;color:var(--text);font-size:13px;outline:none">
            <option value="">Semua Sesi</option>
            <?php foreach($sesiList as $s): ?>
                <option value="<?= $s ?>">Sesi <?= $s ?></option>
            <?php endforeach; ?>
        </select>
        <?php endif; ?>
    </div>

    <div class="table-wrap">
        <table id="summaryTable">
            <thead>
                <tr>
                    <th style="width:45px">No</th>
                    <th>No. Peserta</th>
                    <th>NIS</th>
                    <th>Nama Lengkap</th>
                    <th>Kelas</th>
                    <th>Sesi</th>
                    <?php foreach($subjects as $s): ?>
                        <th style="text-align:center" title="<?= esc($s['nama_mapel']) ?>">
                            <?= esc($s['nama_mapel']) ?>
                        </th>
                    <?php endforeach; ?>
                    <th style="text-align:center;color:var(--accent)">Total</th>
                    <th style="text-align:center;color:var(--success)">Rata-Rata</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach($rows as $row): ?>
                <?php $p = $row['participant']; ?>
                <tr data-nama="<?= strtolower(esc($p['nama'])) ?>"
                    data-username="<?= strtolower(esc($p['nis'] . ' ' . $p['nomor_peserta'])) ?>"
                    data-kelas="<?= esc($p['kelas'] ?? '') ?>"
                    data-sesi="<?= $p['sesi'] ?? 1 ?>">
                    <td class="col-num"><?= $i++ ?></td>
                    <td><span class="badge" style="background:rgba(255,255,255,0.06);font-family:monospace"><?= esc($p['nomor_peserta']) ?></span></td>
                    <td><code><?= esc($p['nis'] ?: '-') ?></code></td>
                    <td><strong><?= esc($p['nama']) ?></strong></td>
                    <td><span class="badge badge-draft"><?= esc($p['kelas']) ?></span></td>
                    <td><?= $p['sesi'] ?? 1 ?></td>
                    <?php foreach($subjects as $s): ?>
                        <?php $val = $row['grades'][$s['id']] ?? null; ?>
                        <td style="text-align:center">
                            <?php if($val !== null): ?>
                                <span style="font-weight:600;color:<?= (float)$val >= 75 ? 'var(--success)' : 'var(--warning)' ?>">
                                    <?= number_format((float)$val, 1) ?>
                                </span>
                            <?php else: ?>
                                <span style="color:var(--muted)">-</span>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                    <td style="text-align:center;font-weight:700;color:var(--accent)">
                        <?= is_numeric($row['total']) ? number_format((float)$row['total'], 1) : '-' ?>
                    </td>
                    <td style="text-align:center;font-weight:700;color:var(--success)">
                        <?= is_numeric($row['average']) ? number_format((float)$row['average'], 2) : '-' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($rows)): ?>
                <tr>
                    <td colspan="<?= 8 + count($subjects) ?>" style="text-align:center;padding:40px;color:var(--muted)">
                        Belum ada peserta terdaftar dalam ujian ini.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function filterSummary() {
    var search = (document.getElementById('summarySearch')?.value || '').toLowerCase().trim();
    var kelas  = (document.getElementById('summaryKelas')?.value || '').toLowerCase();
    var sesi   = document.getElementById('summarySesi')?.value || '';

    var rows = document.querySelectorAll('#summaryTable tbody tr[data-nama]');
    var visible = 0;

    rows.forEach(function(row) {
        var nama     = row.dataset.nama || '';
        var username = row.dataset.username || '';
        var rKelas   = (row.dataset.kelas || '').toLowerCase();
        var rSesi    = row.dataset.sesi || '';

        var ok = true;
        if (search && !nama.includes(search) && !username.includes(search)) ok = false;
        if (kelas && rKelas !== kelas) ok = false;
        if (sesi && rSesi !== sesi) ok = false;

        row.style.display = ok ? '' : 'none';
        if (ok) {
            visible++;
            var numCell = row.querySelector('.col-num');
            if (numCell) numCell.textContent = visible;
        }
    });

    var counter = document.getElementById('summaryCounter');
    if (counter) {
        counter.textContent = 'Menampilkan ' + visible + ' dari ' + rows.length + ' Peserta';
    }
}
filterSummary();
</script>

<?php echo view('layout/footer'); ?>

