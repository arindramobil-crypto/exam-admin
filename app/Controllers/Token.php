<?php

namespace App\Controllers;

use App\Models\{ExamModel, ExamSubjectModel, TokenModel};

class Token extends BaseController
{
    protected $tokenModel;
    protected $examModel;

    public function __construct()
    {
        $this->tokenModel = new TokenModel();
        $this->examModel  = new ExamModel();
    }

    public function index(int $examId)
    {
        $exam = $this->examModel->find($examId);
        if (!$exam) {
            return redirect()->to(base_url('exam'))->with('error', 'Ujian tidak ditemukan!');
        }

        $subjects = (new ExamSubjectModel())->where('exam_id', $examId)->findAll();
        $activeToken = $this->tokenModel->getActiveToken($examId);
        $recentTokens = $this->tokenModel->getTokenHistoryWithDetails($examId, 25);
        $proctorPin = $this->tokenModel->getProctorPin();

        return view('token/index', [
            'title'        => 'Rilis Token CBT',
            'exam'         => $exam,
            'subjects'     => $subjects,
            'activeToken'  => $activeToken,
            'recentTokens' => $recentTokens,
            'proctorPin'   => $proctorPin,
        ]);
    }

    public function saveProctorPin(int $examId)
    {
        $newPin = $this->request->getPost('proctor_pin');
        if (!empty($newPin)) {
            $this->tokenModel->setProctorPin($newPin);
            return redirect()->to(base_url('token/' . $examId))->with('success', 'PIN Buka Kunci Pengawas berhasil diperbarui: ' . strtoupper(trim($newPin)));
        }
        return redirect()->to(base_url('token/' . $examId))->with('error', 'PIN tidak boleh kosong.');
    }

    public function generate(int $examId)
    {
        $subjectId   = $this->request->getPost('subject_id') ? (int)$this->request->getPost('subject_id') : null;
        $durasiMenit = (int)($this->request->getPost('durasi_menit') ?: 15);

        $token = $this->tokenModel->generateToken($examId, $subjectId, $durasiMenit);

        return redirect()->to(base_url('token/' . $examId))->with('success', 'Token baru berhasil dirilis: ' . $token['token']);
    }

    public function deactivate(int $id)
    {
        $success = $this->tokenModel->deactivateToken($id);
        if ($success) {
            return redirect()->back()->with('success', 'Token berhasil dinonaktifkan dan password kuis Moodle dibersihkan.');
        }
        return redirect()->back()->with('error', 'Token tidak ditemukan.');
    }

    public function display(int $examId)
    {
        $exam = $this->examModel->find($examId);
        if (!$exam) {
            return redirect()->to(base_url('exam'))->with('error', 'Ujian tidak ditemukan!');
        }

        $activeToken = $this->tokenModel->getActiveToken($examId);
        $subject = null;
        if ($activeToken && $activeToken['subject_id']) {
            $subject = (new ExamSubjectModel())->find($activeToken['subject_id']);
        }

        return view('token/display', [
            'title'       => 'Layar Token - ' . $exam['nama_ujian'],
            'exam'        => $exam,
            'activeToken' => $activeToken,
            'subject'     => $subject,
        ]);
    }

    public function getActiveTokenJson(int $examId)
    {
        $activeToken = $this->tokenModel->getActiveToken($examId);
        $remainingSeconds = 0;
        if ($activeToken && $activeToken['is_active'] && !empty($activeToken['expires_at'])) {
            $remainingSeconds = max(0, strtotime($activeToken['expires_at']) - time());
        }

        return $this->response->setJSON([
            'token'     => $activeToken ? $activeToken['token'] : '-',
            'is_active' => $activeToken ? (bool)$activeToken['is_active'] : false,
            'remaining' => $remainingSeconds,
            'expires_at'=> $activeToken ? $activeToken['expires_at'] : null,
        ]);
    }
}
