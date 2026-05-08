<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class GuestFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Jika sudah login dan mencoba akses halaman guest (seperti login/register),
        // arahkan kembali ke dashboard masing-masing.
        if ($session->get('logged_in')) {
            $role = $session->get('role');
            
            if ($role === 'admin') {
                return redirect()->to('/admin');
            } elseif ($role === 'staff') {
                return redirect()->to('/staff');
            } else {
                return redirect()->to('/user');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
