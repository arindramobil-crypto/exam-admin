<?php
namespace App\Models;
use CodeIgniter\Model;

class ExamModel extends Model
{
    protected $table      = 'exams';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_ujian','tahun','semester','tgl_mulai','tgl_selesai','status','keterangan'];
    protected $useTimestamps = true;

    public function withSubjectCount()
    {
        return $this->select('exams.*, COUNT(exam_subjects.id) as jml_mapel')
                    ->join('exam_subjects', 'exam_subjects.exam_id = exams.id', 'left')
                    ->groupBy('exams.id');
    }
}