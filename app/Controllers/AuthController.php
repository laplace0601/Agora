<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class AuthController extends BaseController
{
    /**
     * POST /auth/login
     *
     * Recibe 'correo' y 'clave' por POST.
     * Valida credenciales y crea la sesión del usuario.
     * Retorna JSON con resultado de la operación.
     */
    public function login()
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
