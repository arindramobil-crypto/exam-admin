<?php

namespace App\Libraries;

use Config\Database;
use Throwable;

class MoodleService
{
    protected $db;

    public function __construct()
    {
        try {
            $this->db = Database::connect('moodle', false);
        } catch (Throwable $e) {
            $this->db = null;
        }
    }

    /**
     * Check if connection to Moodle database is working.
     */
    public function checkConnection(): array
    {
        try {
            if (!$this->db) {
                $this->db = Database::connect('moodle', false);
            }
            
            // Test query
            $res = $this->db->query('SELECT 1 as test');
            if ($res) {
                $version = $this->db->getVersion();
                $prefix  = $this->db->getPrefix();
                $database = $this->db->getDatabase();
                
                // Count basic entities
                $userCount   = $this->db->table('user')->where('deleted', 0)->where('id >', 1)->countAllResults();
                $courseCount = $this->db->table('course')->where('id >', 1)->countAllResults();
                $quizCount   = $this->db->table('quiz')->countAllResults();

                return [
                    'success'  => true,
                    'message'  => 'Terhubung ke database Moodle',
                    'database' => $database,
                    'prefix'   => $prefix,
                    'version'  => $version,
                    'stats'    => [
                        'users'   => $userCount,
                        'courses' => $courseCount,
                        'quizzes' => $quizCount,
                    ],
                ];
            }
            return ['success' => false, 'message' => 'Query test gagal dijalankan.'];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Gagal terhubung ke database Moodle: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Test connection with specific parameters without saving.
     */
    public static function testConnectionParams(array $config): array
    {
        $customConfig = [
            'DSN'          => '',
            'hostname'     => $config['hostname'] ?? '127.0.0.1',
            'username'     => $config['username'] ?? 'root',
            'password'     => $config['password'] ?? '',
            'database'     => $config['database'] ?? 'moodle',
            'DBDriver'     => 'MySQLi',
            'DBPrefix'     => $config['DBPrefix'] ?? 'mdl_',
            'pConnect'     => false,
            'DBDebug'      => false,
            'charset'      => 'utf8mb4',
            'DBCollat'     => 'utf8mb4_unicode_ci',
            'swapPre'      => '',
            'encrypt'      => false,
            'compress'     => false,
            'strictOn'     => false,
            'failover'     => [],
            'port'         => (int)($config['port'] ?? 3306),
            'numberNative' => false,
            'foundRows'    => false,
            'dateFormat'   => [
                'date'     => 'Y-m-d',
                'datetime' => 'Y-m-d H:i:s',
                'time'     => 'H:i:s',
            ],
        ];

        try {
            $db = Database::connect($customConfig, false);
            $res = $db->query('SELECT 1 as test');
            if ($res) {
                $userCount   = $db->table('user')->where('deleted', 0)->where('id >', 1)->countAllResults();
                $courseCount = $db->table('course')->where('id >', 1)->countAllResults();
                $quizCount   = $db->table('quiz')->countAllResults();
                return [
                    'success' => true,
                    'message' => 'Koneksi berhasil! Ditemukan ' . $userCount . ' pengguna, ' . $courseCount . ' kursus, ' . $quizCount . ' quiz.',
                    'stats'   => [
                        'users'   => $userCount,
                        'courses' => $courseCount,
                        'quizzes' => $quizCount,
                    ],
                ];
            }
            return ['success' => false, 'message' => 'Koneksi gagal saat eksekusi query.'];
        } catch (Throwable $e) {
            return ['success' => false, 'message' => 'Koneksi gagal: ' . $e->getMessage()];
        }
    }

    /**
     * Get all courses with their quizzes.
     */
    public function getCoursesWithQuizzes(): array
    {
        if (!$this->db) return [];
        try {
            $courses = $this->db->table('course')
                ->select('id, fullname, shortname, idnumber, summary')
                ->where('id >', 1)
                ->orderBy('fullname', 'ASC')
                ->get()->getResultArray();

            foreach ($courses as &$c) {
                $quizzes = $this->db->table('quiz')
                    ->select('id, name, timeopen, timeclose, timelimit, grade, sumgrades')
                    ->where('course', $c['id'])
                    ->orderBy('name', 'ASC')
                    ->get()->getResultArray();
                $c['quizzes'] = $quizzes;

                // Student count in this course
                $c['student_count'] = $this->db->table('user_enrolments ue')
                    ->join('enrol e', 'e.id = ue.enrolid')
                    ->join('user u', 'u.id = ue.userid')
                    ->where('e.courseid', $c['id'])
                    ->where('u.deleted', 0)
                    ->where('u.suspended', 0)
                    ->countAllResults();
            }
            return $courses;
        } catch (Throwable $e) {
            return [];
        }
    }
}
