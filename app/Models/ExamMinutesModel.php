<?php
namespace App\Models;
use CodeIgniter\Model;

class ExamMinutesModel extends Model
{
    protected $table      = 'exam_minutes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['exam_id','subject_id','room_id','nama_pengawas1','nama_pengawas2','jml_peserta_hadir','jml_peserta_absen','jml_soal','waktu_mulai','waktu_selesai','kejadian','catatan','status'];
    protected $useTimestamps = true;
}