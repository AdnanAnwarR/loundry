<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PesananModel;

class StaffJadwalController extends BaseController
{
    public function index()
    {
        $staffId = session()->get('id');

        $tanggal = $this->request->getGet('date') ?? date('Y-m-d');

        $pesananModel = new PesananModel();

        $query = $pesananModel->getTugasHarianStaff($staffId, $tanggal);

        $data['tanggal'] = $tanggal;

        $data['tugasHariIni'] = $query
            ->paginate(5, 'default', null, 0);

        $data['pager'] = $pesananModel->pager;

        return view('staff/jadwal', $data);
    }
}
