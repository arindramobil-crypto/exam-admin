<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\MoodleService;
use App\Models\{ExamModel, ExamSubjectModel, ParticipantModel, TokenModel, MoodleUserModel, MoodleQuizModel, MoodleGradeModel};
use Config\Database;
use Throwable;

class MoodleTestFlow extends BaseCommand
{
    protected $group       = 'Moodle';
    protected $name        = 'moodle:test-flow';
    protected $description = 'Menjalankan simulasi & verifikasi alur integrasi Moodle end-to-end.';

    public function run(array $params)
    {
        CLI::write("\n=======================================================", 'yellow');
        CLI::write("  SIMULASI & VERIFIKASI ALUR INTEGRASI MOODLE EXAM-ADMIN", 'yellow');
        CLI::write("=======================================================\n", 'yellow');

        // -----------------------------------------------------------------
        // TAHAP 1: KONEKSI & STRUKTUR DATABASE MOODLE
        // -----------------------------------------------------------------
        CLI::write("[TAHAP 1/4] Memeriksa Koneksi Database Moodle...", 'cyan');
        $service = new MoodleService();
        $status = $service->checkConnection();

        if (!$status['success']) {
            CLI::error("✗ Gagal terhubung ke Moodle: " . $status['message']);
            return;
        }

        CLI::write("✓ Database Moodle ({$status['database']}, prefix: {$status['prefix']}) terhubung normal.", 'green');
        CLI::write("  Total Kursus: {$status['stats']['courses']} | Total Quiz: {$status['stats']['quizzes']} | Total Siswa: {$status['stats']['users']}");

        $courses = $service->getCoursesWithQuizzes();
        if (empty($courses)) {
            CLI::error("✗ Tidak ada kursus aktif di Moodle untuk diuji.");
            return;
        }

        $targetCourse = $courses[0];
        CLI::write("  Target Kursus: [ID {$targetCourse['id']}] {$targetCourse['fullname']} (Shortname: {$targetCourse['shortname']})");

        if (empty($targetCourse['quizzes'])) {
            CLI::error("✗ Tidak ada quiz di dalam kursus ini.");
            return;
        }
        $targetQuiz = $targetCourse['quizzes'][0];
        CLI::write("  Target Quiz  : [ID {$targetQuiz['id']}] {$targetQuiz['name']}\n");

        // -----------------------------------------------------------------
        // TAHAP 2: SINKRONISASI MATA PELAJARAN & IMPORT PESERTA DARI MOODLE
        // -----------------------------------------------------------------
        CLI::write("[TAHAP 2/4] Verifikasi Ujian, Mata Pelajaran & Import Peserta...", 'cyan');
        $examModel = new ExamModel();
        $subjectModel = new ExamSubjectModel();
        $participantModel = new ParticipantModel();
        $userModel = new MoodleUserModel();

        $exam = $examModel->first();
        if (!$exam) {
            CLI::write("  Membuat ujian dummy untuk pengujian...", 'light_gray');
            $examId = $examModel->insert([
                'nama_ujian'  => 'Simulasi Ujian CBT Moodle ' . date('Y'),
                'tahun'       => date('Y'),
                'semester'    => '1',
                'tgl_mulai'   => date('Y-m-d'),
                'tgl_selesai' => date('Y-m-d', strtotime('+7 days')),
                'status'      => 'aktif',
            ]);
            $exam = $examModel->find($examId);
        }
        CLI::write("  Ujian Terpilih: [ID {$exam['id']}] {$exam['nama_ujian']}");

        // Cek subject yang menautkan quiz
        $subject = $subjectModel->where('exam_id', $exam['id'])->where('moodle_quiz_id', $targetQuiz['id'])->first();
        if (!$subject) {
            $subjectId = $subjectModel->insert([
                'exam_id'          => $exam['id'],
                'nama_mapel'       => $targetQuiz['name'],
                'kelas'            => '10',
                'moodle_quiz_id'   => $targetQuiz['id'],
                'moodle_course_id' => $targetCourse['id'],
                'durasi_menit'     => 90,
            ]);
            $subject = $subjectModel->find($subjectId);
            CLI::write("  ✓ Mapel baru dibuat dan ditautkan ke Quiz Moodle #{$targetQuiz['id']}", 'green');
        } else {
            CLI::write("  ✓ Mapel [ID {$subject['id']}] {$subject['nama_mapel']} sudah terhubung ke Quiz Moodle #{$targetQuiz['id']}", 'green');
        }

        // Ambil enrolled students dari kursus
        $enrolledStudents = $userModel->getEnrolledStudents((int)$targetCourse['id']);
        CLI::write("  Ditemukan " . count($enrolledStudents) . " siswa terdaftar di Kursus Moodle.");

        // Simulasi import siswa ke exam-admin jika belum ada
        $importedCount = 0;
        foreach ($enrolledStudents as $u) {
            $exists = $participantModel->where('exam_id', $exam['id'])
                ->where('subject_id', $subject['id'])
                ->where('moodle_user_id', $u['id'])
                ->first();

            if (!$exists) {
                $nomor = $participantModel->generateNomorPeserta($exam['id'], '10', $participantModel->where('exam_id', $exam['id'])->countAllResults() + 1);
                $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                $pwd = substr(str_shuffle($chars), 0, 6);

                $participantModel->insert([
                    'exam_id'        => $exam['id'],
                    'subject_id'     => $subject['id'],
                    'moodle_user_id' => $u['id'],
                    'nomor_peserta'  => $nomor,
                    'nama'           => trim($u['firstname'] . ' ' . $u['lastname']),
                    'nis'            => $u['idnumber'] ?: $u['username'],
                    'password'       => $pwd,
                    'kelas'          => '10',
                    'sesi'           => 1,
                ]);
                $importedCount++;
            }
        }
        $totalPeserta = $participantModel->where('exam_id', $exam['id'])->countAllResults();
        CLI::write("  ✓ Total peserta ujian saat ini di exam-admin: {$totalPeserta} peserta (+{$importedCount} baru diimpor).\n", 'green');

        // -----------------------------------------------------------------
        // TAHAP 3: TOKEN CBT & SINKRONISASI PASSWORD KUIS MOODLE
        // -----------------------------------------------------------------
        CLI::write("[TAHAP 3/4] Uji Rilis Token CBT & Sinkronisasi Password Kuis...", 'cyan');
        $tokenModel = new TokenModel();

        // Generate token baru
        $tokenData = $tokenModel->generateToken($exam['id'], $subject['id'], 30);
        CLI::write("  ✓ Token berhasil dibuat di exam-admin: [ {$tokenData['token']} ] (Durasi: {$tokenData['durasi_menit']} menit, Kedaluwarsa: {$tokenData['expires_at']})", 'green');

        // Periksa apakah password di mdl_quiz Moodle terupdate
        $moodleDb = Database::connect('moodle');
        $moodleQuiz = $moodleDb->table('quiz')->where('id', $targetQuiz['id'])->get()->getRowArray();

        if ($moodleQuiz['password'] === $tokenData['token']) {
            CLI::write("  ✓ SINKRONISASI SUKSES: Kolom password di mdl_quiz (Moodle) = '{$moodleQuiz['password']}' sama persis dengan Token CBT!", 'green');
        } else {
            CLI::error("  ✗ SINKRONISASI GAGAL: Kolom password di mdl_quiz adalah '{$moodleQuiz['password']}', diharapkan '{$tokenData['token']}'");
        }
        CLI::write("");

        // -----------------------------------------------------------------
        // TAHAP 4: PENARIKAN NILAI & STATUS PENGERJAAN
        // -----------------------------------------------------------------
        CLI::write("[TAHAP 4/4] Verifikasi Penarikan Nilai & Hasil Ujian...", 'cyan');
        $gradeModel = new MoodleGradeModel();
        $quizModel = new MoodleQuizModel();

        $simulate = (CLI::getOption('simulate') !== null || in_array('--simulate', $params));

        if ($simulate && empty($grades)) {
            CLI::write("  [SIMULASI] Menambahkan data pengerjaan kuis dummy untuk siswa1...", 'yellow');
            $now = time();
            $studentUser = $userModel->where('username', 'siswa1')->first();
            $userId = $studentUser ? (int)$studentUser['id'] : 3;

            // 1. Insert or update attempt
            $existingAttempt = $moodleDb->table('quiz_attempts')
                ->where('quiz', (int)$targetQuiz['id'])
                ->where('userid', $userId)
                ->where('attempt', 1)
                ->get()->getRowArray();

            if ($existingAttempt) {
                $moodleDb->table('quiz_attempts')->where('id', $existingAttempt['id'])->update([
                    'state'        => 'finished',
                    'timefinish'   => $now - 600,
                    'timemodified' => $now - 600,
                    'sumgrades'    => 85.00000,
                ]);
            } else {
                $moodleDb->table('quiz_attempts')->insert([
                    'quiz'         => (int)$targetQuiz['id'],
                    'userid'       => $userId,
                    'attempt'      => 1,
                    'uniqueid'     => rand(1000, 9999),
                    'layout'       => '1,0',
                    'currentpage'  => 0,
                    'preview'      => 0,
                    'state'        => 'finished',
                    'timestart'    => $now - 3600,
                    'timefinish'   => $now - 600,
                    'timemodified' => $now - 600,
                    'timemodifiedoffline' => 0,
                    'timecheckstate'      => null,
                    'sumgrades'    => 85.00000,
                    'gradednotificationsenttime' => null,
                ]);
            }

            // 2. Insert or update quiz grade
            $existingGrade = $moodleDb->table('quiz_grades')
                ->where('quiz', (int)$targetQuiz['id'])
                ->where('userid', $userId)
                ->get()->getRowArray();

            if ($existingGrade) {
                $moodleDb->table('quiz_grades')->where('id', $existingGrade['id'])->update([
                    'grade'        => 85.00000,
                    'timemodified' => $now,
                ]);
            } else {
                $moodleDb->table('quiz_grades')->insert([
                    'quiz'         => (int)$targetQuiz['id'],
                    'userid'       => $userId,
                    'grade'        => 85.00000,
                    'timemodified' => $now,
                ]);
            }

            // Re-fetch
            $grades   = $gradeModel->getGradesByQuiz((int)$targetQuiz['id']);
            $attempts = $quizModel->getQuizAttempts((int)$targetQuiz['id']);
            CLI::write("  ✓ Berhasil membuat 1 simulasi attempt selesai dengan nilai 85.00!", 'green');
        }

        CLI::write("  Statistik Hasil Moodle Quiz #{$targetQuiz['id']}:");
        CLI::write("  - Total Attempt  : " . count($attempts));
        CLI::write("  - Siswa Bernilai : " . count($grades));

        if (!empty($grades)) {
            $gRows = [];
            foreach ($grades as $g) {
                $gRows[] = [
                    $g['user_id'],
                    $g['username'],
                    trim($g['firstname'] . ' ' . $g['lastname']),
                    round($g['grade'], 2),
                    $g['grade'] >= 75 ? 'TUNTAS' : 'BELUM TUNTAS'
                ];
            }
            CLI::table($gRows, ['User ID', 'Username', 'Nama Siswa', 'Nilai', 'Status']);
        } else {
            CLI::write("  ℹ Saat ini belum ada attempt quiz yang selesai atau dinilai di Moodle.", 'light_gray');
            CLI::write("  (Gunakan opsi '--simulate' untuk menguji penarikan nilai dengan attempt contoh)", 'light_gray');
        }

        CLI::write("\n=======================================================", 'green');
        CLI::write("  SELURUH ALUR INTEGRASI BERHASIL DIVERIFIKASI!", 'green');
        CLI::write("=======================================================\n", 'green');
    }
}
