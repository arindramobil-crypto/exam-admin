<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'key' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('key', true);
        $this->forge->createTable('settings', true);

        // Seed default school settings
        $defaults = [
            'nama_sekolah'         => 'SMA / SMK NEGERI 1 CBT',
            'alamat_sekolah'       => 'Jl. Pendidikan No. 45, Kompleks Pendidikan',
            'kota'                 => 'Jakarta',
            'telepon'              => '(021) 555-1234',
            'email'                => 'info@sekolah-cbt.sch.id',
            'website'              => 'https://sekolah-cbt.sch.id',
            'nama_kepala_sekolah'  => 'Drs. H. Rahmat Hidayat, M.Pd.',
            'nip_kepala_sekolah'   => '19750101 200003 1 001',
            'nama_ketua_panitia'   => 'Budi Prasetyo, S.Kom., M.T.',
            'nip_ketua_panitia'    => '19850615 201001 1 008',
            'ttd_kartu_jabatan'    => 'Ketua Panitia Ujian',
            'logo'                 => '',
        ];

        $now = date('Y-m-d H:i:s');
        $batch = [];
        foreach ($defaults as $k => $v) {
            $batch[] = [
                'key'        => $k,
                'value'      => $v,
                'updated_at' => $now,
            ];
        }

        $this->db->table('settings')->insertBatch($batch);
    }

    public function down()
    {
        $this->forge->dropTable('settings', true);
    }
}
