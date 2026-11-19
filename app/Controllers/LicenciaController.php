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
        try {
            $session = session();
            if (! $session->get('isLoggedIn') || ! in_array($session->get('rol'), ['root', 'admin'], true)) {
                return $this->responder('error', 'Acceso denegado. Debe iniciar sesión para modificar el plan.', 403, [], 'login');
            }

            $isDev = (ENVIRONMENT === 'development');

            // ── 1. Recibir y sanear los datos enviados por POST ─────────────────
            $codigoActivacion = trim((string) ($this->request->getPost('codigo_activacion') ?? ''));
            $planDestino      = trim((string) ($this->request->getPost('plan_destino') ?? ''));

            // ── 2. Mapa de planes: clave del .env con fallback seguro → datos del plan ──
            $mapaPlanes = [
                'Bronce' => [
                    'key'    => trim((string) (env('AGORA_KEY_BRONCE') ?: 'ClaveSecretaBronce2026'), " \t\n\r\0\x0B\"'"),
                    'limite' => 40,
                ],
                'Plata' => [
                    'key'    => trim((string) (env('AGORA_KEY_PLATA') ?: 'ClaveSecretaPlata2026'), " \t\n\r\0\x0B\"'"),
                    'limite' => 120,
                ],
                'Oro' => [
                    'key'    => trim((string) (env('AGORA_KEY_ORO') ?: 'ClaveSecretaOro2026'), " \t\n\r\0\x0B\"'"),
                    'limite' => 9999,
                ],
            ];

            // ── 3. Obtener o inicializar configuración de marca ─────────────────
            $marcaModel = new MarcaModel();
            $marca      = $marcaModel->first();

            if (! $marca) {
                $marcaModel->skipValidation(true)->insert([
                    'id'                  => 1,
                    'nombre_empresa'      => 'Ágora Condominio',
                    'nivel_licencia'      => 'Bronce',
                    'limite_apartamentos' => 40,
                ]);
                $marca = $marcaModel->first();
            }

            if (! $marca) {
                return $this->responder('error', 'No se encontró la configuración de marca.', 404, [], 'super/planes');
            }

            // ── 4. Validación de la clave según el plan seleccionado ───────────
            $planEncontrado = null;
            $planDestinoNormalizado = ucfirst(strtolower($planDestino));

            if (!empty($planDestinoNormalizado) && isset($mapaPlanes[$planDestinoNormalizado])) {
                // Validación estricta: la clave debe ser del plan seleccionado específicamente
                if ($codigoActivacion !== '' && $codigoActivacion === $mapaPlanes[$planDestinoNormalizado]['key']) {
                    $planEncontrado = [
                        'nivel'  => $planDestinoNormalizado,
                        'limite' => $mapaPlanes[$planDestinoNormalizado]['limite'],
                    ];
                } else {
                    // Verificar si la clave ingresada pertenece a otro plan
                    $otroPlan = null;
                    foreach ($mapaPlanes as $pNombre => $pDatos) {
                        if ($codigoActivacion !== '' && $codigoActivacion === $pDatos['key']) {
                            $otroPlan = $pNombre;
                            break;
                        }
                    }

                    if ($otroPlan) {
                        return $this->responder(
                            'error',
                            "La clave ingresada corresponde al Plan {$otroPlan}, pero estás intentando activar el Plan {$planDestinoNormalizado}. Debes ingresar la clave correcta de este plan.",
                            422,
                            [],
                            'super/planes'
                        );
                    }

                    return $this->responder(
                        'error',
                        "Clave de activación incorrecta para el Plan {$planDestinoNormalizado}.",
                        400,
                        [],
                        'super/planes'
                    );
                }
            } else {
                // Flujo de compatibilidad si no se envió plan_destino
                foreach ($mapaPlanes as $nombrePlan => $datosPlan) {
                    if ($codigoActivacion !== '' && $codigoActivacion === $datosPlan['key']) {
                        $planEncontrado = [
                            'nivel'  => $nombrePlan,
                            'limite' => $datosPlan['limite'],
                        ];
                        break;
                    }
                }
            }

            if ($planEncontrado === null) {
                return $this->responder('error', 'Llave de activación inválida o incorrecta.', 400, [], 'super/planes');
            }

            // ── 5. Persistir el cambio de licencia (skipValidation para evitar bloqueos) ─
            $marcaModel->skipValidation(true)->update($marca['id'], [
                'nivel_licencia'           => $planEncontrado['nivel'],
                'limite_apartamentos'      => $planEncontrado['limite'],
                'codigo_activacion'        => $codigoActivacion,
                'fecha_actualizacion_plan' => date('Y-m-d H:i:s'),
            ]);

            log_message('info', "[LicenciaController] Licencia actualizada a nivel '{$planEncontrado['nivel']}' para marca ID {$marca['id']}.");

            return $this->responder('success', "¡Excelente! Licencia actualizada exitosamente a nivel {$planEncontrado['nivel']}.", 200, [
                'success' => true,
                'plan'    => $planEncontrado['nivel'],
                'limite'  => $planEncontrado['limite'],
            ], 'super/planes');

        } catch (\Throwable $e) {
            log_message('error', '[LicenciaController::activarLicencia] Error: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
            return $this->responder('error', 'Error al procesar la activación: ' . $e->getMessage(), 500, [], 'super/planes');
        }
    }
}

