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
        $users = $this->userModel->whereIn('role', ['pelanggan', 'admin'])->orderBy('created_at', 'DESC')->paginate(10, 'default', null, 0);
        return view('admin/user/index', [
            'title' => 'Manajemen User', 
            'users' => $users,
            'pager' => $this->userModel->pager, // Objek pager untuk link halaman
        ]);
    }

    /**
     * Menyimpan user baru ke database
     */
    public function usersStore()
    {
        $rules = [
            'name'     => 'required|min_length[3]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'no_hp'    => 'required|min_length[10]|is_unique[users.no_hp]',
            'password' => 'required|min_length[6]',
            'role'     => 'required|in_list[pelanggan,admin]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())->with('open_add_modal', true);
        }

        $this->userModel->save([
            'name'      => $this->request->getPost('name'),
            'email'     => $this->request->getPost('email'),
            'no_hp'     => $this->request->getPost('no_hp'),
            'password'  => $this->request->getPost('password'),
            'role'      => $this->request->getPost('role'),
            'is_active' => 1,
        ]);

        return redirect()->to('/admin/users')->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Menyimpan pembaruan data user
     */
    public function usersUpdate($id)
    {
        $rules = [
            'name'  => 'required|min_length[3]',
            'email' => "required|valid_email|is_unique[users.email,id,$id]",
            'no_hp' => "required|min_length[10]|is_unique[users.no_hp,id,$id]",
            'role'  => 'required|in_list[pelanggan,admin]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())->with('open_edit_id', $id);
        }

        $data = [
            'name'      => $this->request->getPost('name'),
            'email'     => $this->request->getPost('email'),
            'no_hp'     => $this->request->getPost('no_hp'),
            'role'      => $this->request->getPost('role'),
            'is_active' => $this->request->getPost('is_active') ?? 1,
        ];

        $newPwd = $this->request->getPost('password');
        if ($newPwd) $data['password'] = $newPwd;

        $this->userModel->update($id, $data);
        return redirect()->to('/admin/users')->with('success', 'Data user berhasil diperbarui!');
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
