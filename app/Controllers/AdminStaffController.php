<?php

namespace App\Controllers;

// Mengimpor BaseController bawaan CodeIgniter dan Model User
use App\Controllers\BaseController;
use App\Models\UserModel;

/**
 * Controller untuk mengelola data akun Staf (Pegawai) oleh Admin.
 * Mengatur proses pendaftaran staf baru, penampilan daftar staf, pengubahan data akun, hingga penghapusan akun staf.
 */
class AdminStaffController extends BaseController
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
     * Menampilkan daftar akun staff terpaginasi
     */
    public function staffIndex()
    {
        // Mengambil data pengguna dengan filter role khusus 'staff', diurutkan dari yang terbaru (DESC), 
        // dan dibagi per halaman (pagination) sebanyak 10 data
        $staff = $this->userModel->where('role', 'staff')->orderBy('created_at', 'DESC')->paginate(10, 'default', null, 0);
        
        // Memuat file view cetakan halaman admin staff dan mengirimkan datanya
        return view('admin/staff/index', [
            'title' => 'Kelola Staff', 
            'staff' => $staff, // Data staff hasil paginasi
            'pager' => $this->userModel->pager, // Objek pager untuk link halaman (Next/Prev)
        ]);
    }

    /**
     * Menyimpan data staff baru ke database
     */
    public function staffStore()
    {
        // Menentukan aturan validasi untuk inputan form tambah staff baru
        $rules = [
            'name'     => 'required|min_length[3]',                     // Wajib diisi, minimal 3 karakter
            'email'    => 'required|valid_email|is_unique[users.email]', // Wajib diisi, format email valid, dan belum terdaftar di tabel users
            'no_hp'    => 'required|min_length[10]|is_unique[users.no_hp]', // Wajib diisi, minimal 10 digit, dan belum terdaftar
            'password' => 'required|min_length[6]',                     // Wajib diisi, minimal 6 karakter
        ];

        // Memeriksa jika inputan form TIDAK lolos validasi
        if (!$this->validate($rules)) {
            // Kembali ke form sebelumnya dengan membawa inputan lama,
            // pesan error validasi, dan trigger agar modal tambah otomatis terbuka kembali
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())->with('open_add_modal', true);
        }

        // Jika lolos validasi, simpan data staff baru ke database melalui Model
        $this->userModel->save([
            'name'      => $this->request->getPost('name'),
            'email'     => $this->request->getPost('email'),
            'no_hp'     => $this->request->getPost('no_hp'),
            'password'  => $this->request->getPost('password'), // Sebaiknya di-hash pada model menggunakan password_hash()
            'role'      => 'staff', // Otomatis mengeset role menjadi 'staff'
            'is_active' => 1,       // Otomatis aktif saat pertama kali dibuat
        ]);

        // Redirect kembali ke halaman utama kelola staff dengan pesan sukses
        return redirect()->to('/admin/staff')->with('success', 'Staff berhasil ditambahkan!');
    }

    /**
     * Menyimpan pembaruan data staff berdasarkan ID
     */
    public function staffUpdate($id)
    {
        // Aturan validasi untuk form ubah data staff
        // Note penulisan unik: is_unique[users.email,id,$id] berguna agar sistem mengabaikan email milik user ini sendiri saat dicek keunikannya
        $rules = [
            'name'  => 'required|min_length[3]',
            'email' => "required|valid_email|is_unique[users.email,id,$id]",
            'no_hp' => "required|min_length[10]|is_unique[users.no_hp,id,$id]",
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
            'is_active' => $this->request->getPost('is_active') ?? 1, // Jika checkbox status kosong, set default ke 1
        ];

        // Memeriksa apakah admin juga mengisi kolom password baru
        $newPwd = $this->request->getPost('password');
        if ($newPwd) {
            // Jika kolom password diisi (tidak kosong), masukkan password baru ke dalam array data yang akan diupdate
            $data['password'] = $newPwd;
        }

        // Menjalankan perintah pembaruan data pada tabel users berdasarkan ID staff terpilih
        $this->userModel->update($id, $data);
        
        // Redirect kembali ke halaman utama kelola staff dengan notifikasi sukses
        return redirect()->to('/admin/staff')->with('success', 'Data staff berhasil diperbarui!');
    }

    /**
     * Menghapus akun staff dari database berdasarkan ID
     */
    public function staffDelete($id)
    {
        // Menghapus baris data staff di database yang cocok dengan ID
        $this->userModel->delete($id);
        
        // Redirect kembali ke halaman utama kelola staff dengan notifikasi sukses
        return redirect()->to('/admin/staff')->with('success', 'Staff berhasil dihapus.');
    }
}