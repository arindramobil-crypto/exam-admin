<?php
use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Auth
$routes->get('/', 'Auth::login');
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/login', 'Auth::doLogin');
$routes->get('auth/logout', 'Auth::logout');

// Protected routes
$routes->group('', ['filter' => 'auth'], function($routes) {
    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('dashboard/monitoring/(:num)', 'Dashboard::monitoring/$1');

    // Exam
    $routes->get('exam', 'Exam::index');
    $routes->get('exam/create', 'Exam::create');
    $routes->post('exam/store', 'Exam::store');
    $routes->get('exam/edit/(:num)', 'Exam::edit/$1');
    $routes->post('exam/update/(:num)', 'Exam::update/$1');
    $routes->get('exam/delete/(:num)', 'Exam::delete/$1');
    $routes->get('exam/subjects/(:num)', 'Exam::subjects/$1');
    $routes->post('exam/subjects/(:num)/add', 'Exam::addSubject/$1');
    $routes->get('exam/subjects/delete/(:num)', 'Exam::deleteSubject/$1');

    // Participants
    $routes->get('participant/(:num)', 'Participant::index/$1');
    $routes->post('participant/(:num)/import', 'Participant::importFromMoodle/$1');
    $routes->post('participant/(:num)/assign-rooms', 'Participant::assignRooms/$1');
    $routes->post('participant/(:num)/assign-sessions', 'Participant::assignSessions/$1');
    $routes->post('participant/(:num)/generate-passwords', 'Participant::generatePasswords/$1');
    $routes->get('participant/print-kartu/(:num)', 'Participant::printKartu/$1');
    $routes->get('participant/print-kartu-meja/(:num)', 'Participant::printKartuMeja/$1');
    $routes->get('participant/export-excel/(:num)', 'Participant::exportExcel/$1');
    $routes->post('participant/(:num)/import-excel', 'Participant::importExcel/$1');
    $routes->get('participant/(:num)/sync-moodle', 'Participant::syncToMoodle/$1');
    $routes->get('participant/download-template', 'Participant::downloadTemplateExcel');
    $routes->get('participant/delete/(:num)', 'Participant::delete/$1');

    // Rooms
    $routes->get('room', 'Room::index');
    $routes->get('room/create', 'Room::create');
    $routes->post('room/store', 'Room::store');
    $routes->get('room/edit/(:num)', 'Room::edit/$1');
    $routes->post('room/update/(:num)', 'Room::update/$1');
    $routes->get('room/delete/(:num)', 'Room::delete/$1');
    $routes->get('room/denah/(:num)/(:num)', 'Room::denah/$1/$2');

    // Attendance
    $routes->get('attendance', 'Attendance::index');
    $routes->get('attendance/(:num)', 'Attendance::index/$1');
    $routes->get('attendance/(:num)/(:num)', 'Attendance::index/$1/$2');
    $routes->post('attendance/save/(:num)', 'Attendance::save/$1');
    $routes->post('attendance/save/(:num)/(:num)', 'Attendance::save/$1/$2');
    $routes->get('attendance/print/(:num)', 'Attendance::print/$1');
    $routes->get('attendance/print/(:num)/(:num)', 'Attendance::print/$1/$2');
    $routes->get('attendance/print/(:num)/(:num)/(:num)', 'Attendance::print/$1/$2/$3');

    // Minutes (Berita Acara)
    $routes->get('minutes/(:num)', 'Minutes::index/$1');
    $routes->get('minutes/form/(:num)/(:num)/(:num)', 'Minutes::form/$1/$2/$3');
    $routes->post('minutes/save', 'Minutes::save');
    $routes->get('minutes/print/(:num)', 'Minutes::print/$1');

    // Grades
    $routes->get('grade/(:num)', 'Grade::index/$1');
    $routes->get('grade/view/(:num)/(:num)', 'Grade::view/$1/$2');
    $routes->get('grade/export-excel/(:num)/(:num)', 'Grade::exportExcel/$1/$2');
    $routes->get('grade/export-pdf/(:num)/(:num)', 'Grade::exportPdf/$1/$2');

    // Token CBT
    $routes->get('token/(:num)', 'Token::index/$1');
    $routes->post('token/generate/(:num)', 'Token::generate/$1');
    $routes->get('token/deactivate/(:num)', 'Token::deactivate/$1');
    $routes->get('token/display/(:num)', 'Token::display/$1');
    $routes->get('token/get-active-json/(:num)', 'Token::getActiveTokenJson/$1');

    // Supervisors & Proctors
    $routes->get('supervisor/(:num)', 'Supervisor::index/$1');
    $routes->post('supervisor/store/(:num)', 'Supervisor::store/$1');
    $routes->get('supervisor/delete/(:num)', 'Supervisor::delete/$1');
    $routes->get('supervisor/print-jadwal/(:num)', 'Supervisor::printJadwal/$1');

    // Item Analysis (Analisis Butir Soal)
    $routes->get('analysis/(:num)', 'Analysis::index/$1');
    $routes->get('analysis/detail/(:num)/(:num)', 'Analysis::detail/$1/$2');
    $routes->get('analysis/export-excel/(:num)/(:num)', 'Analysis::exportExcel/$1/$2');

    // Moodle Integration
    $routes->get('moodle', 'Moodle::index');
    $routes->post('moodle/test-connection', 'Moodle::testConnection');
    $routes->post('moodle/save-config', 'Moodle::saveConfig');
    $routes->get('moodle/courses', 'Moodle::courses');
    $routes->get('moodle/quizzes/(:num)', 'Moodle::quizzesByCourse/$1');
    $routes->get('moodle/students/(:num)', 'Moodle::studentsByCourse/$1');
    $routes->get('moodle/live-attempts/(:num)', 'Moodle::liveAttempts/$1');

    // Settings (School Profile, Logo & Kop Surat)
    $routes->get('setting', 'Setting::index');
    $routes->post('setting/save', 'Setting::save');
});