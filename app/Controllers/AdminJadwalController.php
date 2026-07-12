<?php

namespace App\Controllers;

// Mengimpor BaseController bawaan CodeIgniter dan Model Jadwal
use App\Controllers\BaseController;
use App\Models\JadwalModel;

/**
 * Controller untuk mengelola jadwal booking/antrean laundry di halaman admin.
 * Menggunakan framework CodeIgniter 4.
 */
class AdminJadwalController extends BaseController
{
    // Variabel untuk menampung properti objek JadwalModel
    protected $jadwalModel;

    /**
     * Constructor untuk inisialisasi awal saat Controller dipanggil
     */
    public function __construct()
    {
        // Instansiasi JadwalModel agar bisa melakukan operasi database di semua method
        $this->jadwalModel = new JadwalModel();
    }

    /**
     * Menampilkan daftar jadwal booking laundry
     */
    public function jadwalIndex()
    {
        // Mengambil data jadwal dari database, diurutkan berdasarkan tanggal terdekat (ASC)
        // lalu diurutkan lagi berdasarkan slot waktu terkecil (ASC), dan dibagi 10 data per halaman.
        $jadwal = $this->jadwalModel->orderBy('tanggal', 'ASC')->orderBy('slot_waktu', 'ASC')->paginate(10, 'default', null, 0);
        
        // Memanggil file view cetakan halaman admin jadwal dan mengirimkan datanya
        return view('admin/jadwal/index', [
            'title'  => 'Kelola Jadwal', 
            'jadwal' => $jadwal, // Data jadwal hasil paginasi
            'pager'  => $this->jadwalModel->pager, // Objek pager untuk membuat navigasi halaman (pagination)
        ]);
    }

    /**
     * Menyimpan data jadwal baru ke database
     */
    public function jadwalStore()
    {
        // Menentukan aturan validasi untuk inputan form tambah jadwal
        $rules = [
            'tanggal'    => 'required|valid_date',             // Wajib diisi dan format tanggal harus valid
            'slot_waktu' => 'required',                       // Wajib diisi (misal: "08:00 - 10:00")
            'kapasitas'  => 'required|integer|greater_than[0]', // Wajib diisi, harus angka, dan harus lebih besar dari 0
        ];

        // Memeriksa jika inputan form TIDAK lolos validasi
        if (!$this->validate($rules)) {
            // Kembali ke halaman form sebelumnya dengan membawa inputan lama,
            // pesan error validasi, dan trigger agar modal tambah otomatis terbuka lagi
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())->with('open_add_modal', true);
        }

        // Jika lolos validasi, simpan data jadwal baru ke database melalui Model
        $this->jadwalModel->save([
            'tanggal'    => $this->request->getPost('tanggal'),
            'slot_waktu' => $this->request->getPost('slot_waktu'),
            'kapasitas'  => $this->request->getPost('kapasitas'),
            'terisi'     => 0, // Set awal slot terisi menjadi 0 (belum ada pelanggan yang booking)
        ]);

        // Redirect kembali ke halaman utama kelola jadwal dengan pesan sukses
        return redirect()->to('/admin/jadwal')->with('success', 'Jadwal berhasil ditambahkan!');
    }

    /**
     * Menyimpan pembaruan/edit data jadwal berdasarkan ID
     */
    public function jadwalUpdate($id)
    {
        // Aturan validasi yang sama untuk form edit/ubah data jadwal
        $rules = [
            'tanggal'    => 'required|valid_date',
            'slot_waktu' => 'required',
            'kapasitas'  => 'required|integer|greater_than[0]',
        ];

        // Memeriksa jika inputan edit TIDAK valid
        if (!$this->validate($rules)) {
            // Kembali ke form dengan inputan sebelumnya, daftar error, dan ID modal edit yang harus dibuka kembali
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())->with('open_edit_id', $id);
        }

        // Menjalankan perintah pembaruan data pada baris jadwal yang sesuai dengan ID
        $this->jadwalModel->update($id, [
            'tanggal'    => $this->request->getPost('tanggal'),
            'slot_waktu' => $this->request->getPost('slot_waktu'),
            'kapasitas'  => $this->request->getPost('kapasitas'),
            // Kolom 'terisi' sengaja tidak diubah di sini agar jumlah pesanan pelanggan yang masuk tidak hilang
        ]);

        // Redirect ke halaman utama kelola jadwal dengan pesan sukses
        return redirect()->to('/admin/jadwal')->with('success', 'Jadwal berhasil diperbarui!');
    }

    /**
     * Menghapus data jadwal berdasarkan ID
     */
    public function jadwalDelete($id)
    {
        // Menghapus baris data jadwal di database yang cocok dengan ID
        $this->jadwalModel->delete($id);
        
        // Redirect kembali ke halaman utama kelola jadwal dengan pesan sukses
        return redirect()->to('/admin/jadwal')->with('success', 'Jadwal berhasil dihapus!');
    }
}