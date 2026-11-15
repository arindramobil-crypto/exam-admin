<?php

namespace App\Libraries;

use App\Models\ExamModel;

class ExamContext
{
    protected static ?array $cachedActiveExam = null;
    protected static ?array $cachedAllExams   = null;

    /**
     * Dapatkan paket ujian aktif saat ini.
     * Prioritas:
     * 1. ID yang tersimpan di session ('active_exam_id')
     * 2. Ujian yang berstatus 'aktif' di database
     * 3. Ujian terbaru di database
     */
    public static function getActiveExam(): ?array
    {
        if (self::$cachedActiveExam !== null) {
            return self::$cachedActiveExam;
        }

        $examModel = new ExamModel();
        $session   = session();
        $activeId  = $session ? $session->get('active_exam_id') : null;

        if ($activeId) {
            $exam = $examModel->find($activeId);
            if ($exam) {
                self::$cachedActiveExam = $exam;
                return $exam;
            }
        }

        // Cari ujian dengan status 'aktif'
        $exam = $examModel->where('status', 'aktif')->orderBy('id', 'DESC')->first();
        if ($exam) {
            if ($session) {
                $session->set('active_exam_id', (int)$exam['id']);
            }
            self::$cachedActiveExam = $exam;
            return $exam;
        }

        // Fallback: ambil ujian paling baru
        $exam = $examModel->orderBy('id', 'DESC')->first();
        if ($exam) {
            if ($session) {
                $session->set('active_exam_id', (int)$exam['id']);
            }
            self::$cachedActiveExam = $exam;
            return $exam;
        }

        return null;
    }

    /**
     * Dapatkan ID ujian aktif (atau null jika belum ada ujian)
     */
    public static function getActiveExamId(): ?int
    {
        $exam = self::getActiveExam();
        return $exam ? (int)$exam['id'] : null;
    }

    /**
     * Set ujian aktif ke session
     */
    public static function setActiveExam(int $id): bool
    {
        $examModel = new ExamModel();
        $exam = $examModel->find($id);
        if ($exam) {
            if (session()) {
                session()->set('active_exam_id', $id);
            }
            self::$cachedActiveExam = $exam;
            return true;
        }
        return false;
    }

    /**
     * Dapatkan seluruh daftar paket ujian
     */
    public static function getAllExams(): array
    {
        if (self::$cachedAllExams !== null) {
            return self::$cachedAllExams;
        }

        $examModel = new ExamModel();
        self::$cachedAllExams = $examModel->orderBy('id', 'DESC')->findAll();
        return self::$cachedAllExams;
    }
}
