<?php echo view('layout/header', ['title' => $title]); ?>
<div class="card mb-4" style="margin-bottom:20px"><div class="card-body">
<form method="GET" action="" style="display:flex;gap:15px;align-items:flex-end">
<div class="form-group" style="margin:0;flex:1"><label class="form-label">Pilih Mapel</label><select id="subjSelect" class="form-control"><option value="">-- Pilih Mapel --</option><?php foreach($subjects as $s): ?><option value="<?= $s['id'] ?>"><?= $s['nama_mapel'] ?></option><?php endforeach ?></select></div>
<div class="form-group" style="margin:0;flex:1"><label class="form-label">Pilih Ruang</label><select id="roomSelect" class="form-control"><option value="">-- Pilih Ruang --</option><?php foreach($rooms as $r): ?><option value="<?= $r['id'] ?>"><?= $r['nama_ruang'] ?></option><?php endforeach ?></select></div>
<button type="button" class="btn btn-primary" onclick="buatBa()">Buat/Edit Berita Acara</button>
</form></div></div>
<script>function buatBa(){let s=document.getElementById('subjSelect').value; let r=document.getElementById('roomSelect').value; if(s && r){window.location.href="<?= base_url('minutes/form/'.$exam['id']) ?>/"+s+"/"+r;}else{alert('Pilih Mapel dan Ruang!');}}</script>
<div class="card"><div class="card-header"><span class="card-title">Daftar Berita Acara yang Tersimpan</span></div>
<div class="table-wrap"><table>
<thead><tr><th>Mapel</th><th>Ruang</th><th>Pengawas</th><th>Status</th><th>Cetak</th></tr></thead>
<tbody>
<?php foreach($minutes as $m): ?><tr>
<td><?php $s=array_filter($subjects,fn($x)=>$x['id']==$m['subject_id']); $s=reset($s); echo $s?$s['nama_mapel']:'-'; ?></td>
<td><?php $r=array_filter($rooms,fn($x)=>$x['id']==$m['room_id']); $r=reset($r); echo $r?$r['nama_ruang']:'-'; ?></td>
<td><?= esc($m['pengawas']) ?></td>
<td><span class="badge badge-aktif">Tersimpan</span></td>
<td><a href="<?= base_url('minutes/print/'.$m['id']) ?>" target="_blank" class="btn btn-sm btn-outline"><i class="fas fa-print"></i> Cetak</a></td>
</tr><?php endforeach ?>
<?php if(empty($minutes)): ?><tr><td colspan="5" style="text-align:center;padding:30px">Belum ada Berita Acara.</td></tr><?php endif ?>
</tbody></table></div></div>
<?php echo view('layout/footer'); ?>
