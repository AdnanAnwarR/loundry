<?php

namespace App\Models;

use CodeIgniter\Model;

class PesananModel extends Model
{
    protected $table            = 'pesanan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'user_id',
        'layanan_id',
        'jadwal_id',
        'staf_id',
        'order_id',
        'total_harga',
        'catatan',
        'status',
        'rating',
        'ulasan'
    ];

    // Mengaktifkan timestamps otomatis (created_at & updated_at)
    protected $useTimestamps = true;
    // Format tanggal
    protected $dateFormat    = 'datetime';
    // Kolom tanggal dibuat
    protected $createdField  = 'created_at';
    // Kolom tanggal diperbarui
    protected $updatedField  = 'updated_at';

    // --- RELASI PENGGANTI BELONGSTO MENGGUNAKAN QUERY BUILDER MODEL ---

    public function getPelanggan($userId)
    {
        // Hubungkan ke Model User untuk mencari data user berdasarkan ID
        $userModel = new \App\Models\UserModel();
        // Menggunakan metode find bawaan model untuk mengambil data pelanggan sebagai objek
        return $userModel->find($userId);
    }

    public function getLayanan($layananId)
    {
        // Hubungkan ke Model Layanan untuk mencari data layanan berdasarkan ID
        $layananModel = new \App\Models\LayananModel();
        // Menggunakan metode find bawaan model untuk mengambil data layanan sebagai objek
        return $layananModel->find($layananId);
    }

    public function getJadwal($jadwalId)
    {
        // Hubungkan ke Model Jadwal untuk mencari data jadwal berdasarkan ID
        $jadwalModel = new \App\Models\JadwalModel();
        // Menggunakan metode find bawaan model untuk mengambil data jadwal sebagai objek
        return $jadwalModel->find($jadwalId);
    }

    public function getStaf($stafId)
    {
        // Jika staf ID kosong, langsung kembalikan null
        if (!$stafId) return null;
        // Hubungkan ke Model User untuk mencari data staff berdasarkan ID
        $userModel = new \App\Models\UserModel();
        // Menggunakan metode find bawaan model untuk mengambil data staff sebagai objek
        return $userModel->find($stafId);
    }

    // --- SCOPE PENGGANTI MENGGUNAKAN QUERY BUILDER ---

    public function status($status)
    {
        // Menggunakan filter query where pada status
        return $this->where('status', $status);
    }

    public function getTugasHarianStaff($staffId, $tanggal = null)
    {
        // 1. Ambil order_id secara unik dari pesanan milik staff tertentu
        $this->distinct()->select('pesanan.order_id')
             ->where('pesanan.staf_id', $staffId);
        
        // Terapkan filter tanggal jika diinput
        if ($tanggal !== null) {
            $this->join('jadwal j', 'j.id = pesanan.jadwal_id')
                 ->where('j.tanggal', $tanggal);
        }
        
        // Melakukan join ke tabel jadwal untuk pengurutan berdasarkan tanggal dan waktu booking
        $this->join('jadwal j2', 'j2.id = pesanan.jadwal_id')
             ->orderBy('j2.tanggal', 'ASC')
             ->orderBy('j2.slot_waktu', 'ASC');

        // Lakukan paginasi 10 order unik
        $paginatedOrders = $this->paginate(10);
        if (empty($paginatedOrders)) {
            return [];
        }

        // Kumpulkan order_id hasil paginasi
        $orderIds = array_column($paginatedOrders, 'order_id');

        // 2. Ambil detail pesanan lengkap beserta data relasinya untuk order_id yang didapat
        $allPesanan = $this->select('pesanan.*, l.nama_layanan, j.tanggal, j.slot_waktu, u.name as nama_pelanggan, u.no_hp')
            ->join('layanan l', 'l.id = pesanan.layanan_id')
            ->join('jadwal j', 'j.id = pesanan.jadwal_id')
            ->join('users u', 'u.id = pesanan.user_id')
            ->whereIn('pesanan.order_id', $orderIds)
            ->findAll();

        // 3. Gabungkan item-item layanan pesanan dalam memori PHP
        $grouped = [];
        foreach ($paginatedOrders as $po) {
            $orderId = $po->order_id;
            
            // Filter item pesanan yang memiliki order_id ini
            $items = array_filter($allPesanan, function($p) use ($orderId) {
                return $p->order_id === $orderId;
            });
            
            if (empty($items)) continue;

            $firstItem = reset($items);
            
            // Gabungkan seluruh nama layanan yang dipesan dalam order ini
            $layananNames = [];
            foreach ($items as $item) {
                $layananNames[] = $item->nama_layanan;
            }

            // Buat objek standar penampung data untuk dikirim ke view
            $obj = new \stdClass();
            $obj->order_id        = $orderId;
            $obj->tanggal_booking = $firstItem->tanggal;
            $obj->jam             = $firstItem->slot_waktu;
            $obj->tugas           = implode(', ', $layananNames);
            $obj->nama_pelanggan  = $firstItem->nama_pelanggan;
            $obj->no_hp_pelanggan = $firstItem->no_hp;
            $obj->catatan_pesanan = $firstItem->catatan;
            $obj->status          = $firstItem->status;

            $grouped[] = $obj;
        }

        return $grouped;
    }
    public function getTugasRiwayatStaff($staffId, $tanggal = null)
    {
        // 1. Ambil order_id secara unik dari pesanan milik staff tertentu yang sudah selesai
        $this->distinct()->select('pesanan.order_id')
             ->where('pesanan.staf_id', $staffId)
             ->where('pesanan.status', 'selesai');
        // Terapkan filter tanggal jika diinput
        if ($tanggal !== null) {
            $this->join('jadwal j', 'j.id = pesanan.jadwal_id')
                 ->where('j.tanggal', $tanggal);
        }
        
        // Melakukan join ke tabel jadwal untuk pengurutan riwayat pengerjaan terbaru
        $this->join('jadwal j2', 'j2.id = pesanan.jadwal_id')
             ->orderBy('j2.tanggal', 'DESC')
             ->orderBy('j2.slot_waktu', 'DESC');

        // Lakukan paginasi 10 order unik
        $paginatedOrders = $this->paginate(10);
        if (empty($paginatedOrders)) {
            return [];
        }

        // Kumpulkan order_id hasil paginasi
        $orderIds = array_column($paginatedOrders, 'order_id');

        // 2. Ambil detail pesanan lengkap beserta data relasinya untuk order_id yang didapat
        $allPesanan = $this->select('pesanan.*, l.nama_layanan, j.tanggal, j.slot_waktu, u.name as nama_pelanggan')
            ->join('layanan l', 'l.id = pesanan.layanan_id')
            ->join('jadwal j', 'j.id = pesanan.jadwal_id')
            ->join('users u', 'u.id = pesanan.user_id')
            ->whereIn('pesanan.order_id', $orderIds)
            ->findAll();

        // 3. Gabungkan item-item layanan pesanan dalam memori PHP
        $grouped = [];
        foreach ($paginatedOrders as $po) {
            $orderId = $po->order_id;
            
            // Filter item pesanan yang memiliki order_id ini
            $items = array_filter($allPesanan, function($p) use ($orderId) {
                return $p->order_id === $orderId;
            });
            
            if (empty($items)) continue;

            $firstItem = reset($items);
            
            // Gabungkan seluruh nama layanan yang dipesan dalam order ini
            $layananNames = [];
            foreach ($items as $item) {
                $layananNames[] = $item->nama_layanan;
            }

            // Buat objek standar penampung data untuk dikirim ke view
            $obj = new \stdClass();
            $obj->order_id   = $orderId;
            $obj->pelanggan  = $firstItem->nama_pelanggan;
            $obj->layanan    = implode(', ', $layananNames);
            $obj->tanggal    = $firstItem->tanggal;
            $obj->slot_waktu = $firstItem->slot_waktu;
            $obj->status     = $firstItem->status;
            $obj->rating     = $firstItem->rating;

            $grouped[] = $obj;
        }

        return $grouped;
    }

    public function getBookingsByUser($userId)
    {
        // 1. Ambil order_id secara unik dari pesanan aktif (bukan selesai, batal, tolak)
        $this->distinct()->select('pesanan.order_id')
             ->where('pesanan.user_id', $userId)
             ->whereNotIn('pesanan.status', ['selesai', 'dibatalkan', 'ditolak'])
             ->orderBy('pesanan.created_at', 'DESC');

        // Lakukan paginasi 10 order unik
        $paginatedOrders = $this->paginate(10);
        if (empty($paginatedOrders)) {
            return [];
        }

        // Kumpulkan order_id hasil paginasi
        $orderIds = array_column($paginatedOrders, 'order_id');

        // 2. Ambil detail pesanan lengkap beserta data relasinya untuk order_id yang didapat
        $allPesanan = $this->select('pesanan.*, l.nama_layanan, j.tanggal, j.slot_waktu, pem.status_pembayaran, s.name as nama_staff')
            ->join('layanan l', 'l.id = pesanan.layanan_id')
            ->join('jadwal j', 'j.id = pesanan.jadwal_id')
            ->join('pembayaran pem', 'pem.pesanan_id = pesanan.id', 'left')
            ->join('users s', 's.id = pesanan.staf_id', 'left')
            ->whereIn('pesanan.order_id', $orderIds)
            ->findAll();

        // 3. Gabungkan item-item layanan pesanan dalam memori PHP
        $grouped = [];
        foreach ($paginatedOrders as $po) {
            $orderId = $po->order_id;
            
            // Filter item pesanan yang memiliki order_id ini
            $items = array_filter($allPesanan, function($p) use ($orderId) {
                return $p->order_id === $orderId;
            });
            
            if (empty($items)) continue;

            $firstItem = reset($items);
            
            // Hitung grand total dan kumpulkan seluruh nama layanan
            $layananNames = [];
            $grandTotal   = 0;
            foreach ($items as $item) {
                $layananNames[] = $item->nama_layanan;
                $grandTotal   += $item->total_harga;
            }

            // Buat objek standar penampung data untuk dikirim ke view
            $obj = new \stdClass();
            $obj->order_id          = $orderId;
            $obj->layanan_list      = implode(', ', $layananNames);
            $obj->catatan           = $firstItem->catatan;
            $obj->tanggal           = $firstItem->tanggal;
            $obj->slot_waktu        = $firstItem->slot_waktu;
            $obj->grand_total       = $grandTotal;
            $obj->status_pesanan    = $firstItem->status;
            $obj->status_pembayaran = $firstItem->status_pembayaran;
            $obj->nama_staff        = $firstItem->nama_staff;

            $grouped[] = $obj;
        }

        return $grouped;
    }

    public function getBookingsHistoryByUser($userId)
    {
        // 1. Ambil order_id secara unik dari pesanan riwayat (selesai, batal, tolak)
        $this->distinct()->select('pesanan.order_id')
             ->where('pesanan.user_id', $userId)
             ->whereIn('pesanan.status', ['selesai', 'dibatalkan', 'ditolak'])
             ->orderBy('pesanan.created_at', 'DESC');

        // Lakukan paginasi 10 order unik
        $paginatedOrders = $this->paginate(10);
        if (empty($paginatedOrders)) {
            return [];
        }

        // Kumpulkan order_id hasil paginasi
        $orderIds = array_column($paginatedOrders, 'order_id');

        // 2. Ambil detail pesanan lengkap beserta data relasinya untuk order_id yang didapat
        $allPesanan = $this->select('pesanan.*, l.nama_layanan, j.tanggal, j.slot_waktu, pem.status_pembayaran, s.name as nama_staff')
            ->join('layanan l', 'l.id = pesanan.layanan_id')
            ->join('jadwal j', 'j.id = pesanan.jadwal_id')
            ->join('pembayaran pem', 'pem.pesanan_id = pesanan.id', 'left')
            ->join('users s', 's.id = pesanan.staf_id', 'left')
            ->whereIn('pesanan.order_id', $orderIds)
            ->findAll();

        // 3. Gabungkan item-item layanan pesanan dalam memori PHP
        $grouped = [];
        foreach ($paginatedOrders as $po) {
            $orderId = $po->order_id;
            
            // Filter item pesanan yang memiliki order_id ini
            $items = array_filter($allPesanan, function($p) use ($orderId) {
                return $p->order_id === $orderId;
            });
            
            if (empty($items)) continue;

            $firstItem = reset($items);
            
            // Hitung grand total dan kumpulkan seluruh nama layanan
            $layananNames = [];
            $grandTotal   = 0;
            foreach ($items as $item) {
                $layananNames[] = $item->nama_layanan;
                $grandTotal   += $item->total_harga;
            }

            // Buat objek standar penampung data untuk dikirim ke view
            $obj = new \stdClass();
            $obj->order_id          = $orderId;
            $obj->layanan_list      = implode(', ', $layananNames);
            $obj->catatan           = $firstItem->catatan;
            $obj->tanggal           = $firstItem->tanggal;
            $obj->slot_waktu        = $firstItem->slot_waktu;
            $obj->grand_total       = $grandTotal;
            $obj->status_pesanan    = $firstItem->status;
            $obj->status_pembayaran = $firstItem->status_pembayaran;
            $obj->nama_staff        = $firstItem->nama_staff;
            $obj->rating            = $firstItem->rating;
            $obj->ulasan            = $firstItem->ulasan;

            $grouped[] = $obj;
        }

        return $grouped;
    }

    public function getOrderItems($orderId, $userId = null)
    {
        // Membuat query select item pesanan beserta detail nama dan harga layanan
        $builder = $this->select('pesanan.*, l.nama_layanan, l.harga') // Memilih kolom detail pesanan dan layanan
            ->join('layanan l', 'l.id = pesanan.layanan_id') // Melakukan join ke tabel layanan
            ->where('pesanan.order_id', $orderId); // Memfilter berdasarkan kode order_id
        // Jika parameter ID pelanggan diberikan, tambahkan filter keamanan
        if ($userId !== null) { // Proteksi kepemilikan data
            $builder->where('pesanan.user_id', $userId); // Memfilter berdasarkan ID user
        } // Penutup filter user
        return $builder->findAll(); // Mengambil seluruh baris data hasil query
    }

    public function getRecentBookings($limit = 10)
    {
        // 1. Ambil order_id secara unik 10 terbaru
        $this->distinct()->select('pesanan.order_id')
             ->orderBy('pesanan.created_at', 'DESC')
             ->limit($limit);
            
        $orders = $this->findAll();
        if (empty($orders)) {
            return [];
        }

        $orderIds = array_column($orders, 'order_id');

        // 2. Ambil detail pesanan lengkap beserta data relasinya untuk order_id yang didapat
        $allPesanan = $this->select('pesanan.*, u.name as nama_pelanggan, l.nama_layanan, j.tanggal, j.slot_waktu')
            ->join('users u', 'u.id = pesanan.user_id')
            ->join('layanan l', 'l.id = pesanan.layanan_id')
            ->join('jadwal j', 'j.id = pesanan.jadwal_id')
            ->whereIn('pesanan.order_id', $orderIds)
            ->findAll();

        // 3. Gabungkan item-item layanan pesanan dalam memori PHP
        $grouped = [];
        foreach ($orders as $po) {
            $orderId = $po->order_id;
            
            // Filter item pesanan yang memiliki order_id ini
            $items = array_filter($allPesanan, function($p) use ($orderId) {
                return $p->order_id === $orderId;
            });
            
            if (empty($items)) continue;

            $firstItem = reset($items);
            
            // Hitung total harga dan kumpulkan seluruh nama layanan
            $layananNames = [];
            $totalHarga   = 0;
            foreach ($items as $item) {
                $layananNames[] = $item->nama_layanan;
                $totalHarga   += $item->total_harga;
            }

            // Buat objek standar penampung data untuk dikirim ke dashboard admin
            $obj = new \stdClass();
            $obj->order_id       = $orderId;
            $obj->id             = $firstItem->id;
            $obj->nama_pelanggan = $firstItem->nama_pelanggan;
            $obj->nama_layanan   = implode(', ', $layananNames);
            $obj->tanggal        = $firstItem->tanggal;
            $obj->total_harga    = $totalHarga;
            $obj->status         = $firstItem->status;

            $grouped[] = $obj;
        }

        return $grouped;
    }

    public function getAllBookings($statusFilter = null, $tanggalFilter = null, $layananFilter = null)
    {
        // 1. Ambil order_id secara unik dari database
        $this->distinct()->select('pesanan.order_id');
        
        // Terapkan filter status jika diisi
        if ($statusFilter) {
            $this->where('pesanan.status', $statusFilter);
        }
        
        // Terapkan filter tanggal jika diisi
        if ($tanggalFilter) {
            $this->join('jadwal j', 'j.id = pesanan.jadwal_id')
                 ->where('j.tanggal', $tanggalFilter);
        }
        
        // Terapkan filter layanan jika diisi menggunakan subquery agar item dalam order_id tidak terpisah
        if ($layananFilter) {
            $this->where("pesanan.order_id IN (SELECT p_sub.order_id FROM pesanan p_sub WHERE p_sub.layanan_id = " . (int)$layananFilter . ")");
        }
        
        // Urutkan dari pesanan terbaru
        $this->orderBy('pesanan.created_at', 'DESC');

        // Lakukan paginasi 10 order unik
        $paginatedOrders = $this->paginate(10);
        if (empty($paginatedOrders)) {
            return [];
        }

        // Kumpulkan order_id hasil paginasi
        $orderIds = array_column($paginatedOrders, 'order_id');

        // 2. Ambil detail pesanan lengkap beserta data relasinya untuk order_id yang didapat
        $allPesanan = $this->select('pesanan.*, u.name as nama_pelanggan, l.nama_layanan, j.tanggal, j.slot_waktu, pem.status_pembayaran')
            ->join('users u', 'u.id = pesanan.user_id')
            ->join('layanan l', 'l.id = pesanan.layanan_id')
            ->join('jadwal j', 'j.id = pesanan.jadwal_id')
            ->join('pembayaran pem', 'pem.pesanan_id = pesanan.id', 'left')
            ->whereIn('pesanan.order_id', $orderIds)
            ->findAll();

        // 3. Gabungkan item-item layanan pesanan dalam memori PHP
        $grouped = [];
        foreach ($paginatedOrders as $po) {
            $orderId = $po->order_id;
            
            // Filter item pesanan yang memiliki order_id ini
            $items = array_filter($allPesanan, function($p) use ($orderId) {
                return $p->order_id === $orderId;
            });
            
            if (empty($items)) continue;

            $firstItem = reset($items);
            
            // Hitung total harga dan kumpulkan seluruh nama layanan
            $layananNames = [];
            $totalHarga   = 0;
            foreach ($items as $item) {
                $layananNames[] = $item->nama_layanan;
                $totalHarga   += $item->total_harga;
            }

            // Buat objek standar penampung data untuk dikirim ke view kelola booking admin
            $obj = new \stdClass();
            $obj->order_id          = $orderId;
            $obj->id                = $firstItem->id;
            $obj->nama_pelanggan    = $firstItem->nama_pelanggan;
            $obj->nama_layanan      = implode(', ', $layananNames);
            $obj->tanggal           = $firstItem->tanggal;
            $obj->slot_waktu        = $firstItem->slot_waktu;
            $obj->total_harga       = $totalHarga;
            $obj->status            = $firstItem->status;
            $obj->status_pembayaran = $firstItem->status_pembayaran;

            $grouped[] = $obj;
        }

        return $grouped;
    }

    public function getBookingDetail($id)
    {
        // Mengambil data detail pesanan utama beserta relasi u, l, j, s, dan pem
        return $this->select('pesanan.*, u.name as nama_pelanggan, u.email, u.no_hp, l.nama_layanan, l.harga, j.tanggal, j.slot_waktu, s.name as nama_staff, pem.status_pembayaran, pem.metode_bayar') // Memilih kolom detail
            ->join('users u', 'u.id = pesanan.user_id') // Join dengan users pelanggan
            ->join('layanan l', 'l.id = pesanan.layanan_id') // Join dengan layanan
            ->join('jadwal j', 'j.id = pesanan.jadwal_id') // Join dengan jadwal
            ->join('users s', 's.id = pesanan.staf_id', 'left') // Left join dengan staff
            // Join ke tabel pembayaran berdasarkan pesanan terendah di order_id tersebut
            ->join('pembayaran pem', 'pem.pesanan_id = (SELECT MIN(p2.id) FROM pesanan p2 WHERE p2.order_id = pesanan.order_id)', 'left') // Join pembayaran
            ->where('pesanan.id', $id) // Filter ID pesanan
            ->first(); // Mengambil satu baris pertama sebagai objek
    }

    public function verifyStaffOrder($orderId, $staffId)
    {
        // Query builder untuk mengambil satu baris pesanan milik staff tertentu beserta detail pelanggan
        return $this->select('pesanan.order_id, u.name as nama_pelanggan, u.no_hp') // Pilih kolom verifikasi
            ->join('users u', 'u.id = pesanan.user_id') // Join dengan tabel users untuk data pelanggan
            ->where('pesanan.order_id', $orderId) // Filter order_id
            ->where('pesanan.staf_id', $staffId) // Filter staf_id
            ->first(); // Ambil satu baris pertama
    }
}
