<?php echo view('layout/header', ['title' => $title]); ?>

<div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
        <h2 style="font-size:1.25rem;font-weight:700;color:#fff"><i class="fas fa-key" style="color:var(--accent);margin-right:8px"></i> Rilis Token Ujian CBT</h2>
        <p style="font-size:13px;color:var(--muted)"><?= esc($exam['nama_ujian']) ?> (Tahun: <?= esc($exam['tahun']) ?>)</p>
    </div>
    <div style="display:flex;gap:10px">
        <a href="<?= base_url('token/display/'.$exam['id']) ?>" target="_blank" class="btn btn-warning" style="color:#000;font-weight:700">
            <i class="fas fa-tv"></i> Buka Layar Proyektor Token
        </a>
        <a href="<?= base_url('exam') ?>" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1.2fr;gap:24px;align-items:flex-start">
    <!-- Active Token Box & Generator -->
    <div>
        <!-- Active Token Display Card -->
        <div class="card mb-4" style="margin-bottom:24px;border:2px solid <?= !empty($activeToken) && $activeToken['is_active'] ? 'var(--accent)' : 'var(--border)' ?>">
            <div class="card-header" style="background:var(--surface2)">
                <span class="card-title"><i class="fas fa-broadcast-tower"></i> Token Aktif Saat Ini</span>
                <?php if(!empty($activeToken) && $activeToken['is_active']): ?>
                    <span class="badge badge-aktif"><i class="fas fa-check-circle" style="margin-right:4px"></i> Aktif</span>
                <?php else: ?>
                    <span class="badge badge-absen"><i class="fas fa-times-circle" style="margin-right:4px"></i> Tidak Ada Token Aktif</span>
                <?php endif; ?>
            </div>
            <div class="card-body" style="text-align:center;padding:30px 20px">
                <?php if(!empty($activeToken) && $activeToken['is_active']): ?>
                    <div style="font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:0.1em;margin-bottom:8px">Kode Token CBT:</div>
                    <div style="font-size:56px;font-weight:900;letter-spacing:8px;color:#fff;background:linear-gradient(135deg, var(--accent), var(--accent2));-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin:10px 0;font-family:'Courier New', monospace">
                        <?= esc($activeToken['token']) ?>
                    </div>
                    <p style="font-size:13px;color:var(--muted);margin-top:12px">
                        Berlaku hingga: <strong style="color:var(--warning)"><?= date('H:i:s (d M Y)', strtotime($activeToken['expires_at'])) ?></strong>
                    </p>
                    <div style="margin-top:20px;display:flex;justify-content:center;gap:10px">
                        <a href="<?= base_url('token/deactivate/'.$activeToken['id']) ?>" class="btn btn-danger" onclick="return confirm('Nonaktifkan token ini?')">
                            <i class="fas fa-stop-circle"></i> Hentikan / Nonaktifkan
                        </a>
                        <a href="<?= base_url('token/display/'.$exam['id']) ?>" target="_blank" class="btn btn-primary">
                            <i class="fas fa-expand"></i> Tampilkan Layar Penuh
                        </a>
                    </div>
                <?php else: ?>
                    <div style="padding:20px;color:var(--muted)">
                        <i class="fas fa-lock" style="font-size:40px;opacity:0.3;margin-bottom:12px;display:block"></i>
                        <p style="font-size:14px">Belum ada token yang dirilis untuk ujian ini.</p>
                        <small>Gunakan form di bawah untuk membuat token baru.</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Form Rilis Token Baru -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-plus-circle"></i> Rilis / Perbarui Token Baru</span>
            </div>
            <div class="card-body">
                <form action="<?= base_url('token/generate/'.$exam['id']) ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label class="form-label">Pilih Mapel (Opsional)</label>
                        <select name="subject_id" class="form-control">
                            <option value="">-- Berlaku untuk Semua Mapel --</option>
                            <?php foreach($subjects as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= esc($s['nama_mapel']) ?> (Kelas <?= esc($s['kelas']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Masa Berlaku Token (Menit)</label>
                        <select name="durasi_menit" class="form-control">
                            <option value="15" selected>15 Menit (Standar)</option>
                            <option value="30">30 Menit</option>
                            <option value="45">45 Menit</option>
                            <option value="60">60 Menit (1 Jam)</option>
                            <option value="120">120 Menit (2 Jam)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px">
                        <i class="fas fa-sync-alt"></i> Rilis Token Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Riwayat Token -->
    <div>
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-history"></i> Riwayat Token Ujian</span>
            </div>
            <div class="card-body" style="padding:0">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Token</th>
                                <th>Durasi</th>
                                <th>Waktu Rilis</th>
                                <th>Kedaluwarsa</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recentTokens as $t): ?>
                            <tr>
                                <td>
                                    <strong style="font-family:'Courier New', monospace;font-size:15px;color:var(--accent)">
                                        <?= esc($t['token']) ?>
                                    </strong>
                                </td>
                                <td><?= $t['durasi_menit'] ?> Menit</td>
                                <td><?= date('H:i (d/m)', strtotime($t['created_at'])) ?></td>
                                <td><?= date('H:i (d/m)', strtotime($t['expires_at'])) ?></td>
                                <td>
                                    <?php if($t['is_active'] && strtotime($t['expires_at']) > time()): ?>
                                        <span class="badge badge-aktif">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-draft">Kedaluwarsa</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($recentTokens)): ?>
                            <tr>
                                <td colspan="5" style="text-align:center;padding:30px;color:var(--muted)">Belum ada riwayat token.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo view('layout/footer'); ?>
