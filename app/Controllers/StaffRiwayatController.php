<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PesananModel;

class StaffRiwayatController extends BaseController
{
    public function index()
    {
        $staffId = session()->get('id');

        $tanggal = $this->request->getGet('date') ?: null;

        $pesananModel = new PesananModel();

        $data['tanggal'] = $tanggal;

        // Mengambil daftar riwayat tugas staff terpaginasi langsung dari method model
        $data['tugasRiwayat'] = $pesananModel->getTugasRiwayatStaff($staffId, $tanggal);

        // Mengirimkan objek pager untuk tautan paginasi ke view
        $data['pager'] = $pesananModel->pager;

        return view('staff/riwayat', $data);
    }
}
