<?php

namespace App\Controllers;

use App\Models\TicketModel;
use App\Models\ComunicadoModel;
use App\Traits\FileUploadTrait;

class ComunidadController extends BaseController
{
    use FileUploadTrait;

    /**
     * POST /crm/comunidad/comunicado  o  POST /admin/cartelera/publicar
     *
     * El admin publica una noticia o comunicado en la bitácora/cartelera digital.
     * Soporta adjuntar una imagen (jpg, png, webp, máx 2MB) con FileUploadTrait.
     * Solo para rol 'admin'.
     */
    public function crearComunicado()
    {
        $session = session();

        if (! $session->get('isLoggedIn') || $session->get('rol') !== 'admin') {
            return $this->responder('error', 'Acceso denegado. Solo los administradores pueden publicar comunicados.', 403, [], 'admin/cartelera');
        }

        $titulo    = trim((string) ($this->request->getPost('titulo') ?? ''));
        $contenido = trim((string) ($this->request->getPost('contenido') ?? $this->request->getPost('descripcion') ?? ''));

        if ($titulo === '' || $contenido === '') {
            return $this->responder('error', 'El título y el contenido son obligatorios.', 400, [], 'admin/cartelera');
        }

        try {
            // Procesamiento seguro de imagen adjunta vía FileUploadTrait
            $subidaImg = $this->subirImagenSegura(
                $this->request->getFile('imagen'),
                'bitacora',
                null,
                2,
                ['jpg', 'jpeg', 'png', 'webp']
            );

            if (is_array($subidaImg) && isset($subidaImg['error'])) {
                return $this->responder('error', $subidaImg['error'], 400, [], 'admin/cartelera');
            }

            $comunicadoModel = new ComunicadoModel();

            $datos = [
                'autor_id'          => (int) $session->get('usuario_id'),
                'titulo'            => $titulo,
                'contenido'         => $contenido,
                'imagen_url'        => $subidaImg,
                'fecha_publicacion' => date('Y-m-d H:i:s'),
            ];

            if (! $comunicadoModel->insert($datos)) {
                $errores = implode(', ', $comunicadoModel->errors());
                return $this->responder('error', 'Error al publicar el comunicado: ' . $errores, 422, ['errors' => $comunicadoModel->errors()], 'admin/cartelera');
            }

            return $this->responder(
                'success',
                'Comunicado publicado exitosamente en la cartelera.',
                200,
                ['data' => ['comunicado_id' => $comunicadoModel->getInsertID()]],
                'admin/cartelera'
            );
        } catch (\Throwable $e) {
            log_message('error', '[ComunidadController::crearComunicado] Excepción: ' . $e->getMessage());
            return $this->responder('error', 'Error inesperado al guardar el comunicado: ' . $e->getMessage(), 500, [], 'admin/cartelera');
        }
    }

    /**
     * GET /crm/comunidad/bitacora
     *
     * Lista todos los comunicados para cualquier usuario logueado.
     * Accesible por roles: root, admin, residente.
     */
    public function listarComunicados()
    {
        $session = session();

        if (! $session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Debe iniciar sesión para ver la bitácora.',
            ])->setStatusCode(401);
        }

        try {
            $comunicadoModel = new ComunicadoModel();
            $comunicados     = $comunicadoModel->listarConAutor();

            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $comunicados,
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[ComunidadController::listarComunicados] Excepción: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error al obtener los comunicados.',
            ])->setStatusCode(500);
        }
    }

    /**
     * POST /crm/comunidad/ticket  o  POST /residente/soporte/abrir
     *
     * El residente abre un ticket de soporte/queja.
     * Solo para rol 'residente'.
     */
    public function abrirTicket()
    {
        $session = session();

        if (! $session->get('isLoggedIn') || $session->get('rol') !== 'residente') {
            return $this->responder('error', 'Acceso denegado. Solo los residentes pueden abrir tickets de soporte.', 403, [], 'residente/soporte');
        }

        $categoria = trim((string) ($this->request->getPost('categoria') ?? ''));
        $asunto    = trim((string) ($this->request->getPost('asunto') ?? ''));
        $detalle   = trim((string) ($this->request->getPost('detalle') ?? $this->request->getPost('descripcion') ?? ''));

        if ($categoria === '' || $asunto === '' || $detalle === '') {
            return $this->responder('error', 'La categoría, el asunto y el detalle son obligatorios.', 400, [], 'residente/soporte');
        }

        try {
            $ticketModel = new TicketModel();

            $datos = [
                'usuario_id'     => (int) $session->get('usuario_id'),
                'categoria'      => $categoria,
                'asunto'         => $asunto,
                'detalle'        => $detalle,
                'estado'         => 'Abierto',
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ];

            if (! $ticketModel->insert($datos)) {
                $errores = implode(', ', $ticketModel->errors());
                return $this->responder('error', 'Error al abrir el ticket: ' . $errores, 422, ['errors' => $ticketModel->errors()], 'residente/soporte');
            }

            return $this->responder(
                'success',
                'Ticket de soporte creado exitosamente.',
                200,
                ['data' => ['ticket_id' => $ticketModel->getInsertID()]],
                'residente/soporte'
            );
        } catch (\Throwable $e) {
            log_message('error', '[ComunidadController::abrirTicket] Excepción: ' . $e->getMessage());
            return $this->responder('error', 'Error inesperado al registrar el ticket: ' . $e->getMessage(), 500, [], 'residente/soporte');
        }
    }

    /**
     * POST /crm/comunidad/ticket/gestionar  o  POST /admin/soporte/validar
     *
     * El admin cambia el estado de un ticket: 'Abierto', 'En Proceso' o 'Resuelto'.
     * Si es 'Resuelto', se registra automáticamente la fecha_resolucion.
     * Solo para rol 'admin' o 'root'.
     */
    public function responderTicket()
    {
        $session = session();

        if (! $session->get('isLoggedIn') || ! in_array($session->get('rol'), ['admin', 'root'], true)) {
            return $this->responder('error', 'Acceso denegado. Solo los administradores pueden gestionar tickets.', 403, [], 'admin/soporte');
        }

        $ticketId    = (int) $this->request->getPost('ticket_id');
        $nuevoEstado = trim((string) ($this->request->getPost('nuevo_estado') ?? $this->request->getPost('estado') ?? ''));

        if ($ticketId <= 0 || ! in_array($nuevoEstado, ['Abierto', 'En Proceso', 'Resuelto'], true)) {
            return $this->responder('error', 'Debe indicar un ticket válido y un estado válido (Abierto, En Proceso o Resuelto).', 400, [], 'admin/soporte');
        }

        try {
            $ticketModel = new TicketModel();
            $ticket      = $ticketModel->find($ticketId);

            if (! $ticket) {
                return $this->responder('error', 'El ticket indicado no existe.', 404, [], 'admin/soporte');
            }

            // actualizarEstado() registra fecha_resolucion automáticamente si es 'Resuelto'
            $ticketModel->actualizarEstado($ticketId, $nuevoEstado);
            $mensajeExito = "Ticket #{$ticketId} actualizado a: {$nuevoEstado}.";

            return $this->responder('success', $mensajeExito, 200, [], 'admin/soporte');
        } catch (\Throwable $e) {
            log_message('error', '[ComunidadController::responderTicket] Excepción: ' . $e->getMessage());
            return $this->responder('error', 'Error inesperado al actualizar el ticket: ' . $e->getMessage(), 500, [], 'admin/soporte');
        }
    }
}
