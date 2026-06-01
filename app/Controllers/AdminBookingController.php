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

        // Mengambil daftar booking pelanggan terpaginasi langsung dari method model
        $booking = $this->pesananModel->getAllBookings($statusFilter, $tanggalFilter, $layananFilter);
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
        // Ambil satu baris data pesanan berdasarkan ID beserta relasi lengkap menggunakan method model
        $booking = $this->pesananModel->getBookingDetail($id); // Mendapatkan data pesanan utama

        // Jika pesanan tidak ditemukan
        if (!$booking) return redirect()->to('/admin/booking')->with('error', 'Booking tidak ditemukan.'); // Redirect jika kosong

        // Ambil semua item pesanan dengan order_id yang sama menggunakan method model
        $items = $this->pesananModel->getOrderItems($booking->order_id); // Mendapatkan data item pesanan

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
