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

    // --- VALIDASI NATIVE CODEIGNITER 4 MODEL ---
    protected $validationRules = [
        'user_id'     => 'required|numeric',
        'jadwal_id'   => 'required|numeric',
        'staf_id'     => 'permit_empty|numeric',
        'order_id'    => 'required|alpha_dash|min_length[3]|max_length[50]',
        'total_harga' => 'required|numeric',
        'catatan'     => 'permit_empty|string',
        'status'      => 'required|in_list[pending,dikonfirmasi,proses,selesai,dibatalkan,ditolak]',
        'rating'      => 'permit_empty|numeric|greater_than_equal_to[1]|less_than_equal_to[5]',
        'ulasan'      => 'permit_empty|string'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // --- RELASI PENYEDERHANAAN ---

    public function getPelanggan($userId)
    {
        $userModel = new \App\Models\UserModel();
        return $userModel->find($userId);
    }

    public function getJadwal($jadwalId)
    {
        $jadwalModel = new \App\Models\JadwalModel();
        return $jadwalModel->find($jadwalId);
    }

    public function getStaf($stafId)
    {
        if (!$stafId) return null;
        $userModel = new \App\Models\UserModel();
        return $userModel->find($stafId);
    }

    public function getItems($pesananId)
    {
        $detailModel = new \App\Models\DetailPesananModel();
        return $detailModel->where('pesanan_id', $pesananId)->findAll();
    }
}
