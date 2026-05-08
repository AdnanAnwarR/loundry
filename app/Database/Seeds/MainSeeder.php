<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // 1. Data Dummy Users
        $users = [
            [
                'name'       => 'Admin Utama',
                'email'      => 'admin@laundry.com',
                'no_hp'      => '081111111111',
                'role'       => 'admin',
                'password'   => password_hash('password123', PASSWORD_DEFAULT),
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Staff Kasir',
                'email'      => 'staff@laundry.com',
                'no_hp'      => '082222222222',
                'role'       => 'staff',
                'password'   => password_hash('password123', PASSWORD_DEFAULT),
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Budi Pelanggan',
                'email'      => 'budi@gmail.com',
                'no_hp'      => '083333333333',
                'role'       => 'pelanggan',
                'password'   => password_hash('password123', PASSWORD_DEFAULT),
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // 2. Data Dummy Layanan
        $layanan = [
            [
                'nama_layanan' => 'Cuci Kering Teratur',
                'harga'        => 5000.00,
                'durasi'       => 120, // menit
                'deskripsi'    => 'Mencuci pakaian hingga bersih dan dikeringkan secara sempurna.',
                'is_active'    => 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'nama_layanan' => 'Setrika Cepat',
                'harga'        => 4000.00,
                'durasi'       => 60, // menit
                'deskripsi'    => 'Menyetrika pakaian yang sudah bersih agar rapi tanpa lipatan.',
                'is_active'    => 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'nama_layanan' => 'Cuci + Setrika Premium',
                'harga'        => 8000.00,
                'durasi'       => 180, // menit
                'deskripsi'    => 'Layanan komplit mulai dari mencuci, mengeringkan, hingga menyetrika rapi.',
                'is_active'    => 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'nama_layanan' => 'Cuci Karpet Spesial',
                'harga'        => 20000.00,
                'durasi'       => 360, // menit
                'deskripsi'    => 'Pencucian karpet dengan formula khusus anti bakteri.',
                'is_active'    => 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ];

        // 3. Data Dummy Jadwal (Slot waktu pengantaran/pengambilan)
        $besok = date('Y-m-d', strtotime('+1 day'));
        $lusa = date('Y-m-d', strtotime('+2 days'));

        $jadwal = [
            ['tanggal' => $besok, 'slot_waktu' => 'Pagi (08:00 - 12:00)', 'kapasitas' => 10, 'terisi' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['tanggal' => $besok, 'slot_waktu' => 'Siang (13:00 - 17:00)', 'kapasitas' => 10, 'terisi' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['tanggal' => $lusa,  'slot_waktu' => 'Pagi (08:00 - 12:00)', 'kapasitas' => 10, 'terisi' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['tanggal' => $lusa,  'slot_waktu' => 'Siang (13:00 - 17:00)', 'kapasitas' => 10, 'terisi' => 0, 'created_at' => $now, 'updated_at' => $now],
        ];

        // Eksekusi Insert ke Database
        // Kosongkan tabel terlebih dahulu agar tidak duplicate (Opsional tapi direkomendasikan saat seeding awal)
        $this->db->table('users')->emptyTable();
        $this->db->table('layanan')->emptyTable();
        $this->db->table('jadwal')->emptyTable();

        // Insert Batch
        $this->db->table('users')->insertBatch($users);
        $this->db->table('layanan')->insertBatch($layanan);
        $this->db->table('jadwal')->insertBatch($jadwal);
    }
}
