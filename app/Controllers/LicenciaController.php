<?php

namespace App\Controllers;

use App\Models\ApartamentoModel;
use App\Models\MarcaModel;

class LicenciaController extends BaseController
{
    /**
     * POST /crm/apartamentos/registrar
     *
     * CONTROL CRÍTICO CRM — Registro de apartamento con validación de licencia.
     *
     * Flujo:
     * 1. Verifica que el rol en sesión sea 'admin' (403 si no).
     * 2. Consulta si la marca tiene cupo disponible según su límite de licencia.
     * 3. Si no hay cupo, frena el proceso y retorna error de capacidad (409).
     * 4. Si hay cupo, valida datos del POST e inserta el apartamento.
     */
    public function registrarApartamento()
    {
        $session = session();

        // ---------------------------------------------------------------
        // 1. Control de permisos: solo 'admin' puede registrar apartamentos
        // ---------------------------------------------------------------
        if (! $session->get('isLoggedIn') || $session->get('rol') !== 'admin') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Acceso denegado. Solo los administradores pueden registrar apartamentos.',
            ])->setStatusCode(403);
        }

        // ---------------------------------------------------------------
        // 2. Obtener marca_id desde el POST (la marca a la que pertenece el admin)
        // ---------------------------------------------------------------
        $marcaId = (int) $this->request->getPost('marca_id');

        if ($marcaId <= 0) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Debe especificar una marca válida.',
            ])->setStatusCode(400);
        }

        // ---------------------------------------------------------------
        // 3. Verificar cupo de licencia antes de cualquier inserción
        // ---------------------------------------------------------------
        $marcaModel = new MarcaModel();

        if (! $marcaModel->tieneCupoDisponible($marcaId)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Límite de capacidad del CRM superado. Su licencia no permite registrar más apartamentos. Contacte a soporte para ampliar su plan.',
            ])->setStatusCode(409);
        }

        // ---------------------------------------------------------------
        // 4. Sanitizar y preparar datos del apartamento
        // ---------------------------------------------------------------
        $datos = [
            'condominio_id'         => (int) $this->request->getPost('condominio_id'),
            'residente_id'          => $this->request->getPost('residente_id') ? (int) $this->request->getPost('residente_id') : null,
            'nombre_edificio_torre' => trim($this->request->getPost('nombre_edificio_torre') ?? ''),
            'nro_apartamento'       => trim($this->request->getPost('nro_apartamento') ?? ''),
            'alicuota'              => (float) $this->request->getPost('alicuota'),
        ];

        // ---------------------------------------------------------------
        // 5. Insertar usando el modelo (las validationRules del modelo se aplican)
        // ---------------------------------------------------------------
        $apartamentoModel = new ApartamentoModel();

        if (! $apartamentoModel->insert($datos)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error de validación al registrar el apartamento.',
                'errors'  => $apartamentoModel->errors(),
            ])->setStatusCode(422);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Apartamento registrado exitosamente.',
            'data'    => [
                'apartamento_id' => $apartamentoModel->getInsertID(),
            ],
        ]);
    }

    /**
     * POST /crm/licencia/activar
     *
     * Valida y activa una licencia de la plataforma comparando el código
     * con las llaves definidas en .env, usando un mapa de planes para
     * eliminar lógica duplicada entre Plata y Oro.
     *
     * SEGURIDAD: Los bloques "=== DEBUG ===" pueden comentarse en producción
     * sin afectar el flujo principal.
     */
    public function activarLicencia()
    {
        $isDev = (ENVIRONMENT === 'development');

        // ── 1. Recibir y sanear el código enviado por POST ─────────────────
        $codigoActivacion = trim($this->request->getPost('codigo_activacion') ?? '');

        // ── 2. Mapa de planes: clave del .env → datos del plan ─────────────
        //    Añadir nuevos planes aquí sin tocar otra lógica.
        //    trim('"\'') elimina comillas residuales por si el .env las incluye.
        $mapaPlanes = [
            'Plata' => [
                'key'    => trim((string) env('AGORA_KEY_PLATA', ''), " \t\n\r\0\x0B\"'"),
                'limite' => 100,
            ],
            'Oro' => [
                'key'    => trim((string) env('AGORA_KEY_ORO', ''), " \t\n\r\0\x0B\"'"),
                'limite' => 9999,
            ],
        ];

        // === DEBUG START === (comentar este bloque en producción) ============
        if ($isDev) {
            // 2a. Verificar que todas las variables de entorno están cargadas
            foreach ($mapaPlanes as $nombrePlan => $datosPlan) {
                if ($datosPlan['key'] === '') {
                    $envVar = 'AGORA_KEY_' . strtoupper($nombrePlan);
                    log_message('error', "[LicenciaController] {$envVar} no encontrada o vacía en .env");
                    return $this->response->setStatusCode(500)->setJSON([
                        'error' => "Error de configuración: Variable {$envVar} no encontrada.",
                        'csrf'  => csrf_hash(),
                    ]);
                }
            }

            // 2b. Logging unificado de longitudes para detectar caracteres ocultos
            $logPartes = ["POST len=" . mb_strlen($codigoActivacion)];
            foreach ($mapaPlanes as $nombre => $datos) {
                $logPartes[] = "KEY_{$nombre} len=" . mb_strlen($datos['key']);
            }
            log_message('debug', '[LicenciaController::activarLicencia] Comparación de longitudes — ' . implode(' | ', $logPartes));
        }
        // === DEBUG END =======================================================

        // ── 3. Obtener configuración de marca ─────────────────────────────
        $marcaModel = new MarcaModel();
        $marca      = $marcaModel->first();

        if (! $marca) {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'No se encontró la configuración de marca.',
                'csrf'  => csrf_hash(),
            ]);
        }

        // ── 4. Buscar el plan cuya llave coincide (flujo unificado) ────────
        $planEncontrado = null;

        foreach ($mapaPlanes as $nombrePlan => $datosPlan) {
            if ($codigoActivacion === $datosPlan['key']) {
                $planEncontrado = [
                    'nivel'  => $nombrePlan,
                    'limite' => $datosPlan['limite'],
                ];
                break;
            }
        }

        // === DEBUG START === (comentar en producción) ========================
        if ($isDev && $planEncontrado === null) {
            $debugInfo = ['recibido_len' => mb_strlen($codigoActivacion), 'recibido_hex' => bin2hex($codigoActivacion)];
            foreach ($mapaPlanes as $nombre => $datos) {
                $debugInfo["key_{$nombre}_len"] = mb_strlen($datos['key']);
                $debugInfo["key_{$nombre}_hex"] = bin2hex($datos['key']);
            }
            log_message('debug', '[LicenciaController] Llave inválida. Debug HEX: ' . json_encode($debugInfo));
        }
        // === DEBUG END =======================================================

        if ($planEncontrado === null) {
            $response = [
                'error' => 'Llave de activación inválida.',
                'csrf'  => csrf_hash(),
            ];
            if ($isDev) {
                $response['debug_info'] = [
                    'recibido_len' => mb_strlen($codigoActivacion),
                    'recibido_hex' => bin2hex($codigoActivacion),
                ];
            }
            return $this->response->setStatusCode(400)->setJSON($response);
        }

        // ── 5. Persistir el cambio de licencia ────────────────────────────
        $marcaModel->update($marca['id'], [
            'nivel_licencia'           => $planEncontrado['nivel'],
            'limite_apartamentos'      => $planEncontrado['limite'],
            'codigo_activacion'        => $codigoActivacion,
            'fecha_actualizacion_plan' => date('Y-m-d H:i:s'),
        ]);

        log_message('info', "[LicenciaController] Licencia actualizada a nivel '{$planEncontrado['nivel']}' para marca ID {$marca['id']}.");

        return $this->response->setStatusCode(200)->setJSON([
            'success' => true,
            'message' => "Licencia actualizada exitosamente a nivel {$planEncontrado['nivel']}.",
            'csrf'    => csrf_hash(),
        ]);
    }
}

