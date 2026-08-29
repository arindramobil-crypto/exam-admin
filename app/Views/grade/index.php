<?php echo view('layout/header', ['title' => $title]); ?>
<div class="card"><div class="card-header"><span class="card-title">Pilih Mata Pelajaran untuk Download Nilai</span></div>
<div class="table-wrap"><table>
<thead><tr><th>Mata Pelajaran</th><th>Kelas</th><th>Quiz Moodle</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach($subjects as $s): ?><tr>
<td><strong><?= esc($s['nama_mapel']) ?></strong></td>
<td><?= esc($s['kelas']) ?></td>
<td><?= $s['moodle_quiz_id'] ? '<span class="badge badge-aktif"><i class="fas fa-check"></i> Terhubung</span>' : '<span class="badge badge-draft">Belum Terhubung</span>' ?></td>
<td>
<?php if($s['moodle_quiz_id']): ?>
<a href="<?= base_url('grade/view/'.$exam['id'].'/'.$s['id']) ?>" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i> Lihat Nilai</a>
<?php else: ?><span style="color:var(--muted);font-size:12px">Quiz tidak diset</span><?php endif ?>
</td></tr><?php endforeach ?>
</tbody></table></div></div>
<?php echo view('layout/footer'); ?>
