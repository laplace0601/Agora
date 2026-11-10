<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\API\ResponseTrait;

/**
 * GestionUsuariosRootController
 *
 * API JSON para gestión integral de usuarios desde el panel Root.
 * Protegido doblemente: filtro de ruta + guardia de constructor.
 */
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

    /**
     * POST /super/api/usuarios/update/(:num)
     *
     * Actualiza datos de un usuario. Maneja el cambio de contraseña de forma segura:
     * - Validación manual del correo (unicidad excluyendo al usuario actual)
     * - Hash de contraseña via PASSWORD_BCRYPT
     * - No falla si la contraseña no se cambia (campo vacío)
     */
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

        // --- Recoger datos del POST ---
        $correo = trim($this->request->getPost('correo') ?? '');
        $rol    = $this->request->getPost('rol');
        $estado = $this->request->getPost('estado');
        $nuevaClave = $this->request->getPost('nueva_clave');

        // --- Validación manual para evitar conflictos con reglas del modelo ---

        // Validar correo obligatorio y formato
        if (empty($correo) || ! filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(422)->setJSON([
                'error'  => 'Debe ingresar un correo electrónico válido.',
                'csrf'   => csrf_hash(),
            ]);
        }

        // Validar unicidad del correo (excluyendo al usuario actual)
        if ($correo !== $usuario['correo']) {
            $existente = $usuarioModel->where('correo', $correo)
                                      ->where('id !=', $id)
                                      ->first();
            if ($existente) {
                return $this->response->setStatusCode(422)->setJSON([
                    'error'  => "El correo '{$correo}' ya está registrado en otro usuario.",
                    'csrf'   => csrf_hash(),
                ]);
            }
        }

        // Validar rol
        if (! in_array($rol, ['root', 'admin', 'residente'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'error'  => 'El rol seleccionado no es válido.',
                'csrf'   => csrf_hash(),
            ]);
        }

        // Validar estado
        if (! in_array($estado, ['activo', 'inactivo'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'error'  => 'El estado seleccionado no es válido.',
                'csrf'   => csrf_hash(),
            ]);
        }

        // Validar contraseña si se proporcionó
        if (!empty($nuevaClave) && strlen($nuevaClave) < 6) {
            return $this->response->setStatusCode(422)->setJSON([
                'error'  => 'La contraseña debe tener al menos 6 caracteres.',
                'csrf'   => csrf_hash(),
            ]);
        }

        // --- Construir datos de actualización ---
        $data = [
            'correo' => $correo,
            'rol'    => $rol,
            'estado' => $estado,
        ];

        // Solo incluir la clave si se proporcionó una nueva.
        // Se envía en texto plano para que el callback 'beforeUpdate' del UsuarioModel
        // realice el hashing de manera centralizada y segura (evita doble hashing).
        if (!empty($nuevaClave)) {
            $data['clave'] = $nuevaClave;
        }

        // Saltar validación del modelo (ya validamos manualmente arriba)
        $usuarioModel->skipValidation(true);
        
        if (!$usuarioModel->protect(false)->update($id, $data)) {
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Error interno al actualizar el usuario.',
                'csrf'  => csrf_hash(),
            ]);
        }

        $usuarioModel->protect(true);

        log_message('info', "[GestionUsuariosRoot] Usuario #{$id} actualizado por root #" . session()->get('usuario_id'));

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Usuario actualizado exitosamente.',
            'csrf'    => csrf_hash(),
        ]);
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

        // LÓGICA DE ELIMINACIÓN SEGURA: Si es residente, desvincular de apartamentos
        if ($usuario['rol'] === 'residente') {
            $residenteModel = new \App\Models\ResidenteModel();
            $apartamentoModel = new \App\Models\ApartamentoModel();
            
            $residente = $residenteModel->obtenerPorUsuario($id);
            if ($residente) {
                // Liberar el apartamento asignado a este residente
                $apartamentoModel->where('residente_id', $residente['id'])
                                 ->set(['residente_id' => null])
                                 ->update();
            }
        }

        // ELIMINACIÓN LÓGICA (Soft Delete Manual) y prevención de login
        $usuarioModel->skipValidation(true);
        if (!$usuarioModel->update($id, ['estado' => 'eliminado'])) {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error del servidor al intentar eliminar el usuario.', 'csrf' => csrf_hash()]);
        }

        log_message('info', "[GestionUsuariosRoot] Usuario #{$id} eliminado lógicamente por root #" . session()->get('usuario_id'));

        return $this->response->setJSON(['status' => 'success', 'message' => 'Usuario eliminado lógicamente de la plataforma.', 'csrf' => csrf_hash()]);
    }
}
