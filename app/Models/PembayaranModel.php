<?php

namespace App\Models;

// Mengimpor class Model bawaan CodeIgniter
use CodeIgniter\Model;

class PembayaranModel extends Model
{
    // Nama tabel di database
    protected $table            = 'pembayaran';
    
    // Primary key tabel
    protected $primaryKey       = 'id';
    
    // Mengaktifkan auto increment untuk primary key
    protected $useAutoIncrement = true;
    
    // Tipe data kembalian berupa object
    protected $returnType       = 'object';
    
    // Menonaktifkan soft deletes
    protected $useSoftDeletes   = false;

    // Kolom-kolom yang diizinkan untuk diisi secara massal (mass assignment)
    protected $allowedFields    = [
        'pesanan_id',
        'metode_bayar',
        'snap_token',
        'status_pembayaran'
    ];

    // Mengaktifkan pencatatan waktu otomatis (created_at & updated_at)
    protected $useTimestamps = true;
    
    // Format penyimpanan tanggal dan waktu
    protected $dateFormat    = 'datetime';
    
    // Nama kolom untuk pencatatan waktu dibuat
    protected $createdField  = 'created_at';
    
    // Nama kolom untuk pencatatan waktu diperbarui
    protected $updatedField  = 'updated_at';

    /**
     * Mengambil detail pembayaran beserta pesanan terkait menggunakan Query Builder
     * 
     * @param int $pembayaranId
     * @return object|null
     */
    public function getDetailPembayaran($pembayaranId)
    {
        // Menggunakan Query Builder bawaan Model Pembayaran untuk mengambil data pembayaran dan join pesanan
        return $this->select('pembayaran.*, pesanan.order_id, pesanan.total_harga as harga_pesanan') // Memilih kolom detail
            ->join('pesanan', 'pesanan.id = pembayaran.pesanan_id') // Melakukan join dengan tabel pesanan
            ->where('pembayaran.id', $pembayaranId) // Menyaring berdasarkan ID pembayaran
            ->first(); // Mengambil satu baris pertama sebagai objek
    }
}
