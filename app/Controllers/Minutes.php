<?php

namespace App\Controllers;

use App\Models\{ExamModel, ExamSubjectModel, RoomModel, ExamMinutesModel, AttendanceModel, SettingModel};
use Mpdf\Mpdf;

class Minutes extends BaseController
{
    public function index(int $examId)
    {
        $exam     = (new ExamModel())->find($examId);
        $subjects = (new ExamSubjectModel())->where('exam_id', $examId)->findAll();
        $rooms    = (new RoomModel())->findAll();
        $minutes  = (new ExamMinutesModel())->where('exam_id', $examId)->findAll();
        return view('minutes/index', compact('exam', 'subjects', 'rooms', 'minutes') + ['title' => 'Berita Acara']);
    }

    public function form(int $examId, int $subjectId, int $roomId)
    {
        $exam    = (new ExamModel())->find($examId);
        $subject = (new ExamSubjectModel())->find($subjectId);
        $room    = (new RoomModel())->find($roomId);
        $existing = (new ExamMinutesModel())->where('exam_id', $examId)->where('subject_id', $subjectId)->where('room_id', $roomId)->first();
        $hadir = (new AttendanceModel())->where('exam_id', $examId)->where('subject_id', $subjectId)->where('status', 'hadir')->countAllResults();
        $absen = (new AttendanceModel())->where('exam_id', $examId)->where('subject_id', $subjectId)->where('status !=', 'hadir')->countAllResults();
        return view('minutes/form', compact('exam', 'subject', 'room', 'existing', 'hadir', 'absen') + ['title' => 'Input Berita Acara']);
    }

    public function save()
    {
        $data = $this->request->getPost();
        $mModel = new ExamMinutesModel();
        $existing = $mModel->where('exam_id', $data['exam_id'])->where('subject_id', $data['subject_id'])->where('room_id', $data['room_id'])->first();
        if ($existing) {
            $mModel->update($existing['id'], $data);
        } else {
            $mModel->insert($data);
        }
        return redirect()->to(base_url("minutes/{$data['exam_id']}"))->with('success', 'Berita Acara berhasil disimpan!');
    }

    public function print(int $id)
    {
        $ba      = (new ExamMinutesModel())->find($id);
        if (!$ba) {
            return redirect()->back()->with('error', 'Berita Acara tidak ditemukan!');
        }
        $exam    = (new ExamModel())->find($ba['exam_id']);
        $subject = (new ExamSubjectModel())->find($ba['subject_id']);
        $room    = (new RoomModel())->find($ba['room_id']);
        $settings = (new SettingModel())->getAllKeyValue();

        $html    = view('minutes/pdf_berita_acara', compact('ba', 'exam', 'subject', 'room', 'settings'));
        $mpdf    = new Mpdf(['margin_top' => 12, 'margin_bottom' => 12, 'margin_left' => 20, 'margin_right' => 20]);
        $mpdf->WriteHTML($html);
        $mpdf->Output("Berita_Acara_{$subject['nama_mapel']}_{$room['nama_ruang']}.pdf", 'I');
        exit;
    }
}