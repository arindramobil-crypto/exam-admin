<?php

namespace App\Models;

use CodeIgniter\Model;

class ParticipantModel extends Model
{
    protected $table         = 'participants';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'exam_id',
        'subject_id',
        'room_id',
        'nomor_peserta',
        'nomor_meja',
        'sesi',
        'moodle_user_id',
        'nama',
        'nis',
        'nisn',
        'password',
        'kelas',
        'jurusan',
    ];
    protected $useTimestamps = true;

    public function generateNomorPeserta(int $examId, string $kelas, int $urut): string
    {
        $exam = (new ExamModel())->find($examId);
        $tahun = $exam['tahun'] ?? date('Y');
        // Format: YYYY-KK-NNNN (contoh: 2026-10-0001)
        $kelasNum = preg_replace('/[^0-9]/', '', $kelas);
        if (empty($kelasNum)) {
            $kelasNum = '00';
        }
        return sprintf('%s-%s-%04d', $tahun, $kelasNum, $urut);
    }

    public function getByExamRoom(int $examId, int $roomId, int $sesi = 0)
    {
        $q = $this->where('exam_id', $examId)->where('room_id', $roomId);
        if ($sesi > 0) {
            $q->where('sesi', $sesi);
        }
        return $q->orderBy('nomor_meja', 'ASC')->findAll();
    }

    /**
     * Generate random password for participants without password.
     */
    public function generatePasswords(int $examId): int
    {
        $participants = $this->where('exam_id', $examId)->where('password', null)->findAll();
        $updated = 0;
        foreach ($participants as $p) {
            // 6-character clean alphanumeric password (e.g. 8K2M9P)
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
            $pwd = substr(str_shuffle($chars), 0, 6);
            $this->update($p['id'], ['password' => $pwd]);
            $updated++;
        }
        return $updated;
    }
}