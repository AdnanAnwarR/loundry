<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JadwalModel;

class AdminJadwalController extends BaseController
{
    protected $jadwalModel;

    public function __construct()
    {
        // Instansiasi JadwalModel
        $this->jadwalModel = new JadwalModel();
    }

    /**
     * Menampilkan daftar jadwal booking laundry
     */
    public function jadwalIndex()
    {
        // Melakukan paginasi data jadwal sebanyak 10 baris data per halaman menggunakan Model Query Builder
        $jadwal = $this->jadwalModel->orderBy('tanggal', 'ASC')->orderBy('slot_waktu', 'ASC')->paginate(10, 'default', null, 0);
        return view('admin/jadwal/index', [
            'title'  => 'Kelola Jadwal', 
            'jadwal' => $jadwal,
            'pager'  => $this->jadwalModel->pager, // Objek pager untuk link halaman
        ]);
    }

    /**
     * Halaman tambah jadwal baru
     */
    public function jadwalCreate()
    {
        return view('admin/jadwal/create', ['title' => 'Tambah Jadwal']);
    }

    /**
     * Menyimpan data jadwal baru ke database
     */
    public function jadwalStore()
    {
        $rules = [
            'tanggal'    => 'required|valid_date',
            'slot_waktu' => 'required',
            'kapasitas'  => 'required|integer|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->jadwalModel->save([
            'tanggal'    => $this->request->getPost('tanggal'),
            'slot_waktu' => $this->request->getPost('slot_waktu'),
            'kapasitas'  => $this->request->getPost('kapasitas'),
            'terisi'     => 0,
        ]);

        return redirect()->to('/admin/jadwal')->with('success', 'Jadwal berhasil ditambahkan!');
    }

    /**
     * Halaman edit jadwal
     */
    public function jadwalEdit($id)
    {
        $jadwal = $this->jadwalModel->find($id);
        if (!$jadwal) return redirect()->to('/admin/jadwal')->with('error', 'Jadwal tidak ditemukan.');
        return view('admin/jadwal/edit', ['title' => 'Edit Jadwal', 'jadwal' => $jadwal]);
    }

    /**
     * Menyimpan pembaruan data jadwal
     */
    public function jadwalUpdate($id)
    {
        $rules = [
            'tanggal'    => 'required|valid_date',
            'slot_waktu' => 'required',
            'kapasitas'  => 'required|integer|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->jadwalModel->update($id, [
            'tanggal'    => $this->request->getPost('tanggal'),
            'slot_waktu' => $this->request->getPost('slot_waktu'),
            'kapasitas'  => $this->request->getPost('kapasitas'),
        ]);

        return redirect()->to('/admin/jadwal')->with('success', 'Jadwal berhasil diperbarui!');
    }

    /**
     * Menghapus data jadwal
     */
    public function jadwalDelete($id)
    {
        $this->jadwalModel->delete($id);
        return redirect()->to('/admin/jadwal')->with('success', 'Jadwal berhasil dihapus!');
    }
}
