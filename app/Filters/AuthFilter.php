<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('auth/login'))->with('error', 'Debe iniciar sesión para acceder a esta área.');
        }

        if ($arguments && !empty($arguments)) {
            $rol = session()->get('rol');
            if (!in_array($rol, $arguments)) {
                return redirect()->to(site_url('auth/login'))->with('error', 'No tiene permisos para acceder a esta área.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
