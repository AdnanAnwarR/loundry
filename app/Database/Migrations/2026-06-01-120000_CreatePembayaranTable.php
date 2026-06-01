<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePembayaranTable extends Migration
{
    public function up()
    {
        // Mendefinisikan kolom tabel pembayaran sesuai ERD
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
            'metode_bayar' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'snap_token' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'status_pembayaran' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => 'belum_dibayar',
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

        // Primary key
        $this->forge->addKey('id', true);

        // Foreign Key ke tabel pesanan
        $this->forge->addForeignKey('pesanan_id', 'pesanan', 'id', 'CASCADE', 'CASCADE');

        // Indeks untuk optimasi query
        $this->forge->addKey('status_pembayaran');
        $this->forge->addKey('pesanan_id');

        // Eksekusi pembuatan tabel
        $this->forge->createTable('pembayaran');
    }

    public function down()
    {
        // Menghapus tabel pembayaran jika rollback
        $this->forge->dropTable('pembayaran', true);
    }
}
