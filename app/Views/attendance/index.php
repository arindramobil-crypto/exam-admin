<?php echo view('layout/header', ['title' => $title]); ?>

<!-- Header & Quick Actions -->
<div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
        <h2 style="font-size:1.25rem;font-weight:700;color:#fff"><i class="fas fa-check-square" style="color:var(--accent);margin-right:8px"></i> Daftar Hadir: <?= esc($exam['nama_ujian']) ?></h2>
        <p style="font-size:13px;color:var(--muted)">Tahun: <?= esc($exam['tahun']) ?> | Semester: <?= esc($exam['semester']) ?></p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        <?php
            $printUrlParams = http_build_query([
                'subject_id' => $selectedSubjectId,
                'room_id'    => $selectedRoomId,
                'sesi'       => $selectedSesi,
            ]);
        ?>
        <a href="<?= base_url('attendance/print/' . $exam['id'] . '?' . $printUrlParams) ?>" target="_blank" class="btn btn-primary">
            <i class="fas fa-print"></i> Cetak Daftar Hadir
        </a>
        <a href="<?= base_url('attendance/print/' . $exam['id'] . '?' . $printUrlParams . '&export=pdf') ?>" target="_blank" class="btn btn-success">
            <i class="fas fa-file-pdf"></i> Download PDF
        </a>
        <a href="<?= base_url('exam') ?>" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
</div>

