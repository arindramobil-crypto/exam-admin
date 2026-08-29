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
        } else {
            CLI::error('✗ Gagal terhubung ke Moodle: ' . $status['message']);
        }
    }
}
