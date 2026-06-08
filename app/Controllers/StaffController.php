<?php

namespace App\Controllers;

// Mengimpor controller utama
use App\Controllers\BaseController;
// Mengimpor model Pesanan
use App\Models\PesananModel;

class StaffController extends BaseController
{
    /**
     * Dashboard Staff - Menampilkan data ringkasan tugas aktif
     */
    public function index()
    {
        // Mendapatkan ID staff yang sedang login dari session
        $staffId = session()->get('id');

        // Instansiasi Model Pesanan secara langsung untuk memakai Query Builder bawaan Model
        $pesananModel = new PesananModel();

        // Menghitung jumlah tugas baru (status 'dikonfirmasi') yang ditugaskan ke staff ini menggunakan Query Builder
        $pesananBaru = $pesananModel->where('staf_id', $staffId)
            ->where('status', 'dikonfirmasi')
            ->countAllResults();

        // Menghitung jumlah tugas yang sedang diproses (status 'proses') menggunakan Query Builder
        $sedangDiproses = $pesananModel->where('staf_id', $staffId)
            ->where('status', 'proses')
            ->countAllResults();

        // Menghitung jumlah tugas yang selesai dikerjakan (status 'selesai') menggunakan Query Builder
        $selesai = $pesananModel->where('staf_id', $staffId)
            ->where('status', 'selesai')
            ->countAllResults();

        // Mengambil daftar tugas staff terpaginasi langsung dari method model
        $tugasStaff = $pesananModel->getTugasHarianStaff($staffId);

        // Mengirimkan data statistik ke view staff dashboard
        $data = [
            'title'          => 'Dashboard Staff', // Judul halaman
            'pesananBaru'    => $pesananBaru, // Jumlah pesanan baru
            'sedangDiproses' => $sedangDiproses, // Jumlah pesanan diproses
            'selesai'        => $selesai, // Jumlah pesanan selesai
            'tugasStaff'     => $tugasStaff, // Daftar tugas active
            'pager'          => $pesananModel->pager // Objek pager untuk link halaman
        ];

        // Memuat view index dashboard staff dengan data statistik
        return view('staff/index', $data);
    }
}
