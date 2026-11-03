<?php

namespace App\Controllers;

use App\Models\AreaComunModel;
use App\Models\ReservaAreaModel;
use App\Models\ApartamentoModel;
use App\Models\ComunicadoModel;

/**
 * AreasReservasController
 *
 * Gestiona las áreas comunes (CRUD) y las reservas con validación
 * de solapamiento horario e integración automática con la bitácora.
 */
class AreasReservasController extends BaseController
{
    // =================================================================
    // ÁREAS COMUNES — CRUD (solo admin/root)
    // =================================================================

    /**
     * GET /admin/areas-comunes
     *
     * Vista del panel de áreas comunes con listado y formulario de creación.
     */
    public function areasComunes()
    {
        $areaModel = new AreaComunModel();

        return view('admin/areas_comunes', [
            'areas' => $areaModel->listarTodas(),
        ]);
    }

    /**
     * POST /admin/areas-comunes/guardar
     *
     * Crea o actualiza un área común.
     */
    public function guardarArea()
    {
        $session = session();
        if (! $session->get('isLoggedIn') || ! in_array($session->get('rol'), ['admin', 'root'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Acceso denegado.'])->setStatusCode(403);
        }

        $areaModel = new AreaComunModel();
        $id = $this->request->getPost('id');

        $datos = [
            'nombre'           => trim($this->request->getPost('nombre') ?? ''),
            'descripcion'      => trim($this->request->getPost('descripcion') ?? ''),
            'capacidad_maxima' => (int) ($this->request->getPost('capacidad_maxima') ?? 0) ?: null,
            'estado'           => $this->request->getPost('estado') ?? 'Activo',
        ];

        if ($id) {
            if (! $areaModel->update($id, $datos)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Error al actualizar.', 'errors' => $areaModel->errors()])->setStatusCode(422);
            }
            session()->setFlashdata('success', 'Área común actualizada correctamente.');
        } else {
            if (! $areaModel->insert($datos)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Error al crear.', 'errors' => $areaModel->errors()])->setStatusCode(422);
            }
            session()->setFlashdata('success', 'Área común registrada correctamente.');
        }

        return redirect()->to(site_url('admin/areas-comunes'));
    }

    /**
     * GET /admin/areas-comunes/eliminar/(:num)
     *
     * Cambia el estado de un área a 'Inactivo' (soft delete).
     */
    public function eliminarArea(int $id)
    {
        $areaModel = new AreaComunModel();
        $areaModel->update($id, ['estado' => 'Inactivo']);

        return redirect()->to(site_url('admin/areas-comunes'))
            ->with('success', 'Área común desactivada.');
    }

    // =================================================================
    // RESERVAS — Panel admin (listar todas)
    // =================================================================

    /**
     * GET /admin/reservas
     *
     * Lista todas las reservas para el panel admin.
     */
    public function listarReservas()
    {
        $reservaModel = new ReservaAreaModel();
        $areaModel    = new AreaComunModel();

        return view('admin/reservas', [
            'reservas' => $reservaModel->listarConDetalles(),
            'areas'    => $areaModel->listarActivas(),
        ]);
    }

    // =================================================================
    // RESERVAS — Creación con validación de solapamiento + bitácora
    // =================================================================

    /**
     * GET /admin/reservas/nueva  o  GET /residente/reservas
     *
     * Vista del formulario de reserva.
     */
    public function formularioReserva()
    {
        $session   = session();
        $usuarioId = $session->get('usuario_id');
        $rol       = $session->get('rol');

        $areaModel        = new AreaComunModel();
        $apartamentoModel = new ApartamentoModel();

        // Si es residente, solo ve sus apartamentos; si es admin, ve todos
        if ($rol === 'residente') {
            $apartamentos = $apartamentoModel->obtenerPorUsuario($usuarioId);
        } else {
            $apartamentos = $apartamentoModel
                ->select('apartamentos.*, condominios.nombre_condominio')
                ->join('condominios', 'condominios.id = apartamentos.condominio_id')
                ->findAll();
        }

        $viewData = [
            'areas'        => $areaModel->listarActivas(),
            'apartamentos' => $apartamentos,
        ];

        // Usar la vista correcta según el rol
        if ($rol === 'residente') {
            return view('residente/reservas', $viewData);
        }

        return view('admin/reserva_nueva', $viewData);
    }

    /**
     * POST /admin/reservas/crear  o  POST /residente/reservas/crear
     *
     * Procesa la reserva con validación estricta de solapamiento.
     * Si es exitosa, publica automáticamente un comunicado en la bitácora.
     */
    public function crearReserva()
    {
        $session   = session();
        $usuarioId = $session->get('usuario_id');

        if (! $session->get('isLoggedIn') || ! $usuarioId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Debe iniciar sesión.'])->setStatusCode(401);
        }

        $areaId        = (int) $this->request->getPost('area_id');
        $apartamentoId = (int) $this->request->getPost('apartamento_id');
        $fecha         = trim($this->request->getPost('fecha_reserva') ?? '');
        $horaInicio    = trim($this->request->getPost('hora_inicio') ?? '');
        $horaFin       = trim($this->request->getPost('hora_fin') ?? '');

        // --- Validaciones básicas ---
        if ($areaId <= 0 || $apartamentoId <= 0 || $fecha === '' || $horaInicio === '' || $horaFin === '') {
            $msg = 'Todos los campos son obligatorios (área, apartamento, fecha, hora inicio y hora fin).';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg])->setStatusCode(400);
            }
            return redirect()->back()->with('error', $msg);
        }

