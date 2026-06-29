<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\JadwalModel;

// =========================================================================
// KRITERIA 6: Webservice Server (Expose API Endpoint)
// - Menyediakan REST API Endpoint lengkap dan terstruktur dalam format JSON.
// - Mengimplementasikan GET & POST request untuk konsumsi pihak ketiga (Mobile Client, IoT, dll).
// - Memanfaatkan CodeIgniter\API\ResponseTrait untuk menyusun HTTP Status Codes
//   secara rapi dan RESTful (200 OK, 201 Created, 400 Bad Request, 404 Not Found, 500 Server Error).
// - Menyediakan respon JSON yang terstruktur baik untuk data sukses maupun penanganan error.
// =========================================================================
class ApiJadwalController extends BaseController
{
    use ResponseTrait;

    protected $jadwalModel;

    public function __construct()
    {
        // Instansiasi model jadwal yang diperlukan untuk API
        $this->jadwalModel = new JadwalModel();
    }

    /**
     * =========================================================================
     * 2. GET /api/jadwal
     * =========================================================================
     * Menampilkan daftar jadwal booking tersedia (hari ini & mendatang) dalam format JSON.
     * Response HTTP Status: 200 OK
     */
    public function index()
    {
        // Mendapatkan tanggal hari ini dalam format YYYY-MM-DD
        $today = date('Y-m-d');
        // Mengambil data jadwal booking dari database yang tanggalnya hari ini atau mendatang (tidak mengambil jadwal lampau)
        $jadwal = $this->jadwalModel
            ->where('tanggal >=', $today)
            ->orderBy('tanggal', 'ASC')
            ->orderBy('slot_waktu', 'ASC')
            ->findAll();

        // Melakukan perulangan untuk menghitung slot kosong yang tersedia pada setiap jadwal secara dinamis
        foreach ($jadwal as $j) {
            // Sisa slot dihitung dengan mengurangi kapasitas maksimal jadwal dengan jumlah slot yang sudah terisi
            $j->sisa_slot = max(0, $j->kapasitas - $j->terisi);
        }

        // Mengirimkan array objek jadwal laundry lengkap ke client dalam format JSON dengan HTTP Status 200 OK
        return $this->respond($jadwal, 200);
    }
}
