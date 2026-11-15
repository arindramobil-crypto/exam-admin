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
        <a href="<?= base_url('room/denah/'.$exam['id']) ?>" target="_blank" class="btn btn-outline" style="color:var(--accent);border-color:rgba(99,102,241,0.4)">
            <i class="fas fa-th-large"></i> Denah Tempat Duduk
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
                        <strong>Otomatis Buat Akun, Kohor Kelas &amp; Enrol ke Moodle</strong>
                    </label>
                    <small style="display:block;font-size:11px;color:var(--muted);margin-top:3px">
                        <i class="fas fa-info-circle" style="color:var(--accent)"></i> Template Excel kini dilengkapi kolom <strong>Ruang</strong> &amp; <strong>No. Meja</strong>, serta otomatis membuat <strong>Kohor (Cohort)</strong> dari kelas di Moodle.
                    </small>
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
    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <span class="card-title"><i class="fas fa-list-ol"></i> Daftar Peserta Ujian (<?= count($participants) ?> Siswa)</span>
        <?php if(!empty($participants)): ?>
        <div style="display:flex;gap:6px;flex-wrap:wrap">
            <a href="<?= base_url('participant/'.$exam['id'].'/reset-allocation') ?>" class="btn btn-sm btn-outline" style="color:var(--warning);border-color:rgba(245,158,11,0.4);font-size:11.5px" onclick="event.preventDefault(); showConfirm('<?= base_url('participant/'.$exam['id'].'/reset-allocation') ?>', 'Reset Penempatan Ruang & Meja', 'Apakah Anda yakin ingin mengosongkan penempatan ruang dan nomor meja untuk seluruh peserta ujian ini? Data siswa tidak akan terhapus.', 'btn-warning')" title="Kosongkan penempatan ruang dan meja">
                <i class="fas fa-undo"></i> Reset Ruang & Meja
            </a>
            <a href="<?= base_url('participant/'.$exam['id'].'/reset-passwords') ?>" class="btn btn-sm btn-outline" style="color:#38bdf8;border-color:rgba(56,189,248,0.4);font-size:11.5px" onclick="event.preventDefault(); showConfirm('<?= base_url('participant/'.$exam['id'].'/reset-passwords') ?>', 'Reset Password CBT', 'Apakah Anda yakin ingin membuat ulang (generate acak baru) seluruh password peserta ujian ini?', 'btn-info')" title="Generate ulang seluruh password siswa">
                <i class="fas fa-key"></i> Reset Password
            </a>
            <a href="<?= base_url('participant/'.$exam['id'].'/reset-all') ?>" class="btn btn-sm btn-danger" style="font-size:11.5px" onclick="event.preventDefault(); showConfirm('<?= base_url('participant/'.$exam['id'].'/reset-all') ?>', 'Reset Semua Peserta Ujian', 'PERINGATAN: Seluruh <?= count($participants) ?> peserta ujian ini akan DIHAPUS dari daftar ujian. Tindakan ini berguna jika Anda salah upload/tarik data dan ingin memulai dari awal. Yakin ingin melanjutkan?', 'btn-danger')" title="Hapus dan kosongkan seluruh data peserta ujian ini">
                <i class="fas fa-trash-alt"></i> Reset Semua Peserta
            </a>
        </div>
        <?php endif; ?>
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
                    <th style="text-align:center">Aksi</th>
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
                    <td style="text-align:center;white-space:nowrap">
                        <div style="display:inline-flex;gap:4px">
                            <button type="button" class="btn btn-sm btn-warning edit-participant-btn" 
                                    data-id="<?= $p['id'] ?>"
                                    data-nomor="<?= esc($p['nomor_peserta']) ?>"
                                    data-nama="<?= esc($p['nama']) ?>"
                                    data-nis="<?= esc($p['nis']) ?>"
                                    data-nisn="<?= esc($p['nisn']) ?>"
                                    data-kelas="<?= esc($p['kelas']) ?>"
                                    data-room="<?= esc($p['room_id']) ?>"
                                    data-meja="<?= esc($p['nomor_meja']) ?>"
                                    data-sesi="<?= esc($p['sesi'] ?: 1) ?>"
                                    data-password="<?= esc($p['password']) ?>"
                                    data-moodle="<?= esc($p['moodle_user_id']) ?>"
                                    style="padding:4px 8px;font-size:12px;color:#000;font-weight:600"
                                    title="Edit Data Peserta">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <a href="<?= base_url('participant/delete/'.$p['id']) ?>" class="btn btn-sm btn-danger" style="padding:4px 8px;font-size:12px" onclick="event.preventDefault(); showConfirm('<?= base_url('participant/delete/'.$p['id']) ?>', 'Hapus Peserta', 'Yakin ingin menghapus peserta <?= esc(addslashes($p['nama'])) ?> (<?= esc($p['nomor_peserta']) ?>)?', 'btn-danger')" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
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

