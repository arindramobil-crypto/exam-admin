<?php echo view('layout/header', ['title' => $title]); ?>

<!-- Header & Quick Actions -->
<div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
        <h2 style="font-size:1.25rem;font-weight:700;color:#fff">
            <i class="fas fa-users-cog" style="color:var(--accent);margin-right:8px"></i> Manajemen Admin &amp; Operator
        </h2>
        <p style="font-size:13px;color:var(--muted)">Kelola akun pengguna, hak akses, dan status operasional sistem ujian CBT</p>
    </div>
    <div style="display:flex;gap:8px">
        <?php if($currentRole === 'superadmin'): ?>
            <a href="<?= base_url('user/create') ?>" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Tambah Admin Baru
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Daftar Pengguna Sistem</span>
        <span class="badge badge-draft"><?= count($users) ?> Akun Terdaftar</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:50px">No</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Role / Hak Akses</th>
                    <th>Status</th>
                    <th>Terdaftar Sejak</th>
                    <th style="text-align:right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach($users as $u): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td>
                        <strong><?= esc($u['nama']) ?></strong>
                        <?php if((int)$u['id'] === (int)$currentId): ?>
                            <span class="badge badge-selesai" style="font-size:10px;margin-left:4px">Akun Anda</span>
                        <?php endif; ?>
                    </td>
                    <td><code><?= esc($u['username']) ?></code></td>
                    <td>
                        <?php if($u['role'] === 'superadmin'): ?>
                            <span class="badge" style="background:rgba(239,68,68,0.15);color:var(--danger);font-weight:700">
                                <i class="fas fa-crown" style="margin-right:4px"></i> Superadmin
                            </span>
                        <?php elseif($u['role'] === 'admin'): ?>
                            <span class="badge" style="background:rgba(99,102,241,0.15);color:var(--accent);font-weight:600">
                                <i class="fas fa-shield-alt" style="margin-right:4px"></i> Admin
                            </span>
                        <?php else: ?>
                            <span class="badge badge-draft">
                                <i class="fas fa-user" style="margin-right:4px"></i> Operator
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($u['is_active']): ?>
                            <span class="badge badge-aktif"><i class="fas fa-check-circle" style="margin-right:4px"></i> Aktif</span>
                        <?php else: ?>
                            <span class="badge badge-absen"><i class="fas fa-ban" style="margin-right:4px"></i> Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td><small style="color:var(--muted)"><?= !empty($u['created_at']) ? date('d M Y', strtotime($u['created_at'])) : '-' ?></small></td>
                    <td style="text-align:right">
                        <?php if($currentRole === 'superadmin'): ?>
                            <div style="display:inline-flex;gap:6px">
                                <a href="<?= base_url('user/edit/'.$u['id']) ?>" class="btn btn-sm btn-outline" title="Edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <?php if((int)$u['id'] !== (int)$currentId): ?>
                                    <button class="btn btn-sm btn-danger" title="Hapus"
                                        onclick="showConfirm('<?= base_url('user/delete/'.$u['id']) ?>', 'Hapus Akun Pengguna', 'Apakah Anda yakin ingin menghapus akun <?= esc($u['nama']) ?> (<?= esc($u['username']) ?>)? Tindakan ini permanen.', 'btn-danger')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <small style="color:var(--muted);font-style:italic">Hanya Superadmin</small>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($users)): ?>
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:var(--muted)">Belum ada data admin.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php echo view('layout/footer'); ?>

