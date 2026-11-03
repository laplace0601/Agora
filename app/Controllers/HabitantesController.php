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
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Acceso denegado.',
            ])->setStatusCode(403);
        }

        $habitanteModel = new HabitanteModel();

        $id = $this->request->getPost('id');
        $apartamentoId = (int) $this->request->getPost('apartamento_id');

        // Validar que el apartamento existe
        $apartamentoModel = new ApartamentoModel();
        if (! $apartamentoModel->find($apartamentoId)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'El apartamento indicado no existe.',
            ])->setStatusCode(404);
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
            $db = \Config\Database::connect();
            $db->table('habitantes')
               ->where('apartamento_id', $apartamentoId)
               ->update(['es_principal' => 0]);
        }

        if ($id) {
            // Editar
            if (! $habitanteModel->update($id, $datos)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Error al actualizar el habitante.',
                    'errors'  => $habitanteModel->errors(),
                ])->setStatusCode(422);
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Habitante actualizado correctamente.',
            ]);
        }

        // Crear
        if (! $habitanteModel->insert($datos)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error al registrar el habitante.',
                'errors'  => $habitanteModel->errors(),
            ])->setStatusCode(422);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Habitante registrado correctamente.',
            'data'    => ['id' => $habitanteModel->getInsertID()],
        ]);
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
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Acceso denegado.',
            ])->setStatusCode(403);
        }

        $id = (int) $this->request->getPost('id');

        if ($id <= 0) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'ID de habitante inválido.',
            ])->setStatusCode(400);
        }

        $habitanteModel = new HabitanteModel();

        if (! $habitanteModel->find($id)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'El habitante no existe.',
            ])->setStatusCode(404);
        }

        $habitanteModel->delete($id);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Habitante eliminado correctamente.',
        ]);
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
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Habitante no encontrado.',
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $habitante,
        ]);
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
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Acceso denegado.',
            ])->setStatusCode(403);
        }

        $usuarioId = $session->get('usuario_id');

        // Obtener el apartamento del residente
        $apartamentoModel = new ApartamentoModel();
        $apartamentos = $apartamentoModel->obtenerPorUsuario($usuarioId);

        if (empty($apartamentos)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'No tienes un apartamento asignado.',
            ])->setStatusCode(400);
        }

        $apartamentoId = (int) $apartamentos[0]['id'];

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
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error al enviar la solicitud.',
                'errors'  => $habitanteModel->errors(),
            ])->setStatusCode(422);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Solicitud enviada. El administrador debe aprobar al nuevo habitante.',
        ]);
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
            return $this->response->setJSON([
                'status' => 'success',
                'data'   => [],
            ]);
        }

        $habitanteModel = new HabitanteModel();
        $habitantes = $habitanteModel->listarTodosPorApartamento($apartamentos[0]['id']);

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $habitantes,
        ]);
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
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Acceso denegado.',
            ])->setStatusCode(403);
        }

        $id = (int) $this->request->getPost('id');
        $habitanteModel = new HabitanteModel();
        $habitante = $habitanteModel->find($id);

        if (! $habitante) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Habitante no encontrado.',
            ])->setStatusCode(404);
        }

        if ($habitante['estado'] !== 'pendiente') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Esta solicitud ya fue procesada.',
            ])->setStatusCode(400);
        }

        $habitanteModel->update($id, ['estado' => 'aprobado']);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Habitante aprobado exitosamente.',
        ]);
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
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Acceso denegado.',
            ])->setStatusCode(403);
        }

        $id = (int) $this->request->getPost('id');
        $habitanteModel = new HabitanteModel();
        $habitante = $habitanteModel->find($id);

        if (! $habitante) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Habitante no encontrado.',
            ])->setStatusCode(404);
        }

        if ($habitante['estado'] !== 'pendiente') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Esta solicitud ya fue procesada.',
            ])->setStatusCode(400);
        }

        $habitanteModel->update($id, ['estado' => 'rechazado']);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Solicitud rechazada.',
        ]);
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
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Acceso denegado.',
            ])->setStatusCode(403);
        }

        $habitanteModel = new HabitanteModel();
        $pendientes = $habitanteModel->listarPendientes();
        $total = count($pendientes);

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $pendientes,
            'total'  => $total,
        ]);
    }
}
