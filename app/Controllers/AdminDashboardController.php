<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\LayananModel;
use App\Models\PesananModel;

class AdminDashboardController extends BaseController
{
    protected $userModel;
    protected $layananModel;
    protected $pesananModel;

    public function __construct()
    {
        // Instansiasi model yang digunakan untuk dashboard
        $this->userModel    = new UserModel();
        $this->layananModel = new LayananModel();
        $this->pesananModel = new PesananModel();
    }

    /**
     * Tampilan utama dashboard admin dengan grafik pendapatan dan data statistik
     */
    public function dashboard()
    {
        // Statistik booking
        $totalBooking    = $this->pesananModel->countAll();
        // Menghitung jumlah booking pending dengan model query builder
        $bookingPending  = $this->pesananModel->where('status', 'pending')->countAllResults();
        // Menghitung jumlah booking proses dengan model query builder
        $bookingProses   = $this->pesananModel->where('status', 'proses')->countAllResults();
        // Menghitung jumlah booking selesai dengan model query builder
        $bookingSelesai  = $this->pesananModel->where('status', 'selesai')->countAllResults();

        // Total pendapatan (booking selesai + sudah dibayar) menggunakan Query Builder bawaan Model Pesanan dengan Join pembayaran
        $pendapatan = $this->pesananModel
            ->selectSum('pesanan.total_harga') // Menjumlahkan kolom total_harga
            ->join('pembayaran pem', 'pem.pesanan_id = pesanan.id') // Melakukan join dengan tabel pembayaran
            ->where('pesanan.status', 'selesai') // Memfilter pesanan yang berstatus selesai
            ->where('pem.status_pembayaran', 'sudah_dibayar') // Memfilter pembayaran yang sudah dibayar
            ->first(); // Mengambil baris pertama sebagai objek

        // Total pelanggan aktif
        $totalPelanggan = $this->userModel->where('role', 'pelanggan')->where('is_active', 1)->countAllResults();

        // Layanan terpopuler (top 5) menggunakan Query Builder bawaan Model DetailPesanan dengan Join layanan
        $detailModel = new \App\Models\DetailPesananModel();
        $layananPopuler = $detailModel
            ->select('l.nama_layanan, COUNT(detail_pesanan.id) as total_pesan')
            ->join('layanan l', 'l.id = detail_pesanan.layanan_id')
            ->groupBy('detail_pesanan.layanan_id')
            ->orderBy('total_pesan', 'DESC')
            ->limit(5)
            ->findAll();

        // Mengambil daftar booking terbaru (limit 10)
        $bookingTerbaru = $this->pesananModel
            ->select('pesanan.*, u.name as nama_pelanggan, j.tanggal')
            ->join('users u', 'u.id = pesanan.user_id')
            ->join('jadwal j', 'j.id = pesanan.jadwal_id')
            ->orderBy('pesanan.created_at', 'DESC')
            ->limit(10)
            ->findAll();

        // Gabungkan detail layanan untuk setiap booking terbaru
        foreach ($bookingTerbaru as $booking) {
            $items = $detailModel
                ->select('l.nama_layanan')
                ->join('layanan l', 'l.id = detail_pesanan.layanan_id')
                ->where('detail_pesanan.pesanan_id', $booking->id)
                ->findAll();
            
            $booking->nama_layanan = implode(', ', array_column($items, 'nama_layanan'));
        }

        // Data chart pendapatan 7 hari terakhir
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            // Total pendapatan per tanggal dengan join ke tabel pembayaran menggunakan Query Builder bawaan Model Pesanan
            $total = $this->pesananModel
                ->selectSum('pesanan.total_harga') // Menjumlahkan kolom total_harga
                ->join('pembayaran pem', 'pem.pesanan_id = pesanan.id') // Melakukan join dengan tabel pembayaran
                ->where('DATE(pesanan.created_at)', $date) // Memfilter berdasarkan tanggal dibuat
                ->where('pem.status_pembayaran', 'sudah_dibayar') // Memfilter pembayaran yang sudah lunas
                ->first(); // Mengambil baris pertama sebagai objek
            $chartData[] = [
                'date'  => date('d M', strtotime($date)),
                'total' => $total->total_harga ?? 0
            ];
        }

        return view('admin/dashboard', [
            'title'          => 'Dashboard Admin',
            'totalBooking'   => $totalBooking,
            'bookingPending' => $bookingPending,
            'bookingProses'  => $bookingProses,
            'bookingSelesai' => $bookingSelesai,
            'pendapatan'     => $pendapatan->total_harga ?? 0,
            'totalPelanggan' => $totalPelanggan,
            'layananPopuler' => $layananPopuler,
            'bookingTerbaru' => $bookingTerbaru,
            'chartData'      => $chartData,
        ]);
    }
}
