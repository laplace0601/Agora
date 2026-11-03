<?php

namespace App\Controllers;

use App\Models\TicketModel;
use App\Models\ComunicadoModel;

class ComunidadController extends BaseController
{
    /**
     * POST /crm/comunidad/comunicado  o  POST /admin/cartelera/publicar
     *
     * El admin publica una noticia o comunicado en la bitácora/cartelera digital.
     * Soporta adjuntar una imagen (jpg, png, webp, máx 2MB).
     * Solo para rol 'admin'.
     */
    public function crearComunicado()
    {
        $session = session();

        if (! $session->get('isLoggedIn') || $session->get('rol') !== 'admin') {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Acceso denegado. Solo los administradores pueden publicar comunicados.',
                ])->setStatusCode(403);
            }
            return redirect()->back()->with('error', 'Acceso denegado.');
        }

        $titulo    = trim($this->request->getPost('titulo') ?? '');
        $contenido = trim($this->request->getPost('contenido') ?? $this->request->getPost('descripcion') ?? '');

        if ($titulo === '' || $contenido === '') {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'El título y el contenido son obligatorios.',
                ])->setStatusCode(400);
            }
            return redirect()->back()->with('error', 'El título y el contenido son obligatorios.');
        }

        // ---------------------------------------------------------------
        // Procesamiento de imagen adjunta (ETAPA 1)
        // ---------------------------------------------------------------
        $imagenUrl = null;
        $imagen    = $this->request->getFile('imagen');

        if ($imagen && $imagen->isValid() && ! $imagen->hasMoved()) {
            // Validar tipo y tamaño (máx 2MB)
            $reglas = [
                'imagen' => [
                    'rules'  => 'uploaded[imagen]|max_size[imagen,2048]|is_image[imagen]|mime_in[imagen,image/jpeg,image/png,image/webp]',
                    'errors' => [
                        'max_size' => 'La imagen no debe superar los 2 MB.',
                        'is_image' => 'El archivo debe ser una imagen válida.',
                        'mime_in'  => 'Solo se permiten imágenes JPG, PNG o WebP.',
                    ],
                ],
            ];

            if (! $this->validate($reglas)) {
                $erroresImg = implode(' ', $this->validator->getErrors());
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => $erroresImg,
                    ])->setStatusCode(400);
                }
                return redirect()->back()->with('error', $erroresImg);
            }

            // Mover el archivo a public/uploads/bitacora/
            $nuevoNombre = $imagen->getRandomName();
            $imagen->move(FCPATH . 'uploads/bitacora', $nuevoNombre);

            // Guardar la ruta relativa para la BD
            $imagenUrl = 'uploads/bitacora/' . $nuevoNombre;
        }

        $comunicadoModel = new ComunicadoModel();

        $datos = [
            'autor_id'          => $session->get('usuario_id'),
            'titulo'            => $titulo,
            'contenido'         => $contenido,
            'imagen_url'        => $imagenUrl,
            'fecha_publicacion' => date('Y-m-d H:i:s'),
        ];

        if (! $comunicadoModel->insert($datos)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Error al publicar el comunicado.',
                    'errors'  => $comunicadoModel->errors(),
                ])->setStatusCode(422);
            }
            return redirect()->back()->with('error', 'Error al publicar el comunicado.');
        }

        // Respuesta dual: soporta tanto AJAX como formulario estándar
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Comunicado publicado exitosamente.',
                'data'    => [
                    'comunicado_id' => $comunicadoModel->getInsertID(),
                ],
            ]);
        }

        return redirect()->to(site_url('admin/cartelera'))->with('success', 'Comunicado publicado exitosamente.');
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

        $comunicadoModel = new ComunicadoModel();
        $comunicados     = $comunicadoModel->listarConAutor();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $comunicados,
        ]);
    }

    /**
     * POST /crm/comunidad/ticket
     *
     * El residente abre un ticket de soporte/queja.
     * Solo para rol 'residente'.
     */
    public function abrirTicket()
    {
        $session = session();

        if (! $session->get('isLoggedIn') || $session->get('rol') !== 'residente') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Acceso denegado. Solo los residentes pueden abrir tickets de soporte.',
            ])->setStatusCode(403);
        }

        $categoria = trim($this->request->getPost('categoria') ?? '');
        $asunto    = trim($this->request->getPost('asunto') ?? '');
        $detalle   = trim($this->request->getPost('detalle') ?? '');

        if ($categoria === '' || $asunto === '' || $detalle === '') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'La categoría, el asunto y el detalle son obligatorios.',
            ])->setStatusCode(400);
        }

        $ticketModel = new TicketModel();

        $datos = [
            'usuario_id'     => $session->get('usuario_id'),
            'categoria'      => $categoria,
            'asunto'         => $asunto,
            'detalle'        => $detalle,
            'estado'         => 'Abierto',
            'fecha_creacion' => date('Y-m-d H:i:s'),
        ];

        if (! $ticketModel->insert($datos)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error al abrir el ticket.',
                'errors'  => $ticketModel->errors(),
            ])->setStatusCode(422);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Ticket de soporte creado exitosamente.',
            'data'    => [
                'ticket_id' => $ticketModel->getInsertID(),
            ],
        ]);
    }

    /**
     * POST /crm/comunidad/ticket/gestionar
     *
     * El admin cambia el estado de un ticket: 'En Proceso' o 'Resuelto'.
     * Si es 'Resuelto', se registra automáticamente la fecha_resolucion.
     * Solo para rol 'admin'.
     */
    public function responderTicket()
    {
        $session = session();

        if (! $session->get('isLoggedIn') || $session->get('rol') !== 'admin') {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Acceso denegado. Solo los administradores pueden gestionar tickets.',
                ])->setStatusCode(403);
            }
            return redirect()->back()->with('error', 'Acceso denegado. Solo los administradores pueden gestionar tickets.');
        }

        $ticketId    = (int) $this->request->getPost('ticket_id');
        $nuevoEstado = trim($this->request->getPost('nuevo_estado') ?? $this->request->getPost('estado') ?? '');

        if ($ticketId <= 0 || ! in_array($nuevoEstado, ['En Proceso', 'Resuelto'], true)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Debe indicar un ticket_id válido y un estado (En Proceso o Resuelto).',
                ])->setStatusCode(400);
            }
            return redirect()->back()->with('error', 'Debe indicar un ticket_id válido y un estado (En Proceso o Resuelto).');
        }

        $ticketModel = new TicketModel();
        $ticket      = $ticketModel->find($ticketId);

        if (! $ticket) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'El ticket indicado no existe.',
                ])->setStatusCode(404);
            }
            return redirect()->back()->with('error', 'El ticket indicado no existe.');
        }

        // actualizarEstado() registra fecha_resolucion automáticamente si es 'Resuelto'
        $ticketModel->actualizarEstado($ticketId, $nuevoEstado);
        $mensajeExito = "Ticket #{$ticketId} actualizado a: {$nuevoEstado}.";

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => $mensajeExito,
            ]);
        }

        return redirect()->back()->with('success', $mensajeExito);
    }
}
