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
                    <th style="min-width:320px">Aksi & Fitur CBT</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach($exams as $e): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td>
                        <strong><?= esc($e['nama_ujian']) ?></strong>
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
                    <td>
                        <div style="display:flex;gap:5px;flex-wrap:wrap">
                            <a href="<?= base_url('exam/subjects/'.$e['id']) ?>" class="btn btn-sm btn-outline" title="Atur Mata Pelajaran">
                                <i class="fas fa-book"></i> Mapel
                            </a>
                            <a href="<?= base_url('participant/'.$e['id']) ?>" class="btn btn-sm btn-primary" title="Kelola Peserta & Ruang">
                                <i class="fas fa-users"></i> Peserta
                            </a>
                            <a href="<?= base_url('token/'.$e['id']) ?>" class="btn btn-sm btn-warning" style="color:#000;font-weight:600" title="Rilis Token CBT">
                                <i class="fas fa-key"></i> Token
                            </a>
                            <a href="<?= base_url('supervisor/'.$e['id']) ?>" class="btn btn-sm btn-outline" title="Pengawas Ruang">
                                <i class="fas fa-user-shield"></i> Pengawas
                            </a>
                            <a href="<?= base_url('dashboard/monitoring/'.$e['id']) ?>" class="btn btn-sm btn-outline" title="Live Monitoring Ujian">
                                <i class="fas fa-desktop"></i> Monitor
                            </a>
                            <a href="<?= base_url('grade/'.$e['id']) ?>" class="btn btn-sm btn-success" title="Download & Rekap Nilai">
                                <i class="fas fa-chart-bar"></i> Nilai
                            </a>
                            <a href="<?= base_url('analysis/'.$e['id']) ?>" class="btn btn-sm btn-outline" title="Analisis Butir Soal">
                                <i class="fas fa-chart-pie"></i> Analisis
                            </a>
                            <a href="<?= base_url('attendance/'.$e['id']) ?>" class="btn btn-sm btn-outline" title="Daftar Hadir Ujian">
                                <i class="fas fa-check-square"></i> Hadir
                            </a>
                            <a href="<?= base_url('minutes/'.$e['id']) ?>" class="btn btn-sm btn-outline" title="Berita Acara">
                                <i class="fas fa-file-alt"></i> BA
                            </a>
                            <a href="<?= base_url('exam/edit/'.$e['id']) ?>" class="btn btn-sm btn-outline" title="Edit Ujian">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= base_url('exam/delete/'.$e['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus ujian ini?')" title="Hapus Ujian">
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
