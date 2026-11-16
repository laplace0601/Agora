<?php

namespace App\Controllers;

use App\Models\ApartamentoModel;
use App\Models\ReciboModel;
use App\Models\PagoModel;
use App\Models\ComunicadoModel;
use App\Models\TicketModel;

/**
 * ResidenteController
 *
 * Gestiona todas las vistas y acciones del panel del residente.
 */
class ResidenteController extends BaseController
{
    /**
     * GET /residente/dashboard
     *
     * Panel principal del residente: cartelera de comunicados y
     * resumen del estado de cuenta del apartamento.
     */
    public function dashboard()
    {
        $usuarioId = session()->get('usuario_id');

        // Obtener apartamento(s) vinculados al residente
        $apartamentoModel = new ApartamentoModel();
        $apartamentos     = $apartamentoModel->obtenerPorUsuario($usuarioId);

        // Comunicados activos para la cartelera con autor
        $comunicadoModel = new ComunicadoModel();
        $comunicados     = $comunicadoModel->listarActivosConAutor();

        // Estado de cuenta: recibos pendientes del primer apartamento
        $deudaTotal  = 0;
        $pendientes  = [];

        if (! empty($apartamentos)) {
            $reciboModel = new ReciboModel();
            $pendientes  = $reciboModel
                ->where('apartamento_id', $apartamentos[0]['id'])
                ->where('estado_pago', 'Pendiente')
                ->findAll();

            foreach ($pendientes as $r) {
                $deudaTotal += (float) $r['monto_total'];
            }
        }

        return view('residente/dashboard', [
            'apartamentos' => $apartamentos,
            'comunicados'  => $comunicados,
            'deuda_total'  => $deudaTotal,
            'pendientes'   => $pendientes,
        ]);
    }

    /**
     * GET /residente/pago
     *
     * Vista para que el residente reporte un pago de su recibo mensual.
     */
    public function reportarPago()
    {
        $usuarioId = session()->get('usuario_id');

        $apartamentoModel = new ApartamentoModel();
        $apartamentos     = $apartamentoModel->obtenerPorUsuario($usuarioId);

        // Recibos pendientes para llenar el select del formulario
        $recibosPendientes = [];
        $pagosRechazados   = [];
        if (! empty($apartamentos)) {
            $reciboModel       = new ReciboModel();
            $recibosPendientes = $reciboModel
                ->where('apartamento_id', $apartamentos[0]['id'])
                ->where('estado_pago', 'Pendiente')
                ->findAll();

            $pagoModel = new PagoModel();
            $idsRecibos = array_column($recibosPendientes, 'id');
            if (!empty($idsRecibos)) {
                $pagosRechazados = $pagoModel
                    ->select('pagos_recibidos.*, recibos_mensuales.mes, recibos_mensuales.anio, recibos_mensuales.monto_total')
                    ->join('recibos_mensuales', 'recibos_mensuales.id = pagos_recibidos.recibo_mensual_id')
                    ->whereIn('pagos_recibidos.recibo_mensual_id', $idsRecibos)
                    ->where('pagos_recibidos.estado_validacion', 'Rechazado')
                    ->orderBy('pagos_recibidos.fecha_registro', 'DESC')
                    ->findAll();
            }
        }

        return view('residente/reportar_pago', [
            'apartamentos'       => $apartamentos,
            'recibos_pendientes' => $recibosPendientes,
            'pagos_rechazados'   => $pagosRechazados,
        ]);
    }

