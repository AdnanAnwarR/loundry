<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailPesananModel extends Model
{
    protected $table            = 'detail_pesanan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'pesanan_id',
        'layanan_id',
        'total_harga'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Relasi: detail pesanan belongs to pesanan
    public function getPesanan($pesananId)
    {
        $pesananModel = new PesananModel();
        return $pesananModel->find($pesananId);
    }

    // Relasi: detail pesanan belongs to layanan
    public function getLayanan($layananId)
    {
        $layananModel = new LayananModel();
        return $layananModel->find($layananId);
    }
}
