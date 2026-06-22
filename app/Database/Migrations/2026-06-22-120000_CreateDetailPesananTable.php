<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDetailPesananTable extends Migration
{
    public function up()
    {
        // 1. Create detail_pesanan table
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pesanan_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
            ],
            'layanan_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
            ],
            'total_harga' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('pesanan_id', 'pesanan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('layanan_id', 'layanan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_pesanan');

        // 2. Drop layanan_id foreign key constraint and column from pesanan table
        try {
            $this->db->query("ALTER TABLE pesanan DROP FOREIGN KEY pesanan_layanan_id_foreign");
        } catch (\Throwable $e) {
            // Ignore if it fails or has different constraint name
        }
        
        $this->forge->dropColumn('pesanan', 'layanan_id');
    }

    public function down()
    {
        // 1. Re-add column layanan_id and foreign key to pesanan table
        $this->forge->addColumn('pesanan', [
            'layanan_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'after'      => 'user_id',
                'null'       => true,
            ]
        ]);

        try {
            $this->db->query("ALTER TABLE pesanan ADD CONSTRAINT pesanan_layanan_id_foreign FOREIGN KEY (layanan_id) REFERENCES layanan(id) ON DELETE CASCADE ON UPDATE CASCADE");
        } catch (\Throwable $e) {
            // Ignore if constraint already exists
        }

        // 2. Drop detail_pesanan table
        $this->forge->dropTable('detail_pesanan', true);
    }
}
