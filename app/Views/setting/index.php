<?php echo view('layout/header', ['title' => $title]); ?>

<div style="margin-bottom:24px">
    <h2 style="font-size:1.25rem;font-weight:700;color:#fff"><i class="fas fa-school" style="color:var(--accent);margin-right:8px"></i> Pengaturan Sekolah & Kop Surat Dokumen</h2>
    <p style="font-size:13px;color:var(--muted)">Atur identitas lembaga, logo sekolah, serta pejabat penandatangan kartu peserta, kartu meja, daftar hadir, dan berita acara ujian.</p>
</div>

<form action="<?= base_url('setting/save') ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(360px, 1fr));gap:20px;margin-bottom:24px">
        
        <!-- Kolom 1: Identitas Sekolah & Logo -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-building" style="color:var(--accent)"></i> 1. Identitas Lembaga & Logo</span>
            </div>
            <div class="card-body">
                <div class="form-group" style="margin-bottom:16px">
                    <label class="form-label">Nama Sekolah / Lembaga <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="nama_sekolah" class="form-control" value="<?= esc($settings['nama_sekolah'] ?? '') ?>" placeholder="Misal: SMA NEGERI 1 CONTOH" required>
                    <small style="color:var(--muted);font-size:11px">Nama ini akan tampil di bagian atas kop kartu peserta & dokumen.</small>
                </div>

                <div class="form-group" style="margin-bottom:16px">
                    <label class="form-label">Alamat Sekolah</label>
                    <textarea name="alamat_sekolah" class="form-control" rows="2" placeholder="Jl. Pendidikan No. 123"><?= esc($settings['alamat_sekolah'] ?? '') ?></textarea>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
                    <div class="form-group">
                        <label class="form-label">Kota / Kabupaten</label>
                        <input type="text" name="kota" class="form-control" value="<?= esc($settings['kota'] ?? '') ?>" placeholder="Misal: Jakarta">
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="telepon" class="form-control" value="<?= esc($settings['telepon'] ?? '') ?>" placeholder="(021) 1234567">
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
                    <div class="form-group">
                        <label class="form-label">Email Sekolah</label>
                        <input type="email" name="email" class="form-control" value="<?= esc($settings['email'] ?? '') ?>" placeholder="info@sekolah.sch.id">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Website</label>
                        <input type="text" name="website" class="form-control" value="<?= esc($settings['website'] ?? '') ?>" placeholder="https://sekolah.sch.id">
                    </div>
                </div>

                <!-- Upload Logo -->
                <div style="border-top:1px solid var(--border);padding-top:16px;margin-top:16px">
                    <label class="form-label" style="margin-bottom:8px">Logo Sekolah (Untuk Kop Dokumen & Kartu)</label>
                    <div style="display:flex;align-items:center;gap:16px">
                        <div style="width:70px;height:70px;border:1px dashed var(--border);border-radius:8px;background:var(--surface2);display:flex;align-items:center;justify-content:center;overflow:hidden">
                            <?php if(!empty($settings['logo']) && file_exists(FCPATH . $settings['logo'])): ?>
                                <img src="<?= base_url($settings['logo']) ?>" style="max-width:100%;max-height:100%;object-fit:contain">
                            <?php else: ?>
                                <i class="fas fa-image" style="font-size:24px;color:var(--muted)"></i>
                            <?php endif; ?>
                        </div>
                        <div style="flex:1">
                            <input type="file" name="logo_file" class="form-control" accept="image/png,image/jpeg,image/svg+xml" style="font-size:12px">
                            <small style="color:var(--muted);font-size:11px;display:block;margin-top:4px">Format: PNG, JPG, atau SVG transparan. Maks. 2 MB.</small>
                            <?php if(!empty($settings['logo'])): ?>
                                <label style="font-size:12px;color:var(--danger);cursor:pointer;display:inline-flex;align-items:center;gap:4px;margin-top:6px">
                                    <input type="checkbox" name="hapus_logo" value="1"> Hapus logo saat ini
                                </label>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom 2: Pejabat Penandatangan Dokumen -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-file-signature" style="color:var(--success)"></i> 2. Pejabat Penandatangan Dokumen</span>
            </div>
            <div class="card-body">
                
                <!-- Kepala Sekolah -->
                <div style="background:rgba(99,102,241,0.05);border:1px solid rgba(99,102,241,0.2);border-radius:8px;padding:14px;margin-bottom:16px">
                    <h4 style="font-size:13px;font-weight:700;color:var(--accent);margin-bottom:10px">
                        <i class="fas fa-user-tie" style="margin-right:6px"></i> Kepala Sekolah
                    </h4>
                    <div class="form-group" style="margin-bottom:10px">
                        <label class="form-label" style="font-size:12px">Nama Lengkap & Gelar</label>
                        <input type="text" name="nama_kepala_sekolah" class="form-control" value="<?= esc($settings['nama_kepala_sekolah'] ?? '') ?>" placeholder="Drs. H. Fulan, M.Pd.">
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size:12px">NIP / NUPTK</label>
                        <input type="text" name="nip_kepala_sekolah" class="form-control" value="<?= esc($settings['nip_kepala_sekolah'] ?? '') ?>" placeholder="19750101 200003 1 001">
                    </div>
                </div>

                <!-- Ketua Panitia Ujian -->
                <div style="background:rgba(16,185,129,0.05);border:1px solid rgba(16,185,129,0.2);border-radius:8px;padding:14px;margin-bottom:16px">
                    <h4 style="font-size:13px;font-weight:700;color:var(--success);margin-bottom:10px">
                        <i class="fas fa-user-shield" style="margin-right:6px"></i> Ketua Panitia Ujian
                    </h4>
                    <div class="form-group" style="margin-bottom:10px">
                        <label class="form-label" style="font-size:12px">Nama Lengkap & Gelar</label>
                        <input type="text" name="nama_ketua_panitia" class="form-control" value="<?= esc($settings['nama_ketua_panitia'] ?? '') ?>" placeholder="Budi Santoso, S.Kom.">
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size:12px">NIP / NUPTK</label>
                        <input type="text" name="nip_ketua_panitia" class="form-control" value="<?= esc($settings['nip_ketua_panitia'] ?? '') ?>" placeholder="19850615 201001 1 008">
                    </div>
                </div>

                <!-- Pilihan Penandatangan Kartu Peserta -->
                <div class="form-group">
                    <label class="form-label">Pejabat Penandatangan Kartu Peserta</label>
                    <select name="ttd_kartu_jabatan" class="form-control">
                        <option value="Ketua Panitia Ujian" <?= ($settings['ttd_kartu_jabatan'] ?? '') === 'Ketua Panitia Ujian' ? 'selected' : '' ?>>Ketua Panitia Ujian</option>
                        <option value="Kepala Sekolah" <?= ($settings['ttd_kartu_jabatan'] ?? '') === 'Kepala Sekolah' ? 'selected' : '' ?>>Kepala Sekolah</option>
                    </select>
                    <small style="color:var(--muted);font-size:11px">Pilih siapa yang akan tertera menandatangani kartu peserta ujian.</small>
                </div>

            </div>
        </div>
    </div>

    <!-- Tombol Simpan -->
    <div style="display:flex;justify-content:flex-end;gap:12px;margin-bottom:30px">
        <a href="<?= base_url('dashboard') ?>" class="btn btn-outline"><i class="fas fa-times"></i> Batal</a>
        <button type="submit" class="btn btn-primary" style="padding:10px 24px;font-size:14px">
            <i class="fas fa-save"></i> Simpan Pengaturan Sekolah
        </button>
    </div>
</form>

<?php echo view('layout/footer'); ?>
