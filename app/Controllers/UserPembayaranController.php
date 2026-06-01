<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PesananModel;
use App\Models\JadwalModel;
use App\Models\PembayaranModel;

class UserPembayaranController extends BaseController
{
    protected $pesananModel;
    protected $jadwalModel;
    protected $pembayaranModel;

    public function __construct()
    {
        // Instansiasi model yang digunakan untuk pembayaran
        $this->pesananModel    = new PesananModel();
        $this->jadwalModel     = new JadwalModel();
        $this->pembayaranModel = new PembayaranModel();
    }

    
    public function pesananBayar($orderId)
    {
        // Mengambil ID user dari session login
        $userId = session()->get('id');

        // Mengambil seluruh baris pesanan dengan order_id tersebut menggunakan method model
        $items = $this->pesananModel->getOrderItems($orderId, $userId); // Mendapatkan item pesanan terenkapsulasi

        // Jika data pesanan kosong, kembalikan ke dashboard dengan error
        if (empty($items)) {
            return redirect()->to('/user')->with('error', 'Pesanan tidak ditemukan.');
        }

        // Mengambil informasi jadwal dari pesanan pertama menggunakan Query Builder Model Jadwal
        $firstItem = $items[0];
        $jadwal = $this->jadwalModel->find($firstItem->jadwal_id);

        // Mengambil informasi pembayaran terkait menggunakan Query Builder Model Pembayaran
        $pembayaran = $this->pembayaranModel->where('pesanan_id', $firstItem->id)->first();

        // Menghitung total nominal pesanan
        $grandTotal = 0;
        foreach ($items as $item) {
            $grandTotal += $item->total_harga;
        }

        // Menyusun data untuk dikirim ke view
        $data = [
            'title'      => 'Pembayaran Pesanan ' . $orderId,
            'orderId'    => $orderId,
            'items'      => $items,
            'grandTotal' => $grandTotal,
            'jadwal'     => $jadwal,
            'pembayaran' => $pembayaran
        ];

        // Memuat view halaman pembayaran
        return view('user/bayar', $data);
    }

    
    public function pesananProsesBayar($orderId)
    {
        // Mengambil ID user dari session
        $userId = session()->get('id');

        // Mengambil satu pesanan pertama menggunakan Query Builder Model Pesanan
        $pesanan = $this->pesananModel
            ->where('order_id', $orderId)
            ->where('user_id', $userId)
            ->first();

        // Jika pesanan tidak valid
        if (!$pesanan) {
            return redirect()->to('/user')->with('error', 'Pesanan tidak ditemukan.');
        }

        // Mendapatkan metode pembayaran yang dipilih pelanggan
        $metodeBayar = $this->request->getPost('metode_bayar') ?? 'Transfer Bank';

        // Mengambil data pembayaran terkait menggunakan Query Builder Model Pembayaran
        $pembayaran = $this->pembayaranModel->where('pesanan_id', $pesanan->id)->first();

        if ($pembayaran) {
            // Mengupdate status pembayaran di tabel pembayaran menggunakan Query Builder Model Pembayaran
            $this->pembayaranModel->update($pembayaran->id, [
                'metode_bayar'      => $metodeBayar,
                'status_pembayaran' => 'sudah_dibayar', // Mengubah status menjadi lunas
                'updated_at'        => date('Y-m-d H:i:s')
            ]);
        }

        // Redirect ke dashboard user dengan notifikasi sukses
        return redirect()->to('/user')->with('success', "Pembayaran untuk pesanan {$orderId} berhasil diproses! Admin akan meng-assign staff untuk segera memproses pakaian Anda.");
    }
}
