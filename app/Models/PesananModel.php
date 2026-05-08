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
}
