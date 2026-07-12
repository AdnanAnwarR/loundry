<?php

namespace App\Controllers;

// Mengimpor BaseController bawaan CodeIgniter beserta Model yang dibutuhkan
use App\Controllers\BaseController;
use App\Models\PesananModel;
use App\Models\LayananModel;
use App\Models\UserModel;

/**
 * Controller untuk mengelola pesanan masuk (booking) laundry oleh Admin.
 * Mengatur filter data, detail transaksi, konfirmasi, penolakan, hingga pembagian tugas staf.
 */
class AdminBookingController extends BaseController
{
    // Variabel penampung instance model
    protected $pesananModel;
    protected $layananModel;
    protected $userModel;

    /**
     * Constructor untuk inisialisasi awal saat Controller dipanggil
     */
    public function __construct()
    {
        // Instansiasi semua model yang digunakan untuk manajemen Booking
        $this->pesananModel = new PesananModel();
        $this->layananModel = new LayananModel();
        $this->userModel    = new UserModel();
    }

    /**
     * Menampilkan daftar booking pelanggan dengan filter status, tanggal, dan layanan
     */
    public function bookingIndex()
    {
        // 1. Mengambil parameter filter dari URL query string (misal: ?status=pending)
        $statusFilter  = $this->request->getGet('status');
        $tanggalFilter = $this->request->getGet('tanggal');
        $layananFilter = $this->request->getGet('layanan_id');

        // 2. Menyusun query dasar: Menggabungkan tabel pesanan dengan users (pelanggan), jadwal, dan pembayaran
        $pesananQuery = $this->pesananModel
            ->select('pesanan.*, u.name as nama_pelanggan, j.tanggal, j.slot_waktu, pem.status_pembayaran')
            ->join('users u', 'u.id = pesanan.user_id')
            ->join('jadwal j', 'j.id = pesanan.jadwal_id')
            ->join('pembayaran pem', 'pem.pesanan_id = pesanan.id', 'left');

        // 3. Menerapkan kondisi filter dinamis jika dipilih oleh admin
        if ($statusFilter) {
            $pesananQuery->where('pesanan.status', $statusFilter);
        }

        if ($tanggalFilter) {
            $pesananQuery->where('j.tanggal', $tanggalFilter);
        }

        if ($layananFilter) {
            // Menggunakan subquery untuk memfilter pesanan yang mengandung id layanan tertentu
            $pesananQuery->where("pesanan.id IN (SELECT dp_sub.pesanan_id FROM detail_pesanan dp_sub WHERE dp_sub.layanan_id = " . (int)$layananFilter . ")");
        }

        // 4. Mengeksekusi query dengan urutan data terbaru dan membaginya 10 data per halaman
        $booking = $pesananQuery
            ->orderBy('pesanan.created_at', 'DESC')
            ->paginate(10);

        // 5. Menggabungkan string nama layanan (misal: "Cuci Kering, Setrika") untuk ditampilkan di baris tabel
        $detailModel = new \App\Models\DetailPesananModel();
        foreach ($booking as $b) {
            $items = $detailModel
                ->select('l.nama_layanan')
                ->join('layanan l', 'l.id = detail_pesanan.layanan_id')
                ->where('detail_pesanan.pesanan_id', $b->id)
                ->findAll();
            
            // Mengubah array nama layanan menjadi satu baris string yang dipisahkan koma
            $b->nama_layanan = implode(', ', array_column($items, 'nama_layanan'));
        }

        // 6. Mengambil data semua jenis layanan untuk opsi dropdown filter di view
        $layanan  = $this->layananModel->findAll();

        // 7. Memuat halaman view dan melempar semua data siap pakai ke dalamnya
        return view('admin/booking/index', [
            'title'         => 'Kelola Booking',
            'booking'       => $booking,
            'pager'         => $this->pesananModel->pager, // Objek untuk link tombol halaman
            'layanan'       => $layanan,
            'statusFilter'  => $statusFilter,
            'tanggalFilter' => $tanggalFilter,
            'layananFilter' => $layananFilter,
        ]);
    }