<!-- Modal Edit Peserta Ujian -->
<div id="modalEditParticipant" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.7);z-index:99999;align-items:center;justify-content:center;padding:16px;overflow-y:auto">
    <div style="background:var(--card-bg, #1e293b);border:1px solid var(--border, #334155);border-radius:14px;padding:24px;max-width:560px;width:100%;box-shadow:0 25px 60px rgba(0,0,0,0.7);margin:auto">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;border-bottom:1px solid var(--border, #334155);padding-bottom:12px">
            <h4 style="margin:0;color:#fff;display:flex;align-items:center;gap:8px;font-size:16px">
                <i class="fas fa-user-edit" style="color:var(--warning)"></i> Edit Data Peserta Ujian
            </h4>
            <button type="button" onclick="closeEditModal()" style="background:none;border:none;color:var(--muted);font-size:22px;cursor:pointer;line-height:1">&times;</button>
        </div>

        <form id="formEditParticipant" action="" method="POST">
            <?= csrf_field() ?>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
                <div class="form-group" style="margin:0">
                    <label class="form-label" style="font-size:11.5px">Nomor Peserta</label>
                    <input type="text" name="nomor_peserta" id="edit_nomor_peserta" class="form-control" style="font-family:monospace;font-size:13px" required>
                </div>
                <div class="form-group" style="margin:0">
                    <label class="form-label" style="font-size:11.5px">Kelas</label>
                    <input type="text" name="kelas" id="edit_kelas" class="form-control" style="font-size:13px" required>
                </div>
            </div>

            <div class="form-group" style="margin-bottom:12px">
                <label class="form-label" style="font-size:11.5px">Nama Lengkap Siswa</label>
                <input type="text" name="nama" id="edit_nama" class="form-control" style="font-size:13.5px;font-weight:600" required>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
                <div class="form-group" style="margin:0">
                    <label class="form-label" style="font-size:11.5px">NIS (Username)</label>
                    <input type="text" name="nis" id="edit_nis" class="form-control" style="font-size:13px">
                </div>
                <div class="form-group" style="margin:0">
                    <label class="form-label" style="font-size:11.5px">NISN (Opsional)</label>
                    <input type="text" name="nisn" id="edit_nisn" class="form-control" style="font-size:13px">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:10px;margin-bottom:12px">
                <div class="form-group" style="margin:0">
                    <label class="form-label" style="font-size:11.5px">Ruang Ujian</label>
                    <select name="room_id" id="edit_room_id" class="form-control" style="font-size:12.5px">
                        <option value="">-- Belum Diset --</option>
                        <?php foreach($rooms as $r): ?>
                            <option value="<?= $r['id'] ?>"><?= esc($r['nama_ruang']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="margin:0">
                    <label class="form-label" style="font-size:11.5px">No. Meja</label>
                    <input type="number" name="nomor_meja" id="edit_nomor_meja" class="form-control" min="1" placeholder="-" style="font-size:13px">
                </div>
                <div class="form-group" style="margin:0">
                    <label class="form-label" style="font-size:11.5px">Sesi</label>
                    <select name="sesi" id="edit_sesi" class="form-control" style="font-size:12.5px">
                        <option value="1">Sesi 1</option>
                        <option value="2">Sesi 2</option>
                        <option value="3">Sesi 3</option>
                        <option value="4">Sesi 4</option>
                        <option value="5">Sesi 5</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom:14px">
                <label class="form-label" style="font-size:11.5px">Password CBT</label>
                <div style="display:flex;gap:6px">
                    <input type="text" name="password" id="edit_password" class="form-control" style="font-family:monospace;font-size:13.5px;letter-spacing:1px;font-weight:700" required>
                    <button type="button" class="btn btn-sm btn-outline" onclick="generateRandomEditPassword()" title="Generate Password Acak" style="white-space:nowrap">
                        <i class="fas fa-random"></i> Acak
                    </button>
                </div>
            </div>

            <div id="moodleSyncOptionBox" style="background:rgba(99,102,241,0.08);border:1px solid rgba(99,102,241,0.3);border-radius:8px;padding:10px 12px;margin-bottom:16px;display:none">
                <label style="font-size:12px;color:#cbd5e1;cursor:pointer;display:flex;align-items:center;gap:8px;margin:0">
                    <input type="checkbox" name="sync_moodle" id="edit_sync_moodle" value="1" checked>
                    <span>Sinkronkan perubahan nama & password ke akun Moodle aktif</span>
                </label>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:8px;border-top:1px solid var(--border, #334155);padding-top:14px">
                <button type="button" onclick="closeEditModal()" class="btn btn-sm btn-outline">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(data) {
    document.getElementById('formEditParticipant').action = '<?= base_url('participant/update') ?>/' + data.id;
    document.getElementById('edit_nomor_peserta').value = data.nomor || '';
    document.getElementById('edit_nama').value = data.nama || '';
    document.getElementById('edit_nis').value = data.nis || '';
    document.getElementById('edit_nisn').value = data.nisn || '';
    document.getElementById('edit_kelas').value = data.kelas || '';
    document.getElementById('edit_room_id').value = data.room || '';
    document.getElementById('edit_nomor_meja').value = data.meja || '';
    document.getElementById('edit_sesi').value = data.sesi || '1';
    document.getElementById('edit_password').value = data.password || '';

    var moodleBox = document.getElementById('moodleSyncOptionBox');
    if (data.moodle && parseInt(data.moodle) > 0) {
        moodleBox.style.display = 'block';
    } else {
        moodleBox.style.display = 'none';
    }

    var modal = document.getElementById('modalEditParticipant');
    if (modal) modal.style.display = 'flex';
}

function closeEditModal() {
    var modal = document.getElementById('modalEditParticipant');
    if (modal) modal.style.display = 'none';
}

function generateRandomEditPassword() {
    var chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    var res = '';
    for (var i = 0; i < 6; i++) {
        res += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('edit_password').value = res;
}

document.querySelectorAll('.edit-participant-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        openEditModal(this.dataset);
    });
});
</script>

<?php echo view('layout/footer'); ?>
