<?php

namespace App\Controllers;

use App\Models\HabitanteModel;
use App\Models\ApartamentoModel;

/**
 * HabitantesController
 *
 * CRUD completo para el censo de habitantes por apartamento.
 * - Admin/root: crear, editar, eliminar, aprobar/rechazar solicitudes.
 * - Residente: solicitar agregar habitantes (estado 'pendiente').
 */
class HabitantesController extends BaseController
{
    /**
     * GET /admin/apartamento/(:num)/habitantes
     *
     * Lista los habitantes de un apartamento específico (JSON para AJAX).
     * Admin ve TODOS (incluyendo pendientes); residente ve solo aprobados.
     */
    public function listar(int $apartamentoId)
    {
        $habitanteModel = new HabitanteModel();
        $rol = session()->get('rol');

        // Admin/root ve todos los estados; residente solo aprobados
        if (in_array($rol, ['admin', 'root'])) {
            $habitantes = $habitanteModel->listarTodosPorApartamento($apartamentoId);
        } else {
            $habitantes = $habitanteModel->listarPorApartamento($apartamentoId);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $habitantes,
        ]);
    }

    /**
     * POST /admin/habitantes/guardar
     *
     * Crea o actualiza un habitante (solo admin/root).
     * Los habitantes creados por admin se guardan como 'aprobado' directamente.
     */
    public function guardar()
    {
        $session = session();

        if (! $session->get('isLoggedIn') || ! in_array($session->get('rol'), ['admin', 'root'])) {
            return $this->responder('error', 'Acceso denegado.', 403);
        }

        $habitanteModel = new HabitanteModel();

        $id = $this->request->getPost('id');
        $apartamentoId = (int) $this->request->getPost('apartamento_id');

        // Validar que el apartamento existe
        $apartamentoModel = new ApartamentoModel();
        if (! $apartamentoModel->find($apartamentoId)) {
            return $this->responder('error', 'El apartamento indicado no existe.', 404);
        }

        $datos = [
            'apartamento_id'   => $apartamentoId,
            'nombre'           => trim($this->request->getPost('nombre') ?? ''),
            'apellido'         => trim($this->request->getPost('apellido') ?? ''),
            'cedula_identidad' => trim($this->request->getPost('cedula_identidad') ?? ''),
            'telefono'         => trim($this->request->getPost('telefono') ?? ''),
            'tipo_habitante'   => $this->request->getPost('tipo_habitante') ?? 'Inquilino',
            'es_principal'     => $this->request->getPost('es_principal') ? 1 : 0,
            'estado'           => 'aprobado', // Admin crea directamente como aprobado
        ];

        // Si se marca como principal, quitar el flag a los demás del mismo apartamento
        if ($datos['es_principal']) {
            $habitanteModel->desmarcarPrincipal($apartamentoId);
        }

        if ($id) {
            // Editar
            if (! $habitanteModel->update($id, $datos)) {
                return $this->responder('error', 'Error al actualizar el habitante.', 422, ['errors' => $habitanteModel->errors()]);
            }

            return $this->responder('success', 'Habitante actualizado correctamente.');
        }

        // Crear
        if (! $habitanteModel->insert($datos)) {
            return $this->responder('error', 'Error al registrar el habitante.', 422, ['errors' => $habitanteModel->errors()]);
        }

        return $this->responder('success', 'Habitante registrado correctamente.', 200, ['data' => ['id' => $habitanteModel->getInsertID()]]);
    }

    /**
     * POST /admin/habitantes/eliminar
     *
     * Elimina un habitante por ID (solo admin/root).
     */
    public function eliminar()
    {
        $session = session();

        if (! $session->get('isLoggedIn') || ! in_array($session->get('rol'), ['admin', 'root'])) {
            return $this->responder('error', 'Acceso denegado.', 403);
        }

        $id = (int) $this->request->getPost('id');

        if ($id <= 0) {
            return $this->responder('error', 'ID de habitante inválido.', 400);
        }

        $habitanteModel = new HabitanteModel();

        if (! $habitanteModel->find($id)) {
            return $this->responder('error', 'El habitante no existe.', 404);
        }

        $habitanteModel->delete($id);

        return $this->responder('success', 'Habitante eliminado correctamente.');
    }

    /**
     * GET /admin/habitantes/obtener/(:num)
     *
     * Obtiene un habitante por ID (para pre-llenar el formulario de edición).
     */
    public function obtener(int $id)
    {
        $habitanteModel = new HabitanteModel();
        $habitante = $habitanteModel->find($id);

        if (! $habitante) {
            return $this->responder('error', 'Habitante no encontrado.', 404);
        }

        return $this->responder('success', 'Habitante encontrado.', 200, ['data' => $habitante]);
    }

    // ---------------------------------------------------------------
    // Flujo de aprobación: Solicitudes de residentes
    // ---------------------------------------------------------------

    /**
     * POST /residente/habitantes/solicitar
     *
     * El residente solicita agregar un habitante a SU apartamento.
     * Se guarda con estado 'pendiente' hasta que el admin lo apruebe.
     */
    public function solicitar()
    {
        $session = session();

        if (! $session->get('isLoggedIn') || $session->get('rol') !== 'residente') {
            return $this->responder('error', 'Acceso denegado.', 403);
        }

        $usuarioId = $session->get('usuario_id');

        // Obtener apartamento(s) del residente
        $apartamentoModel = new ApartamentoModel();
        $apartamentos = $apartamentoModel->obtenerPorUsuario($usuarioId);

        if (empty($apartamentos)) {
            return $this->responder('error', 'No tienes un apartamento asignado.', 400);
        }

        $apartamentosValidos = array_column($apartamentos, 'id');
        $postAptoId = (int) $this->request->getPost('apartamento_id');

        // Si se envió un apartamento_id, verificar que pertenezca al usuario; de lo contrario, usar el primero
        if ($postAptoId > 0 && in_array($postAptoId, $apartamentosValidos, true)) {
            $apartamentoId = $postAptoId;
        } else {
            $apartamentoId = (int) $apartamentos[0]['id'];
        }

        $datos = [
            'apartamento_id'   => $apartamentoId,
            'nombre'           => trim($this->request->getPost('nombre') ?? ''),
            'apellido'         => trim($this->request->getPost('apellido') ?? ''),
            'cedula_identidad' => trim($this->request->getPost('cedula_identidad') ?? ''),
            'telefono'         => trim($this->request->getPost('telefono') ?? ''),
            'tipo_habitante'   => $this->request->getPost('tipo_habitante') ?? 'Familiar',
            'es_principal'     => 0, // Residente no puede asignar principal
            'estado'           => 'pendiente', // Requiere aprobación del admin
        ];

        $habitanteModel = new HabitanteModel();

        if (! $habitanteModel->insert($datos)) {
            return $this->responder('error', 'Error al enviar la solicitud.', 422, ['errors' => $habitanteModel->errors()]);
        }

        return $this->responder('success', 'Solicitud enviada. El administrador debe aprobar al nuevo habitante.');
    }

    /**
     * GET /residente/habitantes
     *
     * Lista los habitantes del apartamento del residente (JSON).
     * Incluye aprobados Y pendientes para que vea el estado de sus solicitudes.
     */
    public function listarMisHabitantes()
    {
        $session = session();
        $usuarioId = $session->get('usuario_id');

        $apartamentoModel = new ApartamentoModel();
        $apartamentos = $apartamentoModel->obtenerPorUsuario($usuarioId);

        if (empty($apartamentos)) {
            return $this->responder('success', 'OK', 200, ['data' => []]);
        }

        $habitanteModel = new HabitanteModel();
        $habitantes = $habitanteModel->listarTodosPorApartamento($apartamentos[0]['id']);

        return $this->responder('success', 'OK', 200, ['data' => $habitantes]);
    }

    // ---------------------------------------------------------------
    // Admin: Aprobar / Rechazar solicitudes
    // ---------------------------------------------------------------

    /**
     * POST /admin/habitantes/aprobar
     *
     * Aprueba una solicitud de habitante pendiente.
     */
    public function aprobar()
    {
        $session = session();

        if (! $session->get('isLoggedIn') || ! in_array($session->get('rol'), ['admin', 'root'])) {
            return $this->responder('error', 'Acceso denegado.', 403);
        }

        $id = (int) $this->request->getPost('id');
        $habitanteModel = new HabitanteModel();
        $habitante = $habitanteModel->find($id);

        if (! $habitante) {
            return $this->responder('error', 'Habitante no encontrado.', 404);
        }

        if ($habitante['estado'] !== 'pendiente') {
            return $this->responder('error', 'Esta solicitud ya fue procesada.', 400);
        }

        $habitanteModel->update($id, ['estado' => 'aprobado']);

        return $this->responder('success', 'Habitante aprobado exitosamente.');
    }

    /**
     * POST /admin/habitantes/rechazar
     *
     * Rechaza una solicitud de habitante pendiente.
     */
    public function rechazar()
    {
        $session = session();

        if (! $session->get('isLoggedIn') || ! in_array($session->get('rol'), ['admin', 'root'])) {
            return $this->responder('error', 'Acceso denegado.', 403);
        }

        $id = (int) $this->request->getPost('id');
        $habitanteModel = new HabitanteModel();
        $habitante = $habitanteModel->find($id);

        if (! $habitante) {
            return $this->responder('error', 'Habitante no encontrado.', 404);
        }

        if ($habitante['estado'] !== 'pendiente') {
            return $this->responder('error', 'Esta solicitud ya fue procesada.', 400);
        }

        $habitanteModel->update($id, ['estado' => 'rechazado']);

        return $this->responder('success', 'Solicitud rechazada.');
    }

    /**
     * GET /admin/habitantes/pendientes
     *
     * Lista todas las solicitudes pendientes de aprobación (JSON para AJAX).
     */
    public function pendientes()
    {
        $session = session();

        if (! $session->get('isLoggedIn') || ! in_array($session->get('rol'), ['admin', 'root'])) {
            return $this->responder('error', 'Acceso denegado.', 403);
        }

        $habitanteModel = new HabitanteModel();
        $pendientes = $habitanteModel->listarPendientes();
        $total = count($pendientes);

        return $this->responder('success', 'OK', 200, ['data' => $pendientes, 'total' => $total]);
    }
}
