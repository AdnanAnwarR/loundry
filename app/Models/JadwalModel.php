<?php

namespace App\Models;

// Mengimpor class Model bawaan CodeIgniter 4
use CodeIgniter\Model;

/**
 * Model untuk mengelola tabel 'jadwal' (slot waktu booking laundry) di database.
 */
class JadwalModel extends Model
{
    // Properti untuk menentukan nama tabel di database yang dikelola oleh model ini
    protected $table            = 'jadwal';
    
    // Properti untuk menentukan kolom yang bertindak sebagai Primary Key (Kunci Utama)
    protected $primaryKey       = 'id';
    
    // Mengaktifkan fitur auto increment (AI) agar ID bertambah otomatis saat data baru masuk
    protected $useAutoIncrement = true;
    
    protected $returnType       = 'object'; 
    
    // Menonaktifkan fitur Soft Deletes (jika bernilai true, data tidak benar-benar dihapus melainkan hanya diisi kolom deleted_at)
    protected $useSoftDeletes   = false;

    /**
     * Daftar kolom yang diizinkan untuk diisi atau dimanipulasi melalui metode insert() atau update().
     * Berfungsi sebagai proteksi keamanan Mass Assignment (mirip properti $fillable di Laravel).
     */
    protected $allowedFields    = [
        'tanggal',    // Kolom untuk menyimpan tanggal booking (YYYY-MM-DD)
        'slot_waktu',  // Kolom untuk menyimpan rentang jam (misal: "08:00 - 10:00")
        'kapasitas',   // Kolom untuk batas maksimal slot antrean yang tersedia
        'terisi'       // Kolom hitung kuota untuk mencatat berapa banyak slot yang sudah dibooking pelanggan
    ];

    // Mengaktifkan fitur pencatatan waktu otomatis saat data dibuat atau diubah
    protected $useTimestamps = true;
    
    // Menentukan format penyimpanan waktu ke database (menggunakan format date & time penuh)
    protected $dateFormat    = 'datetime';
    
    // Menentukan nama kolom untuk mencatat waktu saat data pertama kali dimasukkan
    protected $createdField  = 'created_at';
    
    // Menentukan nama kolom untuk mencatat waktu setiap kali data diperbarui
    protected $updatedField  = 'updated_at';

    /**
     * Method kustom buatan sendiri untuk menarik data relasi (Relationship).
     * Karena CodeIgniter 4 tidak menyediakan fitur built-in ORM hasMany seperti Laravel secara default,
     * method ini dibuat manual untuk mencari semua pesanan yang terhubung dengan ID jadwal tertentu.
     * 
     * @param int $jadwalId
     * @return array
     */
    public function getPesanan($jadwalId)
    {
        // Instansiasi model pesanan di dalam fungsi
        $pesananModel = new PesananModel();
        
        // Melakukan query filter mencari data di tabel pesanan berdasarkan kolom jadwal_id yang cocok
        return $pesananModel->where('jadwal_id', $jadwalId)->findAll();
    }
}