<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PesananModel;
use App\Models\LayananModel;
use App\Models\UserModel;

class AdminBookingController extends BaseController
{
    protected $pesananModel;
    protected $layananModel;
    protected $userModel;

    public function __construct()
    {
        // Instansiasi model yang digunakan untuk Booking
        $this->pesananModel = new PesananModel();
        $this->layananModel = new LayananModel();
        $this->userModel    = new UserModel();
    }

    /**
     * Menampilkan daftar booking pelanggan dengan filter status, tanggal, dan layanan
     */
    public function bookingIndex()
    {
        // Mengambil parameter filter dari query string
        $statusFilter  = $this->request->getGet('status');
        $tanggalFilter = $this->request->getGet('tanggal');
        $layananFilter = $this->request->getGet('layanan_id');

        $pesananQuery = $this->pesananModel
            ->select('pesanan.*, u.name as nama_pelanggan, j.tanggal, j.slot_waktu, pem.status_pembayaran')
            ->join('users u', 'u.id = pesanan.user_id')
            ->join('jadwal j', 'j.id = pesanan.jadwal_id')
            ->join('pembayaran pem', 'pem.pesanan_id = pesanan.id', 'left');

        if ($statusFilter) {
            $pesananQuery->where('pesanan.status', $statusFilter);
        }

        if ($tanggalFilter) {
            $pesananQuery->where('j.tanggal', $tanggalFilter);
        }

        if ($layananFilter) {
            $pesananQuery->where("pesanan.id IN (SELECT dp_sub.pesanan_id FROM detail_pesanan dp_sub WHERE dp_sub.layanan_id = " . (int)$layananFilter . ")");
        }

        $booking = $pesananQuery
            ->orderBy('pesanan.created_at', 'DESC')
            ->paginate(10);

        // Gabungkan detail layanan untuk setiap booking
        $detailModel = new \App\Models\DetailPesananModel();
        foreach ($booking as $b) {
            $items = $detailModel
                ->select('l.nama_layanan')
                ->join('layanan l', 'l.id = detail_pesanan.layanan_id')
                ->where('detail_pesanan.pesanan_id', $b->id)
                ->findAll();
            
            $b->nama_layanan = implode(', ', array_column($items, 'nama_layanan'));
        }

        // Ambil semua layanan untuk dropdown filter
        $layanan  = $this->layananModel->findAll();

        // Memuat view dengan data yang telah difilter dan ditarik dari database beserta pager paginasi
        return view('admin/booking/index', [
            'title'         => 'Kelola Booking',
            'booking'       => $booking,
            'pager'         => $this->pesananModel->pager, // Objek pager untuk link halaman
            'layanan'       => $layanan,
            'statusFilter'  => $statusFilter,
            'tanggalFilter' => $tanggalFilter,
            'layananFilter' => $layananFilter,
        ]);
    }

    /**
     * Menampilkan detail item booking pesanan dan opsi assignment staff
     */
    public function bookingShow($id)
    {
        // Ambil data detail pesanan utama
        $booking = $this->pesananModel
            ->select('pesanan.*, u.name as nama_pelanggan, u.email, u.no_hp, j.tanggal, j.slot_waktu, s.name as nama_staff, pem.status_pembayaran, pem.metode_bayar')
            ->join('users u', 'u.id = pesanan.user_id')
            ->join('jadwal j', 'j.id = pesanan.jadwal_id')
            ->join('users s', 's.id = pesanan.staf_id', 'left')
            ->join('pembayaran pem', 'pem.pesanan_id = pesanan.id', 'left')
            ->where('pesanan.id', $id)
            ->first();

        // Jika pesanan tidak ditemukan
        if (!$booking) {
            return redirect()->to('/admin/booking')->with('error', 'Booking tidak ditemukan.');
        }

        // Ambil semua item pesanan dari detail_pesanan
        $detailModel = new \App\Models\DetailPesananModel();
        $items = $detailModel
            ->select('detail_pesanan.*, l.nama_layanan, l.harga')
            ->join('layanan l', 'l.id = detail_pesanan.layanan_id')
            ->where('detail_pesanan.pesanan_id', $booking->id)
            ->findAll();

        // Hitung total harga gabungan dari seluruh item layanan dalam pesanan ini
        $grandTotal = 0;
        foreach ($items as $item) {
            $grandTotal += $item->total_harga; // Menambahkan total harga item ke grandTotal
        }

        // Ambil semua staff aktif untuk dropdown penugasan (assign staff)
        $allStaff = $this->userModel->where('role', 'staff')->where('is_active', 1)->findAll();

        // Memuat view detail booking dengan detail seluruh item pesanan
        return view('admin/booking/show', [
            'title'      => 'Detail Booking', // Judul halaman
            'booking'    => $booking, // Data pesanan utama
            'items'      => $items, // Daftar item layanan dalam pesanan ini
            'grandTotal' => $grandTotal, // Total harga gabungan seluruh layanan
            'allStaff'   => $allStaff, // Daftar staff pelaksana
        ]);
    }

