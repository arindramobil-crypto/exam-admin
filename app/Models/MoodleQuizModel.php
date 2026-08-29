<?php

namespace App\Models;

use CodeIgniter\Model;
use Throwable;

class MoodleQuizModel extends Model
{
    protected $table      = 'quiz';
    protected $primaryKey = 'id';
    protected $DBGroup    = 'moodle';

    public function getQuizAttempts(int $quizId): array
    {
        try {
            return $this->db->table('quiz_attempts qa')
                ->select('u.id as user_id, u.firstname, u.lastname, u.username, u.idnumber, u.email, qa.id as attempt_id, qa.attempt, qa.state, qa.timestart, qa.timefinish, qa.timemodified, qa.sumgrades, qg.grade, q.grade as maxgrade')
                ->join('user u', 'u.id = qa.userid')
                ->join('quiz q', 'q.id = qa.quiz')
                ->join('quiz_grades qg', 'qg.userid = qa.userid AND qg.quiz = qa.quiz', 'left')
                ->where('qa.quiz', $quizId)
                ->where('qa.preview', 0)
                ->orderBy('qa.timestart', 'DESC')
                ->get()->getResultArray();
        } catch (Throwable $e) {
            log_message('error', 'MoodleQuizModel::getQuizAttempts failed: ' . $e->getMessage());
            return [];
        }
    }

    public function getAllQuizzes(): array
    {
        try {
            return $this->db->table('quiz q')
                ->select('q.id, q.name, q.intro, q.timeopen, q.timeclose, q.timelimit, q.grade as maxgrade, q.sumgrades, c.fullname as course_name, c.id as course_id')
                ->join('course c', 'c.id = q.course')
                ->where('c.id >', 1)
                ->orderBy('c.fullname', 'ASC')
                ->orderBy('q.name', 'ASC')
                ->get()->getResultArray();
        } catch (Throwable $e) {
            log_message('error', 'MoodleQuizModel::getAllQuizzes failed: ' . $e->getMessage());
            return [];
        }
    }

    public function getQuizzesByCourse(int $courseId): array
    {
        try {
            return $this->db->table('quiz q')
                ->select('q.id, q.name, q.intro, q.timeopen, q.timeclose, q.timelimit, q.grade as maxgrade, q.sumgrades, c.fullname as course_name, c.id as course_id')
                ->join('course c', 'c.id = q.course')
                ->where('q.course', $courseId)
                ->orderBy('q.name', 'ASC')
                ->get()->getResultArray();
        } catch (Throwable $e) {
            log_message('error', 'MoodleQuizModel::getQuizzesByCourse failed: ' . $e->getMessage());
            return [];
        }
    }

    public function getQuizDetails(int $quizId): ?array
    {
        try {
            return $this->db->table('quiz q')
                ->select('q.*, c.fullname as course_name, c.id as course_id')
                ->join('course c', 'c.id = q.course')
                ->where('q.id', $quizId)
                ->get()->getRowArray();
        } catch (Throwable $e) {
            return null;
        }
    }
}