    /**
     * POST /residente/pago/enviar
     *
     * Procesa el reporte de pago del residente.
     */
    public function enviarPago()
    {
        $session   = session();
        $usuarioId = $session->get('usuario_id');

        // Guardia de sesión: solo residentes autenticados
        if (! $session->get('isLoggedIn') || $session->get('rol') !== 'residente' || ! $usuarioId) {
            return redirect()->to(site_url('auth/login'))->with('error', 'Debes iniciar sesión para reportar un pago.');
        }

        $reciboId    = (int) $this->request->getPost('recibo_id');
        $montoPagado = (float) $this->request->getPost('monto');

        if ($reciboId <= 0 || $montoPagado <= 0) {
            return redirect()->back()->with('error', 'El recibo y el monto son obligatorios.');
        }

        // SEGURIDAD: Verificar que el recibo pertenece a alguno de los apartamentos del residente
        // Evita que un residente pague recibos de otro apartamento manipulando el POST
        $apartamentoModel = new ApartamentoModel();
        $apartamentos     = $apartamentoModel->obtenerPorUsuario($usuarioId);

        if (empty($apartamentos)) {
            return redirect()->back()->with('error', 'No tienes un apartamento asignado.');
        }

        $idsApartamentos = array_column($apartamentos, 'id');

        $reciboModel = new ReciboModel();
        $recibo      = $reciboModel->where('id', $reciboId)
                                   ->whereIn('apartamento_id', $idsApartamentos)
                                   ->first();

        if (! $recibo) {
            return redirect()->back()->with('error', 'El recibo indicado no existe o no pertenece a tus apartamentos.');
        }

        $datos = [
            'recibo_mensual_id'      => $reciboId,
            'monto_pagado'           => $montoPagado,
            'metodo_pago'            => trim($this->request->getPost('banco') ?? 'Transferencia'),
            'referencia_transaccion' => trim($this->request->getPost('referencia') ?? ''),
            'comprobante_url'        => '',
            'fecha_registro'         => date('Y-m-d H:i:s'),
            'estado_validacion'      => 'Por Validar',
        ];

        $pagoModel = new PagoModel();

        if (! $pagoModel->insert($datos)) {
            return redirect()->back()
                             ->with('error', 'Error al registrar el pago: ' . implode(', ', $pagoModel->errors()));
        }

        return redirect()->to(site_url('residente/dashboard'))
                         ->with('success', 'Pago reportado. El administrador lo validará pronto.');
    }

    /**
     * GET /residente/soporte
     *
     * Vista de soporte: formulario para abrir tickets + historial.
     */
    public function soporte()
    {
        $usuarioId   = session()->get('usuario_id');
        $ticketModel = new TicketModel();

        $misTickets = $ticketModel
            ->where('usuario_id', $usuarioId)
            ->orderBy('fecha_creacion', 'DESC')
            ->findAll();

        return view('residente/soporte', [
            'mis_tickets' => $misTickets,
        ]);
    }

    /**
     * POST /residente/soporte/abrir
     *
     * Abre un ticket de soporte desde la vista del residente.
     */
    public function abrirTicket()
    {
        $session   = session();
        $usuarioId = $session->get('usuario_id');

        // Guardia de sesión: solo residentes autenticados
        if (! $session->get('isLoggedIn') || $session->get('rol') !== 'residente' || ! $usuarioId) {
            return redirect()->to(site_url('auth/login'))->with('error', 'Debes iniciar sesión para abrir un ticket.');
        }

        $datos = [
            'usuario_id'     => $usuarioId,
            'categoria'      => trim($this->request->getPost('categoria') ?? ''),
            'asunto'         => trim($this->request->getPost('asunto') ?? ''),
            'detalle'        => trim($this->request->getPost('descripcion') ?? ''),
            'estado'         => 'Abierto',
            'fecha_creacion' => date('Y-m-d H:i:s'),
        ];

        if (empty($datos['categoria']) || empty($datos['asunto']) || empty($datos['detalle'])) {
            return redirect()->back()->with('error', 'Todos los campos del ticket son obligatorios.');
        }

        $ticketModel = new TicketModel();

        if (! $ticketModel->insert($datos)) {
            return redirect()->back()
                             ->with('error', 'Error al crear el ticket: ' . implode(', ', $ticketModel->errors()));
        }

        return redirect()->to(site_url('residente/soporte'))
                         ->with('success', 'Ticket de soporte abierto exitosamente. Te contactaremos pronto.');
    }