    /**
     * Menampilkan detail item booking pesanan tertentu dan opsi penugasan staf pelaksana
     */
    public function bookingShow($id)
    {
        // 1. Ambil data detail pesanan utama beserta informasi relasi pengguna, jadwal, staf, dan pembayaran
        $booking = $this->pesananModel
            ->select('pesanan.*, u.name as nama_pelanggan, u.email, u.no_hp, j.tanggal, j.slot_waktu, s.name as nama_staff, pem.status_pembayaran, pem.metode_bayar')
            ->join('users u', 'u.id = pesanan.user_id')
            ->join('jadwal j', 'j.id = pesanan.jadwal_id')
            ->join('users s', 's.id = pesanan.staf_id', 'left') // Left join karena di awal staf bisa saja belum ditugaskan
            ->join('pembayaran pem', 'pem.pesanan_id = pesanan.id', 'left')
            ->where('pesanan.id', $id)
            ->first();

        // 2. Validasi perlindungan jika ID pesanan tidak valid / tidak ada di database
        if (!$booking) {
            return redirect()->to('/admin/booking')->with('error', 'Booking tidak ditemukan.');
        }

        // 3. Ambil rincian produk/layanan laundry apa saja yang ada di dalam pesanan ini
        $detailModel = new \App\Models\DetailPesananModel();
        $items = $detailModel
            ->select('detail_pesanan.*, l.nama_layanan, l.harga')
            ->join('layanan l', 'l.id = detail_pesanan.layanan_id')
            ->where('detail_pesanan.pesanan_id', $booking->id)
            ->findAll();

        // 4. Menghitung akumulasi total biaya keseluruhan dari item yang dibeli
        $grandTotal = 0;
        foreach ($items as $item) {
            $grandTotal += $item->total_harga; 
        }

        // 5. Mengambil list semua staf yang aktif (role = staff) untuk dimasukkan ke dropdown penugasan
        $allStaff = $this->userModel->where('role', 'staff')->where('is_active', 1)->findAll();

        // 6. Merender ke view rincian nota/detail booking
        return view('admin/booking/show', [
            'title'      => 'Detail Booking', 
            'booking'    => $booking, 
            'items'      => $items, 
            'grandTotal' => $grandTotal, 
            'allStaff'   => $allStaff, 
        ]);
    }

    /**
     * Mengonfirmasi pesanan / booking masuk agar berstatus 'dikonfirmasi'
     */
    public function bookingKonfirmasi($id)
    {
        // 1. Ambil data pesanan target untuk mendapatkan kode kelompoknya ('order_id')
        $pesanan = $this->pesananModel->find($id);
        
        if ($pesanan) {
            // 2. Mass-update status: Semua item yang memiliki order_id sama diubah statusnya menjadi dikonfirmasi
            $this->pesananModel->where('order_id', $pesanan->order_id) 
               ->set([
                   'status'     => 'dikonfirmasi', 
                   'updated_at' => date('Y-m-d H:i:s') 
               ])
               ->update(); 
        }
        
        // 3. Refresh halaman detail dengan alert sukses
        return redirect()->to('/admin/booking/' . $id)->with('success', 'Booking berhasil dikonfirmasi!');
    }

    /**
     * Menolak pesanan / booking masuk dan membatalkan status pembayaran terkait
     */
    public function bookingTolak($id)
    {
        // 1. Mengambil alasan penolakan dari form, beri teks default jika kosong
        $alasan = $this->request->getPost('alasan') ?? 'Ditolak oleh admin.';
        
        // 2. Ambil data pesanan target untuk mendapatkan order_id
        $pesanan = $this->pesananModel->find($id);
        
        if ($pesanan) {
            // 3. Perbarui status pesanan menjadi 'ditolak' dan sematkan alasan ke kolom catatan pelanggan
            $this->pesananModel->where('order_id', $pesanan->order_id) 
               ->set([
                   'status'     => 'ditolak', 
                   'catatan'    => $alasan, 
                   'updated_at' => date('Y-m-d H:i:s') 
               ])
               ->update(); 

            // 4. Batalkan transaksi keuangan: Ubah status rekaman pembayarannya menjadi 'gagal'
            $pembayaranModel = new \App\Models\PembayaranModel();
            $pembayaran = $pembayaranModel->where('pesanan_id', $pesanan->id)->first();
            if ($pembayaran) {
                $pembayaranModel->update($pembayaran->id, [
                    'status_pembayaran' => 'gagal',
                    'updated_at'        => date('Y-m-d H:i:s')
                ]);
            }
        }
        
        // 5. Kembali ke halaman rincian dengan notifikasi
        return redirect()->to('/admin/booking/' . $id)->with('success', 'Booking telah ditolak.');
    }

    /**
     * Menugaskan staf pelaksana tertentu ke dalam order pesanan laundry
     */
    public function bookingAssignStaff($id)
    {
        // 1. Tangkap parameter ID staf terpilih dari form penugasan
        $stafId = $this->request->getPost('staf_id');
        
        // 2. Cari entitas datanya untuk mendeteksi order_id kelompok
        $pesanan = $this->pesananModel->find($id);
        
        if ($pesanan) {
            // 3. Masukkan ID staf tersebut ke semua baris pesanan yang berada dalam satu kode order_id yang sama
            $this->pesananModel->where('order_id', $pesanan->order_id) 
               ->set([
                   'staf_id'    => $stafId, 
                   'updated_at' => date('Y-m-d H:i:s') 
               ])
               ->update(); 
        }
        
        // 4. Halaman dimuat ulang disertai pemberitahuan sukses disposisi kerja staf
        return redirect()->to('/admin/booking/' . $id)->with('success', 'Staff berhasil ditugaskan! Menunggu staff mengambil orderan.');
    }
}