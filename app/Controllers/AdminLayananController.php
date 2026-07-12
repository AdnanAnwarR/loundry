<?php

namespace App\Controllers;

// Mengimpor BaseController bawaan CodeIgniter dan Model Layanan
use App\Controllers\BaseController;
use App\Models\LayananModel;

/**
 * Controller untuk mengelola data layanan laundry di halaman admin.
 * Menggunakan framework CodeIgniter 4.
 */
class AdminLayananController extends BaseController
{
    // Variabel untuk menampung instance dari LayananModel
    protected $layananModel;

    /**
     * Constructor untuk inisialisasi awal saat Controller dipanggil
     */
    public function __construct()
    {
        // Instansiasi LayananModel agar bisa digunakan di semua method dalam class ini
        $this->layananModel = new LayananModel();
    }

    /**
     * Menampilkan daftar layanan laundry terpaginasi
     */
    public function layananIndex()
    {
        // Mengambil data dari database, diurutkan dari yang terbaru (DESC) 
        // dan dibagi per halaman (pagination) sebanyak 10 data
        $layanan = $this->layananModel->orderBy('created_at', 'DESC')->paginate(10, 'default', null, 0);
        
        // Memanggil file view (tampilan) dan mengirimkan data ke dalamnya
        return view('admin/layanan/index', [
            'title'   => 'Kelola Layanan',
            'layanan' => $layanan, // Data layanan hasil paginasi
            'pager'   => $this->layananModel->pager, // Objek pager untuk menghasilkan tombol navigasi halaman (Next/Prev)
        ]);
    }

    /**
     * Menyimpan data layanan baru ke database
     */
    public function layananStore()
    {
        // Menentukan aturan validasi untuk inputan form tambah data
        $rules = [
            'nama_layanan' => 'required|min_length[3]', // Wajib diisi, minimal 3 karakter
            'harga'        => 'required|numeric',     // Wajib diisi, harus berupa angka
            'durasi'       => 'required|integer',     // Wajib diisi, harus bilangan bulat (jam/hari)
            'deskripsi'    => 'permit_empty',         // Boleh dikosongkan
        ];

        // Memeriksa apakah inputan tidak lolos validasi
        if (!$this->validate($rules)) {
            // Jika gagal, kembali ke form sebelumnya dengan membawa inputan lama, 
            // pesan error, dan trigger untuk membuka modal tambah data kembali
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())->with('open_add_modal', true);
        }

        // Jika lolos validasi, simpan data baru ke database melalui Model
        $this->layananModel->save([
            'nama_layanan' => $this->request->getPost('nama_layanan'),
            'harga'        => $this->request->getPost('harga'),
            'durasi'       => $this->request->getPost('durasi'),
            'deskripsi'    => $this->request->getPost('deskripsi'),
            'foto'         => null, // Di-set null secara default (belum unggah foto)
            'is_active'    => 1,    // Otomatis aktif saat pertama kali dibuat
        ]);

        // Redirect kembali ke halaman utama layanan dengan pesan sukses
        return redirect()->to('/admin/layanan')->with('success', 'Layanan berhasil ditambahkan!');
    }

    /**
     * Menyimpan pembaruan data layanan berdasarkan ID
     */
    public function layananUpdate($id)
    {
        // Cari data layanan berdasarkan ID di database
        $layanan = $this->layananModel->find($id);
        
        // Jika data tidak ditemukan, kembali ke halaman utama dengan pesan error
        if (!$layanan) return redirect()->to('/admin/layanan')->with('error', 'Layanan tidak ditemukan.');

        // Aturan validasi untuk form edit/ubah data
        $rules = [
            'nama_layanan' => 'required|min_length[3]',
            'harga'        => 'required|numeric',
            'durasi'       => 'required|integer',
        ];

        // Memeriksa jika inputan edit tidak valid
        if (!$this->validate($rules)) {
            // Kembali ke form dengan inputan sebelumnya, daftar error, dan ID modal edit yang harus dibuka
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())->with('open_edit_id', $id);
        }

        // Menyiapkan array data baru yang diambil dari inputan form
        $data = [
            'nama_layanan' => $this->request->getPost('nama_layanan'),
            'harga'        => $this->request->getPost('harga'),
            'durasi'       => $this->request->getPost('durasi'),
            'deskripsi'    => $this->request->getPost('deskripsi'),
            'is_active'    => $this->request->getPost('is_active') ?? 1, // Jika checkbox tidak dicentang, default ke 1
        ];

        // Eksekusi pembaruan data berdasarkan ID
        $this->layananModel->update($id, $data);
        
        // Redirect ke halaman utama layanan dengan pesan sukses
        return redirect()->to('/admin/layanan')->with('success', 'Layanan berhasil diperbarui!');
    }

    /**
     * Menghapus data layanan berdasarkan ID beserta file fotonya jika ada
     */
    public function layananDelete($id)
    {
        // Cari data layanan yang akan dihapus
        $layanan = $this->layananModel->find($id);
        
        // Jika data ada, memiliki nama file foto, dan fisik filenya benar-benar ada di folder uploads...
        if ($layanan && $layanan->foto && file_exists(FCPATH . 'uploads/layanan/' . $layanan->foto)) {
            // Hapus file foto tersebut dari server agar tidak memenuhi penyimpanan
            unlink(FCPATH . 'uploads/layanan/' . $layanan->foto);
        }
        
        // Hapus data baris layanan dari database
        $this->layananModel->delete($id);
        
        // Redirect kembali ke halaman utama dengan pesan sukses
        return redirect()->to('/admin/layanan')->with('success', 'Layanan berhasil dihapus!');
    }

    /**
     * Mengubah status aktif/nonaktif layanan (Toggle Switch) tanpa mengisi form
     */
    public function layananToggle($id)
    {
        // Cari data layanan berdasarkan ID
        $layanan = $this->layananModel->find($id);
        
        if ($layanan) {
            // Jika aktif (1) ubah ke nonaktif (0), jika nonaktif (0) ubah ke aktif (1)
            $this->layananModel->update($id, ['is_active' => $layanan->is_active ? 0 : 1]);
        }
        
        // Kembali ke halaman utama dengan pesan sukses
        return redirect()->to('/admin/layanan')->with('success', 'Status layanan diperbarui.');
    }
}