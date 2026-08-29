<?php echo view('layout/header', ['title' => $title]); ?>

<div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
        <h2 style="font-size:1.25rem;font-weight:700;color:#fff"><i class="fas fa-user-shield" style="color:var(--accent);margin-right:8px"></i> Pengawas & Proktor Ujian</h2>
        <p style="font-size:13px;color:var(--muted)"><?= esc($exam['nama_ujian']) ?> (Tahun: <?= esc($exam['tahun']) ?>)</p>
    </div>
    <div style="display:flex;gap:10px">
        <a href="<?= base_url('supervisor/print-jadwal/'.$exam['id']) ?>" target="_blank" class="btn btn-primary">
            <i class="fas fa-print"></i> Cetak Jadwal Pengawas (PDF)
        </a>
        <a href="<?= base_url('exam') ?>" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:flex-start">
    <!-- Tabel Daftar Pengawas -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-list"></i> Penugasan Pengawas & Proktor Ruang</span>
            <span class="badge badge-selesai"><?= count($supervisors) ?> Petugas</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Peran</th>
                        <th>Nama Petugas</th>
                        <th>NIP / Kontak</th>
                        <th>Ruang Ujian</th>
                        <th>Sesi</th>
                        <th>Mapel</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($supervisors as $sp): ?>
                    <tr>
                        <td>
                            <?php if($sp['peran'] === 'proktor'): ?>
                                <span class="badge badge-aktif"><i class="fas fa-laptop" style="margin-right:4px"></i> Proktor</span>
                            <?php elseif($sp['peran'] === 'teknisi'): ?>
                                <span class="badge badge-warning" style="background:rgba(245,158,11,0.15);color:var(--warning)"><i class="fas fa-wrench" style="margin-right:4px"></i> Teknisi</span>
                            <?php else: ?>
                                <span class="badge badge-draft"><i class="fas fa-eye" style="margin-right:4px"></i> Pengawas</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= esc($sp['nama_pengawas']) ?></strong></td>
                        <td>
                            <?= !empty($sp['nip']) ? esc($sp['nip']) : '-' ?>
                            <?php if(!empty($sp['kontak'])): ?>
                                <br><small style="color:var(--muted)"><i class="fas fa-phone-alt"></i> <?= esc($sp['kontak']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge badge-draft"><?= esc($sp['nama_ruang'] ?? 'Semua Ruang') ?></span></td>
                        <td><span class="badge badge-selesai">Sesi <?= $sp['sesi'] ?></span></td>
                        <td><small><?= esc($sp['nama_mapel'] ?? 'Semua Mapel') ?></small></td>
                        <td>
                            <a href="<?= base_url('supervisor/delete/'.$sp['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus penugasan ini?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($supervisors)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px;color:var(--muted)">
                            <i class="fas fa-user-slash" style="font-size:32px;opacity:0.3;margin-bottom:8px;display:block"></i>
                            Belum ada jadwal penugasan pengawas/proktor. Tambahkan di form samping.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Form Tambah Pengawas -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-plus-circle"></i> Tambah Pengawas / Proktor</span>
        </div>
        <div class="card-body">
            <form action="<?= base_url('supervisor/store/'.$exam['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label class="form-label">Nama Lengkap & Gelar <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="nama_pengawas" class="form-control" placeholder="Contoh: Drs. Budi Santoso, M.Pd" required>
                </div>
                <div class="form-group">
                    <label class="form-label">NIP / NUPTK (Opsional)</label>
                    <input type="text" name="nip" class="form-control" placeholder="19800101...">
                </div>
                <div class="form-group">
                    <label class="form-label">Peran Tugas</label>
                    <select name="peran" class="form-control" required>
                        <option value="pengawas" selected>Pengawas Ruang</option>
                        <option value="proktor">Proktor CBT</option>
                        <option value="teknisi">Teknisi Lab</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tugaskan di Ruang</label>
                    <select name="room_id" class="form-control">
                        <option value="">-- Semua Ruang --</option>
                        <?php foreach($rooms as $r): ?>
                            <option value="<?= $r['id'] ?>"><?= esc($r['nama_ruang']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Sesi Ujian</label>
                    <select name="sesi" class="form-control">
                        <option value="1" selected>Sesi 1</option>
                        <option value="2">Sesi 2</option>
                        <option value="3">Sesi 3</option>
                        <option value="4">Sesi 4</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Mata Pelajaran (Opsional)</label>
                    <select name="subject_id" class="form-control">
                        <option value="">-- Semua Mapel --</option>
                        <?php foreach($subjects as $sb): ?>
                            <option value="<?= $sb['id'] ?>"><?= esc($sb['nama_mapel']) ?> (Kelas <?= esc($sb['kelas']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">No. HP / WhatsApp (Opsional)</label>
                    <input type="text" name="kontak" class="form-control" placeholder="081234567890">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
                    <i class="fas fa-save"></i> Simpan Penugasan
                </button>
            </form>
        </div>
    </div>
</div>

<?php echo view('layout/footer'); ?>
