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

        // 6 uppercase chars — cryptographically secure, excludes confusing chars (I, O, 0, 1)
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $charLen = strlen($chars);
        $tokenStr = '';
        $bytes = random_bytes(12); // extra bytes for better distribution
        for ($i = 0; $i < 6; $i++) {
            $tokenStr .= $chars[ord($bytes[$i]) % $charLen];
        }

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

    /**
     * Get audit trail of token generation with details of admin and subject
     */
    public function getTokenHistoryWithDetails(int $examId, int $limit = 25): array
    {
        return $this->select('exam_tokens.*, admin_users.nama as pembuat, admin_users.username as pembuat_username, exam_subjects.nama_mapel, exam_subjects.kelas')
            ->join('admin_users', 'admin_users.id = exam_tokens.created_by', 'left')
            ->join('exam_subjects', 'exam_subjects.id = exam_tokens.subject_id', 'left')
            ->where('exam_tokens.exam_id', $examId)
            ->orderBy('exam_tokens.id', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get Master Proctor Unlock PIN (separate from quiz token)
     */
    public function getProctorPin(): string
    {
        return (new SettingModel())->getVal('cbt_proctor_pin', 'PROKTOR123');
    }

    /**
     * Save Master Proctor Unlock PIN and sync to Moodle config
     */
    public function setProctorPin(string $pin): bool
    {
        $cleanPin = strtoupper(trim($pin));
        if (empty($cleanPin)) {
            $cleanPin = 'PROKTOR123';
        }

        (new SettingModel())->setVal('cbt_proctor_pin', $cleanPin);

        // Sync to Moodle mdl_config_plugins for local_cbt_kiosk
        try {
            $moodleDb = Database::connect('moodle', false);
            if ($moodleDb) {
                $existing = $moodleDb->table('config_plugins')
                    ->where('plugin', 'local_cbt_kiosk')
                    ->where('name', 'proctor_pin')
                    ->get()->getRowArray();
                if ($existing) {
                    $moodleDb->table('config_plugins')
                        ->where('id', $existing['id'])
                        ->update(['value' => $cleanPin]);
                } else {
                    $moodleDb->table('config_plugins')->insert([
                        'plugin' => 'local_cbt_kiosk',
                        'name'   => 'proctor_pin',
                        'value'  => $cleanPin,
                    ]);
                }
            }
        } catch (Throwable $e) {
            log_message('error', 'TokenModel::setProctorPin sync to Moodle failed: ' . $e->getMessage());
        }

        return true;
    }
}
