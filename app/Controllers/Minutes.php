<?php

namespace App\Controllers;

use App\Models\{ExamModel, ExamSubjectModel, RoomModel, ExamMinutesModel, AttendanceModel, ParticipantModel, SupervisorModel, SettingModel};
use Mpdf\Mpdf;

class Minutes extends BaseController
{
    /**
     * Halaman Utama Berita Acara Ujian
     */
    public function index(int $examId)
    {
        $exam = (new ExamModel())->find($examId);
        if (!$exam) {
            return redirect()->to(base_url('exam'))->with('error', 'Ujian tidak ditemukan!');
        }

        $subjects = (new ExamSubjectModel())->where('exam_id', $examId)->findAll();
        $rooms    = (new RoomModel())->findAll();

        $minutesModel = new ExamMinutesModel();
        $minutes = $minutesModel->select('exam_minutes.*, exam_subjects.nama_mapel, exam_subjects.kelas, rooms.nama_ruang')
            ->join('exam_subjects', 'exam_subjects.id = exam_minutes.subject_id', 'left')
            ->join('rooms', 'rooms.id = exam_minutes.room_id', 'left')
            ->where('exam_minutes.exam_id', $examId)
            ->orderBy('exam_minutes.updated_at', 'DESC')
            ->findAll();

        return view('minutes/index', [
            'title'    => 'Berita Acara: ' . $exam['nama_ujian'],
            'exam'     => $exam,
            'subjects' => $subjects,
            'rooms'    => $rooms,
            'minutes'  => $minutes,
        ]);
    }

    /**
     * Form Input / Edit Berita Acara Per Ruang & Mapel
     */
    public function form(int $examId, int $subjectId, int $roomId)
    {
        $exam    = (new ExamModel())->find($examId);
        $subject = (new ExamSubjectModel())->find($subjectId);
        $room    = (new RoomModel())->find($roomId);

        if (!$exam || !$subject || !$room) {
            return redirect()->to(base_url('minutes/' . $examId))->with('error', 'Data tidak valid!');
        }

        $existing = (new ExamMinutesModel())
            ->where('exam_id', $examId)
            ->where('subject_id', $subjectId)
            ->where('room_id', $roomId)
            ->first();

        // Hitung peserta ruang ini secara spesifik
        $pModel = new ParticipantModel();
        $roomParticipants = $pModel->where('exam_id', $examId)->where('room_id', $roomId)->findAll();
        $totalSeharusnya = count($roomParticipants);
        $pIds = array_column($roomParticipants, 'id') ?: [0];

        $hadir = (new AttendanceModel())
            ->where('exam_id', $examId)
            ->where('subject_id', $subjectId)
            ->whereIn('participant_id', $pIds)
            ->where('status', 'hadir')
            ->countAllResults();

        $absen = max(0, $totalSeharusnya - $hadir);

        // Cari default pengawas & proktor dari data penugasan ruang
        $supervisors = (new SupervisorModel())
            ->where('exam_id', $examId)
            ->where('room_id', $roomId)
            ->findAll();

        $defaultPengawas = '';
        $defaultProktor  = '';
        foreach ($supervisors as $sp) {
            if ($sp['peran'] === 'pengawas' && empty($defaultPengawas)) {
                $defaultPengawas = $sp['nama_pengawas'];
            } elseif (($sp['peran'] === 'proktor' || $sp['peran'] === 'teknisi') && empty($defaultProktor)) {
                $defaultProktor = $sp['nama_pengawas'];
            }
        }

        return view('minutes/form', [
            'title'            => 'Input Berita Acara Ujian',
            'exam'             => $exam,
            'subject'          => $subject,
            'room'             => $room,
            'existing'         => $existing,
            'totalSeharusnya'  => $totalSeharusnya,
            'hadir'            => $hadir,
            'absen'            => $absen,
            'defaultPengawas'  => $defaultPengawas,
            'defaultProktor'   => $defaultProktor,
        ]);
    }

    /**
     * Simpan Berita Acara Ujian
     */
    public function save()
    {
        $examId    = (int)$this->request->getPost('exam_id');
        $subjectId = (int)$this->request->getPost('subject_id');
        $roomId    = (int)$this->request->getPost('room_id');

        $data = [
            'exam_id'           => $examId,
            'subject_id'        => $subjectId,
            'room_id'           => $roomId,
            'nama_pengawas1'    => trim($this->request->getPost('nama_pengawas1') ?? ''),
            'nama_pengawas2'    => trim($this->request->getPost('nama_pengawas2') ?? ''),
            'jml_peserta_hadir' => (int)$this->request->getPost('jml_peserta_hadir'),
            'jml_peserta_absen' => (int)$this->request->getPost('jml_peserta_absen'),
            'jml_soal'          => (int)($this->request->getPost('jml_soal') ?: 40),
            'waktu_mulai'       => $this->request->getPost('waktu_mulai') ?: null,
            'waktu_selesai'     => $this->request->getPost('waktu_selesai') ?: null,
            'kejadian'          => trim($this->request->getPost('kejadian') ?? ''),
            'catatan'           => trim($this->request->getPost('catatan') ?? ''),
            'status'            => $this->request->getPost('status') ?: 'draft',
        ];

        $mModel = new ExamMinutesModel();
        $existing = $mModel->where('exam_id', $examId)
            ->where('subject_id', $subjectId)
            ->where('room_id', $roomId)
            ->first();

        if ($existing) {
            $mModel->update($existing['id'], $data);
        } else {
            $mModel->insert($data);
        }

        return redirect()->to(base_url("minutes/{$examId}"))->with('success', 'Berita Acara ujian berhasil disimpan!');
    }

    /**
     * Cetak Berita Acara Format PDF Resmi
     */
    public function print(int $id)
    {
        $ba = (new ExamMinutesModel())->find($id);
        if (!$ba) {
            return redirect()->back()->with('error', 'Berita Acara tidak ditemukan!');
        }

        $exam     = (new ExamModel())->find($ba['exam_id']);
        $subject  = (new ExamSubjectModel())->find($ba['subject_id']);
        $room     = (new RoomModel())->find($ba['room_id']);
        $settings = (new SettingModel())->getAllKeyValue();

        $html = view('minutes/pdf_berita_acara', compact('ba', 'exam', 'subject', 'room', 'settings'));
        $mpdf = new Mpdf([
            'margin_top'    => 12,
            'margin_bottom' => 12,
            'margin_left'   => 18,
            'margin_right'  => 18,
        ]);
        $mpdf->WriteHTML($html);

        $cleanSubject = preg_replace('/[^A-Za-z0-9_]/', '_', $subject['nama_mapel']);
        $cleanRoom    = preg_replace('/[^A-Za-z0-9_]/', '_', $room['nama_ruang']);
        $mpdf->Output("Berita_Acara_{$cleanSubject}_{$cleanRoom}.pdf", 'I');
        exit;
    }
}
