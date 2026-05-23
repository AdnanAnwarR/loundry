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
        'status_pembayaran',
        'metode_bayar',
        'snap_token',
        'rating',
        'ulasan'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // --- RELASI PENGGANTI BELONGSTO ---
    // Di CI4 Model, kita ambil relasi berdasarkan data Model Lainnya
    
    public function getPelanggan($userId)
    {
        $userModel = new UserModel();
        return $userModel->find($userId);
    }

    public function getLayanan($layananId)
    {
        $layananModel = new LayananModel();
        return $layananModel->find($layananId);
    }

    public function getJadwal($jadwalId)
    {
        $jadwalModel = new JadwalModel();
        return $jadwalModel->find($jadwalId);
    }

    public function getStaf($stafId)
    {
        if (!$stafId) return null;
        $userModel = new UserModel();
        return $userModel->find($stafId);
    }

    // --- SCOPE PENGGANTI ---
    
    // Pengganti scopeStatus
    public function status($status)
    {
        return $this->where('status', $status);
    }

    // Pengganti scopeStatusPembayaran
    public function statusPembayaran($status)
    {
        return $this->where('status_pembayaran', $status);
    }

    // Ambil data tugas harian staff
    public function getTugasHarianStaff($staffId, $tanggal)
    {
        return $this->select('
                jadwal.slot_waktu AS jam,
                layanan.nama_layanan AS tugas,
                users.name AS penanggung_jawab,
                pesanan.status
            ')
            ->join('jadwal', 'jadwal.id = pesanan.jadwal_id')
            ->join('layanan', 'layanan.id = pesanan.layanan_id')
            ->join('users', 'users.id = pesanan.staf_id')
            ->where('pesanan.staf_id', $staffId)
            ->where('jadwal.tanggal', $tanggal)
            ->orderBy('jadwal.slot_waktu', 'ASC');
    }

    // Ambil data riwayat tugas staff
    public function getTugasRiwayatStaff($staffId)
    {
        return $this->select('
                users.name AS pelanggan,
                layanan.nama_layanan AS layanan,
                jadwal.tanggal AS tanggal,
                pesanan.rating,
                pesanan.status
            ')
            ->join('jadwal', 'jadwal.id = pesanan.jadwal_id')
            ->join('layanan', 'layanan.id = pesanan.layanan_id')
            ->join('users', 'users.id = pesanan.staf_id')
            ->where('pesanan.staf_id', $staffId)
            ->orderBy('jadwal.slot_waktu', 'ASC');
    }
}
