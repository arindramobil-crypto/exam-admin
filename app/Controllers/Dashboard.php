<?php
namespace App\Controllers;

use App\Models\{ExamModel, ParticipantModel, RoomModel, MoodleQuizModel, ExamSubjectModel};
use App\Libraries\MoodleService;

class Dashboard extends BaseController
{
    public function index()
    {
        $examModel = new ExamModel();
        $moodleService = new MoodleService();
        $moodleStatus = $moodleService->checkConnection();

        $data = [
            'title'         => 'Dashboard',
            'total_exam'    => $examModel->countAll(),
            'exam_aktif'    => $examModel->where('status', 'aktif')->countAllResults(),
            'exam_selesai'  => $examModel->where('status', 'selesai')->countAllResults(),
            'total_peserta' => (new ParticipantModel())->countAll(),
            'total_ruang'   => (new RoomModel())->countAll(),
            'exams_recent'  => $examModel->orderBy('created_at','DESC')->limit(5)->findAll(),
            'moodle_status' => $moodleStatus,
        ];
        return view('dashboard/index', $data);
    }

    public function monitoring(int $examId)
    {
        $examModel  = new ExamModel();
        $quizModel  = new MoodleQuizModel();
        $exam = $examModel->find($examId);
        if (!$exam) {
            return redirect()->to(base_url('exam'))->with('error', 'Ujian tidak ditemukan!');
        }

        $subjects = (new ExamSubjectModel())->where('exam_id', $examId)->findAll();
        $monitoring = [];
        foreach ($subjects as $subject) {
            if ($subject['moodle_quiz_id']) {
                $attempts = $quizModel->getQuizAttempts((int)$subject['moodle_quiz_id']);
                $selesai = array_filter($attempts, fn($a) => $a['state'] === 'finished');
                $monitoring[] = [
                    'subject'   => $subject,
                    'total'     => count($attempts),
                    'selesai'   => count($selesai),
                    'belum'     => count($attempts) - count($selesai),
                    'attempts'  => $attempts,
                ];
            }
        }
        return view('dashboard/monitoring', ['title'=>'Monitoring Ujian', 'exam'=>$exam, 'monitoring'=>$monitoring]);
    }
}