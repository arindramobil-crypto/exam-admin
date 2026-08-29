<?php echo view('layout/header', ['title' => $title]); ?>

<div style="margin-bottom:24px">
    <h2 style="font-size:1.25rem;font-weight:700;color:#fff"><i class="fas fa-check-square" style="color:var(--accent);margin-right:8px"></i> Daftar Hadir Peserta Ujian</h2>
    <p style="font-size:13px;color:var(--muted)">Pilih paket ujian untuk mengelola absensi, memfilter berdasarkan Ruang & Sesi, dan mencetak daftar hadir resmi.</p>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-clipboard-list"></i> Pilih Paket Ujian</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:50px">No</th>
                    <th>Nama Paket Ujian</th>
                    <th>Tahun / Semester</th>
                    <th>Periode Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach($exams as $e): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><strong><?= esc($e['nama_ujian']) ?></strong></td>
                    <td><?= esc($e['tahun']) ?> / Sem <?= esc($e['semester']) ?></td>
                    <td><?= date('d M Y', strtotime($e['tgl_mulai'])) ?> - <?= date('d M Y', strtotime($e['tgl_selesai'])) ?></td>
                    <td>
                        <?php if($e['status'] === 'aktif'): ?>
                            <span class="badge badge-aktif">Aktif</span>
                        <?php elseif($e['status'] === 'selesai'): ?>
                            <span class="badge badge-selesai">Selesai</span>
                        <?php else: ?>
                            <span class="badge badge-draft">Draft</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= base_url('attendance/' . $e['id']) ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-user-check"></i> Buka Daftar Hadir
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($exams)): ?>
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:var(--muted)">
                        Belum ada paket ujian yang dibuat.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php echo view('layout/footer'); ?>
