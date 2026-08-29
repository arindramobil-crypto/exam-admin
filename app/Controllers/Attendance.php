<?php

namespace App\Controllers;

use App\Models\{ExamModel, ExamSubjectModel, ParticipantModel, AttendanceModel, RoomModel, SettingModel};
use Mpdf\Mpdf;

class Attendance extends BaseController
{
    /**
     * Main Attendance Index - Filter by Exam, Subject, Room, and Session
     */
    public function index(?int $examId = null, ?int $subjectId = null)
    {
        $examModel = new ExamModel();

        // If no exam selected, show exam list
        if (!$examId) {
            $exams = $examModel->orderBy('id', 'DESC')->findAll();
            return view('attendance/select_exam', [
                'title' => 'Daftar Hadir Ujian',
                'exams' => $exams,
            ]);
        }

        $exam = $examModel->find($examId);
        if (!$exam) {
            return redirect()->to(base_url('attendance'))->with('error', 'Ujian tidak ditemukan!');
        }

        $subjects = (new ExamSubjectModel())->where('exam_id', $examId)->findAll();
        $rooms    = (new RoomModel())->findAll();

        // Filter parameters
        $selectedSubjectId = (int)($this->request->getGet('subject_id') ?: ($subjectId ?: 0));
        $selectedRoomId    = (int)($this->request->getGet('room_id') ?: 0);
        $selectedSesi      = (int)($this->request->getGet('sesi') ?: 0);

        $pModel = new ParticipantModel();
        $q = $pModel->where('exam_id', $examId);

        if ($selectedSubjectId > 0) {
            $q->where('subject_id', $selectedSubjectId);
        }
        if ($selectedRoomId > 0) {
            $q->where('room_id', $selectedRoomId);
        }
        if ($selectedSesi > 0) {
            $q->where('sesi', $selectedSesi);
        }

        $participants = $q->orderBy('room_id', 'ASC')->orderBy('sesi', 'ASC')->orderBy('nomor_meja', 'ASC')->orderBy('nomor_peserta', 'ASC')->findAll();

        // Get distinct sessions available in this exam
        $allExamParticipants = $pModel->where('exam_id', $examId)->findAll();
        $sessions = array_unique(array_filter(array_column($allExamParticipants, 'sesi')));
        sort($sessions);
        if (empty($sessions)) {
            $sessions = [1];
        }

        // Attendance records
        $aModel = new AttendanceModel();
        $aQuery = $aModel->where('exam_id', $examId);
        if ($selectedSubjectId > 0) {
            $aQuery->where('subject_id', $selectedSubjectId);
        }
        $rows = $aQuery->findAll();
        $attendances = [];
        foreach ($rows as $r) {
            $attendances[$r['participant_id']] = $r;
        }

        // Count stats
        $stats = ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpa' => 0, 'belum' => 0];
        foreach ($participants as $p) {
            $st = $attendances[$p['id']]['status'] ?? '';
            if (isset($stats[$st])) {
                $stats[$st]++;
            } else {
                $stats['belum']++;
            }
        }

        return view('attendance/index', [
            'title'             => 'Daftar Hadir: ' . $exam['nama_ujian'],
            'exam'              => $exam,
            'subjects'          => $subjects,
            'rooms'             => $rooms,
            'sessions'          => $sessions,
            'participants'      => $participants,
            'attendances'       => $attendances,
            'selectedSubjectId' => $selectedSubjectId,
            'selectedRoomId'    => $selectedRoomId,
            'selectedSesi'      => $selectedSesi,
            'stats'             => $stats,
        ]);
    }

    /**
     * Save attendance status
     */
    public function save(int $examId, int $subjectId = 0)
    {
        $selectedSubjectId = (int)($this->request->getPost('subject_id') ?: $subjectId);
        $statuses = $this->request->getPost('status') ?? [];
        $aModel = new AttendanceModel();
        $adminId = session()->get('admin_id');
        $nowTime = date('H:i:s');

        foreach ($statuses as $pid => $status) {
            if (empty($status)) continue;

            $q = $aModel->where('exam_id', $examId)->where('participant_id', $pid);
            if ($selectedSubjectId > 0) {
                $q->where('subject_id', $selectedSubjectId);
            }
            $existing = $q->first();

            $data = [
                'exam_id'        => $examId,
                'subject_id'     => $selectedSubjectId ?: null,
                'participant_id' => $pid,
                'status'         => $status,
                'jam_absen'      => $nowTime,
                'dicatat_oleh'   => $adminId,
            ];

            if ($existing) {
                $aModel->update($existing['id'], $data);
            } else {
                $aModel->insert($data);
            }
        }

        return redirect()->back()->with('success', 'Daftar hadir peserta berhasil diperbarui!');
    }

    /**
     * Print / Preview Attendance Sheet filtered by Room & Session
     */
    public function print(int $examId, int $subjectId = 0, int $roomId = 0)
    {
        $exam = (new ExamModel())->find($examId);
        if (!$exam) {
            return redirect()->to(base_url('attendance'))->with('error', 'Ujian tidak ditemukan!');
        }

        $selectedSubjectId = (int)($this->request->getGet('subject_id') ?: $subjectId);
        $selectedRoomId    = (int)($this->request->getGet('room_id') ?: $roomId);
        $selectedSesi      = (int)($this->request->getGet('sesi') ?: 0);

        $subject = $selectedSubjectId ? (new ExamSubjectModel())->find($selectedSubjectId) : null;
        $room    = $selectedRoomId ? (new RoomModel())->find($selectedRoomId) : null;
        $rooms   = (new RoomModel())->findAll();
        $roomsMap = array_column($rooms, 'nama_ruang', 'id');

        $pModel = new ParticipantModel();
        $q = $pModel->where('exam_id', $examId);

        if ($selectedSubjectId > 0) {
            $hasSubP = (new ParticipantModel())->where('exam_id', $examId)->where('subject_id', $selectedSubjectId)->countAllResults();
            if ($hasSubP > 0) {
                $q->where('subject_id', $selectedSubjectId);
            }
        }
        if ($selectedRoomId > 0) {
            $q->where('room_id', $selectedRoomId);
        }
        if ($selectedSesi > 0) {
            $q->where('sesi', $selectedSesi);
        }

        $participants = $q->orderBy('room_id', 'ASC')->orderBy('sesi', 'ASC')->orderBy('nomor_meja', 'ASC')->orderBy('nomor_peserta', 'ASC')->findAll();
        $settings = (new SettingModel())->getAllKeyValue();

        // Check if PDF download requested
        if ($this->request->getGet('export') === 'pdf') {
            $html = view('attendance/pdf_daftar_hadir', compact('exam', 'subject', 'room', 'roomsMap', 'participants', 'settings', 'selectedSesi'));
            $mpdf = new Mpdf(['margin_top' => 12, 'margin_bottom' => 12, 'margin_left' => 15, 'margin_right' => 15]);
            $mpdf->WriteHTML($html);
            $roomLabel = $room ? preg_replace('/[^A-Za-z0-9_]/', '_', $room['nama_ruang']) : 'Semua_Ruang';
            $sesiLabel = $selectedSesi ? "_Sesi_{$selectedSesi}" : '';
            $mpdf->Output("Daftar_Hadir_{$exam['nama_ujian']}_{$roomLabel}{$sesiLabel}.pdf", 'I');
            exit;
        }

        // Return web printable preview
        return view('attendance/print_daftar_hadir', compact('exam', 'subject', 'room', 'roomsMap', 'participants', 'settings', 'selectedSesi', 'selectedSubjectId', 'selectedRoomId'));
    }
}