<?php

namespace App\Controllers;

use App\Models\{ExamModel, ExamSubjectModel, ParticipantModel, RoomModel, MoodleUserModel, MoodleQuizModel, SettingModel};
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Mpdf\Mpdf;

class Participant extends BaseController
{
    public function index(int $examId)
    {
        $exam         = (new ExamModel())->find($examId);
        if (!$exam) {
            return redirect()->to(base_url('exam'))->with('error', 'Ujian tidak ditemukan!');
        }
        $subjects     = (new ExamSubjectModel())->where('exam_id', $examId)->findAll();
        $rooms        = (new RoomModel())->findAll();
        $participants = (new ParticipantModel())->where('exam_id', $examId)->orderBy('nomor_peserta', 'ASC')->findAll();

        return view('participant/index', compact('exam', 'subjects', 'rooms', 'participants') + ['title' => 'Peserta Ujian']);
    }

    public function importFromMoodle(int $examId)
    {
        $subjectId = $this->request->getPost('subject_id');
        $subject   = (new ExamSubjectModel())->find($subjectId);

        if (!$subject || !$subject['moodle_quiz_id']) {
            return redirect()->back()->with('error', 'Mapel ini belum ditautkan ke Quiz Moodle! Silakan tautkan di menu Mapel terlebih dahulu.');
        }

        $courseId = $subject['moodle_course_id'];
        if (!$courseId) {
            $quiz = (new MoodleQuizModel())->getQuizDetails((int)$subject['moodle_quiz_id']);
            $courseId = $quiz['course'] ?? ($quiz['course_id'] ?? null);
            if ($courseId) {
                (new ExamSubjectModel())->update($subjectId, ['moodle_course_id' => $courseId]);
            }
        }

        if (!$courseId) {
            return redirect()->back()->with('error', 'ID Kursus Moodle untuk quiz ini tidak ditemukan!');
        }

        $moodleUsers = (new MoodleUserModel())->getEnrolledStudents((int)$courseId);

        if (empty($moodleUsers)) {
            return redirect()->back()->with('error', 'Tidak ada siswa aktif yang terdaftar (enrolled) di kursus Moodle untuk quiz ini.');
        }

        $pModel = new ParticipantModel();
        $existing = $pModel->where('exam_id', $examId)->where('subject_id', $subjectId)->countAllResults();
        $urut = $existing + 1;
        $inserted = 0;
        $skipped = 0;

        $kelasStr = $this->request->getPost('kelas') ?: ($subject['kelas'] ?: '10');

        foreach ($moodleUsers as $u) {
            $exists = $pModel->where('exam_id', $examId)
                ->where('subject_id', $subjectId)
                ->where('moodle_user_id', $u['id'])
                ->first();

            if ($exists) {
                $skipped++;
                continue;
            }

            $nomor = $pModel->generateNomorPeserta($examId, $kelasStr, $urut);
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
            $pwd = substr(str_shuffle($chars), 0, 6);

            $pModel->insert([
                'exam_id'        => $examId,
                'subject_id'     => $subjectId,
                'moodle_user_id' => $u['id'],
                'nomor_peserta'  => $nomor,
                'nama'           => trim($u['firstname'] . ' ' . $u['lastname']),
                'nis'            => $u['idnumber'] ?: $u['username'],
                'password'       => $pwd,
                'kelas'          => $kelasStr,
                'sesi'           => 1,
            ]);
            $urut++;
            $inserted++;
        }

        $msg = "$inserted peserta berhasil diimpor dari Moodle.";
        if ($skipped > 0) {
            $msg .= " ($skipped peserta dilewati karena sudah terdaftar sebelumnya)";
        }

        return redirect()->back()->with('success', $msg);
    }

