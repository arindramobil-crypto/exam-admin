<?php

namespace App\Models;

use CodeIgniter\Model;
use Throwable;

class MoodleGradeModel extends Model
{
    protected $table      = 'grade_grades';
    protected $primaryKey = 'id';
    protected $DBGroup    = 'moodle';

    /**
     * Normalize a raw Moodle grade to a 0–100 scale.
     * Moodle stores grades relative to quiz maxgrade, so we scale accordingly.
     */
    private function normalizeGrade(?float $grade, ?float $maxgrade): ?float
    {
        if ($grade === null) return null;
        if ($maxgrade === null || $maxgrade <= 0) return round($grade, 2);
        return round(($grade / $maxgrade) * 100, 2);
    }

    public function getGradesByQuiz(int $quizId): array
    {
        try {
            $rows = $this->db->table('quiz_grades qg')
                ->select('u.id as user_id, u.firstname, u.lastname, u.username, u.idnumber, u.email, qg.grade, qg.timemodified, q.grade as maxgrade, q.name as quiz_name')
                ->join('user u', 'u.id = qg.userid')
                ->join('quiz q', 'q.id = qg.quiz')
                ->where('qg.quiz', $quizId)
                ->where('u.deleted', 0)
                ->orderBy('u.lastname', 'ASC')
                ->orderBy('u.firstname', 'ASC')
                ->get()->getResultArray();

            // Normalize each grade to 0-100 scale
            foreach ($rows as &$row) {
                $row['grade_raw']    = $row['grade'];
                $row['grade']        = $this->normalizeGrade(
                    $row['grade'] !== null ? (float)$row['grade'] : null,
                    $row['maxgrade'] !== null ? (float)$row['maxgrade'] : null
                );
            }
            return $rows;
        } catch (Throwable $e) {
            log_message('error', 'MoodleGradeModel::getGradesByQuiz failed: ' . $e->getMessage());
            return [];
        }
    }

    public function getGradesByCourse(int $courseId): array
    {
        try {
            return $this->db->table('grade_grades gg')
                ->select('u.id as user_id, u.firstname, u.lastname, u.username, u.idnumber, gi.itemname, gg.finalgrade')
                ->join('grade_items gi', 'gi.id = gg.itemid')
                ->join('user u', 'u.id = gg.userid')
                ->where('gi.courseid', $courseId)
                ->where('gi.itemtype', 'mod')
                ->where('gi.itemmodule', 'quiz')
                ->where('u.deleted', 0)
                ->whereNotNull('gg.finalgrade')
                ->orderBy('u.lastname', 'ASC')
                ->orderBy('u.firstname', 'ASC')
                ->get()->getResultArray();
        } catch (Throwable $e) {
            log_message('error', 'MoodleGradeModel::getGradesByCourse failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get unified grades combining exam participants with Moodle quiz attempts and grades
     */
    public function getUnifiedGrades(int $quizId, int $examId, int $subjectId): array
    {
        $participantModel = new ParticipantModel();
        $participants = $participantModel
            ->where('exam_id', $examId)
            ->where('subject_id', $subjectId)
            ->orderBy('nomor_peserta', 'ASC')
            ->findAll();

        if (empty($participants)) {
            $participants = $participantModel
                ->where('exam_id', $examId)
                ->orderBy('nomor_peserta', 'ASC')
                ->findAll();
        }

        $moodleGrades = $this->getGradesByQuiz($quizId);
        $gradeMap = [];
        foreach ($moodleGrades as $mg) {
            $gradeMap[$mg['user_id']] = $mg;
            $gradeMap['un_' . strtolower(trim($mg['username']))] = $mg;
        }

        $quizModel = new MoodleQuizModel();
        $attempts = $quizModel->getQuizAttempts($quizId);
        $attemptMap = [];
        foreach ($attempts as $at) {
            if (!isset($attemptMap[$at['user_id']])) {
                $attemptMap[$at['user_id']] = $at;
                $attemptMap['un_' . strtolower(trim($at['username']))] = $at;
            }
        }

        $unified = [];
        $handledMoodleUserIds = [];

        foreach ($participants as $p) {
            $mUserId = $p['moodle_user_id'] ? (int)$p['moodle_user_id'] : null;
            $username = strtolower(trim($p['nis'] ?: $p['nomor_peserta']));

            $gData = null;
            if ($mUserId && isset($gradeMap[$mUserId])) {
                $gData = $gradeMap[$mUserId];
            } elseif (isset($gradeMap['un_' . $username])) {
                $gData = $gradeMap['un_' . $username];
            }

            $aData = null;
            if ($mUserId && isset($attemptMap[$mUserId])) {
                $aData = $attemptMap[$mUserId];
            } elseif (isset($attemptMap['un_' . $username])) {
                $aData = $attemptMap['un_' . $username];
            }

            if ($mUserId) {
                $handledMoodleUserIds[$mUserId] = true;
            }
            if ($gData) {
                $handledMoodleUserIds[$gData['user_id']] = true;
            }

            $state = 'not_started';
            $grade = null;
            $timemodified = null;

            if ($gData && $gData['grade'] !== null) {
                $state = 'finished';
                $grade = (float)$gData['grade'];
                $timemodified = $gData['timemodified'];
            } elseif ($aData) {
                $state = $aData['state'];
                $grade = $aData['grade'] !== null ? (float)$aData['grade'] : ($aData['sumgrades'] !== null ? (float)$aData['sumgrades'] : null);
                $timemodified = $aData['timefinish'] ?: $aData['timemodified'];
            }

            $nameParts = preg_split('/\s+/', trim($p['nama']), 2);
            $unified[] = [
                'participant_id' => $p['id'],
                'user_id'        => $mUserId ?? ($gData['user_id'] ?? null),
                'nomor_peserta'  => $p['nomor_peserta'],
                'firstname'      => $nameParts[0] ?? $p['nama'],
                'lastname'       => $nameParts[1] ?? '',
                'username'       => $p['nis'] ?: $p['nomor_peserta'],
                'idnumber'       => $p['nis'] ?: '-',
                'grade'          => $grade,
                'state'          => $state,
                'timemodified'   => $timemodified,
                'kelas'          => $p['kelas'],
                'sesi'           => $p['sesi'] ?? 1,
            ];
        }

        // Include any Moodle users who took the quiz but were not explicitly registered as participants
        foreach ($moodleGrades as $mg) {
            if (!isset($handledMoodleUserIds[$mg['user_id']])) {
                $unified[] = [
                    'participant_id' => null,
                    'user_id'        => $mg['user_id'],
                    'nomor_peserta'  => '-',
                    'firstname'      => $mg['firstname'],
                    'lastname'       => $mg['lastname'],
                    'username'       => $mg['username'],
                    'idnumber'       => $mg['idnumber'] ?: '-',
                    'grade'          => (float)$mg['grade'],
                    'state'          => 'finished',
                    'timemodified'   => $mg['timemodified'],
                    'kelas'          => '-',
                    'sesi'           => 1,
                ];
            }
        }

        return $unified;
    }
}