    /**
     * GET /residente/finanzas
     *
     * Vista de finanzas: recibos vigentes sin pagar y el historial de solvencia (pagados).
     * También incluye pagos rechazados para que el residente vea el motivo.
     */
    public function finanzas()
    {
        $usuarioId = session()->get('usuario_id');
        $apartamentoModel = new ApartamentoModel();
        $apartamentos     = $apartamentoModel->obtenerPorUsuario($usuarioId);

        $recibosPendientes = [];
        $recibosPagados    = [];
        $pagosRechazados   = [];

        if (! empty($apartamentos)) {
            $reciboModel = new ReciboModel();
            
            // Recibos vigentes sin pagar (Pendientes) con el estado de validación del último pago reportado
            $recibosPendientes = $reciboModel
                ->select('recibos_mensuales.*, (SELECT estado_validacion FROM pagos_recibidos WHERE pagos_recibidos.recibo_mensual_id = recibos_mensuales.id ORDER BY fecha_registro DESC LIMIT 1) AS estado_validacion_pago')
                ->where('recibos_mensuales.apartamento_id', $apartamentos[0]['id'])
                ->where('recibos_mensuales.estado_pago', 'Pendiente')
                ->orderBy('recibos_mensuales.anio', 'DESC')
                ->orderBy('recibos_mensuales.mes', 'DESC')
                ->findAll();

            // Historial de recibos pagados (Solvencia) con la fecha real del pago o aprobación
            $recibosPagados = $reciboModel
                ->select('recibos_mensuales.*, COALESCE(recibos_mensuales.fecha_pago, (SELECT pagos_recibidos.fecha_registro FROM pagos_recibidos WHERE pagos_recibidos.recibo_mensual_id = recibos_mensuales.id AND pagos_recibidos.estado_validacion = "Aprobado" ORDER BY pagos_recibidos.id DESC LIMIT 1)) AS fecha_real_pago')
                ->where('recibos_mensuales.apartamento_id', $apartamentos[0]['id'])
                ->where('recibos_mensuales.estado_pago', 'Pagado')
                ->orderBy('recibos_mensuales.anio', 'DESC')
                ->orderBy('recibos_mensuales.mes', 'DESC')
                ->findAll();

            // Pagos rechazados: el residente debe verlos con el motivo
            $pagoModel = new PagoModel();
            $idsRecibos = array_column(
                $reciboModel->where('apartamento_id', $apartamentos[0]['id'])->findAll(),
                'id'
            );
            if (!empty($idsRecibos)) {
                $pagosRechazados = $pagoModel
                    ->select('pagos_recibidos.*, recibos_mensuales.mes, recibos_mensuales.anio, recibos_mensuales.monto_total')
                    ->join('recibos_mensuales', 'recibos_mensuales.id = pagos_recibidos.recibo_mensual_id')
                    ->whereIn('pagos_recibidos.recibo_mensual_id', $idsRecibos)
                    ->where('pagos_recibidos.estado_validacion', 'Rechazado')
                    ->orderBy('pagos_recibidos.fecha_registro', 'DESC')
                    ->findAll();
            }
        }

        return view('residente/finanzas', [
            'apartamentos'       => $apartamentos,
            'recibos_pendientes' => $recibosPendientes,
            'recibos_pagados'    => $recibosPagados,
            'pagos_rechazados'   => $pagosRechazados,
        ]);
    }

    /**
     * GET /residente/habitantes
     *
     * Vista del censo de habitantes del apartamento del residente.
     * Los residentes pueden ver sus habitantes aprobados y solicitudes pendientes,
     * y enviar nuevas solicitudes que requieren aprobación del admin.
     */
    public function habitantes()
    {
        $usuarioId = session()->get('usuario_id');

        $apartamentoModel = new ApartamentoModel();
        $apartamentos     = $apartamentoModel->obtenerPorUsuario($usuarioId);

        return view('residente/habitantes', [
            'apartamentos' => $apartamentos,
        ]);
    }

