<?php echo view('layout/header', ['title' => $title]); ?>

<div style="display:grid;grid-template-columns:1fr 1.2fr;gap:24px;align-items:flex-start">
    <!-- Kolom Kiri: Status & Form Konfigurasi Database -->
    <div>
        <!-- Status Card -->
        <div class="card mb-4" style="margin-bottom:24px">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-network-wired"></i> Status Koneksi Moodle</span>
                <?php if($status['success']): ?>
                    <span class="badge badge-aktif"><i class="fas fa-check-circle" style="margin-right:4px"></i> Terhubung</span>
                <?php else: ?>
                    <span class="badge badge-absen"><i class="fas fa-times-circle" style="margin-right:4px"></i> Terputus</span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if($status['success']): ?>
                    <div style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.25);border-radius:10px;padding:16px;margin-bottom:16px">
                        <div style="display:flex;align-items:center;gap:10px;color:var(--success);font-weight:600;margin-bottom:8px">
                            <i class="fas fa-database" style="font-size:18px"></i> Database Terhubung
                        </div>
                        <p style="font-size:13px;color:var(--text);margin-bottom:4px">Database: <strong><?= esc($status['database']) ?></strong> (Prefix: <code><?= esc($status['prefix']) ?></code>)</p>
                        <p style="font-size:12px;color:var(--muted)">Server MySQL: <?= esc($status['version']) ?></p>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(3, 1fr);gap:12px">
                        <div style="background:var(--surface2);border-radius:8px;padding:12px;text-align:center">
                            <div style="font-size:20px;font-weight:700;color:var(--accent)"><?= $status['stats']['courses'] ?? 0 ?></div>
                            <div style="font-size:11px;color:var(--muted);font-weight:500;margin-top:2px">Kursus Moodle</div>
                        </div>
                        <div style="background:var(--surface2);border-radius:8px;padding:12px;text-align:center">
                            <div style="font-size:20px;font-weight:700;color:var(--success)"><?= $status['stats']['quizzes'] ?? 0 ?></div>
                            <div style="font-size:11px;color:var(--muted);font-weight:500;margin-top:2px">Quiz / Ujian</div>
                        </div>
                        <div style="background:var(--surface2);border-radius:8px;padding:12px;text-align:center">
                            <div style="font-size:20px;font-weight:700;color:var(--warning)"><?= $status['stats']['users'] ?? 0 ?></div>
                            <div style="font-size:11px;color:var(--muted);font-weight:500;margin-top:2px">Siswa Terdaftar</div>
                        </div>
                    </div>
                <?php else: ?>
                    <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.25);border-radius:10px;padding:16px">
                        <div style="display:flex;align-items:center;gap:10px;color:var(--danger);font-weight:600;margin-bottom:8px">
                            <i class="fas fa-exclamation-triangle" style="font-size:18px"></i> Gagal Terhubung
                        </div>
                        <p style="font-size:13px;color:var(--text)"><?= esc($status['message']) ?></p>
                        <p style="font-size:12px;color:var(--muted);margin-top:8px">Pastikan database MySQL Moodle aktif dan kredensial di bawah ini sudah sesuai.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Form Pengaturan Koneksi -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-sliders-h"></i> Pengaturan Koneksi Database Moodle</span>
            </div>
            <div class="card-body">
                <form action="<?= base_url('moodle/save-config') ?>" method="POST" id="configForm">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label class="form-label">Host Database</label>
                        <input type="text" name="hostname" id="cfg_hostname" class="form-control" value="<?= esc($config['hostname']) ?>" required placeholder="Contoh: 127.0.0.1 atau localhost">
                    </div>
                    <div style="display:grid;grid-template-columns:2fr 1fr;gap:12px">
                        <div class="form-group">
                            <label class="form-label">Nama Database Moodle</label>
                            <input type="text" name="database" id="cfg_database" class="form-control" value="<?= esc($config['database']) ?>" required placeholder="Contoh: moodle">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Port</label>
                            <input type="number" name="port" id="cfg_port" class="form-control" value="<?= esc($config['port']) ?>" placeholder="3306">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Table Prefix</label>
                        <input type="text" name="DBPrefix" id="cfg_prefix" class="form-control" value="<?= esc($config['DBPrefix']) ?>" placeholder="Contoh: mdl_">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Username Database</label>
                        <input type="text" name="username" id="cfg_username" class="form-control" value="<?= esc($config['username']) ?>" required placeholder="Contoh: root">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password Database</label>
                        <input type="password" name="password" id="cfg_password" class="form-control" value="<?= esc($config['password']) ?>" placeholder="Kosongkan jika tidak ada password">
                    </div>

                    <div id="testResult" style="display:none;margin-bottom:16px;padding:12px;border-radius:8px;font-size:13px"></div>

                    <div style="display:flex;gap:10px">
                        <button type="button" class="btn btn-outline" id="btnTestConn" style="flex:1;justify-content:center">
                            <i class="fas fa-plug"></i> Uji Koneksi
                        </button>
                        <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Eksplorasi Kursus & Quiz Moodle -->
    <div>
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-graduation-cap"></i> Data Kursus & Quiz di Moodle</span>
                <a href="<?= base_url('moodle/courses') ?>" class="btn btn-sm btn-outline">Lihat Detail Lengkap</a>
            </div>
            <div class="card-body" style="padding:0">
                <?php if(empty($courses)): ?>
                    <div style="text-align:center;padding:40px;color:var(--muted)">
                        <i class="fas fa-folder-open" style="font-size:36px;margin-bottom:12px;opacity:0.4;display:block"></i>
                        <p>Belum ada data kursus atau koneksi Moodle belum tersambung.</p>
                    </div>
                <?php else: ?>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nama Kursus</th>
                                    <th>Siswa</th>
                                    <th>Quiz Tersedia</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($courses as $c): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($c['fullname']) ?></strong>
                                        <br><small style="color:var(--muted)"><?= esc($c['shortname']) ?> (ID: <?= $c['id'] ?>)</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-draft"><i class="fas fa-users" style="margin-right:4px"></i> <?= $c['student_count'] ?></span>
                                    </td>
                                    <td>
                                        <?php if(empty($c['quizzes'])): ?>
                                            <span style="color:var(--muted);font-size:12px">Tidak ada quiz</span>
                                        <?php else: ?>
                                            <?php foreach($c['quizzes'] as $qz): ?>
                                                <div style="margin-bottom:4px">
                                                    <span class="badge badge-aktif">
                                                        <i class="fas fa-tasks" style="margin-right:4px"></i> <?= esc($qz['name']) ?> (ID: <?= $qz['id'] ?>)
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
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

        <!-- Tips Penggunaan -->
        <div class="card" style="margin-top:24px;border-left:4px solid var(--accent)">
            <div class="card-body">
                <h5 style="color:#fff;margin-bottom:8px;font-size:14px"><i class="fas fa-lightbulb" style="color:var(--warning);margin-right:6px"></i> Panduan Alur Integrasi Moodle:</h5>
                <ol style="font-size:13px;color:var(--muted);line-height:1.7;padding-left:20px">
                    <li>Pastikan status koneksi di atas bertuliskan <strong>Terhubung</strong>.</li>
                    <li>Buat ujian di menu <strong>Manajemen Ujian</strong>.</li>
                    <li>Tambahkan Mata Pelajaran dan pilih <strong>Quiz Moodle</strong> terkait.</li>
                    <li>Di menu <strong>Peserta & Nomor</strong>, klik <strong>Import dari Moodle</strong> untuk menarik data siswa secara otomatis.</li>
                    <li>Pantau pengerjaan siswa secara realtime di menu <strong>Monitoring Ujian</strong>.</li>
                    <li>Unduh rekap nilai ujian siswa dalam format Excel/PDF di menu <strong>Download Nilai</strong>.</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('btnTestConn').addEventListener('click', function() {
    var btn = this;
    var originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menguji...';
    btn.disabled = true;

    var resultBox = document.getElementById('testResult');
    resultBox.style.display = 'none';

    var formData = new FormData();
    formData.append('hostname', document.getElementById('cfg_hostname').value);
    formData.append('database', document.getElementById('cfg_database').value);
    formData.append('port', document.getElementById('cfg_port').value);
    formData.append('DBPrefix', document.getElementById('cfg_prefix').value);
    formData.append('username', document.getElementById('cfg_username').value);
    formData.append('password', document.getElementById('cfg_password').value);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    fetch('<?= base_url('moodle/test-connection') ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        resultBox.style.display = 'block';
        if (data.success) {
            resultBox.style.background = 'rgba(16,185,129,0.15)';
            resultBox.style.border = '1px solid rgba(16,185,129,0.3)';
            resultBox.style.color = '#6ee7b7';
            resultBox.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
        } else {
            resultBox.style.background = 'rgba(239,68,68,0.15)';
            resultBox.style.border = '1px solid rgba(239,68,68,0.3)';
            resultBox.style.color = '#fca5a5';
            resultBox.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + data.message;
        }
    })
    .catch(err => {
        resultBox.style.display = 'block';
        resultBox.style.background = 'rgba(239,68,68,0.15)';
        resultBox.style.border = '1px solid rgba(239,68,68,0.3)';
        resultBox.style.color = '#fca5a5';
        resultBox.innerHTML = '<i class="fas fa-exclamation-circle"></i> Terjadi kesalahan jaringan saat menguji koneksi.';
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
});
</script>

<?php echo view('layout/footer'); ?>
