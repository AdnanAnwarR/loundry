<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LayananModel;
use App\Models\JadwalModel;
use App\Models\PesananModel;
use App\Models\PembayaranModel;

class UserBookingController extends BaseController
{
    protected $layananModel;
    protected $jadwalModel;
    protected $pesananModel;
    protected $pembayaranModel;

    public function __construct()
    {
        // Instansiasi model yang digunakan untuk Booking
        $this->layananModel    = new LayananModel();
        $this->jadwalModel     = new JadwalModel();
        $this->pesananModel    = new PesananModel();
        $this->pembayaranModel = new PembayaranModel();
    }

    /**
     * Menampilkan Form Pembuatan Booking Laundry Baru
     */
    public function pesananBaru()
    {
        // Mengambil semua data layanan yang aktif di sistem menggunakan Query Builder Model Layanan
        $layanan = $this->layananModel->where('is_active', 1)->orderBy('nama_layanan', 'ASC')->findAll();

        // Mengambil semua jadwal booking yang kapasitasnya masih ada dan untuk tanggal hari ini atau masa depan
        $jadwal = $this->jadwalModel
            ->where('terisi < kapasitas') // Sisa slot kapasitas harus masih tersedia
            ->where('tanggal >=', date('Y-m-d')) // Tanggal booking minimal hari ini
            ->orderBy('tanggal', 'ASC') // Diurutkan dari tanggal terdekat
            ->orderBy('slot_waktu', 'ASC') // Diurutkan dari jam pagi ke siang
            ->findAll();

        // Menyusun data ke view
        $data = [
            'title'   => 'Buat Pesanan Laundry Baru',
            'layanan' => $layanan,
            'jadwal'  => $jadwal
        ];

        // Memuat view form pesanan baru
        return view('user/pesanan_baru', $data);
    }

    /**
     * Memproses Penyimpanan Data Booking Laundry Baru ke Database
     */
    public function pesananStore()
    {
        // Mendapatkan ID pelanggan yang sedang login
        $userId = session()->get('id');

        // Mengambil inputan form
        $layananIds = $this->request->getPost('layanan_ids');
        $berat      = floatval($this->request->getPost('berat'));
        $jadwalId   = $this->request->getPost('jadwal_id');
        $catatan    = $this->request->getPost('catatan');

        // Validasi input: Harus memilih minimal satu layanan, berat pakaian harus valid dan tidak melebihi 100 kg
        if (empty($layananIds) || $berat <= 0 || $berat > 100 || !$jadwalId) {
            return redirect()->back()->withInput()->with('error', 'Layanan, berat pakaian (maksimal 100 Kg), dan jadwal wajib dipilih/diisi dengan benar!');
        }

        // Ambil data jadwal dari database menggunakan Query Builder Model Jadwal
        $jadwal = $this->jadwalModel->find($jadwalId);
        if (!$jadwal || $jadwal->terisi >= $jadwal->kapasitas) {
            return redirect()->back()->withInput()->with('error', 'Jadwal yang dipilih sudah penuh, silakan pilih jadwal lain.');
        }

        // Generate ID Order Unik secara otomatis (Contoh: ORD-20260601-8392)
        $orderId = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);

        // Membuka transaksi database menggunakan DB instance dari model untuk memastikan integritas data (all-or-nothing)
        $db = $this->pesananModel->db;
        $db->transStart();

        // Menyimpan catatan awal dengan menyisipkan informasi berat pakaian ke dalamnya
        $catatanFormat = "[Berat: {$berat} kg] " . $catatan;

        // Hitung total harga seluruh layanan yang dipilih
        $grandTotal = 0;
        $itemsToInsert = [];
        foreach ($layananIds as $layananId) {
            $layanan = $this->layananModel->find($layananId);
            if (!$layanan) continue;

            $totalHargaItem = $layanan->harga * $berat;
            $grandTotal += $totalHargaItem;
            $itemsToInsert[] = [
                'layanan_id'  => $layananId,
                'total_harga' => $totalHargaItem,
            ];
        }