    /**
     * GET /residente/recibo/pdf/(:num)
     *
     * Genera y descarga el comprobante en PDF de un recibo pagado.
     * Valida que el recibo pertenezca al residente autenticado (o sea administrador)
     * y que su estatus sea 'Pagado'.
     */
    public function descargarRecibo(int $id)
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(site_url('auth/login'))->with('error', 'Debes iniciar sesión para consultar el recibo.');
        }

        $rol       = $session->get('rol');
        $usuarioId = $session->get('usuario_id');

        $reciboModel = new ReciboModel();
        $recibo = $reciboModel
            ->select('recibos_mensuales.*, 
                      apartamentos.nro_apartamento, apartamentos.nombre_edificio_torre, apartamentos.alicuota,
                      condominios.nombre_condominio, condominios.rif_jurisdiccion, condominios.direccion as direccion_condominio,
                      residentes.nombre_completo as residente_nombre, residentes.cedula_identidad as residente_cedula, residentes.telefono as residente_telefono')
            ->join('apartamentos', 'apartamentos.id = recibos_mensuales.apartamento_id')
            ->join('condominios', 'condominios.id = apartamentos.condominio_id', 'left')
            ->join('residentes', 'residentes.id = apartamentos.residente_id', 'left')
            ->where('recibos_mensuales.id', $id)
            ->first();

        if (! $recibo) {
            return redirect()->back()->with('error', 'El recibo solicitado no existe.');
        }

        // Validación de pertenencia para el residente
        if ($rol === 'residente') {
            $apartamentoModel      = new ApartamentoModel();
            $apartamentosResidente = $apartamentoModel->obtenerPorUsuario($usuarioId);
            $idsApartamentos       = array_column($apartamentosResidente, 'id');

            if (! in_array($recibo['apartamento_id'], $idsApartamentos)) {
                return redirect()->back()->with('error', 'Acceso denegado: este recibo no pertenece a tu apartamento.');
            }
        } elseif (! in_array($rol, ['admin', 'root'], true)) {
            return redirect()->back()->with('error', 'No tienes permisos para consultar este recibo.');
        }

        // Validar que el estatus sea "Pagado"
        if (($recibo['estado_pago'] ?? '') !== 'Pagado') {
            return redirect()->back()->with('error', 'El comprobante solo está disponible para recibos con estatus Pagado.');
        }

        // Obtener el pago aprobado asociado
        $pagoModel = new PagoModel();
        $pago = $pagoModel
            ->where('recibo_mensual_id', $id)
            ->where('estado_validacion', 'Aprobado')
            ->orderBy('id', 'DESC')
            ->first();

        if (! $pago) {
            $pago = $pagoModel->where('recibo_mensual_id', $id)->orderBy('id', 'DESC')->first() ?? [];
        }

        // Convertir logo a base64 para máxima fidelidad en PDF e impresión
        $logoBase64 = null;
        $marca      = obtenerMarca();
        if (!empty($marca['logo_url'])) {
            $logoPath = FCPATH . ltrim($marca['logo_url'], '/\\');
            if (is_file($logoPath)) {
                $mimeType   = mime_content_type($logoPath) ?: 'image/png';
                $logoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($logoPath));
            }
        }

        $data = [
            'recibo'     => $recibo,
            'pago'       => $pago,
            'marca'      => $marca,
            'logoBase64' => $logoBase64,
        ];

        // Si dompdf está instalado en el proyecto, generar y enviar PDF binario nativo
        if (class_exists(\Dompdf\Dompdf::class)) {
            $data['isDompdf'] = true;
            $html = view('pdf/recibo_pago', $data);

            $options = new \Dompdf\Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('defaultFont', 'DejaVu Sans');
            $options->setDefaultMediaType('print');

            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $filename = 'Recibo_Solvencia_' . str_pad($recibo['id'], 5, '0', STR_PAD_LEFT) . '.pdf';
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                ->setBody($dompdf->output());
        }

        // Fallback: Si dompdf aún no está instalado vía composer, renderiza la vista imprimible en navegador
        $data['isDompdf'] = false;
        return $this->response->setBody(view('pdf/recibo_pago', $data));
    }
}
