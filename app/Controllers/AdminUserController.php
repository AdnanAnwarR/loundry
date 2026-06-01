<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AdminUserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        // Instansiasi UserModel
        $this->userModel = new UserModel();
    }

    /**
     * Menampilkan daftar user/pelanggan terpaginasi
     */
    public function usersIndex()
    {
        // Melakukan paginasi data user/pelanggan sebanyak 10 baris data per halaman menggunakan Model Query Builder
        $users = $this->userModel->where('role', 'pelanggan')->orderBy('created_at', 'DESC')->paginate(10, 'default', null, 0);
        return view('admin/user/index', [
            'title' => 'Metode Manajemen User', 
            'users' => $users,
            'pager' => $this->userModel->pager, // Objek pager untuk link halaman
        ]);
    }

    /**
     * Mengaktifkan/menonaktifkan akun user pelanggan secara cepat
     */
    public function usersToggle($id)
    {
        $user = $this->userModel->find($id);
        if ($user) {
            $this->userModel->update($id, ['is_active' => $user->is_active ? 0 : 1]);
        }
        return redirect()->to('/admin/users')->with('success', 'Status user diperbarui.');
    }

    /**
     * Menghapus akun user pelanggan
     */
    public function usersDelete($id)
    {
        $this->userModel->delete($id);
        return redirect()->to('/admin/users')->with('success', 'User berhasil dihapus.');
    }
}
