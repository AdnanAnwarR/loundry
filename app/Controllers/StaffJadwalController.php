<?php

namespace App\Controllers;

// Mengimpor controller utama
use App\Controllers\BaseController;
// Mengimpor model Pesanan
use App\Models\PesananModel;

class StaffJadwalController extends BaseController
{
    /**
     * Menampilkan Jadwal Tugas Harian Staff
     */
    public function index()
    {
        // Mendapatkan ID staff yang sedang login dari session
        $staffId = session()->get('id');

        // Instansiasi model pesanan secara langsung untuk memakai Query Builder bawaan Model
        $pesananModel = new PesananModel();

        // Mengambil daftar tugas staff terpaginasi (1 row per order)
        $tugasStaff = $pesananModel
            ->select('pesanan.*, j.tanggal, j.slot_waktu, u.name as nama_pelanggan, u.no_hp')
            ->join('jadwal j', 'j.id = pesanan.jadwal_id')
            ->join('users u', 'u.id = pesanan.user_id')
            ->where('pesanan.staf_id', $staffId)
            ->orderBy('j.tanggal', 'ASC')
            ->orderBy('j.slot_waktu', 'ASC')
            ->paginate(10);

        // Gabungkan detail layanan untuk setiap tugas
        $detailModel = new \App\Models\DetailPesananModel();
        foreach ($tugasStaff as $tugas) {
            $items = $detailModel
                ->select('l.nama_layanan')
                ->join('layanan l', 'l.id = detail_pesanan.layanan_id')
                ->where('detail_pesanan.pesanan_id', $tugas->id)
                ->findAll();
            
            $tugas->tanggal_booking = $tugas->tanggal;
            $tugas->jam             = $tugas->slot_waktu;
            $tugas->tugas           = implode(', ', array_column($items, 'nama_layanan'));
            $tugas->no_hp_pelanggan = $tugas->no_hp;
            $tugas->catatan_pesanan = $tugas->catatan;
        }

        $data['tugasStaff'] = $tugasStaff;
        $data['pager'] = $pesananModel->pager;

        // Memuat view jadwal tugas staff
        return view('staff/jadwal', $data);
    }

    
    public function selesai($orderId)
    {
        // Memuat helper WhatsApp simulation yang sudah dibuat sebelumnya
        helper('whatsapp_helper');

        // Instansiasi Model Pesanan secara langsung untuk memakai Query Builder bawaan Model
        $pesananModel = new PesananModel();

        // Mendapatkan ID staff yang login
        $staffId = session()->get('id');

        // Mengambil data detail pesanan dan nomor HP pelanggan
        $pesanan = $pesananModel->select('pesanan.order_id, u.name as nama_pelanggan, u.no_hp')
            ->join('users u', 'u.id = pesanan.user_id')
            ->where('pesanan.order_id', $orderId)
            ->where('pesanan.staf_id', $staffId)
            ->first();

        // Proteksi: Jika pesanan tidak ditemukan atau bukan milik staff ini
        if (!$pesanan) {
            // Redirect kembali ke halaman jadwal tugas staff yang baru dengan pesan error
            return redirect()->to('/staff/jadwal-tugas')->with('error', 'Pesanan tidak ditemukan atau Anda tidak berhak memproses pesanan ini.');
        }

        // Mengupdate status pesanan menjadi selesai menggunakan Query Builder bawaan Model Pesanan
        $pesananModel->where('order_id', $orderId) // Filter order_id
            ->where('staf_id', $staffId) // Filter staf_id
            ->set([
                'status'     => 'selesai', // Ubah status ke selesai
                'updated_at' => date('Y-m-d H:i:s')
            ])
            ->update(); // Jalankan update query

        // Menyusun isi pesan WhatsApp otomatis kepada pelanggan
        $pesanWa = "Halo {$pesanan->nama_pelanggan}, pesanan laundry Anda dengan ID {$orderId} telah SELESAI dikerjakan. Silakan diambil di counter laundry kami. Terima kasih!";

        // Memanggil fungsi kirim_wa dari whatsapp_helper untuk mencatat simulasi log dan session alert
        kirim_wa($pesanan->no_hp, $pesanWa);

        // Redirect kembali ke halaman jadwal tugas staff dengan notifikasi sukses
        return redirect()->to('/staff/jadwal-tugas')->with('success', "Orderan {$orderId} telah diselesaikan! Pesan WA otomatis telah dikirim ke pelanggan.");
    }

    
    public function ambil($orderId)
    {
        // Instansiasi Model Pesanan secara langsung untuk memakai Query Builder bawaan Model
        $pesananModel = new PesananModel();

        // Mendapatkan ID staff yang login
        $staffId = session()->get('id');

        // Mengambil data detail pesanan untuk memverifikasi kepemilikan tugas
        $pesanan = $pesananModel->select('pesanan.order_id, u.name as nama_pelanggan, u.no_hp')
            ->join('users u', 'u.id = pesanan.user_id')
            ->where('pesanan.order_id', $orderId)
            ->where('pesanan.staf_id', $staffId)
            ->first();

        // Proteksi: Jika pesanan tidak ditemukan atau bukan milik staff ini
        if (!$pesanan) {
            // Redirect kembali ke halaman jadwal tugas staff yang baru dengan pesan error
            return redirect()->to('/staff/jadwal-tugas')->with('error', 'Pesanan tidak ditemukan atau Anda tidak berhak mengambil pesanan ini.');
        }

        // Mengupdate status pesanan menjadi proses menggunakan Query Builder bawaan Model Pesanan
        $pesananModel->where('order_id', $orderId) // Filter order_id
            ->where('staf_id', $staffId) // Filter staf_id
            ->set([
                'status'     => 'proses', // Ubah status ke proses (diambil)
                'updated_at' => date('Y-m-d H:i:s')
            ])
            ->update(); // Jalankan update query

        // Redirect kembali ke halaman jadwal tugas dengan pesan sukses
        return redirect()->to('/staff/jadwal-tugas')->with('success', "Pesanan {$orderId} berhasil diambil! Silakan kerjakan laundry.");
    }
}
