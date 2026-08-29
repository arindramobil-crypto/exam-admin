<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCbtAdvancedFeatures extends Migration
{
    public function up()
    {
        // 1. Tambah kolom sesi & password di tabel participants
        $fields = [
            'sesi'     => ['type' => 'INT', 'constraint' => 5, 'default' => 1, 'after' => 'nomor_meja'],
            'password' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'nisn'],
        ];
        $this->forge->addColumn('participants', $fields);

        // 2. Buat tabel exam_supervisors (Pengawas Ruang & Proktor)
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'exam_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'subject_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'room_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'sesi'          => ['type' => 'INT', 'constraint' => 5, 'default' => 1],
            'nama_pengawas' => ['type' => 'VARCHAR', 'constraint' => 200],
            'nip'           => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'peran'         => ['type' => 'ENUM', 'constraint' => ['pengawas', 'proktor', 'teknisi'], 'default' => 'pengawas'],
            'kontak'        => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('exam_supervisors', true);

        // 3. Buat tabel exam_tokens (Token Rilis Ujian CBT)
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'exam_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'subject_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'token'        => ['type' => 'VARCHAR', 'constraint' => 10],
            'durasi_menit' => ['type' => 'INT', 'constraint' => 5, 'default' => 15],
            'expires_at'   => ['type' => 'DATETIME', 'null' => true],
            'created_by'   => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'is_active'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('exam_tokens', true);
    }

    public function down()
    {
        $this->forge->dropColumn('participants', ['sesi', 'password']);
        $this->forge->dropTable('exam_supervisors', true);
        $this->forge->dropTable('exam_tokens', true);
    }
}