<!-- Filter Panel: Mapel, Ruang & Sesi -->
<div class="card" style="margin-bottom:20px;border:1px solid rgba(99,102,241,0.3)">
    <div class="card-header" style="background:rgba(99,102,241,0.06)">
        <span class="card-title" style="font-size:13.5px"><i class="fas fa-filter" style="color:var(--accent)"></i> Filter Daftar Hadir Berdasarkan Mapel, Ruang & Sesi</span>
    </div>
    <div class="card-body" style="padding:16px">
        <form method="GET" action="<?= base_url('attendance/' . $exam['id']) ?>" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)) 120px;gap:12px;align-items:flex-end">
            <div class="form-group">
                <label class="form-label" style="font-size:12px">Mata Pelajaran</label>
                <select name="subject_id" class="form-control" style="font-size:12.5px">
                    <option value="0">-- Semua Mata Pelajaran --</option>
                    <?php foreach($subjects as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= $selectedSubjectId == $s['id'] ? 'selected' : '' ?>>
                            <?= esc($s['nama_mapel']) ?> (Kelas <?= esc($s['kelas']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" style="font-size:12px">Ruang Ujian</label>
                <select name="room_id" class="form-control" style="font-size:12.5px">
                    <option value="0">-- Semua Ruangan --</option>
                    <?php foreach($rooms as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= $selectedRoomId == $r['id'] ? 'selected' : '' ?>>
                            <?= esc($r['nama_ruang']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" style="font-size:12px">Sesi Ujian</label>
                <select name="sesi" class="form-control" style="font-size:12.5px">
                    <option value="0">-- Semua Sesi --</option>
                    <?php foreach($sessions as $sess): ?>
                        <option value="<?= $sess ?>" <?= $selectedSesi == $sess ? 'selected' : '' ?>>
                            Sesi <?= $sess ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;height:38px">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Stat Cards Kehadiran -->
<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(130px, 1fr));gap:12px;margin-bottom:20px">
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:12px 14px">
        <div style="font-size:11px;color:var(--muted)">Total Peserta</div>
        <div style="font-size:20px;font-weight:700;color:#fff;margin-top:2px"><?= count($participants) ?></div>
    </div>
    <div style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.25);border-radius:10px;padding:12px 14px">
        <div style="font-size:11px;color:var(--success)">Hadir</div>
        <div style="font-size:20px;font-weight:700;color:var(--success);margin-top:2px"><?= $stats['hadir'] ?></div>
    </div>
    <div style="background:rgba(59,130,246,0.08);border:1px solid rgba(59,130,246,0.25);border-radius:10px;padding:12px 14px">
        <div style="font-size:11px;color:#60a5fa">Sakit</div>
        <div style="font-size:20px;font-weight:700;color:#60a5fa;margin-top:2px"><?= $stats['sakit'] ?></div>
    </div>
    <div style="background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.25);border-radius:10px;padding:12px 14px">
        <div style="font-size:11px;color:var(--warning)">Izin</div>
        <div style="font-size:20px;font-weight:700;color:var(--warning);margin-top:2px"><?= $stats['izin'] ?></div>
    </div>
    <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.25);border-radius:10px;padding:12px 14px">
        <div style="font-size:11px;color:var(--danger)">Alpa</div>
        <div style="font-size:20px;font-weight:700;color:var(--danger);margin-top:2px"><?= $stats['alpa'] ?></div>
    </div>
    <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:12px 14px">
        <div style="font-size:11px;color:var(--muted)">Belum Absen</div>
        <div style="font-size:20px;font-weight:700;color:var(--muted);margin-top:2px"><?= $stats['belum'] ?></div>
    </div>
</div>

<!-- Form Absensi Peserta -->
<div class="card">
    <form action="<?= base_url('attendance/save/' . $exam['id']) ?>" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="subject_id" value="<?= $selectedSubjectId ?>">

        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
            <span class="card-title"><i class="fas fa-list-check"></i> Daftar Peserta (<?= count($participants) ?> Siswa)</span>
            <div style="display:flex;gap:8px">
                <button type="button" class="btn btn-sm btn-outline" onclick="setAllAttendance('hadir')">
                    <i class="fas fa-check-double"></i> Set Semua Hadir
                </button>
                <button type="submit" class="btn btn-sm btn-success">
                    <i class="fas fa-save"></i> Simpan Kehadiran
                </button>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width:40px">No</th>
                        <th>No. Peserta</th>
                        <th>Nama Lengkap</th>
                        <th>Kelas</th>
                        <th>Ruang</th>
                        <th>Meja</th>
                        <th>Sesi</th>
                        <th style="min-width:260px">Status Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; foreach($participants as $p): ?>
                    <?php 
                        $currentStatus = $attendances[$p['id']]['status'] ?? '';
                        $roomObj = array_filter($rooms, fn($r) => $r['id'] == $p['room_id']);
                        $roomObj = reset($roomObj);
                        $roomName = $roomObj ? $roomObj['nama_ruang'] : 'Belum Diset';
                    ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><strong style="font-family:monospace;color:var(--accent)"><?= esc($p['nomor_peserta']) ?></strong></td>
                        <td><strong><?= esc($p['nama']) ?></strong></td>
                        <td><span class="badge badge-draft"><?= esc($p['kelas']) ?></span></td>
                        <td><span class="badge badge-aktif"><?= esc($roomName) ?></span></td>
                        <td><strong style="color:var(--warning)">#<?= $p['nomor_meja'] ?: '-' ?></strong></td>
                        <td><span class="badge badge-selesai">Sesi <?= $p['sesi'] ?: 1 ?></span></td>
                        <td>
                            <div style="display:flex;gap:6px">
                                <label style="cursor:pointer;font-size:12px;display:inline-flex;align-items:center;gap:3px;padding:3px 8px;border-radius:6px;background:rgba(16,185,129,0.1);color:var(--success);border:1px solid rgba(16,185,129,0.3)">
                                    <input type="radio" name="status[<?= $p['id'] ?>]" value="hadir" <?= $currentStatus === 'hadir' ? 'checked' : '' ?> class="att-radio-hadir"> Hadir
                                </label>
                                <label style="cursor:pointer;font-size:12px;display:inline-flex;align-items:center;gap:3px;padding:3px 8px;border-radius:6px;background:rgba(59,130,246,0.1);color:#60a5fa;border:1px solid rgba(59,130,246,0.3)">
                                    <input type="radio" name="status[<?= $p['id'] ?>]" value="sakit" <?= $currentStatus === 'sakit' ? 'checked' : '' ?>> Sakit
                                </label>
                                <label style="cursor:pointer;font-size:12px;display:inline-flex;align-items:center;gap:3px;padding:3px 8px;border-radius:6px;background:rgba(245,158,11,0.1);color:var(--warning);border:1px solid rgba(245,158,11,0.3)">
                                    <input type="radio" name="status[<?= $p['id'] ?>]" value="izin" <?= $currentStatus === 'izin' ? 'checked' : '' ?>> Izin
                                </label>
                                <label style="cursor:pointer;font-size:12px;display:inline-flex;align-items:center;gap:3px;padding:3px 8px;border-radius:6px;background:rgba(239,68,68,0.1);color:var(--danger);border:1px solid rgba(239,68,68,0.3)">
                                    <input type="radio" name="status[<?= $p['id'] ?>]" value="alpa" <?= $currentStatus === 'alpa' ? 'checked' : '' ?>> Alpa
                                </label>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($participants)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center;padding:40px;color:var(--muted)">
                            <i class="fas fa-users-slash" style="font-size:36px;opacity:0.3;margin-bottom:8px;display:block"></i>
                            Tidak ada peserta yang cocok dengan filter Ruang / Sesi yang dipilih.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if(!empty($participants)): ?>
        <div class="card-body" style="border-top:1px solid var(--border);display:flex;justify-content:flex-end">
            <button type="submit" class="btn btn-success" style="padding:8px 24px">
                <i class="fas fa-save"></i> Simpan Kehadiran
            </button>
        </div>
        <?php endif; ?>
    </form>
</div>

<script>
function setAllAttendance(status) {
    if (status === 'hadir') {
        document.querySelectorAll('.att-radio-hadir').forEach(function(radio) {
            radio.checked = true;
        });
    }
}
</script>

<?php echo view('layout/footer'); ?>
