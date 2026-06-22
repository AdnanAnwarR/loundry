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

        // Mengambil pesanan dengan order_id tersebut
        $pesanan = $this->pesananModel
            ->where('order_id', $orderId)
            ->where('user_id', $userId)
            ->first();

        // Jika data pesanan kosong, kembalikan ke dashboard dengan error
        if (!$pesanan) {
            return redirect()->to('/user')->with('error', 'Pesanan tidak ditemukan.');
        }

        // Fetch detail items from detail_pesanan table
        $detailModel = new \App\Models\DetailPesananModel();
        $items = $detailModel
            ->select('detail_pesanan.*, l.nama_layanan, l.harga')
            ->join('layanan l', 'l.id = detail_pesanan.layanan_id')
            ->where('detail_pesanan.pesanan_id', $pesanan->id)
            ->findAll();

        // Inject catatan property into items for view compatibility
        foreach ($items as $item) {
            $item->catatan = $pesanan->catatan;
        }

        // Mengambil informasi jadwal dari pesanan
        $jadwal = $this->jadwalModel->find($pesanan->jadwal_id);

        // Mengambil informasi pembayaran terkait menggunakan Query Builder Model Pembayaran
        $pembayaran = $this->pembayaranModel->where('pesanan_id', $pesanan->id)->first();

        // Menghitung total nominal pesanan
        $grandTotal = $pesanan->total_harga;

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
