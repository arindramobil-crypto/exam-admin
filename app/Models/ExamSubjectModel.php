<?php
namespace App\Models;
use CodeIgniter\Model;

class ExamSubjectModel extends Model
{
    protected $table      = 'exam_subjects';
    protected $primaryKey = 'id';
    protected $allowedFields = ['exam_id','nama_mapel','kode_mapel','kelas','moodle_quiz_id','moodle_course_id','durasi_menit','tgl_ujian','jam_mulai','jam_selesai'];
    protected $useTimestamps = true;
}