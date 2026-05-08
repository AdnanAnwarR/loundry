<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalModel extends Model
{
    protected $table            = 'jadwal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    
    // Di CI4 kita mengembalikan data sebagai object atau array (defaultnya array, tapi kita buat object agar mirip Laravel)
    protected $returnType       = 'object'; 
    protected $useSoftDeletes   = false;

    // $fillable di Laravel menjadi $allowedFields di CI4
    protected $allowedFields    = [
        'tanggal',
        'slot_waktu',
        'kapasitas',
        'terisi'
    ];

    // Mengaktifkan fitur timestamps otomatis (created_at & updated_at)
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Di CI4 (Model), relasi tidak didefinisikan dengan hasMany seperti Eloquent.
    // Biasanya kita membuat method khusus untuk menarik data dari model lain.
    public function getPesanan($jadwalId)
    {
        $pesananModel = new PesananModel();
        return $pesananModel->where('jadwal_id', $jadwalId)->findAll();
    }
}