        if ($horaInicio >= $horaFin) {
            $msg = 'La hora de inicio debe ser anterior a la hora de fin.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg])->setStatusCode(400);
            }
            return redirect()->back()->with('error', $msg);
        }

        // --- Verificar que el área existe y está activa ---
        $areaModel = new AreaComunModel();
        $area = $areaModel->find($areaId);
        if (! $area || $area['estado'] !== 'Activo') {
            $msg = 'El área seleccionada no existe o no está disponible.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg])->setStatusCode(400);
            }
            return redirect()->back()->with('error', $msg);
        }

        // --- Verificar que el apartamento existe ---
        $apartamentoModel = new ApartamentoModel();
        $apartamento = $apartamentoModel->find($apartamentoId);
        if (! $apartamento) {
            $msg = 'El apartamento seleccionado no existe.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg])->setStatusCode(400);
            }
            return redirect()->back()->with('error', $msg);
        }

        // =============================================================
        // VALIDACIÓN ESTRICTA DE SOLAPAMIENTO HORARIO
        // =============================================================
        $reservaModel = new ReservaAreaModel();

        if ($reservaModel->existeSolapamiento($areaId, $fecha, $horaInicio, $horaFin)) {
            $msg = "No se puede reservar: ya existe una reserva confirmada para \"{$area['nombre']}\" "
                 . "el día {$fecha} que se solapa con el horario {$horaInicio} - {$horaFin}. "
                 . "Por favor, seleccione otro horario.";

            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg])->setStatusCode(409);
            }
            return redirect()->back()->with('error', $msg);
        }

        // =============================================================
        // CREAR LA RESERVA
        // =============================================================
        $datosReserva = [
            'area_id'        => $areaId,
            'apartamento_id' => $apartamentoId,
            'usuario_id'     => $usuarioId,
            'fecha_reserva'  => $fecha,
            'hora_inicio'    => $horaInicio,
            'hora_fin'       => $horaFin,
            'estado'         => 'Confirmada',
        ];

        if (! $reservaModel->insert($datosReserva)) {
            $msg = 'Error al crear la reserva.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg, 'errors' => $reservaModel->errors()])->setStatusCode(422);
            }
            return redirect()->back()->with('error', $msg);
        }

        // =============================================================
        // INTEGRACIÓN AUTOMÁTICA CON LA BITÁCORA
        // Publicar comunicado informando sobre la reserva confirmada
        // =============================================================
        $nroApto   = $apartamento['nro_apartamento'];
        $nombreArea = $area['nombre'];
        $fechaFormateada = date('d/m/Y', strtotime($fecha));

        $comunicadoModel = new ComunicadoModel();
        $comunicadoModel->insert([
            'autor_id'          => $usuarioId,
            'titulo'            => "Reserva de área común: {$nombreArea}",
            'contenido'         => "El área \"{$nombreArea}\" ha sido reservada por el apartamento {$nroApto} "
                                 . "para el día {$fechaFormateada} de {$horaInicio} a {$horaFin}.",
            'fecha_publicacion' => date('Y-m-d H:i:s'),
            'estado'            => 'publicado',
        ]);

        // --- Respuesta exitosa ---
        $msgExito = "Reserva confirmada: {$nombreArea} el {$fechaFormateada} de {$horaInicio} a {$horaFin}.";

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => $msgExito,
                'data'    => ['reserva_id' => $reservaModel->getInsertID()],
            ]);
        }

        $rol = $session->get('rol');
        $redireccion = ($rol === 'residente') ? 'residente/reservas' : 'admin/reservas';
        return redirect()->to(site_url($redireccion))->with('success', $msgExito);
    }

    /**
     * POST /admin/reservas/cancelar
     *
     * Cancela una reserva existente.
     */
    public function cancelarReserva()
    {
        $session = session();

        if (! $session->get('isLoggedIn') || ! in_array($session->get('rol'), ['admin', 'root'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Acceso denegado.'])->setStatusCode(403);
        }

        $id = (int) $this->request->getPost('reserva_id');
        $reservaModel = new ReservaAreaModel();
        $reserva = $reservaModel->find($id);

        if (! $reserva) {
            return redirect()->back()->with('error', 'La reserva no existe.');
        }

        $reservaModel->update($id, ['estado' => 'Cancelada']);

        return redirect()->to(site_url('admin/reservas'))->with('success', "Reserva #{$id} cancelada.");
    }

    // =================================================================
    // API: Reservas por área y fecha (para AJAX en el calendario)
    // =================================================================

    /**
     * GET /api/reservas/disponibilidad?area_id=X&fecha=YYYY-MM-DD
     *
     * Retorna las reservas existentes para que el frontend muestre disponibilidad.
     */
    public function disponibilidad()
    {
        $areaId = (int) $this->request->getGet('area_id');
        $fecha  = $this->request->getGet('fecha');

        if ($areaId <= 0 || empty($fecha)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Parámetros inválidos.'])->setStatusCode(400);
        }

        $reservaModel = new ReservaAreaModel();
        $reservas = $reservaModel->listarPorAreaYFecha($areaId, $fecha);

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $reservas,
        ]);
    }
}

