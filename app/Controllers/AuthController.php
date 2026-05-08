<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login()
    {
        return view('form/login');
    }

    public function processLogin()
    {
        $session = session();
        $userModel = new UserModel();

        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $user = $userModel->where('email', $email)->first();

        if ($user) {
            // Verifikasi password
            if (password_verify($password, $user->password)) {
                // Set session data
                $ses_data = [
                    'id'       => $user->id,
                    'name'     => $user->name,
                    'email'    => $user->email,
                    'role'     => $user->role,
                    'logged_in' => TRUE
                ];
                $session->set($ses_data);

                // Redirect sesuai role
                if ($user->role === 'admin') {
                    return redirect()->to('/admin');
                } elseif ($user->role === 'staff') {
                    return redirect()->to('/staff');
                } else {
                    return redirect()->to('/user');
                }
            } else {
                $session->setFlashdata('msg', 'Password salah.');
                return redirect()->to('/login');
            }
        } else {
            $session->setFlashdata('msg', 'Email tidak ditemukan.');
            return redirect()->to('/login');
        }
    }

    public function register()
    {
        return view('form/register');
    }

    public function processRegister()
    {
        $rules = [
            'name'     => 'required|min_length[3]|max_length[255]',
            'email'    => 'required|min_length[6]|max_length[255]|valid_email|is_unique[users.email]',
            'no_hp'    => 'required|min_length[10]|max_length[15]|is_unique[users.no_hp]',
            'password' => 'required|min_length[6]|max_length[255]',
        ];

        if ($this->validate($rules)) {
            $userModel = new UserModel();
            
            $data = [
                'name'     => $this->request->getVar('name'),
                'email'    => $this->request->getVar('email'),
                'no_hp'    => $this->request->getVar('no_hp'),
                'password' => $this->request->getVar('password'),
                'role'     => 'pelanggan', // Default role untuk registrasi
                'is_active'=> 1
            ];

            $userModel->save($data);
            
            session()->setFlashdata('success', 'Registrasi berhasil! Silakan login.');
            return redirect()->to('/login');
        } else {
            // Validation failed
            $data['validation'] = $this->validator;
            session()->setFlashdata('errors', $this->validator->getErrors());
            return redirect()->to('/register')->withInput();
        }
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/login');
    }
}
