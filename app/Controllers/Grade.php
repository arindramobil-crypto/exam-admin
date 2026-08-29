<?php

namespace App\Controllers;

use App\Models\{ExamModel, ExamSubjectModel, MoodleGradeModel, SettingModel};
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Mpdf\Mpdf;

class Grade extends BaseController
{
    public function index(int $examId)
    {
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
            $grades = (new MoodleGradeModel())->getGradesByQuiz($subject['moodle_quiz_id']);
        }
        return view('grade/view', compact('exam', 'subject', 'grades') + ['title' => 'Nilai ' . $subject['nama_mapel']]);
    }

    public function exportExcel(int $examId, int $subjectId)
    {
        $exam    = (new ExamModel())->find($examId);
        $subject = (new ExamSubjectModel())->find($subjectId);
        $grades  = $subject['moodle_quiz_id'] ? (new MoodleGradeModel())->getGradesByQuiz($subject['moodle_quiz_id']) : [];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Nilai');
        $sheet->setCellValue('A1', 'DAFTAR NILAI ' . strtoupper($subject['nama_mapel']));
        $sheet->setCellValue('A2', $exam['nama_ujian'] . ' - Kelas ' . $subject['kelas']);
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');

        $header = ['No', 'Nama', 'Username', 'NIS/NISN', 'Nilai', 'Keterangan'];
        foreach ($header as $i => $h) {
            $sheet->setCellValue([$i + 1, 4], $h);
        }

        foreach ($grades as $r => $g) {
            $row = $r + 5;
            $sheet->setCellValue([1, $row], $r + 1);
            $sheet->setCellValue([2, $row], $g['firstname'] . ' ' . $g['lastname']);
            $sheet->setCellValue([3, $row], $g['username']);
            $sheet->setCellValue([4, $row], $g['idnumber']);
            $sheet->setCellValue([5, $row], round($g['grade'], 2));
            $sheet->setCellValue([6, $row], $g['grade'] >= 75 ? 'TUNTAS' : 'TIDAK TUNTAS');
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
        $grades  = $subject['moodle_quiz_id'] ? (new MoodleGradeModel())->getGradesByQuiz($subject['moodle_quiz_id']) : [];
        $settings = (new SettingModel())->getAllKeyValue();

        $html    = view('grade/pdf_nilai', compact('exam', 'subject', 'grades', 'settings'));
        $mpdf    = new Mpdf(['margin_top' => 12, 'margin_bottom' => 12, 'margin_left' => 15, 'margin_right' => 15]);
        $mpdf->WriteHTML($html);
        $mpdf->Output("Nilai_{$subject['nama_mapel']}.pdf", 'I');
        exit;
    }
}