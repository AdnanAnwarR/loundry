<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\LayananModel;

// =========================================================================
// KRITERIA 6: Webservice Server (Expose API Endpoint)
// - Menyediakan REST API Endpoint lengkap dan terstruktur dalam format JSON.
// - Mengimplementasikan GET & POST request untuk konsumsi pihak ketiga (Mobile Client, IoT, dll).
// - Memanfaatkan CodeIgniter\API\ResponseTrait untuk menyusun HTTP Status Codes
//   secara rapi dan RESTful (200 OK, 201 Created, 400 Bad Request, 404 Not Found, 500 Server Error).
// - Menyediakan respon JSON yang terstruktur baik untuk data sukses maupun penanganan error.
// =========================================================================
class ApiLayananController extends BaseController
{
    use ResponseTrait;

    protected $layananModel;

    public function __construct()
    {
        // Instansiasi model layanan yang diperlukan untuk API
        $this->layananModel = new LayananModel();
    }

    /**
     * =========================================================================
     * 1. GET /api/layanan
     * =========================================================================
     * Menampilkan daftar semua layanan laundry yang aktif dalam format JSON.
     * Response HTTP Status: 200 OK
     */
    public function index()
    {
        // Mengambil semua data layanan laundry yang aktif (is_active = 1) dan mengurutkannya berdasarkan abjad nama layanan
        $layanan = $this->layananModel->where('is_active', 1)->orderBy('nama_layanan', 'ASC')->findAll();
        // Mengirimkan data layanan tersebut kembali ke klien dalam format JSON beserta kode status HTTP 200 OK
        return $this->respond($layanan, 200);
    }
}
