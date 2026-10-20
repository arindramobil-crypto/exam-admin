<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;
use Throwable;

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

        // Sync token to Moodle Quiz password so students must use it to start the quiz
        $this->syncToMoodleQuiz($examId, $subjectId, $tokenStr);

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
                $this->clearMoodleQuizPassword($examId, $subjectId);
            }
        }
        return $token;
    }

    public function deactivateToken(int $tokenId): bool
    {
        $token = $this->find($tokenId);
        if (!$token) return false;

        $this->update($tokenId, ['is_active' => 0]);
        $this->clearMoodleQuizPassword((int)$token['exam_id'], !empty($token['subject_id']) ? (int)$token['subject_id'] : null);
        return true;
    }

    public function syncToMoodleQuiz(int $examId, ?int $subjectId, string $tokenStr): void
    {
        try {
            $moodleDb = Database::connect('moodle', false);
            if (!$moodleDb) return;

            $subjectModel = new ExamSubjectModel();
            if ($subjectId) {
                $sub = $subjectModel->find($subjectId);
                $subjects = $sub ? [$sub] : [];
            } else {
                $subjects = $subjectModel->where('exam_id', $examId)->findAll();
            }

            foreach ($subjects as $s) {
                if (!empty($s['moodle_quiz_id'])) {
                    $moodleDb->table('quiz')
                        ->where('id', (int)$s['moodle_quiz_id'])
                        ->update(['password' => $tokenStr]);
                }
            }
        } catch (Throwable $e) {
            log_message('error', 'TokenModel::syncToMoodleQuiz error: ' . $e->getMessage());
        }
    }

    public function clearMoodleQuizPassword(int $examId, ?int $subjectId): void
    {
        try {
            $moodleDb = Database::connect('moodle', false);
            if (!$moodleDb) return;

            $subjectModel = new ExamSubjectModel();
            if ($subjectId) {
                $sub = $subjectModel->find($subjectId);
                $subjects = $sub ? [$sub] : [];
            } else {
                $subjects = $subjectModel->where('exam_id', $examId)->findAll();
            }

            foreach ($subjects as $s) {
                if (!empty($s['moodle_quiz_id'])) {
                    $moodleDb->table('quiz')
                        ->where('id', (int)$s['moodle_quiz_id'])
                        ->update(['password' => '']);
                }
            }
        } catch (Throwable $e) {
            log_message('error', 'TokenModel::clearMoodleQuizPassword error: ' . $e->getMessage());
        }
    }
}