    public function assignRooms(int $examId)
    {
        $subjectId    = $this->request->getPost('subject_id');
        $q = (new ParticipantModel())->where('exam_id', $examId);
        if ($subjectId) {
            $q->where('subject_id', $subjectId);
        }
        $participants = $q->orderBy('nomor_peserta', 'ASC')->findAll();

        if (empty($participants)) {
            return redirect()->back()->with('error', 'Tidak ada peserta yang ditemukan untuk pembagian ruang!');
        }

        $rooms = json_decode($this->request->getPost('rooms_json'), true) ?? [];
        if (empty($rooms)) {
            return redirect()->back()->with('error', 'Belum ada ruang ujian yang dibuat. Silakan tambahkan Ruang Ujian terlebih dahulu!');
        }

        $noMeja = 1;
        $roomIdx = 0;
        $pModel = new ParticipantModel();
        $roomModel = new RoomModel();

        foreach ($participants as $p) {
            if (empty($rooms[$roomIdx])) break;
            $room = $roomModel->find($rooms[$roomIdx]);
            $pModel->update($p['id'], ['room_id' => $rooms[$roomIdx], 'nomor_meja' => $noMeja]);
            $noMeja++;
            if ($noMeja > ($room['kapasitas'] ?? 30)) {
                $noMeja = 1;
                $roomIdx++;
            }
        }

        return redirect()->back()->with('success', 'Peserta berhasil ditempatkan ke dalam ruangan dan nomor meja!');
    }

    public function assignSessions(int $examId)
    {
        $totalSesi = (int)($this->request->getPost('total_sesi') ?: 2);
        $pModel = new ParticipantModel();
        $participants = $pModel->where('exam_id', $examId)->orderBy('nomor_peserta', 'ASC')->findAll();

        if (empty($participants)) {
            return redirect()->back()->with('error', 'Data peserta kosong!');
        }

        $total = count($participants);
        $perSession = (int)ceil($total / $totalSesi);

        $currentSession = 1;
        $count = 0;
        foreach ($participants as $p) {
            if ($count >= $perSession && $currentSession < $totalSesi) {
                $currentSession++;
                $count = 0;
            }
            $pModel->update($p['id'], ['sesi' => $currentSession]);
            $count++;
        }

        return redirect()->back()->with('success', "Peserta berhasil dibagi merata ke dalam $totalSesi sesi ujian!");
    }

    public function generatePasswords(int $examId)
    {
        $pModel = new ParticipantModel();
        $updated = $pModel->generatePasswords($examId);
        return redirect()->back()->with('success', "$updated password peserta berhasil dibuat!");
    }

    public function printKartu(int $examId)
    {
        $exam = (new ExamModel())->find($examId);
        if (!$exam) {
            return redirect()->to(base_url('exam'))->with('error', 'Ujian tidak ditemukan!');
        }
        $rooms = (new RoomModel())->findAll();
        $participants = (new ParticipantModel())->where('exam_id', $examId)->orderBy('nomor_peserta', 'ASC')->findAll();
        $settings = (new SettingModel())->getAllKeyValue();

        return view('participant/print_kartu', compact('exam', 'participants', 'rooms', 'settings'));
    }

    public function printKartuMeja(int $examId)
    {
        $exam = (new ExamModel())->find($examId);
        if (!$exam) {
            return redirect()->to(base_url('exam'))->with('error', 'Ujian tidak ditemukan!');
        }
        $rooms = (new RoomModel())->findAll();
        $participants = (new ParticipantModel())->where('exam_id', $examId)->orderBy('room_id', 'ASC')->orderBy('nomor_meja', 'ASC')->findAll();
        $settings = (new SettingModel())->getAllKeyValue();

        return view('participant/print_kartu_meja', compact('exam', 'participants', 'rooms', 'settings'));
    }

