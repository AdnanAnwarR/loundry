<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PesananModel;
use App\Models\JadwalModel;
use App\Models\PembayaranModel;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

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

    /**
     * Inisialisasi konfigurasi Midtrans
     */
    private function initMidtrans()
    {
        Config::$serverKey    = env('midtrans.serverKey') ?: 'SB-Mid-server-YOUR_SANDBOX_SERVER_KEY';
        Config::$clientKey    = env('midtrans.clientKey') ?: 'SB-Mid-client-YOUR_SANDBOX_CLIENT_KEY';
        Config::$isProduction = env('midtrans.isProduction') ?: false;
        Config::$isSanitized  = env('midtrans.isSanitized') ?: true;
        Config::$is3ds        = env('midtrans.is3ds') ?: true;
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

        // --- PENANGANAN GENERATOR SNAP TOKEN MIDTRANS ---
        if ($pembayaran && $pembayaran->status_pembayaran === 'belum_dibayar' && (empty($pembayaran->snap_token) || strpos($pembayaran->snap_token, 'SNAP-') === 0)) {
            $this->initMidtrans();
            
            // Mengambil data pelanggan
            $customer = $this->pesananModel->getPelanggan($pesanan->user_id);
            
            // Menyusun item details untuk payload Midtrans
            $item_details = [];
            foreach ($items as $item) {
                $item_details[] = [
                    'id'       => $item->layanan_id,
                    'price'    => (int) $item->total_harga,
                    'quantity' => 1,
                    'name'     => $item->nama_layanan,
                ];
            }
            
            // Menyusun parameter request Midtrans Snap
            $params = [
                'transaction_details' => [
                    'order_id'     => $orderId,
                    'gross_amount' => (int) $grandTotal,
                ],
                'customer_details' => [
                    'first_name' => $customer->name,
                    'email'      => $customer->email,
                    'phone'      => $customer->no_hp,
                ],
                'item_details' => $item_details,
            ];
            
            try {
                // Request token snap baru dari Midtrans
                $snapToken = Snap::getSnapToken($params);
                
                // Simpan token snap ke database
                $this->pembayaranModel->update($pembayaran->id, [
                    'snap_token' => $snapToken,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                // Segarkan data pembayaran setelah update
                $pembayaran = $this->pembayaranModel->find($pembayaran->id);
            } catch (\Throwable $e) {
                // Catat log error jika koneksi/kredensial Midtrans gagal dan gunakan fallback mock token yang sudah ada
                log_message('error', 'Gagal generate token Midtrans Snap: ' . $e->getMessage());
            }
        }

        // Menyusun data untuk dikirim ke view
        $data = [
            'title'      => 'Pembayaran Pesanan ' . $orderId,
            'orderId'    => $orderId,
            'items'      => $items,
            'grandTotal' => $grandTotal,
            'jadwal'     => $jadwal,
            'pembayaran' => $pembayaran,
            'clientKey'  => env('midtrans.clientKey') ?: 'SB-Mid-client-YOUR_SANDBOX_CLIENT_KEY'
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

        $pembayaran = $this->pembayaranModel->where('pesanan_id', $pesanan->id)->first();

        // Cek jika request datang dari redirect callback javascript snap.js
        $statusGet = $this->request->getGet('status');
        if ($statusGet && $pembayaran) {
            $resultJson = $this->request->getGet('result');
            $result = json_decode($resultJson);
            
            if ($statusGet === 'success') {
                $this->pembayaranModel->update($pembayaran->id, [
                    'metode_bayar'      => $result->payment_type ?? 'Midtrans',
                    'status_pembayaran' => 'sudah_dibayar',
                    'updated_at'        => date('Y-m-d H:i:s')
                ]);
                return redirect()->to('/user')->with('success', "Pembayaran untuk pesanan {$orderId} berhasil diproses! Admin akan meng-assign staff untuk segera memproses pakaian Anda.");
            } elseif ($statusGet === 'pending') {
                $this->pembayaranModel->update($pembayaran->id, [
                    'metode_bayar'      => $result->payment_type ?? 'Midtrans',
                    'status_pembayaran' => 'belum_dibayar',
                    'updated_at'        => date('Y-m-d H:i:s')
                ]);
                return redirect()->to('/user')->with('success', "Pembayaran untuk pesanan {$orderId} tertunda. Silakan selesaikan pembayaran sesuai instruksi di aplikasi e-wallet / VA bank Anda.");
            }
        }

        // --- RETRO COMPATIBILITY / MOCK MOCKUP FORM SUBMIT ---
        $metodeBayar = $this->request->getPost('metode_bayar') ?? 'Transfer Bank';

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

    /**
     * Webhook Notifikasi status pembayaran otomatis dikirim dari Server Midtrans (Public API)
     */
    public function midtransNotification()
    {
        $this->initMidtrans();
        
        try {
            $notif = new Notification();
            
            $transactionStatus = $notif->transaction_status;
            $paymentType       = $notif->payment_type;
            $orderId           = $notif->order_id;
            $fraudStatus       = $notif->fraud_status;
            
            // Cari data pesanan berdasarkan order_id
            $pesanan = $this->pesananModel->where('order_id', $orderId)->first();
            if (!$pesanan) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Pesanan tidak ditemukan'])->setStatusCode(404);
            }
            
            // Cari data pembayaran terkait
            $pembayaran = $this->pembayaranModel->where('pesanan_id', $pesanan->id)->first();
            if (!$pembayaran) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Data pembayaran tidak ditemukan'])->setStatusCode(404);
            }
            
            $statusPembayaran = 'belum_dibayar';
            $metodeBayar = $paymentType;
            
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $statusPembayaran = 'belum_dibayar';
                } else if ($fraudStatus == 'accept') {
                    $statusPembayaran = 'sudah_dibayar';
                }
            } else if ($transactionStatus == 'settlement') {
                $statusPembayaran = 'sudah_dibayar';
            } else if ($transactionStatus == 'pending') {
                $statusPembayaran = 'belum_dibayar';
            } else if ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
                $statusPembayaran = 'gagal';
            }
            
            // Update status pembayaran ke database
            $this->pembayaranModel->update($pembayaran->id, [
                'metode_bayar'      => $metodeBayar,
                'status_pembayaran' => $statusPembayaran,
                'updated_at'        => date('Y-m-d H:i:s')
            ]);
            
            return $this->response->setJSON(['status' => 'success', 'message' => 'Notifikasi Midtrans berhasil diproses']);
            
        } catch (\Throwable $e) {
            log_message('error', 'Gagal memproses Webhook Midtrans: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()])->setStatusCode(500);
        }
    }
}
