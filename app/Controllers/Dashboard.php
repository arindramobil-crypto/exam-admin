<?php
namespace App\Controllers;

use App\Models\{ExamModel, ParticipantModel, RoomModel, MoodleQuizModel, ExamSubjectModel, TokenModel};
use App\Libraries\MoodleService;

class Dashboard extends BaseController
{
    public function index()
    {
        $examModel     = new ExamModel();
        $tokenModel    = new TokenModel();
        $moodleService = new MoodleService();
        $moodleStatus  = $moodleService->checkConnection();

        $examsRecent = $examModel->orderBy('created_at', 'DESC')->limit(5)->findAll();

        // Kumpulkan info token aktif & jumlah peserta per ujian
        $examStats = [];
        foreach ($examsRecent as $e) {
            $activeToken  = $tokenModel->getActiveToken((int)$e['id']);
            $totalPeserta = (new ParticipantModel())->where('exam_id', $e['id'])->countAllResults();
            $examStats[$e['id']] = [
                'active_token'  => $activeToken,
                'total_peserta' => $totalPeserta,
            ];
        }

        $data = [
            'title'         => 'Dashboard',
            'total_exam'    => $examModel->countAll(),
            'exam_aktif'    => $examModel->where('status', 'aktif')->countAllResults(),
            'exam_selesai'  => $examModel->where('status', 'selesai')->countAllResults(),
            'total_peserta' => (new ParticipantModel())->countAll(),
            'total_ruang'   => (new RoomModel())->countAll(),
            'exams_recent'  => $examsRecent,
            'exam_stats'    => $examStats,
            'moodle_status' => $moodleStatus,
        ];
        return view('dashboard/index', $data);
    }

    public function monitoring(int $examId)
    {
        $examModel = new ExamModel();
        $quizModel = new MoodleQuizModel();
        $exam = $examModel->find($examId);
        if (!$exam) {
            return redirect()->to(base_url('exam'))->with('error', 'Ujian tidak ditemukan!');
        }

        $subjects = (new ExamSubjectModel())->where('exam_id', $examId)->findAll();
        $monitoring = [];
        foreach ($subjects as $subject) {
            if ($subject['moodle_quiz_id']) {
                $attempts = $quizModel->getQuizAttempts((int)$subject['moodle_quiz_id']);
                $selesai  = array_filter($attempts, fn($a) => $a['state'] === 'finished');
                $monitoring[] = [
                    'subject'  => $subject,
                    'total'    => count($attempts),
                    'selesai'  => count($selesai),
                    'belum'    => count($attempts) - count($selesai),
                    'attempts' => $attempts,
                ];
            }
        }
        return view('dashboard/monitoring', [
            'title'      => 'Monitoring Ujian',
            'exam'       => $exam,
            'monitoring' => $monitoring,
        ]);
    }
}
