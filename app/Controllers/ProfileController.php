<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class ProfileController extends BaseController
{
    public function index()
    {
        // Pastikan user sudah login
        $userId = session()->get('id');
        if (!$userId) {
            return redirect()->to('/login');
        }

        // Ambil data user dari database
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        // Tentukan layout berdasarkan role
        $role = session()->get('role');
        $layout = 'layout/main';
        if ($role === 'admin') {
            $layout = 'layout/admin_layout';
        }

        return view('profile/index', [
            'title'  => 'Edit Profil',
            'user'   => $user,
            'layout' => $layout
        ]);
    }

    public function update()
    {
        $session = session();
        $userId = $session->get('id');
        if (!$userId) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        // Aturan validasi
        $rules = [
            'name'  => 'required|min_length[3]|max_length[255]',
            'email' => "required|min_length[6]|max_length[255]|valid_email|is_unique[users.email,id,{$userId}]",
        ];

        // Jika password baru diisi, tambahkan validasi password
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = 'min_length[6]';
        }

        // Validasi file foto profil jika ada yang diupload
        $fotoFile = $this->request->getFile('foto');
        if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
            $rules['foto'] = 'uploaded[foto]|is_image[foto]|max_size[foto,2048]'; // Maksimal ukuran 2MB
        }

        if (!$this->validate($rules)) {
            $session->setFlashdata('errors', $this->validator->getErrors());
            return redirect()->to('/profile')->withInput();
        }

        // Siapkan data pembaruan
        $updateData = [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
        ];

        // Jika password diisi
        if (!empty($password)) {
            // UserModel hashPassword callback akan mengenkripsi otomatis sebelum simpan
            $updateData['password'] = $password;
        }

        // Proses penyimpanan file foto profil
        if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
            // Hapus foto profil lama jika ada
            if ($user->foto && file_exists(FCPATH . 'uploads/profile/' . $user->foto)) {
                @unlink(FCPATH . 'uploads/profile/' . $user->foto);
            }

            // Generate nama acak baru
            $newFotoName = $fotoFile->getRandomName();
            
            // Buat folder uploads/profile jika belum ada
            if (!is_dir(FCPATH . 'uploads/profile/')) {
                mkdir(FCPATH . 'uploads/profile/', 0777, true);
            }
            
            // Pindahkan file foto ke direktori public
            $fotoFile->move(FCPATH . 'uploads/profile/', $newFotoName);
            $updateData['foto'] = $newFotoName;

            // Simpan nama foto baru ke dalam session
            $session->set('foto', $newFotoName);
        }

        // Update database user
        $userModel->update($userId, $updateData);

        // Perbarui session nama dan email
        $session->set('name', $updateData['name']);
        $session->set('email', $updateData['email']);

        $session->setFlashdata('success', 'Profil Anda berhasil diperbarui!');
        return redirect()->to('/profile');
    }
}
