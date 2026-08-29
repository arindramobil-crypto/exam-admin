<?php

namespace App\Controllers;

use App\Libraries\MoodleService;
use App\Models\{MoodleUserModel, MoodleQuizModel, MoodleGradeModel};

class Moodle extends BaseController
{
    protected $moodleService;

    public function __construct()
    {
        $this->moodleService = new MoodleService();
    }

    public function index()
    {
        $connectionStatus = $this->moodleService->checkConnection();
        $courses = [];
        if ($connectionStatus['success']) {
            $courses = $this->moodleService->getCoursesWithQuizzes();
        }

        $config = [
            'hostname' => env('database.moodle.hostname', config('Database')->moodle['hostname'] ?? '127.0.0.1'),
            'database' => env('database.moodle.database', config('Database')->moodle['database'] ?? 'moodle'),
            'username' => env('database.moodle.username', config('Database')->moodle['username'] ?? 'root'),
            'password' => env('database.moodle.password', config('Database')->moodle['password'] ?? ''),
            'DBPrefix' => env('database.moodle.DBPrefix', config('Database')->moodle['DBPrefix'] ?? 'mdl_'),
            'port'     => env('database.moodle.port', config('Database')->moodle['port'] ?? 3306),
        ];

        return view('moodle/index', [
            'title'            => 'Integrasi Moodle',
            'status'           => $connectionStatus,
            'config'           => $config,
            'courses'          => $courses,
        ]);
    }

    public function testConnection()
    {
        $params = [
            'hostname' => $this->request->getPost('hostname'),
            'database' => $this->request->getPost('database'),
            'username' => $this->request->getPost('username'),
            'password' => $this->request->getPost('password') ?? '',
            'DBPrefix' => $this->request->getPost('DBPrefix') ?: 'mdl_',
            'port'     => (int)($this->request->getPost('port') ?: 3306),
        ];

        $result = MoodleService::testConnectionParams($params);
        return $this->response->setJSON($result);
    }

    public function saveConfig()
    {
        $hostname = trim($this->request->getPost('hostname') ?? '127.0.0.1');
        $database = trim($this->request->getPost('database') ?? 'moodle');
        $username = trim($this->request->getPost('username') ?? 'root');
        $password = $this->request->getPost('password') ?? '';
        $prefix   = trim($this->request->getPost('DBPrefix') ?? 'mdl_');
        $port     = (int)($this->request->getPost('port') ?: 3306);

        // Test before saving
        $test = MoodleService::testConnectionParams([
            'hostname' => $hostname,
            'database' => $database,
            'username' => $username,
            'password' => $password,
            'DBPrefix' => $prefix,
            'port'     => $port,
        ]);

        if (!$test['success']) {
            return redirect()->back()->with('error', 'Koneksi gagal diuji: ' . $test['message'])->withInput();
        }

        // Update .env file
        $envPath = ROOTPATH . '.env';
        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);

            $updates = [
                'database.moodle.hostname' => $hostname,
                'database.moodle.database' => $database,
                'database.moodle.username' => $username,
                'database.moodle.password' => $password,
                'database.moodle.DBPrefix' => $prefix,
                'database.moodle.port'     => $port,
            ];

            foreach ($updates as $key => $val) {
                $escapedKey = preg_quote($key, '/');
                $pattern = "/^{$escapedKey}\s*=.*$/m";
                $valStr = (string)$val;
                if (str_contains($valStr, ' ') || str_contains($valStr, '#')) {
                    $valStr = "'{$valStr}'";
                }
                $replacement = "{$key} = {$valStr}";

                if (preg_match($pattern, $envContent)) {
                    $envContent = preg_replace($pattern, $replacement, $envContent);
                } else {
                    $envContent .= "\n{$replacement}";
                }
            }

            file_put_contents($envPath, $envContent);
        }

        return redirect()->to(base_url('moodle'))->with('success', 'Konfigurasi Moodle berhasil disimpan dan diverifikasi!');
    }

    public function courses()
    {
        $status = $this->moodleService->checkConnection();
        $courses = [];
        if ($status['success']) {
            $courses = $this->moodleService->getCoursesWithQuizzes();
        }

        return view('moodle/courses', [
            'title'   => 'Kursus & Quiz Moodle',
            'status'  => $status,
            'courses' => $courses,
        ]);
    }

    public function quizzesByCourse(int $courseId)
    {
        $quizModel = new MoodleQuizModel();
        $quizzes = $quizModel->getQuizzesByCourse($courseId);
        return $this->response->setJSON($quizzes);
    }

    public function studentsByCourse(int $courseId)
    {
        $userModel = new MoodleUserModel();
        $students = $userModel->getEnrolledStudents($courseId);
        return $this->response->setJSON($students);
    }

    public function liveAttempts(int $quizId)
    {
        $quizModel = new MoodleQuizModel();
        $attempts = $quizModel->getQuizAttempts($quizId);
        return $this->response->setJSON($attempts);
    }
}
