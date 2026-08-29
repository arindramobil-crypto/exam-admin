<?php
namespace App\Controllers;
use App\Models\{ExamModel, ExamSubjectModel, MoodleQuizModel};

class Exam extends BaseController
{
    protected $examModel, $subjectModel;
    public function __construct()
    {
        $this->examModel    = new ExamModel();
        $this->subjectModel = new ExamSubjectModel();
    }

    public function index()
    {
        $exams = $this->examModel->withSubjectCount()->findAll();
        return view('exam/index', ['title'=>'Manajemen Ujian', 'exams'=>$exams]);
    }

    public function create()
    {
        return view('exam/form', ['title'=>'Tambah Ujian', 'exam'=>null]);
    }

    public function store()
    {
        $data = $this->request->getPost(['nama_ujian','tahun','semester','tgl_mulai','tgl_selesai','keterangan']);
        $data['status'] = 'draft';
        $this->examModel->insert($data);
        return redirect()->to(base_url('exam'))->with('success', 'Ujian berhasil ditambahkan!');
    }

    public function edit(int $id)
    {
        $exam = $this->examModel->find($id);
        return view('exam/form', ['title'=>'Edit Ujian', 'exam'=>$exam]);
    }

    public function update(int $id)
    {
        $data = $this->request->getPost(['nama_ujian','tahun','semester','tgl_mulai','tgl_selesai','status','keterangan']);
        $this->examModel->update($id, $data);
        return redirect()->to(base_url('exam'))->with('success', 'Ujian berhasil diperbarui!');
    }

    public function delete(int $id)
    {
        $this->examModel->delete($id);
        return redirect()->to(base_url('exam'))->with('success', 'Ujian dihapus!');
    }

    public function subjects(int $examId)
    {
        $exam     = $this->examModel->find($examId);
        $subjects = $this->subjectModel->where('exam_id', $examId)->findAll();
        $quizzes  = (new MoodleQuizModel())->getAllQuizzes();
        return view('exam/subjects', ['title'=>'Mata Pelajaran', 'exam'=>$exam, 'subjects'=>$subjects, 'quizzes'=>$quizzes]);
    }

    public function addSubject(int $examId)
    {
        $data = $this->request->getPost();
        $data['exam_id'] = $examId;
        if (!empty($data['moodle_quiz_id']) && empty($data['moodle_course_id'])) {
            $quiz = (new MoodleQuizModel())->getQuizDetails((int)$data['moodle_quiz_id']);
            if ($quiz) {
                $data['moodle_course_id'] = $quiz['course'] ?? ($quiz['course_id'] ?? null);
            }
        }
        $this->subjectModel->insert($data);
        return redirect()->back()->with('success', 'Mata pelajaran berhasil ditambahkan!');
    }

    public function deleteSubject(int $id)
    {
        $subject = $this->subjectModel->find($id);
        $this->subjectModel->delete($id);
        return redirect()->to(base_url("exam/subjects/{$subject['exam_id']}"))->with('success', 'Mapel dihapus!');
    }
}