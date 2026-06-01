<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PesananModel;

class UserDashboardController extends BaseController
{
    protected $pesananModel;

    public function __construct()
    {
        // Instansiasi model yang dibutuhkan untuk dashboard user
        $this->pesananModel = new PesananModel();
    }

    /**
     * Dashboard Pelanggan - Menampilkan daftar booking aktif dan riwayat pesanan
     */
    public function index()
    {
        // Mengambil ID user dari data session login
        $userId = session()->get('id');

        // Mengambil daftar booking pelanggan terpaginasi langsung dari method model
        $bookings = $this->pesananModel->getBookingsByUser($userId);

        // Menggunakan Query Builder dari Model Pesanan untuk menghitung pengeluaran bulanan yang sudah lunas dibayar
        $totalPengeluaran = $this->pesananModel
            ->selectSum('pesanan.total_harga') // Menjumlahkan harga pesanan
            ->join('pembayaran pem', 'pem.pesanan_id = pesanan.id') // Join ke pembayaran
            ->where('pesanan.user_id', $userId) // Milik user login
            ->where('pem.status_pembayaran', 'sudah_dibayar') // Pembayaran sudah lunas
            ->where('MONTH(pesanan.created_at)', date('m')) // Bulan saat ini
            ->where('YEAR(pesanan.created_at)', date('Y')) // Tahun saat ini
            ->first(); // Ambil satu baris hasil

        // Menyusun data untuk dikirim ke view
        $data = [
            'title'            => 'Dashboard Pelanggan', // Judul halaman
            'bookings'         => $bookings, // Variabel daftar pesanan terpaginasi
            'pager'            => $this->pesananModel->pager, // Objek pager untuk link halaman
            'totalPengeluaran' => $totalPengeluaran->total_harga ?? 0 // Total pengeluaran (default 0 jika null)
        ];

        // Memuat view index dashboard pelanggan dengan data pesanan yang riil
        return view('user/index', $data);
    }
}
