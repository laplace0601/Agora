<?php

namespace App\Controllers;

use App\Models\ApartamentoModel;
use App\Models\ReciboModel;
use App\Models\PagoModel;
use App\Models\SolvenciaModel;

class FinanzasController extends BaseController
{
    /**
     * Emite recibos mensuales masivos para todos los apartamentos de un condominio.
     * Fórmula: monto_total = monto_base * alicuota (por apartamento).
     * Solo para rol 'admin'. 
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
        $descripcion  = trim($this->request->getPost('descripcion') ?? '');

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

        $apartamentosOcupados = array_filter($apartamentos, function ($apto) {
            return !empty($apto['residente_id']);
        });

        $totalOcupados  = count($apartamentosOcupados);
        $excluidosCount = count($apartamentos) - $totalOcupados;

        // 2. Validación de seguridad para evitar división por cero
        if ($totalOcupados > 0) {
            // Aquí es donde calculas la parte proporcional real
            $montoIndividual = $montoGlobal / $totalOcupados;
        } else {
            $montoIndividual = 0;
        }

        $totalDistribuido = 0;
        // 3. Ahora el bucle solo recorre los ocupados y acumula la distribución real
        foreach ($apartamentosOcupados as $apto) {
            $montoApartamento  = $montoIndividual;
            $totalDistribuido += $montoApartamento;

            $simulacion[] = [
                'apartamento_id'  => $apto['id'],
                'nro_apartamento' => $apto['nro_apartamento'] ?? $apto['numero'] ?? 'N/A',
                'alicuota'        => $apto['alicuota'] . '%',
                'monto_a_pagar'   => $montoApartamento,
                'descripcion'     => $descripcion
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
     * POST /crm/finanzas/facturar  y  POST /admin/finanzas/facturar
     *
     * Emite recibos mensuales masivos de forma segura usando transacciones.
     */
    public function emitirRecibos()
    {
        $session = session();
        if (! $session->get('isLoggedIn') || ! in_array($session->get('rol'), ['admin', 'root'])) {
            return $this->responder('error', 'Acceso denegado. Se requiere rol de administrador.', 403, [], 'login');
        }

        $condominioId = (int) $this->request->getPost('condominio_id');
        $montoGlobal  = (float) $this->request->getPost('monto_global_gastos'); // Monto Base de Gastos Globales
        $mes          = (int) $this->request->getPost('mes');
        $anio         = (int) $this->request->getPost('anio');
        $descripcion  = trim((string) $this->request->getPost('descripcion'));

        if ($condominioId <= 0 || $montoGlobal <= 0 || $mes <= 0 || $mes > 12 || $anio <= 2000 || empty($descripcion)) {
            return $this->responder('error', 'Datos inválidos. Verifique el condominio, monto, mes (1-12), año y la descripción del cobro (obligatoria).', 400, [], 'admin/finanzas/cobro');
        }

        $apartamentoModel = new ApartamentoModel();
        $apartamentos = $apartamentoModel->where('condominio_id', $condominioId)->findAll();

        if (empty($apartamentos)) {
            return $this->responder('error', 'No se encontraron apartamentos registrados para este condominio.', 404, [], 'admin/finanzas/cobro');
        }

        // 1. Filtrar los apartamentos ocupados antes de entrar al bucle
        $apartamentosOcupados = array_filter($apartamentos, function ($apto) {
            return !empty($apto['residente_id']) && $apto['residente_id'] != 0;
        });

        $totalOcupados = count($apartamentosOcupados);

        // 2. Validación y cálculo de la distribución exacta
        if ($totalOcupados === 0) {
            return $this->responder('error', 'No hay apartamentos con residentes asignados para facturar en este condominio.', 400, [], 'admin/finanzas/cobro');
        }

        $montoIndividual = $montoGlobal / $totalOcupados;

        $reciboModel = new ReciboModel();
        $idsOcupados = array_map('intval', array_column($apartamentosOcupados, 'id'));

        // Optimización N+1: Consulta consolidada para precargar todos los recibos ya emitidos en este periodo
        $recibosExistentes = $reciboModel
            ->select('apartamento_id')
            ->whereIn('apartamento_id', $idsOcupados)
            ->where('mes', $mes)
            ->where('anio', $anio)
            ->findAll();

        $mapExistentes = array_fill_keys(array_column($recibosExistentes, 'apartamento_id'), true);
        $recibosBatch  = [];
        $yaExistentes  = 0;

        // 3. Recorrer los ocupados verificando contra el mapa en memoria (0 consultas SQL dentro del bucle)
        foreach ($apartamentosOcupados as $apto) {
            $aptoId = (int) $apto['id'];
            if (isset($mapExistentes[$aptoId])) {
                $yaExistentes++;
                continue;
            }

            // Asignación estricta del monto calculado y fecha real de emisión
            $recibosBatch[] = [
                'apartamento_id'  => $aptoId,
                'mes'             => $mes,
                'anio'            => $anio,
                'monto_base'      => $montoIndividual,
                'monto_intereses' => 0,
                'monto_total'     => $montoIndividual,
                'estado_pago'     => 'Pendiente',
                'fecha_emision'   => date('Y-m-d H:i:s'),
                'descripcion'     => $descripcion,
            ];
        }

        // Si todos los recibos ya habían sido facturados en este periodo
        if (empty($recibosBatch)) {
            return $this->responder('error', "No se generaron recibos. Los {$yaExistentes} apartamentos ya estaban facturados en este mes y año.", 200, [], 'admin/finanzas/cobro');
        }

        // ---------------------------------------------------------------
        // TRANSACCIÓN DE BASE DE DATOS (SEGURIDAD DE INTEGRIDAD)
        // ---------------------------------------------------------------
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $reciboModel->insertBatch($recibosBatch);
        } catch (\Throwable $e) {
            $db->transRollback();
            return $this->responder('error', 'Fallo crítico en la base de datos al generar facturas: ' . $e->getMessage(), 500, ['error' => $e->getMessage()], 'admin/finanzas/cobro');
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->responder('error', 'La transacción falló y ha sido revertida de forma segura por el motor de base de datos.', 500, [], 'admin/finanzas/cobro');
        }

