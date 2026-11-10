<?php

namespace App\Controllers;

use App\Models\{RoomModel, ExamModel, ParticipantModel, SettingModel};
use Mpdf\Mpdf;

class Room extends BaseController
{
    public function index()
    {
        $rooms = (new RoomModel())->findAll();
        $exams = (new ExamModel())->orderBy('id', 'DESC')->findAll();
        return view('room/index', [
            'title' => 'Manajemen Ruang Ujian',
            'rooms' => $rooms,
            'exams' => $exams,
        ]);
    }

    public function create()
    {
        return view('room/form', ['title' => 'Tambah Ruang', 'room' => null]);
    }

    public function store()
    {
        (new RoomModel())->insert($this->request->getPost(['nama_ruang', 'gedung', 'lantai', 'kapasitas', 'keterangan']));
        return redirect()->to(base_url('room'))->with('success', 'Ruang berhasil ditambahkan!');
    }

    public function edit(int $id)
    {
        return view('room/form', ['title' => 'Edit Ruang', 'room' => (new RoomModel())->find($id)]);
    }

    public function update(int $id)
    {
        (new RoomModel())->update($id, $this->request->getPost(['nama_ruang', 'gedung', 'lantai', 'kapasitas', 'keterangan']));
        return redirect()->to(base_url('room'))->with('success', 'Ruang diperbarui!');
    }

    public function delete(int $id)
    {
        (new RoomModel())->delete($id);
        return redirect()->to(base_url('room'))->with('success', 'Ruang dihapus!');
    }

    /**
     * Tampilan Denah & Tata Letak Tempat Duduk Ruangan (Web Printable & PDF)
     */
    public function denah(int $examId, int $roomId = 0)
    {
        $exam = (new ExamModel())->find($examId);
        if (!$exam) {
            return redirect()->to(base_url('room'))->with('error', 'Ujian tidak ditemukan!');
        }

        $rooms = (new RoomModel())->findAll();
        if ($roomId === 0 && !empty($rooms)) {
            $roomId = (int)$rooms[0]['id'];
        }
        $room = (new RoomModel())->find($roomId);
        if (!$room) {
            return redirect()->to(base_url('room'))->with('error', 'Ruangan tidak ditemukan!');
        }

        $sesi = (int)($this->request->getGet('sesi') ?: 1);

        $pModel = new ParticipantModel();
        $participants = $pModel->getByExamRoom($examId, $roomId, $sesi);

        // Cari sesi yang tersedia untuk ruang dan ujian ini
        $allRoomParticipants = $pModel->where('exam_id', $examId)->where('room_id', $roomId)->findAll();
        $availableSessions = array_unique(array_filter(array_column($allRoomParticipants, 'sesi')));
        sort($availableSessions);
        if (empty($availableSessions)) {
            $availableSessions = [1];
        }

        $settings = (new SettingModel())->getAllKeyValue();

        // Format PDF jika diminta via parameter
        if ($this->request->getGet('export') === 'pdf') {
            $html = view('room/pdf_denah', compact('exam', 'room', 'rooms', 'participants', 'sesi', 'settings', 'availableSessions'));
            $mpdf = new Mpdf([
                'orientation'   => 'L',
                'margin_top'    => 10,
                'margin_bottom' => 10,
                'margin_left'   => 12,
                'margin_right'  => 12,
            ]);
            $mpdf->WriteHTML($html);
            $cleanRoomName = preg_replace('/[^A-Za-z0-9_]/', '_', $room['nama_ruang']);
            $mpdf->Output("Denah_{$cleanRoomName}_Sesi_{$sesi}.pdf", 'I');
            exit;
        }

        return view('room/print_denah', compact('exam', 'room', 'rooms', 'participants', 'sesi', 'settings', 'availableSessions'));
    }
}
