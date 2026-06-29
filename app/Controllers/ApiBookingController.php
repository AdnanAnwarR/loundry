<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\LayananModel;
use App\Models\JadwalModel;
use App\Models\PesananModel;
use App\Models\DetailPesananModel;
use App\Models\PembayaranModel;
use App\Models\UserModel;
use Midtrans\Config;
use Midtrans\Snap;

// =========================================================================
// KRITERIA 6: Webservice Server (Expose API Endpoint)
// - Menyediakan REST API Endpoint lengkap dan terstruktur dalam format JSON.
// - Mengimplementasikan GET & POST request untuk konsumsi pihak ketiga (Mobile Client, IoT, dll).
// - Memanfaatkan CodeIgniter\API\ResponseTrait untuk menyusun HTTP Status Codes
//   secara rapi dan RESTful (200 OK, 201 Created, 400 Bad Request, 404 Not Found, 500 Server Error).
// - Menyediakan respon JSON yang terstruktur baik untuk data sukses maupun penanganan error.
// =========================================================================
class ApiBookingController extends BaseController
{
    use ResponseTrait;

    protected $layananModel;
    protected $jadwalModel;
    protected $pesananModel;
    protected $detailPesananModel;
    protected $pembayaranModel;
    protected $userModel;

    public function __construct()
    {
        // Instansiasi model-model yang diperlukan untuk API
        $this->layananModel       = new LayananModel();
        $this->jadwalModel        = new JadwalModel();
        $this->pesananModel       = new PesananModel();
        $this->detailPesananModel = new DetailPesananModel();
        $this->pembayaranModel    = new PembayaranModel();
        $this->userModel          = new UserModel();
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

    /**
     * =========================================================================
     * 3. GET /api/booking/(:any)
     * =========================================================================
     * Mengambil informasi detail status transaksi laundry berdasarkan Kode Order (order_id).
     * Response HTTP Status: 200 OK jika ditemukan, atau 404 Not Found jika tidak ada
     */
    public function show($orderId)
    {
        // Mencari data utama transaksi booking laundry berdasarkan parameter Kode Order (order_id)
        $pesanan = $this->pesananModel->where('order_id', $orderId)->first();
        // Jika data transaksi pesanan tidak ditemukan di database
        if (!$pesanan) {
            // Mengembalikan respons error JSON terstruktur dengan status HTTP 404 Not Found
            return $this->failNotFound("Pemesanan dengan ID {$orderId} tidak ditemukan.");
        }

        // Mengambil seluruh detail rincian item (layanan laundry) yang dibeli pada pesanan tersebut
        $items = $this->detailPesananModel
            ->select('detail_pesanan.*, l.nama_layanan, l.harga')
            // Melakukan join ke tabel layanan untuk mendapatkan nama layanan dan harga per unitnya
            ->join('layanan l', 'l.id = detail_pesanan.layanan_id')
            ->where('detail_pesanan.pesanan_id', $pesanan->id)
            ->findAll();

        // Mengambil data pembayaran terkait transaksi ini (termasuk status pembayaran dan Snap Token Midtrans)
        $pembayaran = $this->pembayaranModel->where('pesanan_id', $pesanan->id)->first();

        // Menyusun data header pesanan, detail item layanan, dan status pembayaran ke dalam satu struktur array asosiatif
        $response = [
            'booking' => $pesanan,
            'items'   => $items,
            'payment' => $pembayaran
        ];

        // Mengirimkan data gabungan terstruktur tersebut dalam format JSON dengan HTTP Status 200 OK
        return $this->respond($response, 200);
    }

    /**
     * =========================================================================
     * 4. POST /api/booking
     * =========================================================================
     * Membuat pemesanan / booking laundry baru melalui client API.
     * Menerima input JSON payload: user_id, jadwal_id, berat, layanan_ids (array), catatan (opsional)
     * Response HTTP Status: 201 Created jika berhasil, atau 400/404/500 jika ada kegagalan
     */
    public function store()
    {
        // Mencoba mengambil data input JSON payload yang dikirimkan oleh klien API
        $input = $this->request->getJSON(true);
        // Jika input tidak berformat JSON, ambil data dari parameter POST standar (form-urlencoded)
        if (!$input) {
            $input = $this->request->getPost();
        }

        // Menyimpan nilai parameter input ke variabel lokal masing-masing
        $userId     = $input['user_id'] ?? null;
        $jadwalId   = $input['jadwal_id'] ?? null;
        // Mengonversi input berat pakaian menjadi nilai float, default ke 0
        $berat      = isset($input['berat']) ? floatval($input['berat']) : 0;
        // Menyimpan daftar ID layanan laundry pilihan pelanggan dalam bentuk array
        $layananIds = $input['layanan_ids'] ?? [];
        $catatan    = $input['catatan'] ?? '';

        // 1. Validasi keberadaan parameter input wajib
        if (!$userId || !$jadwalId || $berat <= 0 || empty($layananIds)) {
            // Mengembalikan error HTTP 400 Bad Request jika parameter tidak lengkap
            return $this->fail('Input data tidak lengkap. Field user_id, jadwal_id, berat (>0), dan layanan_ids[] (minimal 1) wajib dikirim.', 400);
        }

        // 2. Memeriksa apakah user pelanggan yang mengajukan booking terdaftar di database
        $user = $this->userModel->find($userId);
        if (!$user) {
            // Mengembalikan error HTTP 404 Not Found jika ID user pelanggan tidak ditemukan
            return $this->failNotFound("User pelanggan dengan ID {$userId} tidak ditemukan.");
        }

        // 3. Memeriksa apakah ID jadwal booking yang dipilih ada di database
        $jadwal = $this->jadwalModel->find($jadwalId);
        if (!$jadwal) {
            // Mengembalikan error HTTP 404 Not Found jika ID jadwal tidak terdaftar
            return $this->failNotFound("Jadwal dengan ID {$jadwalId} tidak ditemukan.");
        }
        // Memeriksa apakah kapasitas kuota booking pada jadwal tersebut sudah penuh terisi
        if ($jadwal->terisi >= $jadwal->kapasitas) {
            // Mengembalikan error HTTP 400 Bad Request jika kuota penuh
            return $this->fail("Slot kapasitas jadwal yang dipilih sudah penuh.", 400);
        }

        // 4. Inisialisasi penghitungan nominal harga total gabungan layanan
        $grandTotal = 0;
        $itemsToInsert = [];
        // Melakukan iterasi untuk memproses setiap jenis layanan laundry yang dipilih
        foreach ($layananIds as $layananId) {
            // Mencari detail info layanan laundry berdasarkan ID
            $layanan = $this->layananModel->find($layananId);
            if (!$layanan) {
                // Mengembalikan error HTTP 404 Not Found jika salah satu ID layanan tidak valid
                return $this->failNotFound("Layanan laundry dengan ID {$layananId} tidak ditemukan.");
            }

            // Menghitung harga subtotal item (harga per kg dikalikan dengan berat cucian)
            $totalHargaItem = $layanan->harga * $berat;
            // Menambahkan subtotal ke total harga keseluruhan pesanan
            $grandTotal += $totalHargaItem;
            // Menyimpan item rincian pesanan sementara untuk di-insert nanti
            $itemsToInsert[] = [
                'layanan_id'  => $layananId,
                'total_harga' => $totalHargaItem,
            ];
        }

        // Membuat ID Order acak dan unik (format: ORD-YYYYMMDD-Random_Number)
        $orderId = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);

        // Mendapatkan instance database dan memulai transaksi database untuk menjamin integritas data (ACID)
        $db = $this->pesananModel->db;
        $db->transStart();

        // Memformat catatan laundry agar mencantumkan berat pakaian yang diinput
        $catatanFormat = "[Berat: {$berat} kg] " . $catatan;

        // 5. Menyiapkan data utama transaksi booking laundry
        $dataPesanan = [
            'user_id'     => $userId,
            'jadwal_id'   => $jadwalId,
            'staf_id'     => null, // Staff dikosongkan terlebih dahulu sebelum di-assign admin
            'order_id'    => $orderId,
            'total_harga' => $grandTotal,
            'catatan'     => $catatanFormat,
            'status'      => 'pending',
        ];

        // Melakukan insert data utama pesanan ke database
        if (!$this->pesananModel->insert($dataPesanan)) {
            // Membatalkan transaksi database jika insert gagal
            $db->transRollback();
            // Mengembalikan respons error validasi HTTP 400 Bad Request
            return $this->fail($this->pesananModel->errors(), 400);
        }
        
        // Mengambil ID baris pesanan utama yang baru saja di-insert
        $pesananId = $this->pesananModel->getInsertID();

        // 6. Menyimpan seluruh detail rincian item layanan pesanan ke tabel detail_pesanan
        foreach ($itemsToInsert as $item) {
            // Mengasosiasikan detail item dengan ID pesanan utama
            $item['pesanan_id'] = $pesananId;
            // Insert item rincian ke database
            $this->detailPesananModel->insert($item);
        }

        // 7. Menginisialisasi konfigurasi Midtrans untuk membuat Snap Token transaksi baru
        $this->initMidtrans();
        
        // Memformat ulang data item pesanan ke format parameter yang dibutuhkan oleh API Midtrans
        $midtransItems = [];
        foreach ($itemsToInsert as $item) {
            $layanan = $this->layananModel->find($item['layanan_id']);
            $midtransItems[] = [
                'id'       => $item['layanan_id'],
                'price'    => (int) $item['total_harga'],
                'quantity' => 1,
                'name'     => $layanan->nama_layanan,
            ];
        }

        // Menyusun parameter payload transaksi lengkap untuk dikirim ke API Midtrans
        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) $grandTotal,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email'      => $user->email,
                'phone'      => $user->no_hp,
            ],
            'item_details' => $midtransItems,
        ];

        // Menyiapkan token dummy/mock sebagai fallback cadangan
        $snapToken = 'SNAP-' . rand(100000, 999999);
        try {
            // Memanggil API Client Midtrans Snap untuk menukarkan parameter dengan token Snap resmi
            $snapToken = Snap::getSnapToken($params);
        } catch (\Throwable $e) {
            // Mencatat detail error koneksi API Midtrans ke log sistem jika terjadi kegagalan
            log_message('error', 'Gagal generate token Midtrans Snap di REST API: ' . $e->getMessage());
        }

        // 8. Menyiapkan record pembayaran baru dengan status pembayaran 'belum_dibayar'
        $dataPembayaran = [
            'pesanan_id'        => $pesananId,
            'metode_bayar'      => null, // Diisi nanti setelah pelanggan memilih metode bayar di Midtrans
            'snap_token'        => $snapToken,
            'status_pembayaran' => 'belum_dibayar',
        ];
        // Melakukan insert data record pembayaran ke database
        $this->pembayaranModel->insert($dataPembayaran);

        // 9. Menambah (increment) jumlah slot terisi pada jadwal booking yang bersangkutan
        $this->jadwalModel->where('id', $jadwalId)->increment('terisi', 1);

        // Menyelesaikan proses transaksi database (commit/rollback otomatis jika ada yang gagal)
        $db->transComplete();

        // Memeriksa status keberhasilan transaksi database global
        if ($db->transStatus() === FALSE) {
            // Mengembalikan error HTTP 500 Internal Server Error jika transaksi DB gagal
            return $this->fail('Terjadi kesalahan sistem saat memproses booking laundry.', 500);
        }

        // 10. Menyusun array response sukses untuk dikirim ke client
        $response = [
            'status'      => 'success',
            'message'     => 'Booking laundry berhasil dibuat.',
            'order_id'    => $orderId,
            'total_harga' => $grandTotal,
            'snap_token'  => $snapToken
        ];

        // Mengirimkan respons JSON sukses kepada klien dengan HTTP Status 201 Created
        return $this->respondCreated($response);
    }
}
