<?php

namespace App\Controllers;

use App\Models\{ExamModel, ExamSubjectModel, ParticipantModel, RoomModel, MoodleUserModel, MoodleQuizModel, SettingModel};
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Mpdf\Mpdf;

class Participant extends BaseController
{
    public function index(?int $examId = null)
    {
        if (!$examId) {
            $active = \App\Libraries\ExamContext::getActiveExam();
            if ($active) {
                return redirect()->to(base_url('participant/' . $active['id']));
            }
            return redirect()->to(base_url('exam'))->with('error', 'Silakan buat paket ujian terlebih dahulu.');
        }

        \App\Libraries\ExamContext::setActiveExam($examId);

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
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');
        @ini_set('memory_limit', '512M');

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

        $existingParticipants = $pModel->where('exam_id', $examId)->findAll();
        $existingByNomor = [];
        foreach ($existingParticipants as $ep) {
            if (!empty($ep['nomor_peserta'])) {
                $existingByNomor[trim((string)$ep['nomor_peserta'])] = true;
            }
        }

        foreach ($moodleUsers as $u) {
            $exists = $pModel->where('exam_id', $examId)
                ->where('subject_id', $subjectId)
                ->where('moodle_user_id', $u['id'])
                ->first();

            if ($exists) {
                $skipped++;
                continue;
            }

            do {
                $nomor = $pModel->generateNomorPeserta($examId, $kelasStr, $urut);
                $urut++;
            } while (isset($existingByNomor[$nomor]));
            $existingByNomor[$nomor] = true;

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

        $pModel   = new ParticipantModel();
        $rooms    = (new RoomModel())->findAll();
        $settings = (new SettingModel())->getAllKeyValue();

        // Ambil daftar kelas unik untuk dropdown filter
        $kelasRows = $pModel->builder()
            ->select('kelas')
            ->distinct()
            ->where('exam_id', $examId)
            ->where('kelas IS NOT NULL')
            ->where('kelas !=', '')
            ->orderBy('kelas', 'ASC')
            ->get()->getResultArray();
        $allKelas = array_values(array_filter(array_column($kelasRows, 'kelas')));

        $filterKelas = trim((string)$this->request->getGet('kelas'));
        $filterRoom  = $this->request->getGet('room_id');
        $filterSesi  = $this->request->getGet('sesi');
        $sortBy      = $this->request->getGet('sort') ?: 'kelas_nama';
        $pageBreak   = $this->request->getGet('break') == '1';

        $q = $pModel->where('exam_id', $examId);
        if (!empty($filterKelas)) {
            $q->where('kelas', $filterKelas);
        }
        if (!empty($filterRoom)) {
            $q->where('room_id', (int)$filterRoom);
        }
        if (!empty($filterSesi)) {
            $q->where('sesi', (int)$filterSesi);
        }

        switch ($sortBy) {
            case 'kelas_nama':
                $q->orderBy('kelas', 'ASC')->orderBy('nama', 'ASC');
                break;
            case 'kelas_meja':
                $q->orderBy('kelas', 'ASC')->orderBy('nomor_meja', 'ASC');
                break;
            case 'kelas_nomor':
                $q->orderBy('kelas', 'ASC')->orderBy('nomor_peserta', 'ASC');
                break;
            case 'room_meja':
                $q->orderBy('room_id', 'ASC')->orderBy('nomor_meja', 'ASC')->orderBy('sesi', 'ASC');
                break;
            case 'nama':
                $q->orderBy('nama', 'ASC');
                break;
            case 'nomor_peserta':
            default:
                $q->orderBy('nomor_peserta', 'ASC');
                break;
        }

        $participants = $q->findAll();

        return view('participant/print_kartu', compact(
            'exam', 'participants', 'rooms', 'settings', 'allKelas',
            'filterKelas', 'filterRoom', 'filterSesi', 'sortBy', 'pageBreak'
        ));
    }

    public function printKartuMeja(int $examId)
    {
        $exam = (new ExamModel())->find($examId);
        if (!$exam) {
            return redirect()->to(base_url('exam'))->with('error', 'Ujian tidak ditemukan!');
        }

        $pModel   = new ParticipantModel();
        $rooms    = (new RoomModel())->findAll();
        $settings = (new SettingModel())->getAllKeyValue();

        // Ambil daftar kelas unik untuk dropdown filter
        $kelasRows = $pModel->builder()
            ->select('kelas')
            ->distinct()
            ->where('exam_id', $examId)
            ->where('kelas IS NOT NULL')
            ->where('kelas !=', '')
            ->orderBy('kelas', 'ASC')
            ->get()->getResultArray();
        $allKelas = array_values(array_filter(array_column($kelasRows, 'kelas')));

        $filterKelas = trim((string)$this->request->getGet('kelas'));
        $filterRoom  = $this->request->getGet('room_id');
        $filterSesi  = $this->request->getGet('sesi');
        $sortBy      = $this->request->getGet('sort') ?: 'kelas_meja';
        $pageBreak   = $this->request->getGet('break') == '1';

        $q = $pModel->where('exam_id', $examId);
        if (!empty($filterKelas)) {
            $q->where('kelas', $filterKelas);
        }
        if (!empty($filterRoom)) {
            $q->where('room_id', (int)$filterRoom);
        }
        if (!empty($filterSesi)) {
            $q->where('sesi', (int)$filterSesi);
        }

        switch ($sortBy) {
            case 'kelas_meja':
                $q->orderBy('kelas', 'ASC')->orderBy('nomor_meja', 'ASC')->orderBy('sesi', 'ASC');
                break;
            case 'kelas_nama':
                $q->orderBy('kelas', 'ASC')->orderBy('nama', 'ASC');
                break;
            case 'kelas_nomor':
                $q->orderBy('kelas', 'ASC')->orderBy('nomor_peserta', 'ASC');
                break;
            case 'room_meja':
                $q->orderBy('room_id', 'ASC')->orderBy('nomor_meja', 'ASC')->orderBy('sesi', 'ASC');
                break;
            case 'nomor_peserta':
                $q->orderBy('nomor_peserta', 'ASC');
                break;
            case 'nama':
                $q->orderBy('nama', 'ASC');
                break;
            default:
                $q->orderBy('kelas', 'ASC')->orderBy('nomor_meja', 'ASC');
                break;
        }

        $participants = $q->findAll();

        return view('participant/print_kartu_meja', compact(
            'exam', 'participants', 'rooms', 'settings', 'allKelas',
            'filterKelas', 'filterRoom', 'filterSesi', 'sortBy', 'pageBreak'
        ));
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
        // Hilangkan batasan waktu eksekusi & naikkan alokasi memori untuk batch besar (1.000+ siswa)
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');
        @ini_set('memory_limit', '512M');

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

            $pModel          = new ParticipantModel();
            $roomModel       = new RoomModel();
            $moodleUserModel = new MoodleUserModel();

            // Cache ruangan yang ada untuk lookup map
            $allRooms = $roomModel->findAll();
            $roomMap  = [];
            foreach ($allRooms as $r) {
                $roomMap[strtolower(trim($r['nama_ruang']))] = (int)$r['id'];
                $roomMap[(string)$r['id']] = (int)$r['id'];
            }

            // Default column index mapping (0-based)
            $colMap = [
                'no'         => 0,
                'no_peserta' => 1,
                'nama'       => 2,
                'nis'        => 3,
                'nisn'       => 4,
                'kelas'      => 5,
                'ruang'      => 6,
                'meja'       => 7,
                'sesi'       => 8,
                'password'   => 9,
            ];

            $startRow = 1;
            // Deteksi baris header dan mapping kolom otomatis
            for ($i = 0; $i < min(10, count($rows)); $i++) {
                $r = $rows[$i];
                if (!is_array($r)) continue;
                $rowStr = strtolower(implode(' ', array_filter(array_map('strval', $r))));
                if (str_contains($rowStr, 'nama') || str_contains($rowStr, 'peserta') || str_contains($rowStr, 'nis')) {
                    $startRow = $i + 1;

                    foreach ($r as $cIdx => $cVal) {
                        $cLower = strtolower(trim((string)$cVal));
                        if (empty($cLower)) continue;

                        if (str_contains($cLower, 'peserta')) {
                            $colMap['no_peserta'] = $cIdx;
                        } elseif (str_contains($cLower, 'nama')) {
                            $colMap['nama'] = $cIdx;
                        } elseif (str_contains($cLower, 'nisn')) {
                            $colMap['nisn'] = $cIdx;
                        } elseif ($cLower === 'nis' || str_starts_with($cLower, 'nis ')) {
                            $colMap['nis'] = $cIdx;
                        } elseif (str_contains($cLower, 'kelas') || str_contains($cLower, 'rombel')) {
                            $colMap['kelas'] = $cIdx;
                        } elseif (str_contains($cLower, 'ruang') || str_contains($cLower, 'room')) {
                            $colMap['ruang'] = $cIdx;
                        } elseif (str_contains($cLower, 'meja') || str_contains($cLower, 'seat') || str_contains($cLower, 'kursi')) {
                            $colMap['meja'] = $cIdx;
                        } elseif (str_contains($cLower, 'sesi') || str_contains($cLower, 'session')) {
                            $colMap['sesi'] = $cIdx;
                        } elseif (str_contains($cLower, 'password') || str_contains($cLower, 'sandi') || str_contains($cLower, 'pass')) {
                            $colMap['password'] = $cIdx;
                        }
                    }
                    break;
                }
            }

            // Preload data peserta yang sudah ada di paket ujian ini untuk cegah duplikasi & collision nomor_peserta
            $existingParticipants = $pModel->where('exam_id', $examId)->findAll();
            $existingByNis = [];
            $existingByNomor = [];
            foreach ($existingParticipants as $ep) {
                if (!empty($ep['nis'])) {
                    $existingByNis[trim((string)$ep['nis'])] = $ep;
                }
                if (!empty($ep['nomor_peserta'])) {
                    $existingByNomor[trim((string)$ep['nomor_peserta'])] = $ep;
                }
            }

            $urut = count($existingParticipants) + 1;
            $inserted = 0;
            $updated = 0;
            $skipped = 0;
            $moodleCreated = 0;
            $moodleEnrolled = 0;
            $cohortsSynced = 0;
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

            for ($i = $startRow; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (empty($row)) continue;

                $nama = trim((string)($row[$colMap['nama']] ?? ''));
                if (empty($nama)) continue;

                try {
                    $noPesertaRaw = trim((string)($row[$colMap['no_peserta']] ?? ''));
                    $nis          = trim((string)($row[$colMap['nis']] ?? ''));
                    $nisn         = trim((string)($row[$colMap['nisn']] ?? ''));
                    $kelas        = trim((string)($row[$colMap['kelas']] ?? '10'));
                    $ruangRaw     = trim((string)($row[$colMap['ruang']] ?? ''));
                    $mejaRaw      = trim((string)($row[$colMap['meja']] ?? ''));
                    $sesiRaw      = trim((string)($row[$colMap['sesi']] ?? ''));
                    $pwdRaw       = trim((string)($row[$colMap['password']] ?? ''));

                    // Deteksi apakah siswa ini sudah pernah ada berdasarkan NIS
                    $existingRecord = (!empty($nis) && isset($existingByNis[$nis])) ? $existingByNis[$nis] : null;

                    // Tentukan Nomor Peserta (pastikan unik dan tidak menabrak peserta lain)
                    if (!empty($noPesertaRaw) && str_contains($noPesertaRaw, '-')) {
                        $nomor = $noPesertaRaw;
                    } elseif ($existingRecord && !empty($existingRecord['nomor_peserta'])) {
                        $nomor = $existingRecord['nomor_peserta'];
                    } else {
                        do {
                            $nomor = $pModel->generateNomorPeserta($examId, $kelas, $urut);
                            $urut++;
                        } while (isset($existingByNomor[$nomor]));
                    }

                    // Tentukan Password CBT
                    if (!empty($pwdRaw)) {
                        $pwd = $pwdRaw;
                    } elseif ($existingRecord && !empty($existingRecord['password'])) {
                        $pwd = $existingRecord['password'];
                    } else {
                        $pwd = substr(str_shuffle($chars), 0, 6);
                    }

                    // Tentukan Sesi
                    $sesiClean = preg_replace('/[^0-9]/', '', $sesiRaw);
                    $sesi = !empty($sesiClean) ? (int)$sesiClean : ($existingRecord['sesi'] ?? 1);

                    // Tentukan Nomor Meja
                    $mejaClean = preg_replace('/[^0-9]/', '', $mejaRaw);
                    $nomorMeja = !empty($mejaClean) ? (int)$mejaClean : ($existingRecord['nomor_meja'] ?? null);

                    // Tentukan Ruangan
                    $roomId = $existingRecord['room_id'] ?? null;
                    if (!empty($ruangRaw)) {
                        $ruangKey = strtolower($ruangRaw);
                        if (isset($roomMap[$ruangKey])) {
                            $roomId = $roomMap[$ruangKey];
                        } else {
                            // Jika ruangan belum terdaftar, buat otomatis di tabel rooms
                            $newRoomId = $roomModel->insert([
                                'nama_ruang' => $ruangRaw,
                                'kapasitas'  => 36,
                                'keterangan' => 'Dibuat otomatis dari Import Excel',
                            ]);
                            if ($newRoomId) {
                                $roomId = (int)$newRoomId;
                                $roomMap[$ruangKey] = $roomId;
                            }
                        }
                    }

                    $moodleUserId = $existingRecord['moodle_user_id'] ?? null;

                    // Sync to Moodle & Kohor Otomatis
                    if ($syncMoodle) {
                        $username = strtolower(preg_replace('/[^a-zA-Z0-9._-]/', '', $nis ?: $nomor));
                        $moodleId = $moodleUserModel->createOrUpdateStudent([
                            'username' => $username,
                            'password' => $pwd,
                            'nama'     => $nama,
                            'nis'      => $nis ?: $username,
                            'kelas'    => $kelas,
                        ]);

                        if ($moodleId) {
                            $moodleUserId = $moodleId;
                            $moodleCreated++;

                            // Buat kohor otomatis dari nama kelas dan masukkan siswa
                            if (!empty($kelas)) {
                                $cohortId = $moodleUserModel->syncStudentToClassCohort($moodleId, $kelas);
                                if ($cohortId) {
                                    $cohortsSynced++;
                                }
                            }

                            // Enrol to course if courseId is found
                            if ($courseId) {
                                if ($moodleUserModel->enrolStudentToCourse($moodleId, (int)$courseId)) {
                                    $moodleEnrolled++;
                                }
                            }
                        }
                    }

                    $saveData = [
                        'exam_id'        => $examId,
                        'subject_id'     => $subjectId ?: null,
                        'room_id'        => $roomId,
                        'nomor_meja'     => $nomorMeja,
                        'sesi'           => $sesi,
                        'moodle_user_id' => $moodleUserId,
                        'nomor_peserta'  => $nomor,
                        'nama'           => $nama,
                        'nis'            => $nis,
                        'nisn'           => $nisn,
                        'kelas'          => $kelas,
                        'password'       => $pwd,
                    ];

                    if ($existingRecord) {
                        $pModel->update($existingRecord['id'], $saveData);
                        $saveData['id'] = $existingRecord['id'];
                        $updated++;
                    } else {
                        $newId = $pModel->insert($saveData);
                        $saveData['id'] = $newId;
                        $inserted++;
                    }

                    // Daftarkan ke in-memory cache
                    if (!empty($nis)) {
                        $existingByNis[$nis] = $saveData;
                    }
                    $existingByNomor[$nomor] = $saveData;

                } catch (\Throwable $rowErr) {
                    log_message('error', "Import row $i failed: " . $rowErr->getMessage());
                    $skipped++;
                }
            }

            $msg = "$inserted peserta baru berhasil ditambahkan";
            if ($updated > 0) {
                $msg .= ", $updated peserta diperbarui";
            }
            $msg .= " dari Excel.";
            if ($skipped > 0) {
                $msg .= " ($skipped baris dilewati karena format tidak sesuai)";
            }
            if ($syncMoodle) {
                $msg .= " ($moodleCreated akun siswa & kohor kelas disinkronkan di Moodle";
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
        // Hilangkan batas waktu eksekusi untuk batch besar
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');
        @ini_set('memory_limit', '512M');

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
        $cohortsSynced = 0;

        foreach ($participants as $p) {
            try {
                $username = strtolower(preg_replace('/[^a-zA-Z0-9._-]/', '', $p['nis'] ?: $p['nomor_peserta']));
                $pwd = $p['password'] ?: '123456';

                $moodleId = $moodleUserModel->createOrUpdateStudent([
                    'username' => $username,
                    'password' => $pwd,
                    'nama'     => $p['nama'],
                    'nis'      => $p['nis'] ?: $username,
                    'kelas'    => $p['kelas'] ?? '',
                ]);

                if ($moodleId) {
                    $pModel->update($p['id'], ['moodle_user_id' => $moodleId]);
                    $synced++;

                    // Sinkronkan ke kohor kelas di Moodle secara otomatis
                    if (!empty($p['kelas'])) {
                        if ($moodleUserModel->syncStudentToClassCohort($moodleId, $p['kelas'])) {
                            $cohortsSynced++;
                        }
                    }

                    foreach ($courseIds as $cId) {
                        if ($moodleUserModel->enrolStudentToCourse($moodleId, $cId)) {
                            $enrolled++;
                        }
                    }
                }
            } catch (\Throwable $rowErr) {
                log_message('error', 'Participant::syncToMoodle row error: ' . $rowErr->getMessage());
            }
        }

        return redirect()->back()->with('success', "Sukses! $synced akun siswa & kohor kelas berhasil disinkronkan ke Moodle, dan $enrolled penugasan kursus terdaftar.");
    }

    public function downloadTemplateExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Peserta');

        $headers = [
            'No',
            'No. Peserta (Opsional)',
            'Nama Lengkap',
            'NIS',
            'NISN',
            'Kelas',
            'Ruang',
            'No. Meja',
            'Sesi',
            'Password (Opsional)'
        ];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue([$i + 1, 1], $h);
        }

        // Baris Contoh 1
        $sheet->setCellValue('A2', 1);
        $sheet->setCellValue('B2', '2026-10-0001');
        $sheet->setCellValue('C2', 'Ahmad Fauzi');
        $sheet->setCellValueExplicit('D2', '7001', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('E2', '0081234567', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('F2', 'X TKJ 1');
        $sheet->setCellValue('G2', '10 TKJ 1');
        $sheet->setCellValue('H2', 1);
        $sheet->setCellValue('I2', 1);
        $sheet->setCellValue('J2', 'P2BDRE');

        // Baris Contoh 2
        $sheet->setCellValue('A3', 2);
        $sheet->setCellValue('B3', '2026-10-0002');
        $sheet->setCellValue('C3', 'Adinda Ainur Rohmasari');
        $sheet->setCellValueExplicit('D3', '7002', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('E3', '0081234568', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('F3', 'X TKJ 1');
        $sheet->setCellValue('G3', '10 TKJ 1');
        $sheet->setCellValue('H3', 2);
        $sheet->setCellValue('I3', 1);
        $sheet->setCellValue('J3', 'ZM2SAD');

        // Baris Contoh 3
        $sheet->setCellValue('A4', 3);
        $sheet->setCellValue('B4', '2026-10-0003');
        $sheet->setCellValue('C4', 'Budi Santoso');
        $sheet->setCellValueExplicit('D4', '7003', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('E4', '0081234569', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('F4', 'X TKJ 2');
        $sheet->setCellValue('G4', '10 TKJ 2');
        $sheet->setCellValue('H4', 1);
        $sheet->setCellValue('I4', 1);
        $sheet->setCellValue('J4', 'K248MC');

        // Style Header
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

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

    /**
     * Update individual participant details
     */
    public function update(int $id)
    {
        $pModel = new ParticipantModel();
        $p = $pModel->find($id);
        if (!$p) {
            return redirect()->back()->with('error', 'Data peserta tidak ditemukan!');
        }

        $examId       = (int)$p['exam_id'];
        $nama         = trim((string)$this->request->getPost('nama'));
        $nomorPeserta = trim((string)$this->request->getPost('nomor_peserta'));
        $nis          = trim((string)$this->request->getPost('nis'));
        $nisn         = trim((string)$this->request->getPost('nisn'));
        $kelas        = trim((string)$this->request->getPost('kelas'));
        $roomId       = $this->request->getPost('room_id') ? (int)$this->request->getPost('room_id') : null;
        $nomorMeja    = $this->request->getPost('nomor_meja') ? (int)$this->request->getPost('nomor_meja') : null;
        $sesi         = (int)($this->request->getPost('sesi') ?: 1);
        $password     = trim((string)$this->request->getPost('password'));
        $syncMoodle   = $this->request->getPost('sync_moodle') == '1';

        if (empty($nama) || empty($nomorPeserta)) {
            return redirect()->back()->with('error', 'Nama lengkap dan Nomor Peserta wajib diisi.');
        }

        $pModel->update($id, [
            'nama'          => $nama,
            'nomor_peserta' => $nomorPeserta,
            'nis'           => $nis,
            'nisn'          => $nisn,
            'kelas'         => $kelas,
            'room_id'       => $roomId,
            'nomor_meja'    => $nomorMeja,
            'sesi'          => $sesi,
            'password'      => $password,
        ]);

        // Jika terhubung ke Moodle dan opsi sync dipilih, update juga akun Moodle-nya
        if ($syncMoodle && !empty($p['moodle_user_id'])) {
            try {
                $username = strtolower(preg_replace('/[^a-zA-Z0-9._-]/', '', $nis ?: $nomorPeserta));
                $moodleUserModel = new MoodleUserModel();
                $moodleUserModel->createOrUpdateStudent([
                    'username' => $username,
                    'password' => $password ?: '123456',
                    'nama'     => $nama,
                    'nis'      => $nis ?: $username,
                    'kelas'    => $kelas,
                ]);

                // Update kohor kelas Moodle
                if (!empty($kelas)) {
                    $moodleUserModel->syncStudentToClassCohort((int)$p['moodle_user_id'], $kelas);
                }
            } catch (\Throwable $e) {
                log_message('error', 'Participant::update sync to Moodle failed: ' . $e->getMessage());
            }
        }

        return redirect()->to(base_url("participant/{$examId}"))
            ->with('success', "Data peserta '{$nama}' berhasil diperbarui!");
    }

    /**
     * Reset / Hapus seluruh data peserta ujian ini
     */
    public function resetAll(int $examId)
    {
        $pModel = new ParticipantModel();
        $total = $pModel->where('exam_id', $examId)->countAllResults();

        if ($total === 0) {
            return redirect()->back()->with('error', 'Tidak ada data peserta yang perlu direset.');
        }

        $pModel->where('exam_id', $examId)->delete();

        return redirect()->to(base_url("participant/{$examId}"))
            ->with('success', "Seluruh data peserta ({$total} siswa) pada ujian ini berhasil direset/dikosongkan.");
    }

    /**
     * Reset / Kosongkan pembagian ruang dan nomor meja peserta
     */
    public function resetAllocation(int $examId)
    {
        $pModel = new ParticipantModel();
        $pModel->where('exam_id', $examId)->set([
            'room_id'    => null,
            'nomor_meja' => null,
            'sesi'       => 1,
        ])->update();

        return redirect()->to(base_url("participant/{$examId}"))
            ->with('success', 'Penempatan ruang, sesi, dan nomor meja seluruh peserta berhasil direset!');
    }

    /**
     * Reset / Generate ulang seluruh password peserta ujian
     */
    public function resetPasswords(int $examId)
    {
        $pModel = new ParticipantModel();
        $participants = $pModel->where('exam_id', $examId)->findAll();

        if (empty($participants)) {
            return redirect()->back()->with('error', 'Data peserta kosong!');
        }

        $moodleUserModel = new MoodleUserModel();
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $moodleUpdated = 0;

        foreach ($participants as $p) {
            $pwd = substr(str_shuffle($chars), 0, 6);
            $pModel->update($p['id'], ['password' => $pwd]);

            // Update password di Moodle langsung jika siswa sudah terhubung ke Moodle
            if (!empty($p['moodle_user_id'])) {
                if ($moodleUserModel->updatePassword((int)$p['moodle_user_id'], $pwd)) {
                    $moodleUpdated++;
                }
            }
        }

        $msg = 'Password CBT seluruh peserta (' . count($participants) . ' siswa) berhasil di-generate ulang!';
        if ($moodleUpdated > 0) {
            $msg .= " ($moodleUpdated akun di Moodle otomatis diperbarui password-nya).";
        }

        return redirect()->to(base_url("participant/{$examId}"))->with('success', $msg);
    }
}