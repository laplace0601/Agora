<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\ResidenteModel;
use App\Models\CondominioModel;
use App\Models\ApartamentoModel;

class UsuarioController extends BaseController
{
    // ---------------------------------------------------------------
    // Vistas
    // ---------------------------------------------------------------

    public function crearUsuario()
    {
        $condominioModel = new CondominioModel();
        $apartamentoModel = new ApartamentoModel();

        $datos = [
            'condominios'  => $condominioModel->findAll(),
            'apartamentos' => $apartamentoModel->findAll(),
        ];

        return view('root/crear_usuario', $datos);
    }

    public function usuarios()
    {
        return view('root/gestion_usuarios');
    }

    // ---------------------------------------------------------------
    // Creación de Usuarios
    // ---------------------------------------------------------------

    public function guardarResidente()
    {
        $usuarioModel = new UsuarioModel();
        $residenteModel = new ResidenteModel();

        $nombre = trim($this->request->getPost('nombre') ?? '');
        $cedula = trim($this->request->getPost('cedula') ?? '');
        $telefono = trim($this->request->getPost('telefono') ?? '');
        $correo = trim($this->request->getPost('correo') ?? '');
        $clave = $this->request->getPost('clave');
        $apartamentoId = $this->request->getPost('apartamento_id');

        if (empty($nombre) || empty($cedula) || empty($correo) || empty($apartamentoId) || empty($clave)) {
            return redirect()->back()->with('error', 'Faltan campos obligatorios para registrar el residente.');
        }

        // VALIDACIÓN ESTRICTA DE ROLES (Cruce de Roles Crítico)
        $usuarioExistente = $usuarioModel->where('correo', $correo)->first();
        if ($usuarioExistente && in_array($usuarioExistente['rol'], ['admin', 'root'])) {
            return redirect()->back()->with('error', 'No se puede registrar este correo como residente porque pertenece a un administrador del sistema.');
        }

        $datosUsuario = [
            'correo' => $correo,
            'nombre_usuario' => strtolower(explode(' ', $nombre)[0] . rand(10, 99)),
            'clave' => $clave, // El UsuarioModel aplicará password_hash() automáticamente antes de insertar
            'rol' => 'residente',
            'estado' => 'activo',
        ];

        $datosResidente = [
            'nombre_completo' => $nombre,
            'cedula_identidad' => $cedula,
            'telefono' => $telefono,
        ];

        $datosTransaccion = [
            'usuario' => $datosUsuario,
            'residente' => $datosResidente,
            'apartamento_id' => $apartamentoId
        ];

        $errores = [];
        if (!$residenteModel->crearResidenteCompleto($datosTransaccion, $errores)) {
            $msjError = !empty($errores) ? implode(', ', $errores) : 'Error desconocido.';
            return redirect()->back()->with('error', 'Error al crear residente: ' . $msjError);
        }

        return redirect()->to(site_url('super/crear-usuario'))->with('success', 'Residente dado de alta y asignado al apartamento exitosamente.');
    }

    public function guardarAdmin()
    {
        $usuarioModel = new UsuarioModel();

        $correo        = trim($this->request->getPost('correo') ?? '');
        $nombreUsuario = trim($this->request->getPost('nombre_usuario') ?? '');
        $clave         = $this->request->getPost('clave');

        // Validación de campos obligatorios
        if (empty($correo) || empty($nombreUsuario) || empty($clave)) {
            return redirect()->back()->with('error', 'El correo, nombre de usuario y contraseña son obligatorios.');
        }

        // Prevención de correos duplicados
        if ($usuarioModel->where('correo', $correo)->first()) {
            return redirect()->back()->with('error', "El correo '{$correo}' ya está registrado en el sistema.");
        }

        $datosUsuario = [
            'correo'         => $correo,
            'nombre_usuario' => $nombreUsuario,
            'clave'          => $clave, // UsuarioModel aplica password_hash() via beforeInsert
            'rol'            => 'admin',
            'estado'         => 'activo',
        ];

        if (! $usuarioModel->insert($datosUsuario)) {
            return redirect()->back()->with('error', 'Error al crear el administrador: ' . implode(', ', $usuarioModel->errors()));
        }

        log_message('info', "[UsuarioController] Admin creado: correo={$correo}, nombre_usuario={$nombreUsuario}");

        return redirect()->to(site_url('super/crear-usuario'))
            ->with('success', "Administrador '{$nombreUsuario}' dado de alta exitosamente.");
    }

    public function guardarSuper()
    {
        if (session()->get('rol') !== 'root') {
            return redirect()->to(site_url('auth/login'))->with('error', 'Acceso denegado. Solo un root puede crear otro súper usuario.');
        }

        $usuarioModel = new UsuarioModel();

        $correo        = trim($this->request->getPost('correo') ?? '');
        $nombreUsuario = trim($this->request->getPost('nombre_usuario') ?? '');
        $clave         = $this->request->getPost('clave');

        if (empty($correo) || empty($nombreUsuario) || empty($clave)) {
            return redirect()->back()->with('error', 'El correo, nombre de usuario y contraseña son obligatorios.');
        }

        // Prevención de correos duplicados
        if ($usuarioModel->where('correo', $correo)->first()) {
            return redirect()->back()->with('error', "El correo '{$correo}' ya está registrado en el sistema.");
        }

        $datosUsuario = [
            'correo'         => $correo,
            'nombre_usuario' => $nombreUsuario,
            'clave'          => $clave, // UsuarioModel aplica password_hash() via beforeInsert
            'rol'            => 'root',
            'estado'         => 'activo',
        ];

        if (! $usuarioModel->insert($datosUsuario)) {
            return redirect()->back()->with('error', 'Error al registrar el súper usuario: ' . implode(', ', $usuarioModel->errors()));
        }

        log_message('info', "[UsuarioController] Nuevo root creado: correo={$correo}, nombre_usuario={$nombreUsuario}");

        return redirect()->to(site_url('super/crear-usuario'))
            ->with('success', "Súper Usuario '{$nombreUsuario}' registrado exitosamente.");
    }
}
