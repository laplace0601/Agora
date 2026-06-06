<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    // Muestra el formulario de login
    public function index()
    {
        return view('general/login');
    }

    // Procesa los datos enviados por el formulario
    public function processLogin()
    {
        // Iniciamos el servicio de sesión
        $session = session();
        $model = new UserModel();

        // Capturamos los datos del POST
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // 1. Buscamos al usuario por su email
        $user = $model->where('email', $email)->first();

        if ($user) {
            // 2. Verificamos la contraseña
            // password_verify compara el texto plano ingresado con el hash guardado en la BD
            $authenticatePassword = password_verify($password, $user['password']);

            if ($authenticatePassword) {
                // 3. Si es correcto, creamos las variables de sesión
                $ses_data = [
                    'user_id'    => $user['id'],
                    'email'      => $user['email'],
                    'logged_in'  => TRUE // Bandera para saber que está logueado
                ];
                $session->set($ses_data);

                // Redirigimos a la página privada
                return redirect()->to('/dashboard');
            } else {
                // Contraseña incorrecta
                $session->setFlashdata('msg', 'Contraseña incorrecta.');
                return redirect()->to('/login');
            }
        } else {
            // El usuario no existe
            $session->setFlashdata('msg', 'El correo no fue encontrado.');
            return redirect()->to('/login');
        }
    }

    // Lógica para cerrar sesión
    public function logout()
    {
        $session = session();
        $session->destroy(); // Destruimos todas las variables de sesión
        return redirect()->to('/login');
    }
}
