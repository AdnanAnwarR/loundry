<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AdminStaffController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        // Instansiasi UserModel
        $this->userModel = new UserModel();
    }

    /**
     * Menampilkan daftar staff terpaginasi
     */
    public function staffIndex()
    {
        // Melakukan paginasi data staff sebanyak 10 baris data per halaman menggunakan Model Query Builder
        $staff = $this->userModel->where('role', 'staff')->orderBy('created_at', 'DESC')->paginate(10, 'default', null, 0);
        return view('admin/staff/index', [
            'title' => 'Kelola Staff', 
            'staff' => $staff,
            'pager' => $this->userModel->pager, // Objek pager untuk link halaman
        ]);
    }

    /**
     * Halaman tambah staff baru
     */
    public function staffCreate()
    {
        return view('admin/staff/create', ['title' => 'Tambah Staff']);
    }

    /**
     * Menyimpan data staff baru ke database
     */
    public function staffStore()
    {
        $rules = [
            'name'     => 'required|min_length[3]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'no_hp'    => 'required|min_length[10]|is_unique[users.no_hp]',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())->with('open_add_modal', true);
        }

        $this->userModel->save([
            'name'      => $this->request->getPost('name'),
            'email'     => $this->request->getPost('email'),
            'no_hp'     => $this->request->getPost('no_hp'),
            'password'  => $this->request->getPost('password'),
            'role'      => 'staff',
            'is_active' => 1,
        ]);

        return redirect()->to('/admin/staff')->with('success', 'Staff berhasil ditambahkan!');
    }

    /**
     * Halaman edit data staff
     */
    public function staffEdit($id)
    {
        $staff = $this->userModel->find($id);
        if (!$staff || $staff->role !== 'staff') return redirect()->to('/admin/staff')->with('error', 'Staff tidak ditemukan.');
        return view('admin/staff/edit', ['title' => 'Edit Staff', 'staff' => $staff]);
    }

    /**
     * Menyimpan pembaruan data staff
     */
    public function staffUpdate($id)
    {
        $rules = [
            'name'  => 'required|min_length[3]',
            'email' => "required|valid_email|is_unique[users.email,id,$id]",
            'no_hp' => "required|min_length[10]|is_unique[users.no_hp,id,$id]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())->with('open_edit_id', $id);
        }

        $data = [
            'name'      => $this->request->getPost('name'),
            'email'     => $this->request->getPost('email'),
            'no_hp'     => $this->request->getPost('no_hp'),
            'is_active' => $this->request->getPost('is_active') ?? 1,
        ];

        $newPwd = $this->request->getPost('password');
        if ($newPwd) $data['password'] = $newPwd;

        $this->userModel->update($id, $data);
        return redirect()->to('/admin/staff')->with('success', 'Data staff berhasil diperbarui!');
    }

    /**
     * Menghapus staff dari database
     */
    public function staffDelete($id)
    {
        $this->userModel->delete($id);
        return redirect()->to('/admin/staff')->with('success', 'Staff berhasil dihapus.');
    }
}
