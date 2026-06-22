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

        // Mengambil daftar riwayat tugas staff terpaginasi (1 row per order)
        $pesananQuery = $pesananModel
            ->select('pesanan.*, j.tanggal, j.slot_waktu, u.name as nama_pelanggan')
            ->join('jadwal j', 'j.id = pesanan.jadwal_id')
            ->join('users u', 'u.id = pesanan.user_id')
            ->where('pesanan.staf_id', $staffId)
            ->where('pesanan.status', 'selesai');

        if ($tanggal !== null) {
            $pesananQuery->where('j.tanggal', $tanggal);
        }

        $tugasRiwayat = $pesananQuery
            ->orderBy('j.tanggal', 'DESC')
            ->orderBy('j.slot_waktu', 'DESC')
            ->paginate(10);

        // Gabungkan detail layanan untuk setiap riwayat tugas
        $detailModel = new \App\Models\DetailPesananModel();
        foreach ($tugasRiwayat as $tugas) {
            $items = $detailModel
                ->select('l.nama_layanan')
                ->join('layanan l', 'l.id = detail_pesanan.layanan_id')
                ->where('detail_pesanan.pesanan_id', $tugas->id)
                ->findAll();
            
            $tugas->pelanggan  = $tugas->nama_pelanggan;
            $tugas->layanan    = implode(', ', array_column($items, 'nama_layanan'));
        }

        $data['tugasRiwayat'] = $tugasRiwayat;
        $data['pager'] = $pesananModel->pager;

        return view('staff/riwayat', $data);
    }
}
