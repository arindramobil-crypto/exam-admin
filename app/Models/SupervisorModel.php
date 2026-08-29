<?php

namespace App\Models;

use CodeIgniter\Model;

class SupervisorModel extends Model
{
    protected $table         = 'exam_supervisors';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'exam_id',
        'subject_id',
        'room_id',
        'sesi',
        'nama_pengawas',
        'nip',
        'peran',
        'kontak',
    ];
    protected $useTimestamps = true;

    public function getSupervisorsByExam(int $examId): array
    {
        return $this->select('exam_supervisors.*, rooms.nama_ruang, exam_subjects.nama_mapel')
            ->join('rooms', 'rooms.id = exam_supervisors.room_id', 'left')
            ->join('exam_subjects', 'exam_subjects.id = exam_supervisors.subject_id', 'left')
            ->where('exam_supervisors.exam_id', $examId)
            ->orderBy('exam_supervisors.sesi', 'ASC')
            ->orderBy('rooms.nama_ruang', 'ASC')
            ->findAll();
    }
}