    public function exportExcel(int $examId)
    {
        $exam = (new ExamModel())->find($examId);
        $rooms = (new RoomModel())->findAll();
        $roomsMap = array_column($rooms, 'nama_ruang', 'id');
        $participants = (new ParticipantModel())->where('exam_id', $examId)->orderBy('nomor_peserta', 'ASC')->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Peserta');

        $sheet->setCellValue('A1', 'DAFTAR PESERTA UJIAN: ' . strtoupper($exam['nama_ujian']));
        $sheet->setCellValue('A2', 'Tahun: ' . $exam['tahun'] . ' | Semester: ' . $exam['semester']);
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');

        $headers = ['No', 'No. Peserta', 'Nama Lengkap', 'NIS', 'NISN', 'Kelas', 'Ruang', 'No. Meja', 'Sesi', 'Password'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue([$i + 1, 4], $h);
        }

        foreach ($participants as $r => $p) {
            $row = $r + 5;
            $sheet->setCellValue([1, $row], $r + 1);
            $sheet->setCellValue([2, $row], $p['nomor_peserta']);
            $sheet->setCellValue([3, $row], $p['nama']);
            $sheet->setCellValue([4, $row], $p['nis']);
            $sheet->setCellValue([5, $row], $p['nisn']);
            $sheet->setCellValue([6, $row], $p['kelas']);
            $sheet->setCellValue([7, $row], $roomsMap[$p['room_id']] ?? '-');
            $sheet->setCellValue([8, $row], $p['nomor_meja'] ?: '-');
            $sheet->setCellValue([9, $row], $p['sesi'] ?: '1');
            $sheet->setCellValue([10, $row], $p['password'] ?: '-');
        }

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Peserta_' . preg_replace('/[^A-Za-z0-9_]/', '_', $exam['nama_ujian']) . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function importExcel(int $examId)
    {
        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File Excel tidak valid atau belum dipilih!');
        }

        $syncMoodle = $this->request->getPost('sync_moodle') == '1';
        $subjectId  = $this->request->getPost('subject_id');
        $courseId   = null;

        if ($subjectId) {
            $subject = (new ExamSubjectModel())->find($subjectId);
            $courseId = $subject['moodle_course_id'] ?? null;
            if (!$courseId && !empty($subject['moodle_quiz_id'])) {
                $quiz = (new MoodleQuizModel())->getQuizDetails((int)$subject['moodle_quiz_id']);
                $courseId = $quiz['course'] ?? null;
            }
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $pModel = new ParticipantModel();
            $moodleUserModel = new MoodleUserModel();
            $existing = $pModel->where('exam_id', $examId)->countAllResults();
            $urut = $existing + 1;
            $inserted = 0;
            $moodleCreated = 0;
            $moodleEnrolled = 0;

            $startRow = 1;
            for ($i = 0; $i < min(10, count($rows)); $i++) {
                if (isset($rows[$i][0]) && (strtolower(trim($rows[$i][0])) === 'no' || strtolower(trim($rows[$i][1] ?? '')) === 'no. peserta' || strtolower(trim($rows[$i][1] ?? '')) === 'nama')) {
                    $startRow = $i + 1;
                    break;
                }
            }

            for ($i = $startRow; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (empty($row[1]) && empty($row[2])) continue;

                $nama = trim($row[2] ?? ($row[1] ?? ''));
                if (empty($nama)) continue;

                $nis   = trim($row[3] ?? ($row[2] ?? ''));
                $nisn  = trim($row[4] ?? '');
                $kelas = trim($row[5] ?? ($row[3] ?? '10'));

                $nomor = !empty($row[1]) && str_contains($row[1], '-') ? trim($row[1]) : $pModel->generateNomorPeserta($examId, $kelas, $urut);

                $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                $pwd = !empty($row[9]) ? trim($row[9]) : substr(str_shuffle($chars), 0, 6);

                $moodleUserId = null;

                // Sync to Moodle if enabled
                if ($syncMoodle) {
                    $username = strtolower(preg_replace('/[^a-zA-Z0-9._-]/', '', $nis ?: $nomor));
                    $moodleId = $moodleUserModel->createOrUpdateStudent([
                        'username' => $username,
                        'password' => $pwd,
                        'nama'     => $nama,
                        'nis'      => $nis ?: $username,
                    ]);

                    if ($moodleId) {
                        $moodleUserId = $moodleId;
                        $moodleCreated++;

                        // Enrol to course if courseId is found
                        if ($courseId) {
                            if ($moodleUserModel->enrolStudentToCourse($moodleId, (int)$courseId)) {
                                $moodleEnrolled++;
                            }
                        }
                    }
                }

                $pModel->insert([
                    'exam_id'        => $examId,
                    'subject_id'     => $subjectId ?: null,
                    'moodle_user_id' => $moodleUserId,
                    'nomor_peserta'  => $nomor,
                    'nama'           => $nama,
                    'nis'            => $nis,
                    'nisn'           => $nisn,
                    'kelas'          => $kelas,
                    'sesi'           => !empty($row[8]) ? (int)$row[8] : 1,
                    'password'       => $pwd,
                ]);

                $urut++;
                $inserted++;
            }

            $msg = "$inserted peserta berhasil diimpor dari Excel.";
            if ($syncMoodle) {
                $msg .= " ($moodleCreated akun siswa dibuat/disinkronkan di Moodle";
                if ($courseId) {
                    $msg .= ", $moodleEnrolled siswa dienrol ke kursus)";
                } else {
                    $msg .= ")";
                }
            }

            return redirect()->back()->with('success', $msg);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
        }
    }

