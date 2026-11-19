<?php
namespace App\Controllers;

use App\Models\{ExamModel, ParticipantModel, RoomModel, MoodleQuizModel, ExamSubjectModel, TokenModel, MoodleViolationModel};
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

    public function monitoring(?int $examId = null)
    {
        if (!$examId) {
            $active = \App\Libraries\ExamContext::getActiveExam();
            if ($active) {
                return redirect()->to(base_url('dashboard/monitoring/' . $active['id']));
            }
            return redirect()->to(base_url('exam'))->with('error', 'Silakan buat paket ujian terlebih dahulu.');
        }

        \App\Libraries\ExamContext::setActiveExam($examId);

        $examModel      = new ExamModel();
        $quizModel      = new MoodleQuizModel();
        $violationModel = new MoodleViolationModel();
        $tokenModel     = new TokenModel();

        $exam = $examModel->find($examId);
        if (!$exam) {
            return redirect()->to(base_url('exam'))->with('error', 'Ujian tidak ditemukan!');
        }

        $subjects = (new ExamSubjectModel())->where('exam_id', $examId)->findAll();
        $quizIds  = array_filter(array_map('intval', array_column($subjects, 'moodle_quiz_id')));

        // Ambil data statistik dan log pelanggaran tab / kiosk
        $violationStats   = !empty($quizIds) ? $violationModel->getStudentViolationStats($quizIds) : [];
        $recentViolations = !empty($quizIds) ? $violationModel->getViolationsByQuizIds($quizIds, 50) : [];
        $proctorPin       = $tokenModel->getProctorPin();
        $activeToken      = $tokenModel->getActiveToken($examId);

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

        // Hitung rekap pelanggaran global
        $totalLocked = 0;
        foreach ($violationStats as $st) {
            if (!empty($st['is_locked'])) {
                $totalLocked++;
            }
        }
        $totalViolations = count(array_filter($recentViolations, fn($v) => in_array($v['action'], ['warning_1', 'warning_2', 'locked'])));
        $totalUnlocked   = count(array_filter($recentViolations, fn($v) => $v['action'] === 'unlocked'));

        return view('dashboard/monitoring', [
            'title'            => 'Monitoring Ujian Live',
            'exam'             => $exam,
            'monitoring'       => $monitoring,
            'violationStats'   => $violationStats,
            'recentViolations' => $recentViolations,
            'totalViolations'  => $totalViolations,
            'totalLocked'      => $totalLocked,
            'totalUnlocked'    => $totalUnlocked,
            'proctorPin'       => $proctorPin,
            'activeToken'      => $activeToken,
        ]);
    }

    /**
     * Remote unlock a locked student directly from the admin dashboard
     */
    public function remoteUnlock(int $examId)
    {
        $quizId    = (int)$this->request->getPost('quiz_id');
        $userId    = (int)$this->request->getPost('user_id');
        $userName  = trim((string)($this->request->getPost('student_name') ?: 'Siswa'));

        if ($quizId <= 0 || $userId <= 0) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Parameter tidak valid.']);
            }
            return redirect()->to(base_url('dashboard/monitoring/' . $examId))->with('error', 'Parameter tidak valid.');
        }

        $adminName = session()->get('nama') ?: (session()->get('username') ?: 'Admin');

        $violationModel = new MoodleViolationModel();
        $ok = $violationModel->remoteUnlock($quizId, $userId, $adminName);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => $ok,
                'message' => $ok ? "Kunci ujian untuk {$userName} berhasil dibuka kembali." : 'Gagal membuka kunci ujian.',
            ]);
        }

        if ($ok) {
            return redirect()->to(base_url('dashboard/monitoring/' . $examId))
                ->with('success', "Kunci ujian untuk {$userName} berhasil dibuka kembali dari Dashboard.");
        }
        return redirect()->to(base_url('dashboard/monitoring/' . $examId))
            ->with('error', "Gagal membuka kunci ujian untuk {$userName}.");
    }

    /**
     * Reset all violation logs for subjects in this exam
     */
    public function resetViolations(int $examId)
    {
        $examModel = new ExamModel();
        $exam = $examModel->find($examId);
        if (!$exam) {
            return redirect()->to(base_url('exam'))->with('error', 'Ujian tidak ditemukan!');
        }

        $subjects = (new ExamSubjectModel())->where('exam_id', $examId)->findAll();
        $quizIds  = array_filter(array_map('intval', array_column($subjects, 'moodle_quiz_id')));

        if (empty($quizIds)) {
            return redirect()->to(base_url('dashboard/monitoring/' . $examId))
                ->with('error', 'Tidak ada kuis Moodle yang tertaut pada ujian ini.');
        }

        $violationModel = new MoodleViolationModel();
        $ok = $violationModel->resetViolationsByQuizIds($quizIds);

        if ($ok) {
            return redirect()->to(base_url('dashboard/monitoring/' . $examId))
                ->with('success', 'Seluruh log audit pelanggaran untuk ujian ini berhasil dibersihkan.');
        }

        return redirect()->to(base_url('dashboard/monitoring/' . $examId))
            ->with('error', 'Gagal membersihkan log pelanggaran.');
    }

    /**
     * Reset all student attempts for a specific quiz in Moodle (misal setelah gladi bersih / uji coba)
     */
    public function resetQuizAttempts(int $examId, int $quizId)
    {
        $examModel = new ExamModel();
        $exam = $examModel->find($examId);
        if (!$exam) {
            return redirect()->to(base_url('exam'))->with('error', 'Ujian tidak ditemukan!');
        }

        $quizModel = new MoodleQuizModel();
        $ok = $quizModel->resetQuizAttempts($quizId);

        if ($ok) {
            return redirect()->to(base_url('dashboard/monitoring/' . $examId))
                ->with('success', 'Seluruh data pengerjaan (attempt) siswa pada kuis Moodle berhasil dibersihkan. Siswa dapat memulai kuis baru.');
        }

        return redirect()->to(base_url('dashboard/monitoring/' . $examId))
            ->with('error', 'Gagal mereset data pengerjaan kuis.');
    }
}
