<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateMinutesTables extends Migration {
    public function up() {
        $this->forge->addField([
            'id'              => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'exam_id'         => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'subject_id'      => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'room_id'         => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'nama_pengawas1'  => ['type'=>'VARCHAR','constraint'=>200,'null'=>true],
            'nama_pengawas2'  => ['type'=>'VARCHAR','constraint'=>200,'null'=>true],
            'jml_peserta_hadir' => ['type'=>'INT','constraint'=>11,'default'=>0],
            'jml_peserta_absen' => ['type'=>'INT','constraint'=>11,'default'=>0],
            'jml_soal'        => ['type'=>'INT','constraint'=>11,'default'=>0],
            'waktu_mulai'     => ['type'=>'TIME','null'=>true],
            'waktu_selesai'   => ['type'=>'TIME','null'=>true],
            'kejadian'        => ['type'=>'TEXT','null'=>true],
            'catatan'         => ['type'=>'TEXT','null'=>true],
            'status'          => ['type'=>'ENUM','constraint'=>['draft','final'],'default'=>'draft'],
            'created_at'      => ['type'=>'DATETIME','null'=>true],
            'updated_at'      => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('exam_minutes');
    }
    public function down() {
        $this->forge->dropTable('exam_minutes', true);
    }
}