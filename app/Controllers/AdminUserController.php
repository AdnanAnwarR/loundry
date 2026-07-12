<?php

namespace App\Controllers;

// Mengimpor BaseController bawaan CodeIgniter dan Model User
use App\Controllers\BaseController;
use App\Models\UserModel;

/**
 * Controller untuk mengelola akun dengan role 'pelanggan' dan 'admin' oleh Admin Utama.
 * Mengatur penampilan daftar pengguna, pembuatan akun baru, pengubahan profil/role, 
 * aktivasi status akun, hingga penghapusan akun.
 */
class AdminUserController extends BaseController
{
    // Variabel untuk menampung properti objek UserModel
    protected $userModel;

    /**
     * Constructor untuk inisialisasi awal saat Controller dipanggil
     */
    public function __construct()
    {
        // Instansiasi UserModel agar bisa melakukan operasi CRUD pada tabel users
        $this->userModel = new UserModel();
    }

    /**
     * Menampilkan daftar user/pelanggan terpaginasi
     */
    public function usersIndex()
    {
        // Mengambil data pengguna dengan filter role khusus 'pelanggan' dan 'admin' (staf tidak ikut di sini), 
        // diurutkan dari yang terbaru (DESC), dan dibagi per halaman (pagination) sebanyak 10 data
        $users = $this->userModel->whereIn('role', ['pelanggan', 'admin'])->orderBy('created_at', 'DESC')->paginate(10, 'default', null, 0);
        
        // Memuat file view cetakan halaman manajemen user dan melempar datanya
        return view('admin/user/index', [
            'title' => 'Manajemen User', 
            'users' => $users, // Data pengguna hasil paginasi
            'pager' => $this->userModel->pager, // Objek pager untuk link halaman (Next/Prev)
        ]);
    }

    /**
     * Menyimpan data user baru (pelanggan/admin) ke database
     */
    public function usersStore()
    {
        // Menentukan aturan validasi untuk inputan form tambah user baru
        $rules = [
            'name'     => 'required|min_length[3]',                     // Wajib diisi, minimal 3 karakter
            'email'    => 'required|valid_email|is_unique[users.email]', // Wajib diisi, format email valid, belum terdaftar
            'no_hp'    => 'required|min_length[10]|is_unique[users.no_hp]', // Wajib diisi, minimal 10 digit, belum terdaftar
            'password' => 'required|min_length[6]',                     // Wajib diisi, minimal 6 karakter
            'role'     => 'required|in_list[pelanggan,admin]',          // Wajib diisi, nilainya harus antara 'pelanggan' atau 'admin'
        ];

        // Memeriksa jika inputan form TIDAK lolos validasi
        if (!$this->validate($rules)) {
            // Kembali ke form sebelumnya dengan membawa inputan lama,
            // pesan error validasi, dan trigger agar modal tambah otomatis terbuka kembali
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())->with('open_add_modal', true);
        }

        // Jika lolos validasi, simpan data user baru ke database melalui Model
        $this->userModel->save([
            'name'      => $this->request->getPost('name'),
            'email'     => $this->request->getPost('email'),
            'no_hp'     => $this->request->getPost('no_hp'),
            'password'  => $this->request->getPost('password'), // Di-hash otomatis di model jika dikonfigurasi
            'role'      => $this->request->getPost('role'),     // Menyimpan sesuai pilihan (pelanggan/admin)
            'is_active' => 1,                                   // Otomatis aktif saat pertama kali dibuat
        ]);

        // Redirect kembali ke halaman utama manajemen user dengan pesan sukses
        return redirect()->to('/admin/users')->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Menyimpan pembaruan data user berdasarkan ID
     */
    public function usersUpdate($id)
    {
        // Aturan validasi untuk form ubah data user
        // Pengecekan unik mengabaikan ID user bersangkutan agar data tidak bentrok dengan dirinya sendiri
        $rules = [
            'name'  => 'required|min_length[3]',
            'email' => "required|valid_email|is_unique[users.email,id,$id]",
            'no_hp' => "required|min_length[10]|is_unique[users.no_hp,id,$id]",
            'role'  => 'required|in_list[pelanggan,admin]',
        ];

        // Memeriksa jika inputan edit TIDAK valid
        if (!$this->validate($rules)) {
            // Kembali ke form dengan membawa inputan sebelumnya, daftar error, dan ID modal edit yang harus dibuka kembali
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())->with('open_edit_id', $id);
        }

        // Menyiapkan susunan array data baru yang diambil dari inputan form
        $data = [
            'name'      => $this->request->getPost('name'),
            'email'     => $this->request->getPost('email'),
            'no_hp'     => $this->request->getPost('no_hp'),
            'role'      => $this->request->getPost('role'),
            'is_active' => $this->request->getPost('is_active') ?? 1, // Jika checkbox status kosong, set default ke 1
        ];

        // Memeriksa apakah pengubah juga mengisi kolom password baru
        $newPwd = $this->request->getPost('password');
        if ($newPwd) {
            // Jika kolom password diisi (tidak kosong), ikut perbarui password lama dengan password baru
            $data['password'] = $newPwd;
        }

        // Menjalankan perintah pembaruan data pada tabel users berdasarkan ID user terpilih
        $this->userModel->update($id, $data);
        
        // Redirect kembali ke halaman utama manajemen user dengan notifikasi sukses
        return redirect()->to('/admin/users')->with('success', 'Data user berhasil diperbarui!');
    }

    /**
     * Mengaktifkan/menonaktifkan akun user pelanggan/admin secara cepat (Toggle Switch)
     */
    public function usersToggle($id)
    {
        // Cari data user berdasarkan ID
        $user = $this->userModel->find($id);
        
        if ($user) {
            // Jika status aktif (1) ubah ke nonaktif (0), jika nonaktif (0) ubah ke aktif (1)
            $this->userModel->update($id, ['is_active' => $user->is_active ? 0 : 1]);
        }
        
        // Kembali ke halaman utama dengan pesan sukses
        return redirect()->to('/admin/users')->with('success', 'Status user diperbarui.');
    }

    /**
     * Menghapus akun user dari database berdasarkan ID
     */
    public function usersDelete($id)
    {
        // Menghapus baris data user di database yang cocok dengan ID
        $this->userModel->delete($id);
        
        // Redirect kembali ke halaman utama manajemen user dengan notifikasi sukses
        return redirect()->to('/admin/users')->with('success', 'User berhasil dihapus.');
    }
}