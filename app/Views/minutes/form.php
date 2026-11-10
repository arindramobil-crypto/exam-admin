<?php echo view('layout/header', ['title' => $title]); ?>

<!-- Header & Quick Actions -->
<div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
        <h2 style="font-size:1.25rem;font-weight:700;color:#fff">
            <i class="fas fa-file-alt" style="color:var(--accent);margin-right:8px"></i> <?= esc($title) ?>
        </h2>
        <p style="font-size:13px;color:var(--muted)">
            <?= esc($exam['nama_ujian']) ?> &bull; Mapel: <strong><?= esc($subject['nama_mapel']) ?> (<?= esc($subject['kelas']) ?>)</strong> &bull; Ruang: <strong><?= esc($room['nama_ruang']) ?></strong>
        </p>
    </div>
    <div>
        <a href="<?= base_url('minutes/'.$exam['id']) ?>" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Berita Acara
        </a>
    </div>
</div>

<div class="card" style="max-width:760px">
    <div class="card-header">
        <span class="card-title">Formulir Berita Acara Pelaksanaan Ujian CBT</span>
        <span class="badge <?= ($existing && $existing['status'] === 'final') ? 'badge-aktif' : 'badge-draft' ?>">
            <?= ($existing && $existing['status'] === 'final') ? 'Status: Final' : 'Status: Draft' ?>
        </span>
    </div>
    <div class="card-body">
        <form action="<?= base_url('minutes/save') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="exam_id" value="<?= $exam['id'] ?>">
            <input type="hidden" name="subject_id" value="<?= $subject['id'] ?>">
            <input type="hidden" name="room_id" value="<?= $room['id'] ?>">

            <!-- Ringkasan Info Ujian -->
            <div style="background:rgba(99,102,241,0.06);border:1px solid rgba(99,102,241,0.25);border-radius:10px;padding:16px;margin-bottom:22px;display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:12px">
                <div>
                    <div style="font-size:11px;color:var(--muted)">Mata Pelajaran</div>
                    <div style="font-size:14px;font-weight:700;color:#fff;margin-top:2px"><?= esc($subject['nama_mapel']) ?></div>
                    <small style="color:var(--muted)">Kelas <?= esc($subject['kelas']) ?></small>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--muted)">Ruangan &amp; Kapasitas</div>
                    <div style="font-size:14px;font-weight:700;color:var(--accent);margin-top:2px"><?= esc($room['nama_ruang']) ?></div>
                    <small style="color:var(--muted)"><?= esc($room['gedung'] ?: 'Gedung Utama') ?> (Lt. <?= esc($room['lantai'] ?: 1) ?>)</small>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--muted)">Peserta Terdaftar Ruang Ini</div>
                    <div style="font-size:14px;font-weight:700;color:var(--success);margin-top:2px"><?= $totalSeharusnya ?> Siswa</div>
                    <small style="color:var(--muted)">Sesuai penetapan meja</small>
                </div>
            </div>

            <!-- Jam Pelaksanaan & Jumlah Soal -->
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px">
                <div class="form-group">
                    <label class="form-label">Waktu Mulai Ujian</label>
                    <input type="time" name="waktu_mulai" class="form-control"
                        value="<?= old('waktu_mulai', $existing['waktu_mulai'] ?? ($subject['jam_mulai'] ?? '07:30')) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Waktu Selesai Ujian</label>
                    <input type="time" name="waktu_selesai" class="form-control"
                        value="<?= old('waktu_selesai', $existing['waktu_selesai'] ?? ($subject['jam_selesai'] ?? '09:00')) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Jumlah Butir Soal</label>
                    <input type="number" name="jml_soal" class="form-control"
                        value="<?= old('jml_soal', $existing['jml_soal'] ?? 40) ?>" min="1" required>
                </div>
            </div>

            <!-- Kehadiran Siswa -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                <div class="form-group">
                    <label class="form-label" style="color:var(--success)">
                        <i class="fas fa-user-check"></i> Jumlah Peserta Hadir *
                    </label>
                    <input type="number" name="jml_peserta_hadir" class="form-control"
                        value="<?= old('jml_peserta_hadir', $existing['jml_peserta_hadir'] ?? $hadir) ?>" min="0" required>
                    <small style="color:var(--muted);font-size:11px">Otomatis sinkron dari daftar hadir (<?= $hadir ?> hadir)</small>
                </div>
                <div class="form-group">
                    <label class="form-label" style="color:var(--danger)">
                        <i class="fas fa-user-times"></i> Jumlah Peserta Tidak Hadir (Absen) *
                    </label>
                    <input type="number" name="jml_peserta_absen" class="form-control"
                        value="<?= old('jml_peserta_absen', $existing['jml_peserta_absen'] ?? $absen) ?>" min="0" required>
                    <small style="color:var(--muted);font-size:11px">Otomatis sinkron (<?= $absen ?> tidak hadir)</small>
                </div>
            </div>

            <!-- Petugas Ruang: Pengawas & Proktor -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user-shield" style="color:var(--accent)"></i> Nama Pengawas Ruang *
                    </label>
                    <input type="text" name="nama_pengawas1" class="form-control" placeholder="Nama Lengkap Pengawas"
                        value="<?= old('nama_pengawas1', $existing['nama_pengawas1'] ?? ($defaultPengawas ?: '')) ?>" required>
                    <?php if(!empty($defaultPengawas)): ?>
                        <small style="color:var(--accent);font-size:11px"><i class="fas fa-info-circle"></i> Sesuai penugasan jadwal pengawas</small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-laptop" style="color:var(--warning)"></i> Nama Proktor / Teknisi *
                    </label>
                    <input type="text" name="nama_pengawas2" class="form-control" placeholder="Nama Lengkap Proktor"
                        value="<?= old('nama_pengawas2', $existing['nama_pengawas2'] ?? ($defaultProktor ?: '')) ?>" required>
                    <?php if(!empty($defaultProktor)): ?>
                        <small style="color:var(--warning);font-size:11px"><i class="fas fa-info-circle"></i> Sesuai penugasan proktor</small>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Catatan Kejadian Penting -->
            <div class="form-group">
                <label class="form-label">Catatan Kejadian Penting Selama Ujian</label>
                <textarea name="catatan" class="form-control" rows="4" placeholder="Tuliskan catatan kejadian penting jika ada (misal: Peserta A izin sakit di tengah ujian, kendala jaringan/koneksi, mati lampu, pergantian komputer, dll. Jika tidak ada, biarkan kosong)."><?= old('catatan', $existing['catatan'] ?? '') ?></textarea>
            </div>

            <!-- Status Berita Acara -->
            <div class="form-group">
                <label class="form-label">Status Dokumen Berita Acara</label>
                <select name="status" class="form-control">
                    <option value="draft" <?= old('status', $existing['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft (Dapat diedit kembali)</option>
                    <option value="final" <?= old('status', $existing['status'] ?? 'draft') === 'final' ? 'selected' : '' ?>>Final (Siap dicetak resmi)</option>
                </select>
            </div>

            <div style="margin-top:24px;display:flex;gap:10px;justify-content:flex-end">
                <a href="<?= base_url('minutes/'.$exam['id']) ?>" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Berita Acara
                </button>
            </div>
        </form>
    </div>
</div>

<?php echo view('layout/footer'); ?>
