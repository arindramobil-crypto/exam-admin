<?php

namespace App\Models;

use CodeIgniter\Model;

class TokenModel extends Model
{
    protected $table         = 'exam_tokens';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'exam_id',
        'subject_id',
        'token',
        'durasi_menit',
        'expires_at',
        'created_by',
        'is_active',
    ];
    protected $useTimestamps = true;

    public function generateToken(int $examId, ?int $subjectId = null, int $durasiMenit = 15): array
    {
        // Deactivate previous active tokens for this exam & subject
        $q = $this->where('exam_id', $examId)->where('is_active', 1);
        if ($subjectId) {
            $q->where('subject_id', $subjectId);
        }
        $q->set(['is_active' => 0])->update();

        // 6 uppercase letters for clean CBT token (excluding confusing I, O, 0, 1)
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $tokenStr = substr(str_shuffle($chars), 0, 6);

        $now = time();
        $expiresAt = date('Y-m-d H:i:s', $now + ($durasiMenit * 60));

        $data = [
            'exam_id'      => $examId,
            'subject_id'   => $subjectId,
            'token'        => $tokenStr,
            'durasi_menit' => $durasiMenit,
            'expires_at'   => $expiresAt,
            'created_by'   => session()->get('admin_id') ?? 1,
            'is_active'    => 1,
        ];

        $id = $this->insert($data);
        $data['id'] = $id;
        return $data;
    }

    public function getActiveToken(int $examId, ?int $subjectId = null): ?array
    {
        $q = $this->where('exam_id', $examId)->where('is_active', 1);
        if ($subjectId) {
            $q->where('subject_id', $subjectId);
        }
        $token = $q->orderBy('id', 'DESC')->first();

        if ($token) {
            // Check if expired
            if (strtotime($token['expires_at']) < time()) {
                $this->update($token['id'], ['is_active' => 0]);
                $token['is_active'] = 0;
            }
        }
        return $token;
    }
}
