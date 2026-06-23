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
    /**
     * POST /crm/finanzas/simular-facturacion
     *
     * Genera una pre-visualización de la facturación basada en el monto global
     * y las alícuotas de cada apartamento. No guarda en base de datos.
     */
    public function simularFacturacion()
    {
        $session = session();
        if (! $session->get('isLoggedIn') || $session->get('rol') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Acceso denegado.'])->setStatusCode(403);
        }

        $condominioId = (int) $this->request->getPost('condominio_id');
        $montoGlobal  = (float) $this->request->getPost('monto_global_gastos');

        if ($condominioId <= 0 || $montoGlobal <= 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Datos inválidos. El monto global debe ser mayor a 0.'])->setStatusCode(400);
        }

        $apartamentoModel = new ApartamentoModel();
        $apartamentos = $apartamentoModel->where('condominio_id', $condominioId)->findAll();

        if (empty($apartamentos)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No hay apartamentos registrados en este condominio.'])->setStatusCode(404);
        }

        // VALIDACIÓN CRÍTICA: Regla del 100%
        $sumaAlicuotas = 0;
        foreach ($apartamentos as $apto) {
            $sumaAlicuotas += (float) $apto['alicuota'];
        }

        // Margen de tolerancia [99.9, 100.1] para errores de coma flotante
        if ($sumaAlicuotas < 99.9 || $sumaAlicuotas > 100.1) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Operación abortada: La suma de las alícuotas del condominio no es 100%. Suma actual: ' . $sumaAlicuotas . '%. Por favor corrija la alícuota de los inmuebles antes de facturar.'
            ])->setStatusCode(422);
        }

        $simulacion = [];
        $totalDistribuido = 0;
        $excluidosCount = 0;

        foreach ($apartamentos as $apto) {
            // Lógica de cálculo: Se cobra el monto base exacto por apartamento
            $montoApartamento = $montoGlobal;

            // Regla de Negocio (Opción A): Excluir si no tiene residente asignado
            if (empty($apto['residente_id'])) {
                $excluidosCount++;
                continue; // No se incluye en la facturación ni suma al total distribuido
            }

            $totalDistribuido += $montoApartamento;

            $simulacion[] = [
                'apartamento_id'  => $apto['id'],
                'nro_apartamento' => $apto['nro_apartamento'] ?? $apto['numero'] ?? 'N/A',
                'alicuota'        => $apto['alicuota'] . '%',
                'monto_a_pagar'   => $montoApartamento
            ];
        }

        $notaAuditoria = "Apartamentos sin residente excluidos del cobro: {$excluidosCount}";

        // Auditoría financiera en la respuesta
        return $this->response->setJSON([
            'status'             => 'success',
            'monto_global_base'  => $montoGlobal,
            'suma_alicuotas'     => $sumaAlicuotas . '%',
            'total_distribuido'  => round($totalDistribuido, 2),
            'descuadre_centavos' => round($montoGlobal - $totalDistribuido, 2),
            'excluidos_count'    => $excluidosCount,
            'nota_auditoria'     => $notaAuditoria,
            'detalle'            => $simulacion
        ]);
    }

    /**
     * POST /crm/finanzas/facturar
     *
     * Emite recibos mensuales masivos de forma segura usando transacciones.
     */
    public function emitirRecibos()
    {
        $session = session();
        if (! $session->get('isLoggedIn') || $session->get('rol') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Acceso denegado.'])->setStatusCode(403);
        }

        $condominioId = (int) $this->request->getPost('condominio_id');
        $montoGlobal  = (float) $this->request->getPost('monto_global_gastos'); // Monto Base de Gastos Globales
        $mes          = (int) $this->request->getPost('mes');
        $anio         = (int) $this->request->getPost('anio');

        if ($condominioId <= 0 || $montoGlobal <= 0 || $mes <= 0 || $mes > 12 || $anio <= 2000) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Datos inválidos. Verifique condominio_id, monto, mes y año.'])->setStatusCode(400);
        }

        $apartamentoModel = new ApartamentoModel();
        $apartamentos = $apartamentoModel->where('condominio_id', $condominioId)->findAll();

        if (empty($apartamentos)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No se encontraron apartamentos.'])->setStatusCode(404);
        }

        // VALIDACIÓN CRÍTICA: Regla del 100% de Alícuota
        $sumaAlicuotas = 0;
        foreach ($apartamentos as $apto) {
            $sumaAlicuotas += (float) $apto['alicuota'];
        }

        if ($sumaAlicuotas < 99.9 || $sumaAlicuotas > 100.1) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Facturación bloqueada por seguridad: La suma de alícuotas del edificio (' . $sumaAlicuotas . '%) no equivale al 100%.'
            ])->setStatusCode(422);
        }

        $reciboModel = new ReciboModel();
        $recibosBatch = [];
        $yaExistentes = 0;

        foreach ($apartamentos as $apto) {
            // Regla de Negocio (Opción A): Excluir si no tiene residente asignado
            if (empty($apto['residente_id'])) {
                continue;
            }

            // Evitar duplicar recibos del mismo mes/año para el mismo apartamento
            if ($reciboModel->existeRecibo((int) $apto['id'], $mes, $anio)) {
                $yaExistentes++;
                continue;
            }

            // Cálculo individual: Se registra el valor base exacto por apartamento (sin multiplicar por alícuota)
            $montoApartamento = $montoGlobal;

            $recibosBatch[] = [
                'apartamento_id'  => $apto['id'],
                'mes'             => $mes,
                'anio'            => $anio,
                'monto_base'      => $montoApartamento,
                'monto_intereses' => 0,
                'monto_total'     => $montoApartamento,
                'estado_pago'     => 'Pendiente',
            ];
        }

        // Si todos los recibos ya habían sido facturados en este periodo
        if (empty($recibosBatch)) {
            return $this->response->setJSON([
                'status'  => 'warning',
                'message' => "No se generaron recibos. Los {$yaExistentes} apartamentos ya estaban facturados en este mes y año."
            ]);
        }

        // ---------------------------------------------------------------
        // TRANSACCIÓN DE BASE DE DATOS (SEGURIDAD DE INTEGRIDAD)
        // ---------------------------------------------------------------
        $db = \Config\Database::connect();
        $db->transStart(); // Inicia la transacción

        try {
            // Inserción en bloque masiva, más eficiente que N inserts
            $reciboModel->insertBatch($recibosBatch);
        } catch (\Exception $e) {
            $db->transRollback(); // Revertir todo si falla uno solo
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Fallo crítico en la base de datos al generar facturas. Ningún recibo fue emitido por seguridad.',
                'error'   => $e->getMessage()
            ])->setStatusCode(500);
        }

        $db->transComplete(); // Confirmar la transacción si todo fue bien

        // Segunda capa de seguridad de transacción
        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'La transacción falló y ha sido revertida de forma segura por el motor de base de datos.'
            ])->setStatusCode(500);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => "Facturación masiva exitosa: " . count($recibosBatch) . " recibos emitidos.",
            'data'    => [
                'recibos_emitidos'    => count($recibosBatch),
                'recibos_ya_existian' => $yaExistentes,
                'total_apartamentos'  => count($apartamentos),
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
    public function listarPagosPendientes()
    {
        // Restricción: Solo administrador
        if (session()->get('rol') !== 'admin') {
            return $this->response->setJSON(['error' => 'Acceso denegado. Permisos insuficientes.'])->setStatusCode(403);
        }

        $pagoModel = new \App\Models\PagoModel();

        // Consulta optimizada con JOINs para traer los datos del recibo y del apartamento
        $pagosPendientes = $pagoModel->select('pagos.*, recibos_mensuales.mes, recibos_mensuales.anio, apartamentos.numero as apartamento')
            ->join('recibos_mensuales', 'recibos_mensuales.id = pagos.recibo_mensual_id')
            ->join('apartamentos', 'apartamentos.id = recibos_mensuales.apartamento_id')
            ->where('pagos.estado_validacion', 'Por Validar')
            ->findAll();

        return $this->response->setJSON($pagosPendientes)->setStatusCode(200);
    }
}
