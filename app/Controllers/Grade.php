<?php

namespace App\Controllers;

use App\Models\{ExamModel, ExamSubjectModel, MoodleGradeModel, SettingModel, ParticipantModel};
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Mpdf\Mpdf;

class Grade extends BaseController
{
    public function index(?int $examId = null)
    {
        if (!$examId) {
            $active = \App\Libraries\ExamContext::getActiveExam();
            if ($active) {
                return redirect()->to(base_url('grade/' . $active['id']));
            }
            return redirect()->to(base_url('exam'))->with('error', 'Silakan buat paket ujian terlebih dahulu.');
        }

        \App\Libraries\ExamContext::setActiveExam($examId);

        $exam     = (new ExamModel())->find($examId);
        $subjects = (new ExamSubjectModel())->where('exam_id', $examId)->findAll();
        return view('grade/index', compact('exam', 'subjects') + ['title' => 'Download Nilai']);
    }

    public function view(int $examId, int $subjectId)
    {
        $exam    = (new ExamModel())->find($examId);
        $subject = (new ExamSubjectModel())->find($subjectId);
        $grades  = [];
        if ($subject['moodle_quiz_id']) {
            $grades = (new MoodleGradeModel())->getUnifiedGrades((int)$subject['moodle_quiz_id'], $examId, $subjectId);
        }
        return view('grade/view', compact('exam', 'subject', 'grades') + ['title' => 'Nilai ' . $subject['nama_mapel']]);
    }

