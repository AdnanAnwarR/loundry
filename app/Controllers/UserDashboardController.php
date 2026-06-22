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

        // Mengambil daftar booking pelanggan terpaginasi (1 row per order)
        $bookings = $this->pesananModel
            ->select('pesanan.*, j.tanggal, j.slot_waktu, pem.status_pembayaran, s.name as nama_staff')
            ->join('jadwal j', 'j.id = pesanan.jadwal_id')
            ->join('pembayaran pem', 'pem.pesanan_id = pesanan.id', 'left')
            ->join('users s', 's.id = pesanan.staf_id', 'left')
            ->where('pesanan.user_id', $userId)
            ->whereNotIn('pesanan.status', ['selesai', 'dibatalkan', 'ditolak'])
            ->orderBy('pesanan.created_at', 'DESC')
            ->paginate(10);

        // Gabungkan detail layanan untuk setiap booking
        $detailModel = new \App\Models\DetailPesananModel();
        foreach ($bookings as $booking) {
            $items = $detailModel
                ->select('l.nama_layanan')
                ->join('layanan l', 'l.id = detail_pesanan.layanan_id')
                ->where('detail_pesanan.pesanan_id', $booking->id)
                ->findAll();
            
            $booking->layanan_list = implode(', ', array_column($items, 'nama_layanan'));
            $booking->grand_total   = $booking->total_harga;
            $booking->status_pesanan = $booking->status;
        }

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
