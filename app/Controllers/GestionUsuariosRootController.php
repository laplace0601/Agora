<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\API\ResponseTrait;

class GestionUsuariosRootController extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        $session = session();
        
        // SEGURIDAD CRÍTICA: Impedir el acceso a cualquier rol que no sea 'root'
        // Esto protege el controlador incluso si se omiten los filtros en Routes.php
        // Garantiza la separación entre el Superadmin de infraestructura y el Administrador de condominio.
        if ($session->get('rol') !== 'root') {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['error' => 'Acceso denegado. Se requiere privilegios de superadmin (root).']);
            exit;
        }
    }

    public function index()
    {
        $usuarioModel = new UsuarioModel();
        
        // Listar usuarios excluyendo los que tienen eliminación lógica
        $usuarios = $usuarioModel->where('estado !=', 'eliminado')->findAll();
        
        // Sanitizar el payload (nunca devolver hashes de claves al frontend)
        foreach ($usuarios as &$u) {
            unset($u['clave']);
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $usuarios]);
    }

    public function update($id = null)
    {
        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID de usuario requerido.']);
        }

        // PREVENCIÓN DE AUTO-EDICIÓN: El Root no puede modificar sus propios privilegios accidentalmente
        if (session()->get('usuario_id') == $id) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acción denegada: No puedes auto-editarte desde este panel.']);
        }

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->find($id);

        if (!$usuario || $usuario['estado'] === 'eliminado') {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Usuario no encontrado.']);
        }

        $data = [
            'correo' => $this->request->getPost('correo'),
            'rol'    => $this->request->getPost('rol'),
            'estado' => $this->request->getPost('estado'),
        ];

        // Solo actualizar clave si el campo nueva_clave no está vacío
        $nuevaClave = $this->request->getPost('nueva_clave');
        if (!empty($nuevaClave)) {
            // Se inyecta en 'clave' para que el callback 'hashearClave' del UsuarioModel 
            // lo intercepte y aplique password_hash automáticamente.
            $data['clave'] = $nuevaClave; 
        }

        if (!$usuarioModel->update($id, $data)) {
            return $this->response->setStatusCode(422)->setJSON([
                'error' => 'Error de validación.',
                'detalles' => $usuarioModel->errors(),
                'csrf' => csrf_hash()
            ]);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Usuario actualizado exitosamente.', 'csrf' => csrf_hash()]);
    }

    public function delete($id = null)
    {
        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID de usuario requerido.', 'csrf' => csrf_hash()]);
        }

        // PREVENCIÓN DE AUTO-ELIMINACIÓN: Evitar que el Root se bloquee a sí mismo fuera del sistema
        if (session()->get('usuario_id') == $id) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acción crítica denegada: No puedes auto-eliminarte.', 'csrf' => csrf_hash()]);
        }

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->find($id);

        if (!$usuario || $usuario['estado'] === 'eliminado') {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Usuario no encontrado o ya ha sido eliminado.', 'csrf' => csrf_hash()]);
        }

        // ELIMINACIÓN LÓGICA (Soft Delete Manual)
        if (!$usuarioModel->update($id, ['estado' => 'eliminado'])) {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error del servidor al intentar eliminar el usuario.', 'csrf' => csrf_hash()]);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Usuario eliminado lógicamente de la plataforma.', 'csrf' => csrf_hash()]);
    }
}