    public function exportExcel(int $examId, int $subjectId)
    {
        $exam    = (new ExamModel())->find($examId);
        $subject = (new ExamSubjectModel())->find($subjectId);
        $grades  = $subject['moodle_quiz_id'] 
            ? (new MoodleGradeModel())->getUnifiedGrades((int)$subject['moodle_quiz_id'], $examId, $subjectId) 
            : [];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Nilai');
        $sheet->setCellValue('A1', 'DAFTAR NILAI ' . strtoupper($subject['nama_mapel']));
        $sheet->setCellValue('A2', $exam['nama_ujian'] . ' - Kelas ' . $subject['kelas']);
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');

        $header = ['No', 'No. Peserta', 'Nama Siswa', 'Username / NIS', 'Nilai Akhir', 'Status Pengerjaan', 'Keterangan'];
        foreach ($header as $i => $h) {
            $sheet->setCellValue([$i + 1, 4], $h);
        }

        foreach ($grades as $r => $g) {
            $row = $r + 5;
            $ket = '-';
            $statusStr = 'Belum Ujian';
            if ($g['grade'] !== null) {
                $statusStr = 'Selesai';
                $ket = $g['grade'] >= 75 ? 'TUNTAS' : 'BELUM TUNTAS';
            } elseif (($g['state'] ?? '') === 'inprogress') {
                $statusStr = 'Sedang Mengerjakan';
            }

            $sheet->setCellValue([1, $row], $r + 1);
            $sheet->setCellValue([2, $row], $g['nomor_peserta'] ?? '-');
            $sheet->setCellValue([3, $row], trim($g['firstname'] . ' ' . $g['lastname']));
            $sheet->setCellValue([4, $row], $g['username']);
            $sheet->setCellValue([5, $row], $g['grade'] !== null ? round($g['grade'], 2) : '-');
            $sheet->setCellValue([6, $row], $statusStr);
            $sheet->setCellValue([7, $row], $ket);
        }

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Nilai_' . preg_replace('/[^A-Za-z0-9_]/', '_', $subject['nama_mapel']) . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function exportPdf(int $examId, int $subjectId)
    {
        $exam    = (new ExamModel())->find($examId);
        $subject = (new ExamSubjectModel())->find($subjectId);
        $grades  = $subject['moodle_quiz_id'] 
            ? (new MoodleGradeModel())->getUnifiedGrades((int)$subject['moodle_quiz_id'], $examId, $subjectId) 
            : [];
        $settings = (new SettingModel())->getAllKeyValue();

        $html    = view('grade/pdf_nilai', compact('exam', 'subject', 'grades', 'settings'));
        $mpdf    = new Mpdf(['margin_top' => 12, 'margin_bottom' => 12, 'margin_left' => 15, 'margin_right' => 15]);
        $mpdf->WriteHTML($html);
        $mpdf->Output("Nilai_{$subject['nama_mapel']}.pdf", 'I');
        exit;
    }

    /**
     * Matrix summary for all subjects in an exam
     */
    public function summary(int $examId)
    {
        $exam = (new ExamModel())->find($examId);
        if (!$exam) {
            return redirect()->to(base_url('exam'))->with('error', 'Ujian tidak ditemukan!');
        }

        $subjects = (new ExamSubjectModel())->where('exam_id', $examId)->findAll();
        $pModel   = new ParticipantModel();
        
        $allParticipants = $pModel->where('exam_id', $examId)
            ->orderBy('kelas', 'ASC')
            ->orderBy('nomor_peserta', 'ASC')
            ->findAll();

        $uniqueParticipants = [];
        foreach ($allParticipants as $p) {
            $key = $p['nomor_peserta'] ?: ($p['nis'] ?: $p['id']);
            if (!isset($uniqueParticipants[$key])) {
                $uniqueParticipants[$key] = $p;
            }
        }
        $participants = array_values($uniqueParticipants);

        $gradeMap = [];
        $mGradeModel = new MoodleGradeModel();

        foreach ($subjects as $s) {
            if ($s['moodle_quiz_id']) {
                $grades = $mGradeModel->getUnifiedGrades((int)$s['moodle_quiz_id'], $examId, $s['id']);
                foreach ($grades as $g) {
                    $pid = $g['participant_id'] ?? null;
                    $un  = strtolower(trim($g['username'] ?? ''));
                    if ($pid) {
                        $gradeMap['p_' . $pid][$s['id']] = $g['grade'];
                    }
                    if ($un !== '') {
                        $gradeMap['u_' . $un][$s['id']] = $g['grade'];
                    }
                }
            }
        }

        $rows = [];
        foreach ($participants as $p) {
            $pidKey = 'p_' . $p['id'];
            $unKey  = 'u_' . strtolower(trim($p['nis'] ?: $p['nomor_peserta']));

            $pGrades = [];
            $total = 0;
            $countGraded = 0;

            foreach ($subjects as $s) {
                $g = $gradeMap[$pidKey][$s['id']] ?? ($gradeMap[$unKey][$s['id']] ?? null);
                $pGrades[$s['id']] = $g;
                if ($g !== null) {
                    $total += (float)$g;
                    $countGraded++;
                }
            }

            $avg = $countGraded > 0 ? round($total / $countGraded, 2) : null;

            $rows[] = [
                'participant' => $p,
                'grades'      => $pGrades,
                'total'       => $countGraded > 0 ? round($total, 2) : '-',
                'average'     => $avg !== null ? $avg : '-',
                'count'       => $countGraded,
            ];
        }

        return view('grade/summary', [
            'title'        => 'Rekap Nilai Gabungan: ' . $exam['nama_ujian'],
            'exam'         => $exam,
            'subjects'     => $subjects,
            'rows'         => $rows,
            'participants' => $participants,
        ]);
    }

    /**
     * Export all subjects recap matrix to Excel
     */
    public function exportSummaryExcel(int $examId)
    {
        $exam = (new ExamModel())->find($examId);
        if (!$exam) {
            return redirect()->to(base_url('exam'))->with('error', 'Ujian tidak ditemukan!');
        }

        $subjects = (new ExamSubjectModel())->where('exam_id', $examId)->findAll();
        $pModel   = new ParticipantModel();
        
        $allParticipants = $pModel->where('exam_id', $examId)
            ->orderBy('kelas', 'ASC')
            ->orderBy('nomor_peserta', 'ASC')
            ->findAll();

        $uniqueParticipants = [];
        foreach ($allParticipants as $p) {
            $key = $p['nomor_peserta'] ?: ($p['nis'] ?: $p['id']);
            if (!isset($uniqueParticipants[$key])) {
                $uniqueParticipants[$key] = $p;
            }
        }
        $participants = array_values($uniqueParticipants);

        $gradeMap = [];
        $mGradeModel = new MoodleGradeModel();

        foreach ($subjects as $s) {
            if ($s['moodle_quiz_id']) {
                $grades = $mGradeModel->getUnifiedGrades((int)$s['moodle_quiz_id'], $examId, $s['id']);
                foreach ($grades as $g) {
                    $pid = $g['participant_id'] ?? null;
                    $un  = strtolower(trim($g['username'] ?? ''));
                    if ($pid) {
                        $gradeMap['p_' . $pid][$s['id']] = $g['grade'];
                    }
                    if ($un !== '') {
                        $gradeMap['u_' . $un][$s['id']] = $g['grade'];
                    }
                }
            }
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Nilai');

        $sheet->setCellValue('A1', 'REKAPITULASI NILAI GABUNGAN PESERTA UJIAN');
        $sheet->setCellValue('A2', strtoupper($exam['nama_ujian']) . ' - TAHUN ' . $exam['tahun'] . ' (SEMESTER ' . $exam['semester'] . ')');

        $headers = ['No', 'No. Peserta', 'NIS', 'Nama Siswa', 'Kelas', 'Sesi'];
        foreach ($subjects as $s) {
            $headers[] = $s['nama_mapel'];
        }
        $headers[] = 'Total Nilai';
        $headers[] = 'Rata-Rata';

        $totalCols = count($headers);
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalCols);

        $sheet->mergeCells("A1:{$lastColLetter}1");
        $sheet->mergeCells("A2:{$lastColLetter}2");

        foreach ($headers as $i => $h) {
            $sheet->setCellValue([$i + 1, 4], $h);
        }

        foreach ($participants as $r => $p) {
            $rowNum = $r + 5;
            $pidKey = 'p_' . $p['id'];
            $unKey  = 'u_' . strtolower(trim($p['nis'] ?: $p['nomor_peserta']));

            $colIdx = 1;
            $sheet->setCellValue([$colIdx++, $rowNum], $r + 1);
            $sheet->setCellValue([$colIdx++, $rowNum], $p['nomor_peserta']);
            $sheet->setCellValue([$colIdx++, $rowNum], $p['nis'] ?: '-');
            $sheet->setCellValue([$colIdx++, $rowNum], $p['nama']);
            $sheet->setCellValue([$colIdx++, $rowNum], $p['kelas']);
            $sheet->setCellValue([$colIdx++, $rowNum], $p['sesi'] ?? 1);

            $total = 0;
            $countGraded = 0;

            foreach ($subjects as $s) {
                $g = $gradeMap[$pidKey][$s['id']] ?? ($gradeMap[$unKey][$s['id']] ?? null);
                if ($g !== null) {
                    $sheet->setCellValue([$colIdx++, $rowNum], round((float)$g, 2));
                    $total += (float)$g;
                    $countGraded++;
                } else {
                    $sheet->setCellValue([$colIdx++, $rowNum], '-');
                }
            }

            $sheet->setCellValue([$colIdx++, $rowNum], $countGraded > 0 ? round($total, 2) : '-');
            $sheet->setCellValue([$colIdx++, $rowNum], $countGraded > 0 ? round($total / $countGraded, 2) : '-');
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Rekap_Nilai_' . preg_replace('/[^A-Za-z0-9_]/', '_', $exam['nama_ujian']) . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}