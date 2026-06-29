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

        // =========================================================================
        // KRITERIA 5: Integrasi Webservice Client (Konsumsi API Eksternal)
        // - Menggunakan SDK Midtrans PHP untuk mengonsumsi REST API Midtrans secara dinamis.
        // - Caching Data: Sebelum melakukan pemanggilan API, sistem memeriksa cache token 
        //   di database ($pembayaran->snap_token). Jika sudah ada, sistem TIDAK memanggil 
        //   API Midtrans ulang untuk efisiensi resource & performa cepat.
        // =========================================================================
        // Memeriksa apakah data pembayaran valid, berstatus belum dibayar, dan belum memiliki token snap asli dari Midtrans (caching check)
        if ($pembayaran && $pembayaran->status_pembayaran === 'belum_dibayar' && (empty($pembayaran->snap_token) || strpos($pembayaran->snap_token, 'SNAP-') === 0)) {
            // Memanggil metode inisialisasi kredensial (Client Key, Server Key) untuk koneksi ke API Midtrans
            $this->initMidtrans();
            
            // Mengambil informasi identitas pelanggan seperti nama, email, dan nomor HP dari database berdasarkan user_id pesanan
            $customer = $this->pesananModel->getPelanggan($pesanan->user_id);
            
            // Menginisialisasi array kosong untuk menyimpan daftar item layanan yang dipesan oleh pelanggan
            $item_details = [];
            // Melakukan perulangan untuk memformat setiap item pesanan sesuai dengan struktur parameter yang diminta oleh Midtrans
            foreach ($items as $item) {
                // Menambahkan detail item berupa ID layanan, total harga item (dikonversi ke integer), jumlah kuantitas (1), dan nama layanannya
                $item_details[] = [
                    'id'       => $item->layanan_id,
                    'price'    => (int) $item->total_harga,
                    'quantity' => 1,
                    'name'     => $item->nama_layanan,
                ];
            }
            
            // Menyusun array parameter konfigurasi transaksi lengkap untuk dikirimkan ke REST API Midtrans Snap
            $params = [
                // Mengatur ID pesanan unik dan nominal total belanja (gross amount) untuk divalidasi oleh Midtrans
                'transaction_details' => [
                    'order_id'     => $orderId,
                    'gross_amount' => (int) $grandTotal,
                ],
                // Mengatur informasi profile pembayar agar tampil pada formulir tagihan/invoice pembayaran Midtrans
                'customer_details' => [
                    'first_name' => $customer->name,
                    'email'      => $customer->email,
                    'phone'      => $customer->no_hp,
                ],
                // Melampirkan daftar rincian layanan laundry yang didefinisikan sebelumnya
                'item_details' => $item_details,
            ];
            
            // Menggunakan struktur penanganan kesalahan try-catch agar jika server Midtrans offline, web utama tidak ikut tumbang
            try {
                // =========================================================================
                // KRITERIA 5 (Lanjutan): Panggilan API & Error Handling
                // - Memanggil API Midtrans Snap untuk mendapatkan snap token.
                // - Ditangani dengan blok try-catch sebagai error handling agar kegagalan 
                //   koneksi pihak ketiga tidak merusak/meng-crash aplikasi web utama.
                // =========================================================================
                // Melakukan pemanggilan web service client HTTP request (API Client) ke Midtrans untuk menukarkan parameter dengan token transaksi Snap
                $snapToken = Snap::getSnapToken($params);
                
                // Menyimpan snap token resmi yang baru didapat ke dalam database pembayaran sebagai mekanisme data caching token
                $this->pembayaranModel->update($pembayaran->id, [
                    'snap_token' => $snapToken,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                // Mengambil ulang (refresh) data pembayaran terbaru dari database setelah token snap diperbarui
                $pembayaran = $this->pembayaranModel->find($pembayaran->id);
            } catch (\Throwable $e) {
                // Mencatat pesan kesalahan detail koneksi API Midtrans ke dalam log sistem CodeIgniter 4 sebagai bentuk error handling
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
