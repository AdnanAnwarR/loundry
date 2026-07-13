<?php

namespace App\Controllers;

// Mengimpor BaseController bawaan CodeIgniter beserta Model yang dibutuhkan untuk menyusun statistik
use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\LayananModel;
use App\Models\PesananModel;

/**
 * Controller untuk mengelola halaman ringkasan utama (Dashboard) Admin.
 * Berfungsi mengagregasi data dari berbagai tabel untuk disajikan dalam bentuk angka statistik, daftar ringkas, dan grafik.
 */
class AdminDashboardController extends BaseController
{
    // Variabel penampung objek model untuk akses database
    protected $userModel;
    protected $layananModel;
    protected $pesananModel;

    /**
     * Constructor untuk inisialisasi awal saat Controller dipanggil
     */
    public function __construct()
    {
        // Instansiasi model yang digunakan untuk menampilkan data ringkasan di dashboard
        $this->userModel    = new UserModel();
        $this->layananModel = new LayananModel();
        $this->pesananModel = new PesananModel();
    }

    /**
     * Tampilan utama dashboard admin dengan grafik pendapatan dan data statistik
     */
    public function dashboard()
    {
        // 1. Menghitung total keseluruhan baris pesanan laundry yang masuk di tabel database
        $totalBooking    = $this->pesananModel->countAll();
        
        // 2. Menghitung jumlah pesanan yang statusnya masih antre ('pending')
        $bookingPending  = $this->pesananModel->where('status', 'pending')->countAllResults();
        
        // 3. Menghitung jumlah pesanan yang sedang dikerjakan oleh staf ('proses')
        $bookingProses   = $this->pesananModel->where('status', 'proses')->countAllResults();
        
        // 4. Menghitung jumlah pesanan yang telah rampung dan siap diambil ('selesai')
        $bookingSelesai  = $this->pesananModel->where('status', 'selesai')->countAllResults();

        // 5. Menghitung akumulasi nominal laba kotor dari transaksi yang sudah sukses dikerjakan DAN sudah lunas dibayar
        $pendapatan = $this->pesananModel
            ->selectSum('pesanan.total_harga') // Menjumlahkan kolom total_harga
            ->join('pembayaran pem', 'pem.pesanan_id = pesanan.id') // Join dengan tabel pembayaran untuk cek validitas uang masuk
            ->where('pesanan.status', 'selesai') // Memfilter pesanan yang berstatus selesai
            ->where('pem.status_pembayaran', 'sudah_dibayar') // Memfilter pembayaran yang sudah berstatus lunas
            ->first(); // Mengambil baris pertama sebagai objek tunggal

        // 6. Menghitung total jumlah akun pelanggan yang berstatus aktif di dalam sistem
        $totalPelanggan = $this->userModel->where('role', 'pelanggan')->where('is_active', 1)->countAllResults();

        // 7. Mengambil daftar 5 produk/layanan laundry terlaris yang paling sering di-booking berdasarkan tabel detail pesanan
        $detailModel = new \App\Models\DetailPesananModel();
        $layananPopuler = $detailModel
            ->select('l.nama_layanan, COUNT(detail_pesanan.id) as total_pesan')
            ->join('layanan l', 'l.id = detail_pesanan.layanan_id')
            ->groupBy('detail_pesanan.layanan_id') // Kelompokkan berdasarkan id layanan
            ->orderBy('total_pesan', 'DESC') // Urutkan dari jumlah yang terbanyak
            ->limit(5) // Batasi hanya mengambil top 5 data saja
            ->findAll();

        // 8. Mengambil 10 transaksi terbaru yang masuk ke sistem untuk ditampilkan pada feed log dashboard
        $bookingTerbaru = $this->pesananModel
            ->select('pesanan.*, u.name as nama_pelanggan, j.tanggal')
            ->join('users u', 'u.id = pesanan.user_id')
            ->join('jadwal j', 'j.id = pesanan.jadwal_id')
            ->orderBy('pesanan.created_at', 'DESC')
            ->limit(10)
            ->findAll();

        // 9. Perulangan untuk memformat nama-nama layanan dari 10 booking terbaru tersebut menjadi teks string terpisah koma
        foreach ($bookingTerbaru as $booking) {
            $items = $detailModel
                ->select('l.nama_layanan')
                ->join('layanan l', 'l.id = detail_pesanan.layanan_id')
                ->where('detail_pesanan.pesanan_id', $booking->id)
                ->findAll();
            
            // Menggabungkan array nama layanan menjadi string tunggal (misal: "Cuci Karpet, Setrika Kilat")
            $booking->nama_layanan = implode(', ', array_column($items, 'nama_layanan'));
        }

        // 10. Pengumpulan data grafik (Chart) omzet pendapatan harian selama 7 hari ke belakang secara mundur
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            // Menghasilkan string tanggal (misal: 2026-07-13, 2026-07-12, dst.)
            $date = date('Y-m-d', strtotime("-$i days"));
            
            // Hitung total pendapatan masuk yang lunas pada tanggal tersebut
            $total = $this->pesananModel
                ->selectSum('pesanan.total_harga') 
                ->join('pembayaran pem', 'pem.pesanan_id = pesanan.id') 
                ->where('DATE(pesanan.created_at)', $date) // Filter presisi per hari berdasarkan tanggal dibuat
                ->where('pem.status_pembayaran', 'sudah_dibayar') 
                ->first(); 
                
            // Memasukkan label tanggal berformat cantik (misal: "13 Jul") dan nominal uangnya ke array chart
            $chartData[] = [
                'date'  => date('d M', strtotime($date)),
                'total' => $total->total_harga ?? 0 // Jika tidak ada pemasukan, set nilai default ke 0
            ];
        }

        // 11. Merender halaman dashboard dan melempar seluruh variabel penampung statistik ke view
        return view('admin/dashboard', [
            'title'          => 'Dashboard Admin',
            'totalBooking'   => $totalBooking,
            'bookingPending' => $bookingPending,
            'bookingProses'  => $bookingProses,
            'bookingSelesai' => $bookingSelesai,
            'pendapatan'     => $pendapatan->total_harga ?? 0, // Mengirim hasil akhir laba kotor
            'totalPelanggan' => $totalPelanggan,
            'layananPopuler' => $layananPopuler,
            'bookingTerbaru' => $bookingTerbaru,
            'chartData'      => $chartData, // Mengirim data array koordinat grafik
        ]);
    }
}