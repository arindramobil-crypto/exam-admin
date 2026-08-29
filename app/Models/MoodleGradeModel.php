<?php

namespace App\Models;

use CodeIgniter\Model;
use Throwable;

class MoodleGradeModel extends Model
{
    protected $table      = 'grade_grades';
    protected $primaryKey = 'id';
    protected $DBGroup    = 'moodle';

    public function getGradesByQuiz(int $quizId): array
    {
        try {
            return $this->db->table('quiz_grades qg')
                ->select('u.id as user_id, u.firstname, u.lastname, u.username, u.idnumber, u.email, qg.grade, qg.timemodified, q.grade as maxgrade, q.name as quiz_name')
                ->join('user u', 'u.id = qg.userid')
                ->join('quiz q', 'q.id = qg.quiz')
                ->where('qg.quiz', $quizId)
                ->where('u.deleted', 0)
                ->orderBy('u.lastname', 'ASC')
                ->orderBy('u.firstname', 'ASC')
                ->get()->getResultArray();
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
}