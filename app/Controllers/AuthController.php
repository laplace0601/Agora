<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class AuthController extends BaseController
{
    /**
     * GET / o GET /login
     *
     * Carga la vista del formulario de Login.
     * Si el usuario ya tiene sesión activa, lo redirige al panel correspondiente.
     */
    public function login()
    {
        // Evitar que un usuario ya autenticado vea el login (previene loop inverso)
        if (session()->get('isLoggedIn')) {
            return $this->_redirigirPorRol(session()->get('rol'));
        }

        return view('auth/login');
    }

    /**
     * POST /auth/procesar-login
     *
     * Procesa las credenciales enviadas por el formulario tradicional (no AJAX).
     * Responde con redirect() en todos los casos.
     */
    public function processLogin()
    {
        $correo = $this->request->getPost('correo');
        $clave  = $this->request->getPost('clave');

        if (empty($correo) || empty($clave)) {
            return redirect()->back()->with('error', 'El correo y la contraseña son requeridos.');
        }

        $modelo  = new UsuarioModel();
        // Usamos el método del modelo que valida correo + clave + estado 'activo'
        $usuario = $modelo->verificarCredenciales($correo, $clave);

        if (! $usuario) {
            // No revelamos si es el correo o la contraseña lo incorrecto (seguridad)
            return redirect()->back()->with('error', 'Credenciales incorrectas o cuenta inactiva.');
        }

        $rol = $usuario['rol'] ?? '';

        // ---------------------------------------------------------------
        // Guardar sesión — CLAVE UNIFICADA: 'isLoggedIn' (camelCase)
        // Todos los filtros y controladores usan esta misma clave.
        // ---------------------------------------------------------------
        session()->set([
            'usuario_id'         => $usuario['id'],
            'correo'             => $usuario['correo'],
            'rol'                => $rol,
            'isLoggedIn'         => true,          // ← clave canónica del sistema
            'session_start_time' => time(),
        ]);

        return $this->_redirigirPorRol($rol);
    }

    /**
     * GET /auth/logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('auth/login'));
    }

    // ---------------------------------------------------------------
    // Helpers privados
    // ---------------------------------------------------------------

    /**
     * Devuelve un redirect al panel correspondiente según el rol.
     */
    private function _redirigirPorRol(string $rol)
    {
        switch ($rol) {
            case 'root':
                return redirect()->to(site_url('super/apartamentos'));
            case 'admin':
                return redirect()->to(site_url('admin/cartelera'));
            case 'residente':
                return redirect()->to(site_url('residente/dashboard'));
            default:
                session()->destroy();
                return redirect()->to(site_url('auth/login'))
                    ->with('error', 'Rol no reconocido. Contacte al administrador.');
        }
    }
}
