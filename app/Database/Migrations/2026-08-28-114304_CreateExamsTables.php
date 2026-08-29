<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateExamsTables extends Migration {
    public function up() {
        // Tabel Ujian utama
        $this->forge->addField([
            'id'          => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'nama_ujian'  => ['type'=>'VARCHAR','constraint'=>200],
            'tahun'       => ['type'=>'YEAR'],
            'semester'    => ['type'=>'ENUM','constraint'=>['1','2'],'default'=>'1'],
            'tgl_mulai'   => ['type'=>'DATE'],
            'tgl_selesai' => ['type'=>'DATE'],
            'status'      => ['type'=>'ENUM','constraint'=>['draft','aktif','selesai'],'default'=>'draft'],
            'keterangan'  => ['type'=>'TEXT','null'=>true],
            'created_at'  => ['type'=>'DATETIME','null'=>true],
            'updated_at'  => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('exams');

        // Tabel mata pelajaran per ujian
        $this->forge->addField([
            'id'          => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'exam_id'     => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'nama_mapel'  => ['type'=>'VARCHAR','constraint'=>200],
            'kode_mapel'  => ['type'=>'VARCHAR','constraint'=>50,'null'=>true],
            'kelas'       => ['type'=>'VARCHAR','constraint'=>20],
            'moodle_quiz_id' => ['type'=>'INT','constraint'=>11,'null'=>true],
            'moodle_course_id' => ['type'=>'INT','constraint'=>11,'null'=>true],
            'durasi_menit'=> ['type'=>'INT','constraint'=>11,'default'=>90],
            'tgl_ujian'   => ['type'=>'DATE','null'=>true],
            'jam_mulai'   => ['type'=>'TIME','null'=>true],
            'jam_selesai' => ['type'=>'TIME','null'=>true],
            'created_at'  => ['type'=>'DATETIME','null'=>true],
            'updated_at'  => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('exam_id', 'exams', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('exam_subjects');

        // Tabel admin users
        $this->forge->addField([
            'id'         => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'nama'       => ['type'=>'VARCHAR','constraint'=>200],
            'username'   => ['type'=>'VARCHAR','constraint'=>100],
            'password'   => ['type'=>'VARCHAR','constraint'=>255],
            'role'       => ['type'=>'ENUM','constraint'=>['superadmin','admin','operator'],'default'=>'operator'],
            'is_active'  => ['type'=>'TINYINT','constraint'=>1,'default'=>1],
            'created_at' => ['type'=>'DATETIME','null'=>true],
            'updated_at' => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('username');
        $this->forge->createTable('admin_users');
    }
    public function down() {
        $this->forge->dropTable('exam_subjects', true);
        $this->forge->dropTable('exams', true);
        $this->forge->dropTable('admin_users', true);
    }
}