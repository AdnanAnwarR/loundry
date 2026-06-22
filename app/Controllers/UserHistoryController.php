<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PesananModel;

class UserHistoryController extends BaseController
{
    protected $pesananModel;

    public function __construct()
    {
        // Instansiasi model yang digunakan untuk history
        $this->pesananModel = new PesananModel();
    }

    /**
     * Menampilkan riwayat pesanan (history) pelanggan
     */
    public function history()
    {
        $userId = session()->get('id');

        // Mengambil daftar riwayat pesanan (history) pelanggan terpaginasi (1 row per order)
        $bookings = $this->pesananModel
            ->select('pesanan.*, j.tanggal, j.slot_waktu, pem.status_pembayaran, s.name as nama_staff')
            ->join('jadwal j', 'j.id = pesanan.jadwal_id')
            ->join('pembayaran pem', 'pem.pesanan_id = pesanan.id', 'left')
            ->join('users s', 's.id = pesanan.staf_id', 'left')
            ->where('pesanan.user_id', $userId)
            ->whereIn('pesanan.status', ['selesai', 'dibatalkan', 'ditolak'])
            ->orderBy('pesanan.created_at', 'DESC')
            ->paginate(10);

        // Gabungkan detail layanan untuk setiap booking
        $detailModel = new \App\Models\DetailPesananModel();
        foreach ($bookings as $booking) {
            $items = $detailModel
                ->select('l.nama_layanan')
                ->join('layanan l', 'l.id = detail_pesanan.layanan_id')
                ->where('detail_pesanan.pesanan_id', $booking->id)
                ->findAll();
            
            $booking->layanan_list = implode(', ', array_column($items, 'nama_layanan'));
            $booking->grand_total   = $booking->total_harga;
            $booking->status_pesanan = $booking->status;
        }

        $data = [
            'title'    => 'Riwayat Pesanan',
            'bookings' => $bookings,
            'pager'    => $this->pesananModel->pager
        ];

        return view('user/history', $data);
    }

    
    public function pesananUlasan($orderId)
    {
        $userId = session()->get('id');

        // Validasi input
        $rules = [
            'rating' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]',
            'ulasan' => 'required|min_length[5]|max_length[1000]'
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('errors', $this->validator->getErrors());
            return redirect()->back();
        }

        $rating = intval($this->request->getPost('rating'));
        $ulasan = $this->request->getPost('ulasan');

        // Update rating & ulasan untuk seluruh item pesanan dengan order_id yang sama
        $this->pesananModel->where('order_id', $orderId)
            ->where('user_id', $userId)
            ->set([
                'rating'     => $rating,
                'ulasan'     => $ulasan,
                'updated_at' => date('Y-m-d H:i:s')
            ])
            ->update();

        session()->setFlashdata('success', 'Terima kasih atas ulasan dan penilaian bintang yang Anda berikan!');
        return redirect()->back();
    }
}
