<?php echo view('layout/header', ['title' => $title]); ?>
<div class="card"><div class="card-header"><span class="card-title">Daftar Ruang Ujian</span><a href="<?= base_url('room/create') ?>" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Tambah Ruang</a></div>
<div class="table-wrap"><table>
<thead><tr><th>No</th><th>Nama Ruang</th><th>Gedung</th><th>Lantai</th><th>Kapasitas</th><th>Aksi</th></tr></thead>
<tbody>
<?php $i=1; foreach($rooms as $r): ?><tr>
<td><?= $i++ ?></td>
<td><strong><?= esc($r['nama_ruang']) ?></strong></td>
<td><?= esc($r['gedung']) ?></td>
<td><?= esc($r['lantai']) ?></td>
<td><?= esc($r['kapasitas']) ?> Kursi</td>
<td><a href="<?= base_url('room/edit/'.$r['id']) ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></a> <a href="<?= base_url('room/delete/'.$r['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus ruang?')"><i class="fas fa-trash"></i></a></td>
</tr><?php endforeach ?>
<?php if(empty($rooms)): ?><tr><td colspan="6" style="text-align:center;padding:30px">Data ruang kosong.</td></tr><?php endif ?>
</tbody></table></div></div>
<?php echo view('layout/footer'); ?>