        $msg = "Creación de recibos exitosa: " . count($recibosBatch) . " recibos emitidos.";
        return $this->responder('success', $msg, 200, [
            'data' => [
                'recibos_emitidos'    => count($recibosBatch),
                'recibos_ya_existian' => $yaExistentes,
                'total_apartamentos'  => count($apartamentos),
            ],
        ], 'admin/finanzas/cobro');
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
            return $this->responder('error', 'Acceso denegado. Solo los residentes pueden registrar pagos.', 403, [], 'auth/login');
        }

        try {
            $metodoInput = trim($this->request->getPost('metodo_pago') ?? '');
            $metodoPago  = formatearMetodoPago($metodoInput);
            $referencia  = trim($this->request->getPost('referencia_transaccion') ?? '');

            if ($metodoPago === 'Efectivo' && empty($referencia)) {
                $referencia = 'Efectivo / Sin referencia';
            }

            $datos = [
                'recibo_mensual_id'      => (int) $this->request->getPost('recibo_mensual_id'),
                'monto_pagado'           => (float) $this->request->getPost('monto_pagado'),
                'metodo_pago'            => $metodoPago,
                'referencia_transaccion' => $referencia,
                'comprobante_url'        => trim($this->request->getPost('comprobante_url') ?? ''),
                'fecha_registro'         => date('Y-m-d H:i:s'),
                'estado_validacion'      => 'Por Validar',
            ];

            if ($datos['recibo_mensual_id'] <= 0 || $datos['monto_pagado'] <= 0) {
                return $this->responder('error', 'El recibo y el monto pagado son obligatorios.', 400, [], 'residente/pago');
            }

            $pagoModel = new PagoModel();

            if (! $pagoModel->insert($datos)) {
                return $this->responder('error', 'Error de validación al registrar el pago.', 422, ['errors' => $pagoModel->errors()], 'residente/pago');
            }

            return $this->responder('success', 'Pago registrado exitosamente. Queda pendiente de validación por el administrador.', 200, [
                'data' => [
                    'pago_id' => $pagoModel->getInsertID(),
                ],
            ], 'residente/pago');
        } catch (\Throwable $e) {
            log_message('error', '[FinanzasController::registrarPago] ' . $e->getMessage());
            return $this->responder('error', 'Error al registrar el pago: ' . $e->getMessage(), 500, [], 'residente/pago');
        }
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
            return $this->responder('error', 'Acceso denegado. Solo los administradores pueden validar pagos.', 403, [], 'auth/login');
        }

        $pagoId = (int) $this->request->getPost('pago_id');
        $accion = trim($this->request->getPost('accion') ?? '');

        if ($pagoId <= 0 || ! in_array($accion, ['aprobar', 'rechazar'], true)) {
            return $this->responder('error', 'Debe indicar un pago_id válido y una acción (aprobar o rechazar).', 400, [], 'admin/finanzas/pagos');
        }

        // Validar que el motivo sea obligatorio al rechazar
        $motivoRechazo = trim($this->request->getPost('motivo_rechazo') ?? '');
        if ($accion === 'rechazar' && $motivoRechazo === '') {
            return $this->responder('error', 'El motivo de rechazo es obligatorio para rechazar un pago.', 422, [], 'admin/finanzas/pagos');
        }

        $pagoModel = new PagoModel();
        $pago      = $pagoModel->obtenerConRecibo($pagoId);

        if (! $pago) {
            return $this->responder('error', 'El pago indicado no existe.', 404, [], 'admin/finanzas/pagos');
        }

        $reciboModel = new ReciboModel();
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            if ($accion === 'aprobar') {
                // Actualizar pago → Aprobado
                $pagoModel->actualizarValidacion($pagoId, 'Aprobado');

                // AUTOMATIZACIÓN: actualizar recibo → Pagado con la fecha real del pago o aprobación
                $fechaPago = !empty($pago['fecha_registro']) ? $pago['fecha_registro'] : date('Y-m-d H:i:s');
                $reciboModel->actualizarEstadoPago((int) $pago['recibo_mensual_id'], 'Pagado', $fechaPago);

                $mensajeExito = 'Pago aprobado. El recibo mensual ha sido marcado como Pagado automáticamente.';
            } else {
                // Acción: rechazar — guardar también el motivo
                $pagoModel->actualizarValidacion($pagoId, 'Rechazado', $motivoRechazo);
                $mensajeExito = 'Pago rechazado. El recibo mensual permanece como Pendiente.';
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->responder('error', 'La transacción contable falló y fue revertida por seguridad.', 500, [], 'admin/finanzas/pagos');
            }

            return $this->responder('success', $mensajeExito, 200, [], 'admin/finanzas/pagos');
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('critical', '[FinanzasController::validarPago] Error transaccional: ' . $e->getMessage());
            return $this->responder('error', 'Ocurrió un error en la base de datos al procesar el pago: ' . $e->getMessage(), 500, [], 'admin/finanzas/pagos');
        }
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

        $descripcion = trim($this->request->getPost('descripcion') ?? '');

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
            'descripcion'            => $descripcion,
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

    /**
     * GET /admin/recibo/pdf/(:num) o /crm/finanzas/recibo/pdf/(:num)
     *
     * Permite descargar o previsualizar el PDF del recibo para administradores o residentes autorizados.
     */
    public function descargarRecibo(int $id)
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('auth/login'))->with('error', 'Debes iniciar sesión.');
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

        if (($recibo['estado_pago'] ?? '') !== 'Pagado') {
            return redirect()->back()->with('error', 'El recibo aún no se encuentra marcado como Pagado.');
        }

        $pagoModel = new PagoModel();
        $pago = $pagoModel
            ->where('recibo_mensual_id', $id)
            ->where('estado_validacion', 'Aprobado')
            ->orderBy('id', 'DESC')
            ->first();

        if (! $pago) {
            $pago = $pagoModel->where('recibo_mensual_id', $id)->orderBy('id', 'DESC')->first() ?? [];
        }

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

        $data['isDompdf'] = false;
        return $this->response->setBody(view('pdf/recibo_pago', $data));
    }
}
