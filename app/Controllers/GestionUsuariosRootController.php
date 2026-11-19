<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * GestionUsuariosRootController
 *
 * API JSON para gestión integral de usuarios desde el panel Root.
 * Protegido doblemente: filtro de ruta + guardia de ciclo de vida CI4.
 */
class GestionUsuariosRootController extends BaseController
{
    use ResponseTrait;

    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        // Guardia de seguridad en el ciclo de vida de CI4:
        // El filtro 'auth:root' en Routes.php ya protege estas rutas, pero esta comprobación
        // proporciona defensa en profundidad sin romper el framework con llamadas a exit().
        if (session()->get('rol') !== 'root') {
            throw PageNotFoundException::forPageNotFound('Acceso denegado. Se requieren privilegios de superadmin.');
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
            return $this->responder('error', 'ID de usuario requerido.', 400, [], 'super/gestion_usuarios');
        }

        // PREVENCIÓN DE AUTO-EDICIÓN: El Root no puede modificar sus propios privilegios accidentalmente
        if (session()->get('usuario_id') == $id) {
            return $this->responder('error', 'Acción denegada: No puedes auto-editarte desde este panel.', 403, [], 'super/gestion_usuarios');
        }

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->find($id);

        if (!$usuario || $usuario['estado'] === 'eliminado') {
            return $this->responder('error', 'Usuario no encontrado.', 404, [], 'super/gestion_usuarios');
        }

        // --- Recoger datos del POST ---
        $correo = trim($this->request->getPost('correo') ?? '');
        $rol    = $this->request->getPost('rol');
        $estado = $this->request->getPost('estado');
        $nuevaClave = $this->request->getPost('nueva_clave');
        $nuevaClaveConfirm = $this->request->getPost('nueva_clave_confirm');

        // --- Validación manual para evitar conflictos con reglas del modelo ---

        // Validar correo obligatorio y formato
        if (empty($correo) || ! filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return $this->responder('error', 'Debe ingresar un correo electrónico válido.', 422, [], 'super/gestion_usuarios');
        }

        // Validar unicidad del correo (excluyendo al usuario actual)
        if ($correo !== $usuario['correo']) {
            $existente = $usuarioModel->where('correo', $correo)
                                      ->where('id !=', $id)
                                      ->first();
            if ($existente) {
                return $this->responder('error', "El correo '{$correo}' ya está registrado en otro usuario.", 422, [], 'super/gestion_usuarios');
            }
        }

        // Validar rol
        if (! in_array($rol, ['root', 'admin', 'residente'])) {
            return $this->responder('error', 'El rol seleccionado no es válido.', 422, [], 'super/gestion_usuarios');
        }

        // Validar estado
        if (! in_array($estado, ['activo', 'inactivo'])) {
            return $this->responder('error', 'El estado seleccionado no es válido.', 422, [], 'super/gestion_usuarios');
        }

        // Validar contraseña si se proporcionó en cualquiera de las 2 casillas
        if (!empty($nuevaClave) || !empty($nuevaClaveConfirm)) {
            if (strlen((string) $nuevaClave) < 8) {
                return $this->responder('error', 'La nueva contraseña debe tener al menos 8 caracteres.', 422, [], 'super/gestion_usuarios');
            }
            if ($nuevaClave !== $nuevaClaveConfirm) {
                return $this->responder('error', 'Las contraseñas no coinciden. Por favor verifíquelas en ambas casillas.', 422, [], 'super/gestion_usuarios');
            }
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
            return $this->responder('error', 'Error interno al actualizar el usuario.', 500, [], 'super/gestion_usuarios');
        }

        $usuarioModel->protect(true);

        log_message('info', "[GestionUsuariosRoot] Usuario #{$id} actualizado por root #" . session()->get('usuario_id'));

        return $this->responder('success', 'Usuario actualizado exitosamente.', 200, [], 'super/gestion_usuarios');
    }

    public function delete($id = null)
    {
        if (!$id) {
            return $this->responder('error', 'ID de usuario requerido.', 400, [], 'super/gestion_usuarios');
        }

        // PREVENCIÓN DE AUTO-ELIMINACIÓN: Evitar que el Root se bloquee a sí mismo fuera del sistema
        if (session()->get('usuario_id') == $id) {
            return $this->responder('error', 'Acción crítica denegada: No puedes auto-eliminarte.', 403, [], 'super/gestion_usuarios');
        }

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->find($id);

        if (!$usuario) {
            return $this->responder('error', 'Usuario no encontrado.', 404, [], 'super/gestion_usuarios');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Si es residente, eliminar en cascada todas sus relaciones
            if ($usuario['rol'] === 'residente') {
                $residenteModel    = new \App\Models\ResidenteModel();
                $apartamentoModel  = new \App\Models\ApartamentoModel();

                $residente = $residenteModel->obtenerPorUsuario((int)$id);

                if ($residente) {
                    $residenteId = $residente['id'];

                    // 1. Liberar apartamento(s) asignado(s) a este residente
                    $db->table('apartamentos')
                       ->where('residente_id', $residenteId)
                       ->update(['residente_id' => null]);

                    // 2. Eliminar pagos recibidos relacionados (FK a recibos_mensuales)
                    $reciboIds = $db->table('recibos_mensuales')
                                   ->select('id')
                                   ->where('residente_id', $residenteId)
                                   ->get()->getResultArray();

                    if (!empty($reciboIds)) {
                        $ids = array_column($reciboIds, 'id');
                        $db->table('pagos_recibidos')
                           ->whereIn('recibo_mensual_id', $ids)
                           ->delete();
                    }

                    // 3. Eliminar recibos mensuales del residente
                    $db->table('recibos_mensuales')
                       ->where('residente_id', $residenteId)
                       ->delete();

                    // 4. Eliminar recibos de solvencia del residente
                    $db->table('recibos_solvencia')
                       ->where('residente_id', $residenteId)
                       ->delete();

                    // 5. Eliminar reservas de áreas comunes del residente
                    $db->table('reservas_areas')
                       ->where('residente_id', $residenteId)
                       ->delete();

                    // 6. Eliminar tickets / comunicados del residente (si existen)
                    if ($db->tableExists('tickets')) {
                        $db->table('tickets')
                           ->where('residente_id', $residenteId)
                           ->delete();
                    }

                    // 7. Eliminar el registro de residente
                    $db->table('residentes')
                       ->where('id', $residenteId)
                       ->delete();
                }
            }

            // Hard-delete definitivo del usuario
            $db->table('usuarios')
               ->where('id', $id)
               ->delete();

        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', "[GestionUsuariosRoot] Error al eliminar usuario #{$id}: " . $e->getMessage());
            return $this->responder('error', 'Error interno al eliminar el usuario. Inténtalo de nuevo.', 500, [], 'super/gestion_usuarios');
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->responder('error', 'La operación falló a nivel de base de datos. No se eliminó el usuario.', 500, [], 'super/gestion_usuarios');
        }

        log_message('info', "[GestionUsuariosRoot] Usuario #{$id} (rol: {$usuario['rol']}) ELIMINADO PERMANENTEMENTE por root #" . session()->get('usuario_id'));

        return $this->responder('success', 'Usuario eliminado permanentemente de la plataforma.', 200, [], 'super/gestion_usuarios');
    }
}
