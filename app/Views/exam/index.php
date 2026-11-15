<?php echo view('layout/header', ['title' => $title]); ?>

<div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
        <h2 style="font-size:1.25rem;font-weight:700;color:#fff"><i class="fas fa-clipboard-list" style="color:var(--accent);margin-right:8px"></i> Manajemen Ujian CBT</h2>
        <p style="font-size:13px;color:var(--muted)">Kelola paket ujian, mata pelajaran, peserta, pengawas, token rilis, dan rekap nilai</p>
    </div>
    <a href="<?= base_url('exam/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Buat Ujian Baru
    </a>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Daftar Paket Ujian</span>
        <span class="badge badge-selesai"><?= count($exams) ?> Paket Ujian</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:50px">No</th>
                    <th>Nama Paket Ujian</th>
                    <th>Tahun / Sem</th>
                    <th>Periode Tanggal</th>
                    <th>Status</th>
                    <th>Jml Mapel</th>
                    <th style="width:230px;text-align:right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach($exams as $e): ?>
                <?php $isCurrent = ($activeExam && $activeExam['id'] == $e['id']); ?>
                <tr <?= $isCurrent ? 'style="background:rgba(99,102,241,0.05)"' : '' ?>>
                    <td><?= $i++ ?></td>
                    <td>
                        <strong><?= esc($e['nama_ujian']) ?></strong>
                        <?php if($isCurrent): ?>
                            <span class="badge badge-aktif" style="font-size:10px;margin-left:6px"><i class="fas fa-check-circle"></i> Terpilih</span>
                        <?php endif; ?>
                        <?php if(!empty($e['keterangan'])): ?>
                            <br><small style="color:var(--muted)"><?= esc($e['keterangan']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($e['tahun']) ?> / Sem <?= esc($e['semester']) ?></td>
                    <td><?= date('d M Y', strtotime($e['tgl_mulai'])) ?> s/d <?= date('d M Y', strtotime($e['tgl_selesai'])) ?></td>
                    <td>
                        <?php if($e['status'] === 'aktif'): ?>
                            <span class="badge badge-aktif"><i class="fas fa-play" style="margin-right:4px"></i> Aktif</span>
                        <?php elseif($e['status'] === 'selesai'): ?>
                            <span class="badge badge-selesai"><i class="fas fa-check" style="margin-right:4px"></i> Selesai</span>
                        <?php else: ?>
                            <span class="badge badge-draft"><i class="fas fa-file" style="margin-right:4px"></i> Draft</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge badge-draft"><?= $e['jml_mapel'] ?? 0 ?> Mapel</span>
                    </td>
                    <td style="text-align:right">
                        <div style="display:inline-flex;gap:6px;align-items:center;justify-content:flex-end">
                            <a href="<?= base_url('exam/subjects/'.$e['id']) ?>" class="btn btn-sm btn-outline" title="Atur Mata Pelajaran">
                                <i class="fas fa-book"></i> Mapel
                            </a>
                            <a href="<?= base_url('exam/set-active/'.$e['id']) ?>" class="btn btn-sm <?= $isCurrent ? 'btn-success' : 'btn-primary' ?>" title="Pilih & Buka Pelaksanaan CBT">
                                <i class="fas fa-play-circle"></i> <?= $isCurrent ? 'Buka CBT' : 'Pilih CBT' ?>
                            </a>

                            <!-- Dropdown Akses Cepat Fitur CBT -->
                            <div style="position:relative;display:inline-block">
                                <button type="button" class="btn btn-sm btn-outline" onclick="toggleDropdown(event, 'drop_<?= $e['id'] ?>')" title="Akses Cepat Fitur CBT">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div id="drop_<?= $e['id'] ?>" class="dropdown-menu-custom">
                                    <div style="padding:6px 14px;font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;border-bottom:1px solid rgba(255,255,255,0.06)">
                                        Pelaksanaan CBT
                                    </div>
                                    <a href="<?= base_url('participant/'.$e['id']) ?>" class="dropdown-item-custom"><i class="fas fa-users" style="width:20px;color:var(--accent)"></i> Peserta &amp; Kartu</a>
                                    <a href="<?= base_url('token/'.$e['id']) ?>" class="dropdown-item-custom"><i class="fas fa-key" style="width:20px;color:#f59e0b"></i> Rilis Token CBT</a>
                                    <a href="<?= base_url('supervisor/'.$e['id']) ?>" class="dropdown-item-custom"><i class="fas fa-user-shield" style="width:20px;color:#38bdf8"></i> Pengawas &amp; Proktor</a>
                                    <a href="<?= base_url('dashboard/monitoring/'.$e['id']) ?>" class="dropdown-item-custom"><i class="fas fa-desktop" style="width:20px;color:#a855f7"></i> Live Monitoring</a>
                                    <a href="<?= base_url('attendance/'.$e['id']) ?>" class="dropdown-item-custom"><i class="fas fa-check-square" style="width:20px;color:#10b981"></i> Daftar Hadir</a>
                                    <a href="<?= base_url('minutes/'.$e['id']) ?>" class="dropdown-item-custom"><i class="fas fa-file-alt" style="width:20px;color:#f97316"></i> Berita Acara</a>
                                    <a href="<?= base_url('room/denah/'.$e['id']) ?>" class="dropdown-item-custom"><i class="fas fa-th-large" style="width:20px;color:#818cf8"></i> Denah Ruangan</a>
                                    
                                    <div style="margin:4px 0;border-top:1px solid rgba(255,255,255,0.06)"></div>
                                    <div style="padding:6px 14px;font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px">
                                        Pelaporan &amp; Analisis
                                    </div>
                                    <a href="<?= base_url('grade/'.$e['id']) ?>" class="dropdown-item-custom"><i class="fas fa-chart-bar" style="width:20px;color:#10b981"></i> Download Nilai</a>
                                    <a href="<?= base_url('analysis/'.$e['id']) ?>" class="dropdown-item-custom"><i class="fas fa-chart-pie" style="width:20px;color:#06b6d4"></i> Analisis Butir Soal</a>
                                </div>
                            </div>

                            <a href="<?= base_url('exam/edit/'.$e['id']) ?>" class="btn btn-sm btn-outline" title="Edit Ujian">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= base_url('exam/delete/'.$e['id']) ?>" class="btn btn-sm btn-danger" onclick="event.preventDefault(); showConfirm('<?= base_url('exam/delete/'.$e['id']) ?>', 'Hapus Paket Ujian', 'Yakin ingin menghapus ujian <?= esc(addslashes($e['nama_ujian'])) ?> beserta seluruh data terkait? Tindakan ini tidak dapat dibatalkan.', 'btn-danger')" title="Hapus Ujian">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($exams)): ?>
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:var(--muted)">
                        <i class="fas fa-folder-open" style="font-size:32px;opacity:0.3;margin-bottom:8px;display:block"></i>
                        Belum ada paket ujian yang dibuat. Silakan klik tombol <strong>Buat Ujian Baru</strong> di atas.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php echo view('layout/footer'); ?>
