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
        if (! empty($apartamentos)) {
            $reciboModel       = new ReciboModel();
            $recibosPendientes = $reciboModel
                ->where('apartamento_id', $apartamentos[0]['id'])
                ->where('estado_pago', 'Pendiente')
                ->findAll();
        }

        return view('residente/reportar_pago', [
            'apartamentos'      => $apartamentos,
            'recibos_pendientes' => $recibosPendientes,
        ]);
    }

    /**
     * POST /residente/pago/enviar
     *
     * Procesa el reporte de pago del residente.
     */
    public function enviarPago()
    {
        $datos = [
            'recibo_mensual_id'      => (int)   $this->request->getPost('recibo_id'),
            'monto_pagado'           => (float)  $this->request->getPost('monto'),
            'metodo_pago'            => trim($this->request->getPost('banco') ?? 'Transferencia'),
            'referencia_transaccion' => trim($this->request->getPost('referencia') ?? ''),
            'comprobante_url'        => '',        // Pendiente: subida de archivos
            'fecha_registro'         => date('Y-m-d H:i:s'),
            'estado_validacion'      => 'Por Validar',
        ];

        if ($datos['recibo_mensual_id'] <= 0 || $datos['monto_pagado'] <= 0) {
            return redirect()->back()->with('error', 'El recibo y el monto son obligatorios.');
        }

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
        $datos = [
            'usuario_id'     => session()->get('usuario_id'),
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
     */
    public function finanzas()
    {
        $usuarioId = session()->get('usuario_id');
        $apartamentoModel = new ApartamentoModel();
        $apartamentos     = $apartamentoModel->obtenerPorUsuario($usuarioId);

        $recibosPendientes = [];
        $recibosPagados    = [];

        if (! empty($apartamentos)) {
            $reciboModel = new ReciboModel();
            
            // Recibos vigentes sin pagar (Pendientes)
            // Ordenamos por año y mes DESC (la tabla no tiene columna fecha_emision).
            $recibosPendientes = $reciboModel
                ->where('apartamento_id', $apartamentos[0]['id'])
                ->where('estado_pago', 'Pendiente')
                ->orderBy('anio', 'DESC')
                ->orderBy('mes', 'DESC')
                ->findAll();

            // Historial de recibos pagados (Solvencia)
            $recibosPagados = $reciboModel
                ->where('apartamento_id', $apartamentos[0]['id'])
                ->where('estado_pago', 'Pagado')
                ->orderBy('anio', 'DESC')
                ->orderBy('mes', 'DESC')
                ->findAll();
        }

        return view('residente/finanzas', [
            'apartamentos'       => $apartamentos,
            'recibos_pendientes' => $recibosPendientes,
            'recibos_pagados'    => $recibosPagados,
        ]);
    }
}
