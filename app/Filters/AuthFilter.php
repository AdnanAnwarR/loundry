<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Cek apakah user sudah login
        if (!$session->get('logged_in')) {
            $session->setFlashdata('msg', 'Silakan login terlebih dahulu.');
            return redirect()->to('/login');
        }
        
        // Cek Role jika diberikan argumen pada route
        if ($arguments !== null) {
            $role = $session->get('role');
            
            // Cek apakah role user saat ini ada dalam daftar role yang diperbolehkan
            if (!in_array($role, $arguments)) {
                // Lempar kembali ke dashboard yang sesuai dengan rolenya
                if ($role === 'admin') {
                    return redirect()->to('/admin');
                } elseif ($role === 'staff') {
                    return redirect()->to('/staff');
                } else {
                    return redirect()->to('/user');
                }
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
