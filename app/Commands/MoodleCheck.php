<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\MoodleService;

class MoodleCheck extends BaseCommand
{
    protected $group       = 'Moodle';
    protected $name        = 'moodle:check';
    protected $description = 'Memeriksa status koneksi dan statistik database Moodle.';

    public function run(array $params)
    {
        CLI::write('Memeriksa koneksi ke database Moodle...', 'yellow');

        $service = new MoodleService();
        $status  = $service->checkConnection();

        if ($status['success']) {
            CLI::write('✓ Berhasil terhubung ke Moodle!', 'green');
            CLI::table([
                ['Database', $status['database']],
                ['Prefix Table', $status['prefix']],
                ['Versi MySQL', $status['version']],
                ['Total Kursus', $status['stats']['courses']],
                ['Total Quiz', $status['stats']['quizzes']],
                ['Total Siswa', $status['stats']['users']],
            ], ['Properti', 'Nilai']);

            $courses = $service->getCoursesWithQuizzes();
            CLI::write("\nDaftar Kursus & Quiz di Moodle:", 'cyan');
            if (empty($courses)) {
                CLI::write('  (Belum ada kursus aktif selain site home)', 'light_gray');
            } else {
                $cRows = [];
                $moodleDb = \Config\Database::connect('moodle');
                foreach ($courses as $c) {
                    $quizNames = [];
                    foreach ($c['quizzes'] as $q) {
                        $qDetail = $moodleDb->table('quiz')->where('id', $q['id'])->get()->getRowArray();
                        $pwd = !empty($qDetail['password']) ? $qDetail['password'] : '(tidak ada token)';
                        $quizNames[] = "#{$q['id']}: {$q['name']} [Token/Pwd: {$pwd}]";
                    }
                    $cRows[] = [
                        $c['id'],
                        $c['fullname'],
                        $c['shortname'],
                        $c['student_count'],
                        empty($quizNames) ? '-' : implode("\n", $quizNames)
                    ];
                }
                CLI::table($cRows, ['ID Kursus', 'Nama Kursus', 'Shortname', 'Enrolled Siswa', 'Daftar Quiz']);
            }

            // Moodle Attempts & Grades
            $quizModel = new \App\Models\MoodleQuizModel();
            $quizzes = $quizModel->getAllQuizzes();
            if (!empty($quizzes)) {
                CLI::write("\nStatus Pengerjaan & Nilai Quiz Moodle:", 'cyan');
                foreach ($quizzes as $qz) {
                    $attempts = $quizModel->getQuizAttempts((int)$qz['id']);
                    CLI::write("Quiz: {$qz['name']} (ID: {$qz['id']}) - Total Attempt: " . count($attempts), 'yellow');
                    if (!empty($attempts)) {
                        $attRows = [];
                        foreach ($attempts as $at) {
                            $attRows[] = [
                                $at['attempt_id'],
                                $at['username'],
                                trim($at['firstname'] . ' ' . $at['lastname']),
                                $at['state'],
                                $at['grade'] !== null ? round($at['grade'], 2) : '-',
                                date('Y-m-d H:i', $at['timestart']),
                                $at['timefinish'] ? date('Y-m-d H:i', $at['timefinish']) : 'Sedang Mengerjakan'
                            ];
                        }
                        CLI::table($attRows, ['Attempt ID', 'Username', 'Nama', 'State', 'Nilai', 'Mulai', 'Selesai']);
                    } else {
                        CLI::write('  (Belum ada siswa yang mengerjakan quiz ini)', 'light_gray');
                    }
                }
            }

            // Moodle Users
            $userModel = new \App\Models\MoodleUserModel();
            $students = $userModel->getAllStudents();
            CLI::write("\nDaftar Siswa di Moodle (mdl_user):", 'cyan');
            if (empty($students)) {
                CLI::write('  (Belum ada siswa terdaftar)', 'light_gray');
            } else {
                $uRows = [];
                foreach ($students as $s) {
                    $uRows[] = [
                        $s['id'],
                        trim($s['firstname'] . ' ' . $s['lastname']),
                        $s['username'],
                        $s['idnumber'] ?: '-',
                        $s['email']
                    ];
                }
                CLI::table($uRows, ['ID', 'Nama Lengkap', 'Username', 'ID Number / NIS', 'Email']);
            }
        } else {
            CLI::error('✗ Gagal terhubung ke Moodle: ' . $status['message']);
        }
    }
}
