<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateAttendanceTables extends Migration {
    public function up() {
        $this->forge->addField([
            'id'             => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'exam_id'        => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'subject_id'     => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'participant_id' => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'status'         => ['type'=>'ENUM','constraint'=>['hadir','tidak_hadir','sakit','izin'],'default'=>'hadir'],
            'jam_absen'      => ['type'=>'TIME','null'=>true],
            'keterangan'     => ['type'=>'VARCHAR','constraint'=>200,'null'=>true],
            'dicatat_oleh'   => ['type'=>'INT','constraint'=>11,'null'=>true],
            'created_at'     => ['type'=>'DATETIME','null'=>true],
            'updated_at'     => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('attendances');
    }
    public function down() {
        $this->forge->dropTable('attendances', true);
    }
}