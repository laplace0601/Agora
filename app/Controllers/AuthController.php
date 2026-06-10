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

    /**
     * POST /auth/login
     *
     * Valida credenciales y procesa el inicio de sesión.
     * Retorna JSON.
     */
    public function processLogin()
    {
        $correo = trim($this->request->getPost('correo') ?? '');
        $clave  = $this->request->getPost('clave') ?? '';

        // Validación de campos vacíos
        if ($correo === '' || $clave === '') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'El correo y la contraseña son obligatorios.',
            ])->setStatusCode(400);
        }

        $usuarioModel = new UsuarioModel();
        $usuario      = $usuarioModel->verificarCredenciales($correo, $clave);

        if (! $usuario) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Credenciales incorrectas o cuenta inactiva.',
            ])->setStatusCode(401);
        }

        // Crear sesión nativa de CodeIgniter
        $session = session();
        $session->set([
            'usuario_id' => $usuario['id'],
            'correo'     => $usuario['correo'],
            'rol'        => $usuario['rol'],
            'isLoggedIn' => true,
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Inicio de sesión exitoso.',
            'data'    => [
                'usuario_id' => $usuario['id'],
                'correo'     => $usuario['correo'],
                'rol'        => $usuario['rol'],
            ],
        ]);
    }

    /**
     * GET /auth/logout
     *
     * Destruye la sesión activa y retorna JSON de confirmación.
     */
    public function logout()
    {
        $session = session();
        $session->destroy();

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }
}
