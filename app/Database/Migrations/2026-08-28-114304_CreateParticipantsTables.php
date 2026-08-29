<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateParticipantsTables extends Migration {
    public function up() {
        $this->forge->addField([
            'id'              => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'exam_id'         => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'subject_id'      => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'null'=>true],
            'room_id'         => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'null'=>true],
            'nomor_peserta'   => ['type'=>'VARCHAR','constraint'=>30],
            'nomor_meja'      => ['type'=>'INT','constraint'=>11,'null'=>true],
            'moodle_user_id'  => ['type'=>'INT','constraint'=>11,'null'=>true],
            'nama'            => ['type'=>'VARCHAR','constraint'=>200],
            'nis'             => ['type'=>'VARCHAR','constraint'=>50,'null'=>true],
            'nisn'            => ['type'=>'VARCHAR','constraint'=>50,'null'=>true],
            'kelas'           => ['type'=>'VARCHAR','constraint'=>20],
            'jurusan'         => ['type'=>'VARCHAR','constraint'=>100,'null'=>true],
            'created_at'      => ['type'=>'DATETIME','null'=>true],
            'updated_at'      => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['exam_id','nomor_peserta']);
        $this->forge->createTable('participants');
    }
    public function down() {
        $this->forge->dropTable('participants', true);
    }
}