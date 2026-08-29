<?php echo view('layout/header', ['title' => $title]); ?>

<div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
        <h2 style="font-size:1.25rem;font-weight:700;color:#fff"><i class="fas fa-users" style="color:var(--accent);margin-right:8px"></i> Kelola Peserta Ujian: <?= esc($exam['nama_ujian']) ?></h2>
        <p style="font-size:13px;color:var(--muted)">Tahun: <?= esc($exam['tahun']) ?> | Semester: <?= esc($exam['semester']) ?></p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        <a href="<?= base_url('participant/print-kartu/'.$exam['id']) ?>" target="_blank" class="btn btn-primary">
            <i class="fas fa-id-card"></i> Cetak Kartu Peserta
        </a>
        <a href="<?= base_url('participant/print-kartu-meja/'.$exam['id']) ?>" target="_blank" class="btn btn-warning" style="color:#000;font-weight:600">
            <i class="fas fa-tags"></i> Cetak Kartu Meja
        </a>
        <a href="<?= base_url('participant/export-excel/'.$exam['id']) ?>" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Export Excel
        </a>
        <a href="<?= base_url('exam') ?>" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
</div>

<!-- Quick Action Panels Grid -->
<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:16px;margin-bottom:24px">
    <!-- Panel 1: Import dari Moodle -->
    <div class="card" style="border:1px solid rgba(99,102,241,0.3)">
        <div class="card-header" style="background:rgba(99,102,241,0.08)">
            <span class="card-title" style="font-size:13.5px"><i class="fas fa-graduation-cap" style="color:var(--accent)"></i> 1. Tarik Siswa dari Moodle</span>
        </div>
        <div class="card-body" style="padding:16px">
            <form action="<?= base_url('participant/'.$exam['id'].'/import') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="form-group" style="margin-bottom:12px">
                    <label class="form-label" style="font-size:12px">Pilih Mapel (Quiz Moodle)</label>
                    <select name="subject_id" class="form-control" style="font-size:12.5px" required>
                        <option value="">-- Pilih Mapel --</option>
                        <?php foreach($subjects as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= esc($s['nama_mapel']) ?> (Kelas <?= esc($s['kelas']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:12px">
                    <label class="form-label" style="font-size:12px">Set Label Kelas (Utk Nomor)</label>
                    <input type="text" name="kelas" class="form-control" style="font-size:12.5px" placeholder="Misal: 10 IPA" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;font-size:12.5px">
                    <i class="fas fa-download"></i> Tarik Data dari Moodle
                </button>
            </form>
        </div>
    </div>

    <!-- Panel 2: Import dari Excel + Auto-Sync ke Moodle -->
    <div class="card" style="border:1px solid rgba(16,185,129,0.3)">
        <div class="card-header" style="background:rgba(16,185,129,0.08)">
            <span class="card-title" style="font-size:13.5px"><i class="fas fa-file-excel" style="color:var(--success)"></i> 2. Import Excel & Sync Moodle</span>
        </div>
        <div class="card-body" style="padding:16px">
            <form action="<?= base_url('participant/'.$exam['id'].'/import-excel') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="form-group" style="margin-bottom:10px">
                    <label class="form-label" style="font-size:12px">Pilih File Spreadsheet (.xlsx/.xls)</label>
                    <input type="file" name="file_excel" class="form-control" accept=".xlsx,.xls,.csv" style="font-size:12px" required>
                </div>
                <div class="form-group" style="margin-bottom:10px">
                    <label class="form-label" style="font-size:12px">Tautkan ke Mapel Ujian (Opsional)</label>
                    <select name="subject_id" class="form-control" style="font-size:12px">
                        <option value="">-- Semua / Tanpa Enrol Khusus --</option>
                        <?php foreach($subjects as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= esc($s['nama_mapel']) ?> (Kelas <?= esc($s['kelas']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:12px">
                    <label style="font-size:12px;color:#cbd5e1;cursor:pointer;display:inline-flex;align-items:center;gap:6px">
                        <input type="checkbox" name="sync_moodle" value="1" checked> 
                        <strong>Otomatis Buat Akun & Enrol ke Moodle</strong>
                    </label>
                </div>
                <div style="display:flex;gap:8px">
                    <a href="<?= base_url('participant/download-template') ?>" class="btn btn-sm btn-outline" style="flex:1;justify-content:center;font-size:12px" title="Unduh Format Excel">
                        <i class="fas fa-download"></i> Template
                    </a>
                    <button type="submit" class="btn btn-sm btn-success" style="flex:1;justify-content:center;font-size:12px">
                        <i class="fas fa-upload"></i> Upload & Sync
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Panel 3: Pembagian Ruang, Sesi, & Sinkronisasi -->
    <div class="card" style="border:1px solid rgba(245,158,11,0.3)">
        <div class="card-header" style="background:rgba(245,158,11,0.08)">
            <span class="card-title" style="font-size:13.5px"><i class="fas fa-cogs" style="color:var(--warning)"></i> 3. Distribusi Ruang, Sesi & Sync</span>
        </div>
        <div class="card-body" style="padding:16px;display:flex;flex-direction:column;gap:10px">
            <!-- Form Bagi Ruang -->
            <form action="<?= base_url('participant/'.$exam['id'].'/assign-rooms') ?>" method="POST">
                <?= csrf_field() ?>
                <?php $roomIds = array_column($rooms, 'id'); ?>
                <input type="hidden" name="rooms_json" value='<?= json_encode($roomIds) ?>'>
                <button type="submit" class="btn btn-sm btn-outline" style="width:100%;justify-content:center;font-size:12px" onclick="return confirm('Bagi seluruh peserta ke dalam ruang ujian yang ada?')">
                    <i class="fas fa-door-open"></i> Bagi Ruang & Nomor Meja
                </button>
            </form>

            <!-- Form Bagi Sesi -->
            <form action="<?= base_url('participant/'.$exam['id'].'/assign-sessions') ?>" method="POST" style="display:flex;gap:6px">
                <?= csrf_field() ?>
                <select name="total_sesi" class="form-control" style="width:110px;font-size:12px">
                    <option value="2">2 Sesi</option>
                    <option value="3">3 Sesi</option>
                    <option value="4">4 Sesi</option>
                </select>
                <button type="submit" class="btn btn-sm btn-outline" style="flex:1;justify-content:center;font-size:12px">
                    <i class="fas fa-layer-group"></i> Bagi Sesi
                </button>
            </form>

            <!-- Form Sync Massal ke Moodle -->
            <a href="<?= base_url('participant/'.$exam['id'].'/sync-moodle') ?>" class="btn btn-sm btn-primary" style="justify-content:center;font-size:12px" onclick="return confirm('Sinkronkan seluruh peserta ujian ini menjadi akun Moodle aktif?')">
                <i class="fas fa-sync-alt"></i> Sinkronkan Akun ke Moodle
            </a>
        </div>
    </div>
</div>

<!-- Tabel Daftar Peserta -->
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-list-ol"></i> Daftar Peserta Ujian (<?= count($participants) ?> Siswa)</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No. Peserta</th>
                    <th>Nama Lengkap</th>
                    <th>NIS / NISN</th>
                    <th>Kelas</th>
                    <th>Ruang</th>
                    <th>Meja</th>
                    <th>Sesi</th>
                    <th>Password CBT</th>
                    <th>Status Moodle</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($participants as $p): ?>
                <tr>
                    <td><strong style="font-family:monospace;font-size:13.5px;color:var(--accent)"><?= esc($p['nomor_peserta']) ?></strong></td>
                    <td><strong><?= esc($p['nama']) ?></strong></td>
                    <td>
                        <?= esc($p['nis']) ?>
                        <?php if(!empty($p['nisn'])): ?>
                            <small style="color:var(--muted)">/ <?= esc($p['nisn']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge badge-draft"><?= esc($p['kelas']) ?></span></td>
                    <td>
                        <?php 
                            $r = array_filter($rooms, fn($x) => $x['id'] == $p['room_id']); 
                            $r = reset($r); 
                            echo $r ? '<span class="badge badge-aktif">' . esc($r['nama_ruang']) . '</span>' : '<span class="badge badge-draft">Belum Diset</span>'; 
                        ?>
                    </td>
                    <td>
                        <?= !empty($p['nomor_meja']) ? '<strong style="color:var(--warning)">#' . $p['nomor_meja'] . '</strong>' : '-' ?>
                    </td>
                    <td>
                        <span class="badge badge-selesai">Sesi <?= $p['sesi'] ?: 1 ?></span>
                    </td>
                    <td>
                        <code style="background:var(--surface2);padding:2px 6px;border-radius:4px;color:#fff"><?= esc($p['password'] ?: '-') ?></code>
                    </td>
                    <td>
                        <?php if(!empty($p['moodle_user_id'])): ?>
                            <span class="badge badge-aktif" title="Terkoneksi ke Akun Moodle ID: <?= $p['moodle_user_id'] ?>">
                                <i class="fas fa-check-circle" style="margin-right:3px"></i> Moodle #<?= $p['moodle_user_id'] ?>
                            </span>
                        <?php else: ?>
                            <span class="badge badge-draft" title="Belum memiliki akun Moodle">
                                <i class="fas fa-minus-circle" style="margin-right:3px"></i> Belum Sync
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= base_url('participant/delete/'.$p['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus peserta ini?')" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($participants)): ?>
                <tr>
                    <td colspan="10" style="text-align:center;padding:40px;color:var(--muted)">
                        <i class="fas fa-user-friends" style="font-size:36px;opacity:0.3;margin-bottom:8px;display:block"></i>
                        Belum ada peserta terdaftar. Silakan tarik dari Moodle atau unggah file Excel di atas.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php echo view('layout/footer'); ?>
