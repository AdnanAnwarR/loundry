<?php

namespace App\Models;

use CodeIgniter\Model;

class LayananModel extends Model
{
    protected $table            = 'layanan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'nama_layanan',
        'harga',
        'durasi',
        'deskripsi',
        'foto',
        'is_active'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Casting di CI4 biasanya diproses sebelum validasi/save dengan menggunakan callbacks,
    // atau menggunakan class Entity (sebagai representasi baris).
    // Tapi secara default CI4 sudah bisa menyimpan boolean 1/0 dan decimal dengan baik jika tipe datanya sesuai.

    // Relasi hasMany ke detail_pesanan
    public function getPesanan($layananId)
    {
        $detailModel = new DetailPesananModel();
        return $detailModel->where('layanan_id', $layananId)->findAll();
    }

    // Pengganti scopeActive() di Laravel
    // Cara panggil di Controller: $model->active()->findAll();
    public function active()
    {
        return $this->where('is_active', 1);
    }
}