        // Simpan ke tabel pesanan (order header)
        $dataPesanan = [
            'user_id'     => $userId, // ID Pelanggan
            'jadwal_id'   => $jadwalId, // ID Jadwal booking
            'staf_id'     => null, // Staff dikosongkan dahulu sebelum di-assign admin
            'order_id'    => $orderId, // Kode order unik pemesanan
            'total_harga' => $grandTotal, // Total harga gabungan
            'catatan'     => $catatanFormat, // Catatan beserta info berat pakaian
            'status'      => 'pending', // Status awal booking laundry adalah pending
        ];

        $this->pesananModel->insert($dataPesanan);
        $pesananId = $this->pesananModel->getInsertID();

        // Simpan item layanan ke detail_pesanan
        $detailPesananModel = new \App\Models\DetailPesananModel();
        foreach ($itemsToInsert as $item) {
            $item['pesanan_id'] = $pesananId;
            $detailPesananModel->insert($item);
        }

        // Membuat data pembayaran awal untuk transaksi order ini
        $dataPembayaran = [
            'pesanan_id'        => $pesananId, // Mengaitkan ke ID pesanan
            'metode_bayar'      => null, // Belum ditentukan metode bayar
            'snap_token'        => 'SNAP-' . rand(100000, 999999), // Mock Snap Token untuk simulator
            'status_pembayaran' => 'belum_dibayar', // Status awal pembayaran belum dibayar
        ];

        // Menyimpan data pembayaran menggunakan Query Builder bawaan Model Pembayaran
        $this->pembayaranModel->insert($dataPembayaran);

        // Menambahkan counter jumlah terisi pada jadwal slot waktu tersebut menggunakan Query Builder bawaan Model Jadwal
        $this->jadwalModel->where('id', $jadwalId)->increment('terisi', 1);

        // Menyelesaikan transaksi database
        $db->transComplete();

        // Cek jika ada kegagalan transaksi
        if ($db->transStatus() === FALSE) {
            return redirect()->to('/user')->with('error', 'Terjadi kesalahan sistem saat membuat booking.');
        }

        // Redirect ke dashboard dengan petunjuk untuk melakukan pembayaran
        return redirect()->to('/user')->with('success', "Booking laundry {$orderId} berhasil dibuat! Silakan lakukan pembayaran agar pesanan diproses.");
    }

    
    public function pesananBatal($orderId)
    {
        // Mengambil ID user dari session
        $userId = session()->get('id');

        // Mengambil pesanan terkait menggunakan Query Builder Model Pesanan
        $pesanan = $this->pesananModel
            ->where('order_id', $orderId)
            ->where('user_id', $userId)
            ->first();

        if (!$pesanan) {
            return redirect()->to('/user')->with('error', 'Pesanan tidak ditemukan.');
        }

        // Mengambil data pembayaran terkait menggunakan Query Builder Model Pembayaran
        $pembayaran = $this->pembayaranModel->where('pesanan_id', $pesanan->id)->first();

        // Proteksi: Jika status pembayaran sudah_dibayar, maka pesanan TIDAK BISA dibatalkan
        if ($pembayaran && $pembayaran->status_pembayaran === 'sudah_dibayar') {
            return redirect()->to('/user')->with('error', 'Pesanan ini sudah dibayar dan sedang diproses, sehingga tidak dapat dibatalkan.');
        }

        // Mulai transaksi pembatalan
        $db = $this->pesananModel->db;
        $db->transStart();

        // Update status di tabel pesanan menjadi 'dibatalkan' menggunakan Query Builder Model Pesanan
        $this->pesananModel->update($pesanan->id, [
            'status'     => 'dibatalkan',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Mengupdate status pembayaran di tabel pembayaran menggunakan Query Builder Model Pembayaran
        if ($pembayaran) {
            $this->pembayaranModel->update($pembayaran->id, [
                'status_pembayaran' => 'gagal',
                'updated_at'        => date('Y-m-d H:i:s')
            ]);
        }

        // Mengurangi counter jumlah terisi pada jadwal slot waktu tersebut (memulihkan kapasitas) menggunakan Model Jadwal
        $this->jadwalModel->where('id', $pesanan->jadwal_id)->decrement('terisi', 1);

        // Menyelesaikan transaksi database
        $db->transComplete();

        // Redirect dengan pemberitahuan pembatalan berhasil
        return redirect()->to('/user')->with('success', "Pesanan laundry {$orderId} telah berhasil dibatalkan.");
    }
}
