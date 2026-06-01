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

        // Layanan terpopuler (top 5) menggunakan Query Builder bawaan Model Pesanan dengan Join layanan
        $layananPopuler = $this->pesananModel
            ->select('l.nama_layanan, COUNT(pesanan.id) as total_pesan') // Memilih kolom nama layanan dan menghitung jumlah pesanan
            ->join('layanan l', 'l.id = pesanan.layanan_id') // Melakukan join dengan tabel layanan
            ->groupBy('pesanan.layanan_id') // Mengelompokkan berdasarkan id layanan
            ->orderBy('total_pesan', 'DESC') // Mengurutkan dari total pesan terbanyak
            ->limit(5) // Membatasi hasil hanya 5 layanan terpopuler
            ->findAll(); // Mengambil semua hasil sebagai array objek

        // Mengambil daftar booking terbaru (limit 10) menggunakan method query terenkapsulasi di dalam model
        $bookingTerbaru = $this->pesananModel->getRecentBookings(10); // Mendapatkan 10 data booking terbaru

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
