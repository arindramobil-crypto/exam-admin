<?php echo view('layout/header', ['title' => $title]); ?>

<div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
        <h2 style="font-size:1.25rem;font-weight:700;color:#fff">
            <i class="fas fa-user-edit" style="color:var(--accent);margin-right:8px"></i> <?= esc($title) ?>
        </h2>
        <p style="font-size:13px;color:var(--muted)">Konfigurasikan informasi nama, username, role, dan hak akses akun</p>
    </div>
    <div>
        <a href="<?= base_url('user') ?>" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Admin
        </a>
    </div>
</div>

<div class="card" style="max-width:600px">
    <div class="card-header">
        <span class="card-title"><?= $user ? 'Formulir Edit Admin' : 'Formulir Tambah Admin Baru' ?></span>
    </div>
    <div class="card-body">
        <form action="<?= $user ? base_url('user/update/'.$user['id']) : base_url('user/store') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" name="nama" class="form-control" placeholder="Contoh: Budi Santoso, S.Kom"
                    value="<?= old('nama', $user['nama'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Username *</label>
                <input type="text" name="username" class="form-control" placeholder="Contoh: budi_admin"
                    value="<?= old('username', $user['username'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">
                    Password <?= $user ? '<small style="color:var(--muted)">(Kosongkan jika tidak ingin mengubah password)</small>' : '*' ?>
                </label>
                <input type="password" name="password" class="form-control" placeholder="<?= $user ? 'Biarkan kosong untuk mempertahankan password lama' : 'Minimal 6 karakter' ?>"
                    <?= $user ? '' : 'required' ?> minlength="6" autocomplete="new-password">
            </div>

            <div class="form-group">
                <label class="form-label">Role / Hak Akses *</label>
                <select name="role" class="form-control" required>
                    <?php $currentRole = old('role', $user['role'] ?? 'operator'); ?>
                    <option value="operator" <?= $currentRole === 'operator' ? 'selected' : '' ?>>Operator (Akses operasional ujian)</option>
                    <option value="admin" <?= $currentRole === 'admin' ? 'selected' : '' ?>>Admin (Akses penuh kecuali manajemen admin)</option>
                    <option value="superadmin" <?= $currentRole === 'superadmin' ? 'selected' : '' ?>>Superadmin (Akses penuh + manajemen admin &amp; sistem)</option>
                </select>
            </div>

            <div class="form-group" style="margin-top:20px">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;color:var(--text);font-size:13.5px">
                    <input type="checkbox" name="is_active" value="1" <?= old('is_active', $user['is_active'] ?? 1) ? 'checked' : '' ?>>
                    Status Akun Aktif (Dapat Login ke Sistem)
                </label>
            </div>

            <div style="margin-top:24px;display:flex;gap:10px;justify-content:flex-end">
                <a href="<?= base_url('user') ?>" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?= $user ? 'Simpan Perubahan' : 'Buat Akun' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?php echo view('layout/footer'); ?>

