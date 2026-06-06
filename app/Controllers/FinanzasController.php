<?php

namespace App\Controllers;

use App\Models\ApartamentoModel;
use App\Models\ReciboModel;
use App\Models\PagoModel;
use App\Models\SolvenciaModel;

class FinanzasController extends BaseController
{
    /**
     * POST /crm/finanzas/facturar
     *
     * Emite recibos mensuales masivos para todos los apartamentos de un condominio.
     * Fórmula: monto_total = monto_base * alicuota (por apartamento).
     * Solo para rol 'admin'.
     */
    public function emitirRecibos()
    {
        $session = session();

        if (! $session->get('isLoggedIn') || $session->get('rol') !== 'admin') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Acceso denegado. Solo los administradores pueden emitir recibos.',
            ])->setStatusCode(403);
        }

        $condominioId = (int) $this->request->getPost('condominio_id');
        $montoBase    = (float) $this->request->getPost('monto_base');
        $mes          = (int) $this->request->getPost('mes');
        $anio         = (int) $this->request->getPost('anio');

        // Validación de campos obligatorios
        if ($condominioId <= 0 || $montoBase <= 0 || $mes <= 0 || $mes > 12 || $anio <= 2000) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Datos inválidos. Verifique condominio_id, monto_base, mes y año.',
            ])->setStatusCode(400);
        }

        // Obtener todos los apartamentos del condominio
        $apartamentoModel = new ApartamentoModel();
        $apartamentos     = $apartamentoModel->where('condominio_id', $condominioId)->findAll();

        if (empty($apartamentos)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'No se encontraron apartamentos para este condominio.',
            ])->setStatusCode(404);
        }

        // Generar recibos masivamente
        $reciboModel    = new ReciboModel();
        $insertados     = 0;
        $yaExistentes   = 0;

        foreach ($apartamentos as $apto) {
            // Evitar duplicar recibos del mismo mes/año
            if ($reciboModel->existeRecibo((int) $apto['id'], $mes, $anio)) {
                $yaExistentes++;
                continue;
            }

            $montoTotal = round($montoBase * (float) $apto['alicuota'], 2);

            $reciboModel->insert([
                'apartamento_id'  => $apto['id'],
                'mes'             => $mes,
                'anio'            => $anio,
                'monto_base'      => $montoBase,
                'monto_intereses' => 0,
                'monto_total'     => $montoTotal,
                'estado_pago'     => 'Pendiente',
            ]);

            $insertados++;
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => "Facturación completada: {$insertados} recibos emitidos.",
            'data'    => [
                'recibos_emitidos'     => $insertados,
                'recibos_ya_existian'  => $yaExistentes,
                'total_apartamentos'   => count($apartamentos),
            ],
        ]);
    }

    /**
     * POST /crm/finanzas/pagar
     *
     * El residente registra un pago con estado 'Por Validar'.
     * Solo para rol 'residente'.
     */
    public function registrarPago()
    {
        $session = session();

        if (! $session->get('isLoggedIn') || $session->get('rol') !== 'residente') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Acceso denegado. Solo los residentes pueden registrar pagos.',
            ])->setStatusCode(403);
        }

        $datos = [
            'recibo_mensual_id'      => (int) $this->request->getPost('recibo_mensual_id'),
            'monto_pagado'           => (float) $this->request->getPost('monto_pagado'),
            'metodo_pago'            => trim($this->request->getPost('metodo_pago') ?? ''),
            'referencia_transaccion' => trim($this->request->getPost('referencia_transaccion') ?? ''),
            'comprobante_url'        => trim($this->request->getPost('comprobante_url') ?? ''),
            'fecha_registro'         => date('Y-m-d H:i:s'),
            'estado_validacion'      => 'Por Validar',
        ];

        if ($datos['recibo_mensual_id'] <= 0 || $datos['monto_pagado'] <= 0) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'El recibo y el monto pagado son obligatorios.',
            ])->setStatusCode(400);
        }

        $pagoModel = new PagoModel();

        if (! $pagoModel->insert($datos)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error de validación al registrar el pago.',
                'errors'  => $pagoModel->errors(),
            ])->setStatusCode(422);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Pago registrado exitosamente. Queda pendiente de validación por el administrador.',
            'data'    => [
                'pago_id' => $pagoModel->getInsertID(),
            ],
        ]);
    }

    /**
     * POST /crm/finanzas/validar-pago
     *
     * El admin aprueba o rechaza un pago.
     * - Aprobar: estado_validacion → 'Aprobado' + estado_pago del recibo → 'Pagado'.
     * - Rechazar: estado_validacion → 'Rechazado' (recibo sigue 'Pendiente').
     * Solo para rol 'admin'.
     */
    public function validarPago()
    {
        $session = session();

        if (! $session->get('isLoggedIn') || $session->get('rol') !== 'admin') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Acceso denegado. Solo los administradores pueden validar pagos.',
            ])->setStatusCode(403);
        }

        $pagoId = (int) $this->request->getPost('pago_id');
        $accion = trim($this->request->getPost('accion') ?? '');

        if ($pagoId <= 0 || ! in_array($accion, ['aprobar', 'rechazar'], true)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Debe indicar un pago_id válido y una acción (aprobar o rechazar).',
            ])->setStatusCode(400);
        }

        $pagoModel = new PagoModel();
        $pago      = $pagoModel->obtenerConRecibo($pagoId);

        if (! $pago) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'El pago indicado no existe.',
            ])->setStatusCode(404);
        }

        $reciboModel = new ReciboModel();

        if ($accion === 'aprobar') {
            // Actualizar pago → Aprobado
            $pagoModel->actualizarValidacion($pagoId, 'Aprobado');

            // AUTOMATIZACIÓN: actualizar recibo → Pagado
            $reciboModel->actualizarEstadoPago((int) $pago['recibo_mensual_id'], 'Pagado');

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Pago aprobado. El recibo mensual ha sido marcado como Pagado automáticamente.',
            ]);
        }

        // Acción: rechazar
        $pagoModel->actualizarValidacion($pagoId, 'Rechazado');

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Pago rechazado. El recibo mensual permanece como Pendiente.',
        ]);
    }

    /**
     * GET /crm/finanzas/solvencia/(:num)
     *
     * Verifica morosidad y emite certificado de solvencia si califica.
     * Para roles 'admin' o 'residente'.
     */
    public function procesarSolvencia(int $apartamentoId)
    {
        $session = session();

        if (! $session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Debe iniciar sesión para procesar solvencia.',
            ])->setStatusCode(401);
        }

        $rolActual = $session->get('rol');

        if (! in_array($rolActual, ['admin', 'residente'], true)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Acceso denegado. Solo administradores y residentes pueden consultar solvencia.',
            ])->setStatusCode(403);
        }

        // Verificar morosidad: contar recibos pendientes
        $reciboModel     = new ReciboModel();
        $pendientes      = $reciboModel->contarPendientes($apartamentoId);

        if ($pendientes > 0) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => "El apartamento tiene {$pendientes} recibo(s) pendiente(s). No califica para solvencia.",
                'data'    => [
                    'recibos_pendientes' => $pendientes,
                ],
            ])->setStatusCode(409);
        }

        // Sin deuda: emitir solvencia
        $solvenciaModel = new SolvenciaModel();
        $codigoVerif    = $solvenciaModel->generarCodigoVerificacion();
        $ahora          = date('Y-m-d H:i:s');

        $solvenciaModel->insert([
            'apartamento_id'         => $apartamentoId,
            'emitido_por_usuario_id' => $session->get('usuario_id'),
            'fecha_emision'          => $ahora,
            'hasta_mes'              => (int) date('m'),
            'hasta_anio'             => (int) date('Y'),
            'codigo_verificacion'    => $codigoVerif,
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Certificado de solvencia emitido exitosamente.',
            'data'    => [
                'solvencia_id'        => $solvenciaModel->getInsertID(),
                'codigo_verificacion' => $codigoVerif,
                'fecha_emision'       => $ahora,
            ],
        ]);
    }
}
