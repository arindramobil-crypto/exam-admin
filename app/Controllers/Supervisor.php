<?php

namespace App\Controllers;

use App\Models\{ExamModel, ExamSubjectModel, RoomModel, SupervisorModel, SettingModel};
use Mpdf\Mpdf;

class Supervisor extends BaseController
{
    protected $supervisorModel;
    protected $examModel;

    public function __construct()
    {
        $this->supervisorModel = new SupervisorModel();
        $this->examModel       = new ExamModel();
    }

    public function index(int $examId)
    {
        $exam = $this->examModel->find($examId);
        if (!$exam) {
            return redirect()->to(base_url('exam'))->with('error', 'Ujian tidak ditemukan!');
        }

        $subjects    = (new ExamSubjectModel())->where('exam_id', $examId)->findAll();
        $rooms       = (new RoomModel())->findAll();
        $supervisors = $this->supervisorModel->getSupervisorsByExam($examId);

        return view('supervisor/index', [
            'title'       => 'Pengawas & Proktor Ujian',
            'exam'        => $exam,
            'subjects'    => $subjects,
            'rooms'       => $rooms,
            'supervisors' => $supervisors,
        ]);
    }

    public function store(int $examId)
    {
        $data = $this->request->getPost();
        $data['exam_id'] = $examId;

        $this->supervisorModel->insert($data);
        return redirect()->to(base_url('supervisor/' . $examId))->with('success', 'Data pengawas/proktor berhasil ditambahkan!');
    }

    public function delete(int $id)
    {
        $sp = $this->supervisorModel->find($id);
        if ($sp) {
            $examId = $sp['exam_id'];
            $this->supervisorModel->delete($id);
            return redirect()->to(base_url('supervisor/' . $examId))->with('success', 'Pengawas dihapus!');
        }
        return redirect()->back()->with('error', 'Data tidak ditemukan!');
    }

    public function printJadwal(int $examId)
    {
        $exam        = $this->examModel->find($examId);
        $supervisors = $this->supervisorModel->getSupervisorsByExam($examId);
        $rooms       = (new RoomModel())->findAll();
        $settings    = (new SettingModel())->getAllKeyValue();

        $html = view('supervisor/pdf_jadwal', compact('exam', 'supervisors', 'rooms', 'settings'));
        $mpdf = new Mpdf(['margin_top' => 12, 'margin_bottom' => 12, 'margin_left' => 15, 'margin_right' => 15]);
        $mpdf->WriteHTML($html);
        $mpdf->Output("Jadwal_Pengawas_{$exam['nama_ujian']}.pdf", 'I');
        exit;
    }
}