    /**
     * Konfirmasi pesanan / booking masuk
     */
    public function bookingKonfirmasi($id)
    {
        // Cari data pesanan terlebih dahulu untuk mendapatkan order_id
        $pesanan = $this->pesananModel->find($id);
        
        if ($pesanan) {
            // Mengupdate status pesanan untuk seluruh item dengan order_id yang sama menjadi dikonfirmasi menggunakan Query Builder Model
            $this->pesananModel->where('order_id', $pesanan->order_id) // Filter berdasarkan order_id yang sama
               ->set([
                   'status'     => 'dikonfirmasi', // Mengubah status menjadi dikonfirmasi
                   'updated_at' => date('Y-m-d H:i:s') // Mengubah waktu update
               ])
               ->update(); // Menjalankan perintah update pada model
        }
        
        // Redirect kembali ke halaman detail dengan notifikasi sukses
        return redirect()->to('/admin/booking/' . $id)->with('success', 'Booking berhasil dikonfirmasi!');
    }

    /**
     * Menolak pesanan / booking masuk dengan menyertakan alasan penolakan
     */
    public function bookingTolak($id)
    {
        // Mengambil input alasan penolakan dari form
        $alasan = $this->request->getPost('alasan') ?? 'Ditolak oleh admin.';
        
        // Cari data pesanan terlebih dahulu untuk mendapatkan order_id
        $pesanan = $this->pesananModel->find($id);
        
        if ($pesanan) {
            // Mengupdate status pesanan untuk seluruh item dengan order_id yang sama menjadi ditolak menggunakan Query Builder Model
            $this->pesananModel->where('order_id', $pesanan->order_id) // Filter berdasarkan order_id yang sama
               ->set([
                   'status'     => 'ditolak', // Mengubah status menjadi ditolak
                   'catatan'    => $alasan, // Menyimpan alasan penolakan ke catatan
                   'updated_at' => date('Y-m-d H:i:s') // Mengubah waktu update
               ])
               ->update(); // Menjalankan perintah update pada model

            // Mengupdate status pembayaran terkait menjadi gagal
            $pembayaranModel = new \App\Models\PembayaranModel();
            $pembayaran = $pembayaranModel->where('pesanan_id', $pesanan->id)->first();
            if ($pembayaran) {
                $pembayaranModel->update($pembayaran->id, [
                    'status_pembayaran' => 'gagal',
                    'updated_at'        => date('Y-m-d H:i:s')
                ]);
            }
        }
        
        // Redirect kembali dengan notifikasi sukses penolakan
        return redirect()->to('/admin/booking/' . $id)->with('success', 'Booking telah ditolak.');
    }

    /**
     * Menugaskan staff pelaksana ke order pesanan
     */
    public function bookingAssignStaff($id)
    {
        // Mengambil input staf_id dari form
        $stafId = $this->request->getPost('staf_id');
        
        // Cari data pesanan terlebih dahulu untuk mendapatkan order_id
        $pesanan = $this->pesananModel->find($id);
        
        if ($pesanan) {
            // Mengupdate staf_id untuk seluruh item dengan order_id yang sama menggunakan Query Builder Model Pesanan
            $this->pesananModel->where('order_id', $pesanan->order_id) // Filter berdasarkan order_id yang sama
               ->set([
                   'staf_id'    => $stafId, // Mengisi kolom staf_id dengan staff yang dipilih
                   'updated_at' => date('Y-m-d H:i:s') // Mengubah waktu update
               ])
               ->update(); // Menjalankan perintah update pada model
        }
        
        // Redirect kembali dengan notifikasi sukses penugasan staff
        return redirect()->to('/admin/booking/' . $id)->with('success', 'Staff berhasil ditugaskan! Menunggu staff mengambil orderan.');
    }
}
