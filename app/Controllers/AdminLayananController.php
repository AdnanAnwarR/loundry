<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LayananModel;

class AdminLayananController extends BaseController
{
    protected $layananModel;

    public function __construct()
    {
        // Instansiasi LayananModel
        $this->layananModel = new LayananModel();
    }

    /**
     * Menampilkan daftar layanan laundry terpaginasi
     */
    public function layananIndex()
    {
        // Melakukan paginasi data layanan sebanyak 10 baris data per halaman menggunakan Model Query Builder
        $layanan = $this->layananModel->orderBy('created_at', 'DESC')->paginate(10, 'default', null, 0);
        return view('admin/layanan/index', [
            'title'   => 'Kelola Layanan',
            'layanan' => $layanan,
            'pager'   => $this->layananModel->pager, // Objek pager untuk link halaman
        ]);
    }

    /**
     * Halaman tambah layanan baru
     */
    public function layananCreate()
    {
        return view('admin/layanan/create', ['title' => 'Tambah Layanan']);
    }

    /**
     * Menyimpan data layanan baru ke database
     */
    public function layananStore()
    {
        $rules = [
            'nama_layanan' => 'required|min_length[3]',
            'harga'        => 'required|numeric',
            'durasi'       => 'required|integer',
            'deskripsi'    => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->layananModel->save([
            'nama_layanan' => $this->request->getPost('nama_layanan'),
            'harga'        => $this->request->getPost('harga'),
            'durasi'       => $this->request->getPost('durasi'),
            'deskripsi'    => $this->request->getPost('deskripsi'),
            'foto'         => null,
            'is_active'    => 1,
        ]);

        return redirect()->to('/admin/layanan')->with('success', 'Layanan berhasil ditambahkan!');
    }

    /**
     * Halaman edit layanan
     */
    public function layananEdit($id)
    {
        $layanan = $this->layananModel->find($id);
        if (!$layanan) return redirect()->to('/admin/layanan')->with('error', 'Layanan tidak ditemukan.');
        return view('admin/layanan/edit', ['title' => 'Edit Layanan', 'layanan' => $layanan]);
    }

    /**
     * Menyimpan pembaruan data layanan
     */
    public function layananUpdate($id)
    {
        $layanan = $this->layananModel->find($id);
        if (!$layanan) return redirect()->to('/admin/layanan')->with('error', 'Layanan tidak ditemukan.');

        $rules = [
            'nama_layanan' => 'required|min_length[3]',
            'harga'        => 'required|numeric',
            'durasi'       => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama_layanan' => $this->request->getPost('nama_layanan'),
            'harga'        => $this->request->getPost('harga'),
            'durasi'       => $this->request->getPost('durasi'),
            'deskripsi'    => $this->request->getPost('deskripsi'),
            'is_active'    => $this->request->getPost('is_active') ?? 1,
        ];

        $this->layananModel->update($id, $data);
        return redirect()->to('/admin/layanan')->with('success', 'Layanan berhasil diperbarui!');
    }

    /**
     * Menghapus data layanan
     */
    public function layananDelete($id)
    {
        $layanan = $this->layananModel->find($id);
        if ($layanan && $layanan->foto && file_exists(FCPATH . 'uploads/layanan/' . $layanan->foto)) {
            unlink(FCPATH . 'uploads/layanan/' . $layanan->foto);
        }
        $this->layananModel->delete($id);
        return redirect()->to('/admin/layanan')->with('success', 'Layanan berhasil dihapus!');
    }

    /**
     * Mengaktifkan/menonaktifkan layanan secara cepat
     */
    public function layananToggle($id)
    {
        $layanan = $this->layananModel->find($id);
        if ($layanan) {
            $this->layananModel->update($id, ['is_active' => $layanan->is_active ? 0 : 1]);
        }
        return redirect()->to('/admin/layanan')->with('success', 'Status layanan diperbarui.');
    }
}
