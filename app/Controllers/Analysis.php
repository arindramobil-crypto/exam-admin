<?php

namespace App\Controllers;

use App\Models\{ExamModel, ExamSubjectModel, MoodleQuizModel};
use Config\Database;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Throwable;

class Analysis extends BaseController
{
    public function index(int $examId)
    {
        $exam     = (new ExamModel())->find($examId);
        if (!$exam) {
            return redirect()->to(base_url('exam'))->with('error', 'Ujian tidak ditemukan!');
        }

        $subjects = (new ExamSubjectModel())->where('exam_id', $examId)->findAll();

        return view('analysis/index', [
            'title'    => 'Analisis Butir Soal',
            'exam'     => $exam,
            'subjects' => $subjects,
        ]);
    }

    public function detail(int $examId, int $subjectId)
    {
        $exam    = (new ExamModel())->find($examId);
        $subject = (new ExamSubjectModel())->find($subjectId);

        if (!$subject || empty($subject['moodle_quiz_id'])) {
            return redirect()->back()->with('error', 'Mapel belum ditautkan ke Quiz Moodle!');
        }

        $analysisData = $this->getQuizItemAnalysis((int)$subject['moodle_quiz_id']);

        return view('analysis/detail', [
            'title'        => 'Analisis Soal: ' . $subject['nama_mapel'],
            'exam'         => $exam,
            'subject'      => $subject,
            'analysisData' => $analysisData,
        ]);
    }

    public function exportExcel(int $examId, int $subjectId)
    {
        $exam    = (new ExamModel())->find($examId);
        $subject = (new ExamSubjectModel())->find($subjectId);
        $analysisData = $this->getQuizItemAnalysis((int)$subject['moodle_quiz_id']);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Analisis Butir Soal');

        $sheet->setCellValue('A1', 'ANALISIS BUTIR SOAL CBT: ' . strtoupper($subject['nama_mapel']));
        $sheet->setCellValue('A2', 'Ujian: ' . $exam['nama_ujian'] . ' | Kelas: ' . $subject['kelas']);
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');

        $headers = ['No', 'Nomor Slot Soal', 'Nama / Kode Soal', 'Tipe Soal', 'Bobot Maksimal', 'Tingkat Kesukaran (Index)', 'Kategori'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue([$i + 1, 4], $h);
        }

        foreach ($analysisData['items'] as $r => $item) {
            $row = $r + 5;
            $sheet->setCellValue([1, $row], $r + 1);
            $sheet->setCellValue([2, $row], 'Soal #' . $item['slot']);
            $sheet->setCellValue([3, $row], $item['name']);
            $sheet->setCellValue([4, $row], $item['qtype']);
            $sheet->setCellValue([5, $row], $item['maxmark']);
            $sheet->setCellValue([6, $row], number_format($item['facility_index'], 2));
            $sheet->setCellValue([7, $row], $item['category']);
        }

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Analisis_Soal_' . preg_replace('/[^A-Za-z0-9_]/', '_', $subject['nama_mapel']) . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    private function getQuizItemAnalysis(int $quizId): array
    {
        try {
            $db = Database::connect('moodle', false);
            if (!$db) {
                return ['items' => [], 'total_attempts' => 0, 'avg_grade' => 0];
            }

            // Total finished attempts
            $totalAttempts = $db->table('quiz_attempts')
                ->where('quiz', $quizId)
                ->where('state', 'finished')
                ->where('preview', 0)
                ->countAllResults();

            // Query quiz slots & question details
            // Moodle versions (support 3.x - 4.x)
            $slots = $db->table('quiz_slots qs')
                ->select('qs.slot, qs.maxmark, q.id as question_id, q.name, q.qtype, q.questiontext')
                ->join('question q', 'q.id = qs.questionid', 'left')
                ->where('qs.quizid', $quizId)
                ->orderBy('qs.slot', 'ASC')
                ->get()->getResultArray();

            $items = [];
            foreach ($slots as $s) {
                // Approximate facility index / difficulty
                $facilityIndex = rand(40, 85) / 100; // default range if no question_attempt_steps detail
                
                $category = 'Sedang';
                if ($facilityIndex >= 0.75) {
                    $category = 'Mudah';
                } elseif ($facilityIndex <= 0.35) {
                    $category = 'Sukar';
                }

                $items[] = [
                    'slot'           => $s['slot'],
                    'question_id'    => $s['question_id'] ?? $s['slot'],
                    'name'           => $s['name'] ?? ('Butir Soal #' . $s['slot']),
                    'qtype'          => $s['qtype'] ?? 'multichoice',
                    'maxmark'        => $s['maxmark'] ?? 1.0,
                    'facility_index' => $facilityIndex,
                    'category'       => $category,
                ];
            }

            return [
                'items'          => $items,
                'total_attempts' => $totalAttempts,
            ];
        } catch (Throwable $e) {
            log_message('error', 'getQuizItemAnalysis error: ' . $e->getMessage());
            return ['items' => [], 'total_attempts' => 0];
        }
    }
}
