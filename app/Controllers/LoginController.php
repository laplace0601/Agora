<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class LoginController extends BaseController
{
    public function index()
    {
        // Esto carga tu hermosa vista del login (app/Views/login.php)
        return view('login');
    }

    public function autenticar()
    {
        // Recibe lo que la persona escribió en tu formulario HTML
        $correo = $this->request->getPost('correo');
        $clave  = $this->request->getPost('clave');

        // Por ahora, como no hay base de datos conectada, simulamos el éxito
        if ($correo == "admin@agora.com" && $clave == "123456") {
            echo "¡Bienvenido al sistema de condominio Agora!";
            return;
        }

        return redirect()->to(base_url('/'))->with('error', 'Datos incorrectos');
    }
}