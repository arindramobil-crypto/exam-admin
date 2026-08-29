<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateRoomsTables extends Migration {
    public function up() {
        $this->forge->addField([
            'id'           => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'nama_ruang'   => ['type'=>'VARCHAR','constraint'=>100],
            'gedung'       => ['type'=>'VARCHAR','constraint'=>100,'null'=>true],
            'lantai'       => ['type'=>'VARCHAR','constraint'=>20,'null'=>true],
            'kapasitas'    => ['type'=>'INT','constraint'=>11,'default'=>30],
            'keterangan'   => ['type'=>'TEXT','null'=>true],
            'created_at'   => ['type'=>'DATETIME','null'=>true],
            'updated_at'   => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('rooms');

        $this->forge->addField([
            'id'           => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'exam_id'      => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'room_id'      => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'subject_id'   => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'null'=>true],
            'nama_pengawas1' => ['type'=>'VARCHAR','constraint'=>200,'null'=>true],
            'nama_pengawas2' => ['type'=>'VARCHAR','constraint'=>200,'null'=>true],
            'created_at'   => ['type'=>'DATETIME','null'=>true],
            'updated_at'   => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('exam_rooms');
    }
    public function down() {
        $this->forge->dropTable('exam_rooms', true);
        $this->forge->dropTable('rooms', true);
    }
}