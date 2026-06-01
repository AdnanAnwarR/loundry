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

        // Mengambil daftar riwayat pesanan (history) pelanggan terpaginasi langsung dari method model
        $bookings = $this->pesananModel->getBookingsHistoryByUser($userId);

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
