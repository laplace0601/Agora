<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class AuthController extends BaseController
{
    /**
     * GET / o GET /login
     *
     * Carga la vista del formulario de Login.
     */
    public function login()
    {
        return view('auth/login');
    }
    public function processLogin()
    {
        $correo = $this->request->getPost('correo');
        $clave = $this->request->getPost('clave');

        if (empty($correo) || empty($clave)) {
            return redirect()->back()->with('error', 'El correo y la contraseña son requeridos.');
        }

        $modelo = new \App\Models\UsuarioModel();
        $usuario = $modelo->where('correo', $correo)->first();

        // Verificación Paso 1: El correo existe
        if (!$usuario) {
            return redirect()->back()->with('error', 'El correo no está registrado.');
        }

        // Verificación Paso 2: La contraseña es correcta
        if (!password_verify($clave, $usuario['clave'])) {
            return redirect()->back()->with('error', 'La contraseña es incorrecta.');
        }

        // Inicio de sesión exitoso
        $rol = $usuario['rol'] ?? '';
        session()->set([
            'usuario_id'   => $usuario['id'] ?? null,
            'correo'       => $usuario['correo'],
            'rol'          => $rol,
            'is_logged_in' => true
        ]);

        switch ($rol) {
            case 'root':
                return redirect()->to(site_url('super/panel'));
            case 'admin':
                return redirect()->to(site_url('admin/comunidad'));
            case 'residente':
                return redirect()->to(site_url('residente/dashboard'));
            default:
                session()->destroy();
                return redirect()->back()->with('error', 'El rol asignado no es válido.');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('auth/login'));
    }
}