    /**
     * Batch sync all existing participants in this exam to Moodle accounts & enrolments
     */
    public function syncToMoodle(int $examId)
    {
        $pModel = new ParticipantModel();
        $participants = $pModel->where('exam_id', $examId)->findAll();

        if (empty($participants)) {
            return redirect()->back()->with('error', 'Data peserta kosong!');
        }

        $subjects = (new ExamSubjectModel())->where('exam_id', $examId)->findAll();
        $courseIds = [];
        foreach ($subjects as $s) {
            if (!empty($s['moodle_course_id'])) {
                $courseIds[] = (int)$s['moodle_course_id'];
            } elseif (!empty($s['moodle_quiz_id'])) {
                $quiz = (new MoodleQuizModel())->getQuizDetails((int)$s['moodle_quiz_id']);
                if (!empty($quiz['course'])) {
                    $courseIds[] = (int)$quiz['course'];
                }
            }
        }
        $courseIds = array_unique(array_filter($courseIds));

        $moodleUserModel = new MoodleUserModel();
        $synced = 0;
        $enrolled = 0;

        foreach ($participants as $p) {
            $username = strtolower(preg_replace('/[^a-zA-Z0-9._-]/', '', $p['nis'] ?: $p['nomor_peserta']));
            $pwd = $p['password'] ?: '123456';

            $moodleId = $moodleUserModel->createOrUpdateStudent([
                'username' => $username,
                'password' => $pwd,
                'nama'     => $p['nama'],
                'nis'      => $p['nis'] ?: $username,
            ]);

            if ($moodleId) {
                $pModel->update($p['id'], ['moodle_user_id' => $moodleId]);
                $synced++;

                foreach ($courseIds as $cId) {
                    if ($moodleUserModel->enrolStudentToCourse($moodleId, $cId)) {
                        $enrolled++;
                    }
                }
            }
        }

        return redirect()->back()->with('success', "Sukses! $synced akun siswa disinkronkan ke Moodle, dan $enrolled penugasan kursus berhasil didaftarkan.");
    }

    public function downloadTemplateExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Peserta');

        $headers = ['No', 'No. Peserta (Opsional)', 'Nama Siswa', 'NIS', 'NISN', 'Kelas', 'Sesi', 'Password (Opsional)'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue([$i + 1, 1], $h);
        }

        $sheet->setCellValue('A2', '1');
        $sheet->setCellValue('B2', '2026-10-0001');
        $sheet->setCellValue('C2', 'Ahmad Fauzi');
        $sheet->setCellValue('D2', '1001');
        $sheet->setCellValue('E2', '0081234567');
        $sheet->setCellValue('F2', '10 IPA 1');
        $sheet->setCellValue('G2', '1');
        $sheet->setCellValue('H2', 'CBT99');

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Template_Import_Peserta_CBT.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function delete(int $id)
    {
        $pModel = new ParticipantModel();
        $p = $pModel->find($id);
        if ($p) {
            $examId = $p['exam_id'];
            $pModel->delete($id);
            return redirect()->to(base_url("participant/{$examId}"))->with('success', 'Peserta berhasil dihapus!');
        }
        return redirect()->back()->with('error', 'Peserta tidak ditemukan!');
    }
}