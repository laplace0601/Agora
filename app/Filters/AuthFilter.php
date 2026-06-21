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

        // Validación de inactividad
        $sessionStartTime = session()->get('session_start_time');
        if ($sessionStartTime && (time() - $sessionStartTime > 600)) {
            session()->destroy();
            return redirect()->to(site_url('auth/login'))->with('error', 'Tu sesión ha expirado por inactividad. Por favor, inicia sesión de nuevo.');
        }

        // Renovar el tiempo de inicio de sesión para contar inactividad
        session()->set('session_start_time', time());

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
