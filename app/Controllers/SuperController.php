<?php

namespace App\Controllers;

use App\Models\PagoModel;
use App\Traits\FileUploadTrait;

class SuperController extends BaseController
{
    use FileUploadTrait;

    public function panel()
    {
        return redirect()->to(base_url('super/apartamentos'));
    }


    /**
     * GET /super/apartamentos
     *
     * Vista de gestión inmobiliaria: condominios y apartamentos.
     */
    public function apartamentos()
    {
        $condominioModel = new \App\Models\CondominioModel();
        $apartamentoModel = new \App\Models\ApartamentoModel();

        $apartamentos = $apartamentoModel->select('apartamentos.*, condominios.nombre_condominio, residentes.nombre_completo AS nombre_residente')
            ->join('condominios', 'condominios.id = apartamentos.condominio_id')
            ->join('residentes', 'residentes.id = apartamentos.residente_id', 'left')
            ->findAll();

        $datos = [
            'condominios' => $condominioModel->findAll(),
            'apartamentos' => $apartamentos,
        ];

        return view('root/apartamentos', $datos);
    }

    /**
     * Dispatcher para solicitudes POST a super/apartamentos
     */
    public function procesarInmuebles()
    {
        $accion = $this->request->getPost('accion');
        if ($accion === 'registrar_apartamento') {
            return $this->registrarApartamento();
        }
        return $this->registrarCondominio();
    }

    /**
     * POST /super/apartamentos/registrar-condominio
     */
    public function registrarCondominio()
    {
        $condominioModel = new \App\Models\CondominioModel();

        $nombre  = trim($this->request->getPost('nombre_condo') ?? '');
        $rif     = trim($this->request->getPost('rif_jurisdiccion') ?? '');
        $metros  = $this->request->getPost('total_metros_cuadrados');

        // Validación de seguridad backend: metros no puede ser 0 ni negativo
        if (empty($nombre)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'error'  => 'El nombre del condominio es obligatorio.',
                    'csrf'   => csrf_hash()
                ]);
            }
            return redirect()->to(base_url('super/apartamentos'))->with('error', 'El nombre del condominio es obligatorio.')->withInput();
        }

        $metrosFloat = $this->sanitizarDecimal($metros);
        if ($metrosFloat <= 0) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'error'  => 'El total de metros cuadrados debe ser mayor a 0.',
                    'csrf'   => csrf_hash()
                ]);
            }
            return redirect()->to(base_url('super/apartamentos'))->with('error', 'El total de metros cuadrados debe ser mayor a 0.')->withInput();
        }

        // Asegurar que la configuración de marca (id=1) exista para no violar la FK
        try {
            $db = \Config\Database::connect();
            $marcaExiste = $db->table('configuracion_marca')->where('id', 1)->countAllResults();
            if ($marcaExiste === 0) {
                $db->table('configuracion_marca')->insert([
                    'id'                  => 1,
                    'nombre_empresa'      => 'Mi Administradora Base',
                    'color_primario'      => '#007bff',
                    'correo_contacto'     => 'contacto@agora.com',
                    'nivel_licencia'      => 'Bronce',
                    'limite_apartamentos' => 100,
                ]);
            }
        } catch (\Throwable $e) {
            log_message('error', '[SuperController] Error verificando configuracion_marca: ' . $e->getMessage());
        }

        // Manejo seguro del RIF (en MySQL tiene constraint UNIQUE y máx 20 caracteres)
        if (empty($rif)) {
            $rif = 'SR-' . substr(str_replace('.', '', uniqid('', true)), 0, 16);
        } else {
            $rif = substr($rif, 0, 20);
            $condoExistente = $condominioModel->where('rif_jurisdiccion', $rif)->first();
            if ($condoExistente) {
                $msgError = "Ya existe un condominio con el RIF '{$rif}'.";
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['status' => 'error', 'error' => $msgError, 'csrf' => csrf_hash()]);
                }
                return redirect()->to(base_url('super/apartamentos'))->with('error', $msgError)->withInput();
            }
        }

        $datos = [
            'nombre_condominio'      => $nombre,
            'rif_jurisdiccion'       => $rif,
            'total_metros_cuadrados' => $metrosFloat,
            'marca_id'               => 1,
        ];

        try {
            if (! $condominioModel->insert($datos)) {
                $errores = implode(', ', $condominioModel->errors());
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'error'  => 'Error al registrar: ' . $errores,
                        'csrf'   => csrf_hash()
                    ]);
                }
                return redirect()->to(base_url('super/apartamentos'))->with('error', 'Error al registrar: ' . $errores)->withInput();
            }
        } catch (\Throwable $e) {
            log_message('error', '[SuperController::registrarCondominio] ' . $e->getMessage());
            $errorMsg = 'Error en base de datos al registrar condominio: ' . $e->getMessage();
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error',
                    'error'  => $errorMsg,
                    'csrf'   => csrf_hash()
                ]);
            }
            return redirect()->to(base_url('super/apartamentos'))->with('error', $errorMsg)->withInput();
        }

        session()->setFlashdata('success', 'Condominio registrado exitosamente.');

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Condominio registrado exitosamente.',
                'csrf'    => csrf_hash()
            ]);
        }

        return redirect()->to(base_url('super/apartamentos'));
    }

    /**
     * POST /super/apartamentos/condominios/delete/(:num)
     */
    public function deleteCondominio($id = null)
    {
        if (!$id) {
            return $this->responder('error', 'ID requerido.', 400, [], 'super/apartamentos');
        }

        $condominioModel = new \App\Models\CondominioModel();

        if ($condominioModel->delete($id)) {
            return $this->responder('success', 'Condominio eliminado correctamente.', 200, [], 'super/apartamentos');
        }

        return $this->responder('error', 'Error al eliminar el condominio.', 500, [], 'super/apartamentos');
    }

    /**
     * POST /super/apartamentos/delete/(:num)
     * Elimina un apartamento individual y limpia sus relaciones.
     */
    public function deleteApartamento($id = null)
    {
        if (! $id) {
            return $this->responder('error', 'ID de apartamento requerido.', 400, [], 'super/apartamentos');
        }

        try {
            $apartamentoModel = new \App\Models\ApartamentoModel();
            $apto = $apartamentoModel->find($id);

            if (! $apto) {
                return $this->responder('error', 'El apartamento no existe o ya fue eliminado.', 404, [], 'super/apartamentos');
            }

            $db = \Config\Database::connect();
            $db->transStart();

            // 1. Eliminar pagos recibidos asociados a recibos de este apartamento
            $recibos = $db->table('recibos_mensuales')
                ->select('id')
                ->where('apartamento_id', $id)
                ->get()
                ->getResultArray();

            if (! empty($recibos)) {
                $recibosIds = array_column($recibos, 'id');
                if ($db->tableExists('pagos_recibidos')) {
                    $db->table('pagos_recibidos')->whereIn('recibo_mensual_id', $recibosIds)->delete();
                }
                $db->table('recibos_mensuales')->whereIn('id', $recibosIds)->delete();
            }

            // 2. Limpieza de recibos de solvencia
            if ($db->tableExists('recibos_solvencia')) {
                $db->table('recibos_solvencia')->where('apartamento_id', $id)->delete();
            }

            // 3. Limpieza de habitantes
            if ($db->tableExists('habitantes')) {
                $db->table('habitantes')->where('apartamento_id', $id)->delete();
            }

            // 4. Limpieza de reservas de áreas comunes
            if ($db->tableExists('reservas_areas')) {
                $db->table('reservas_areas')->where('apartamento_id', $id)->delete();
            }

            // 5. Eliminar el registro del apartamento
            $apartamentoModel->delete($id);

            $db->transComplete();

            if ($db->transStatus() === false) {
                $dbError = $db->error();
                log_message('error', '[SuperController::deleteApartamento] Error BD: ' . json_encode($dbError));
                return $this->responder('error', 'No se pudo eliminar el apartamento: ' . ($dbError['message'] ?? 'Error de base de datos.'), 500, [], 'super/apartamentos');
            }

            $nro = $apto['nro_apartamento'] ?? "#{$id}";
            return $this->responder('success', "Apartamento {$nro} eliminado exitosamente.", 200, [], 'super/apartamentos');

        } catch (\Throwable $e) {
            log_message('error', '[SuperController::deleteApartamento] Excepción: ' . $e->getMessage());
            return $this->responder('error', 'Error al eliminar el apartamento: ' . $e->getMessage(), 500, [], 'super/apartamentos');
        }
    }

    /**
     * POST /super/apartamentos/delete-batch
     * Elimina múltiples apartamentos seleccionados en lote.
     */
    public function deleteApartamentosBatch()
    {
        $ids = $this->request->getPost('ids');
        if (! is_array($ids) || empty($ids)) {
            return $this->responder('error', 'No se seleccionó ningún apartamento para eliminar.', 400, [], 'super/apartamentos');
        }

        $ids = array_filter(array_map('intval', $ids), fn($v) => $v > 0);
        if (empty($ids)) {
            return $this->responder('error', 'Selección de apartamentos no válida.', 400, [], 'super/apartamentos');
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // 1. Eliminar pagos y recibos de todos los apartamentos seleccionados
            $recibos = $db->table('recibos_mensuales')
                ->select('id')
                ->whereIn('apartamento_id', $ids)
                ->get()
                ->getResultArray();

            if (! empty($recibos)) {
                $recibosIds = array_column($recibos, 'id');
                if ($db->tableExists('pagos_recibidos')) {
                    $db->table('pagos_recibidos')->whereIn('recibo_mensual_id', $recibosIds)->delete();
                }
                $db->table('recibos_mensuales')->whereIn('id', $recibosIds)->delete();
            }

            // 2. Limpieza de recibos de solvencia
            if ($db->tableExists('recibos_solvencia')) {
                $db->table('recibos_solvencia')->whereIn('apartamento_id', $ids)->delete();
            }

            // 3. Limpieza de habitantes
            if ($db->tableExists('habitantes')) {
                $db->table('habitantes')->whereIn('apartamento_id', $ids)->delete();
            }

            // 4. Limpieza de reservas de áreas comunes
            if ($db->tableExists('reservas_areas')) {
                $db->table('reservas_areas')->whereIn('apartamento_id', $ids)->delete();
            }

            // 5. Eliminar apartamentos seleccionados
            $apartamentoModel = new \App\Models\ApartamentoModel();
            $apartamentoModel->whereIn('id', $ids)->delete();

            $db->transComplete();

            if ($db->transStatus() === false) {
                $dbError = $db->error();
                log_message('error', '[SuperController::deleteApartamentosBatch] Error BD: ' . json_encode($dbError));
                return $this->responder('error', 'No se pudieron eliminar los apartamentos: ' . ($dbError['message'] ?? 'Error de base de datos.'), 500, [], 'super/apartamentos');
            }

            $total = count($ids);
            return $this->responder('success', "Se eliminaron {$total} apartamento(s) exitosamente.", 200, [], 'super/apartamentos');

        } catch (\Throwable $e) {
            log_message('error', '[SuperController::deleteApartamentosBatch] Excepción: ' . $e->getMessage());
            return $this->responder('error', 'Error al eliminar apartamentos: ' . $e->getMessage(), 500, [], 'super/apartamentos');
        }
    }

    /**
     * POST /super/apartamentos/registrar-apartamento
     */
    public function registrarApartamento()
    {
        $apartamentoModel = new \App\Models\ApartamentoModel();
        $condominioModel  = new \App\Models\CondominioModel();

        $condominioId  = (int) $this->request->getPost('condominio_id');
        $prefijo       = trim($this->request->getPost('prefijo') ?? '');
        $numeroInicial = (int) $this->request->getPost('numero_inicial');
        $cantidad      = (int) $this->request->getPost('cantidad');
        $metrosAptoStr = $this->request->getPost('metros_cuadrados_apto');
        $metrosApto    = $this->sanitizarDecimal($metrosAptoStr);

        // Helper: respuesta unificada de error (AJAX o Redirect)
        $responderError = function (string $mensaje, int $code = 400, string $tipo = 'error') {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode($code)->setJSON([
                    'status'     => 'error',
                    'error_type' => $tipo,
                    'error'      => $mensaje,
                    'csrf'       => csrf_hash()
                ]);
            }
            return redirect()->to(base_url('super/apartamentos'))->with('error', $mensaje)->withInput();
        };

        // Validaciones básicas
        if ($condominioId <= 0) {
            return $responderError('Debe seleccionar un condominio válido.');
        }
        if ($numeroInicial <= 0 || $cantidad <= 0) {
            return $responderError('El número inicial y la cantidad deben ser mayores a 0.');
        }
        if ($metrosApto <= 0) {
            return $responderError('Los metros cuadrados por apartamento deben ser mayor a 0.');
        }

        // Obtener el condominio
        $condominio = $condominioModel->find($condominioId);
        if (! $condominio) {
            return $responderError('El condominio seleccionado no existe.');
        }

        $totalMetrosCondominio = (float) ($condominio['total_metros_cuadrados'] ?? 0);
        if ($totalMetrosCondominio <= 0) {
            return $responderError('El condominio no tiene metros cuadrados registrados.');
        }

        // ── Validación 0: Límite Global del Plan (tenant-level) ────────────────
        $marcaId  = (int) ($condominio['marca_id'] ?? 1);
        $db       = \Config\Database::connect();
        $marcaRow = $db->table('configuracion_marca')
            ->select('nivel_licencia, limite_apartamentos')
            ->where('id', $marcaId)
            ->get()->getRowArray();

        $limitePlan = (int) ($marcaRow['limite_apartamentos'] ?? 0);
        $nivelPlan  = $marcaRow['nivel_licencia'] ?? 'Desconocido';

        $totalGlobal = $db->table('apartamentos')
            ->join('condominios', 'condominios.id = apartamentos.condominio_id')
            ->where('condominios.marca_id', $marcaId)
            ->countAllResults();

        if ($limitePlan > 0 && ($totalGlobal + $cantidad) > $limitePlan) {
            $disponibles = max(0, $limitePlan - $totalGlobal);
            if ($disponibles <= 0) {
                $mensajeLimite = "Has alcanzado el límite máximo de tu Plan {$nivelPlan} ({$limitePlan} de {$limitePlan} apartamentos registrados). "
                    . "Ya no dispones de cupos libres para crear más unidades. "
                    . "Para ampliar la capacidad de tu sistema, puedes mejorar tu suscripción en la sección «Mi Plan».";
            } else {
                $mensajeLimite = "Cupo insuficiente en tu Plan {$nivelPlan}: este plan permite hasta {$limitePlan} apartamentos en total. "
                    . "Actualmente tienes {$totalGlobal} registrados y solo dispones de cupo para {$disponibles} más (intentaste registrar {$cantidad}). "
                    . "Por favor reduce la cantidad a {$disponibles} o actualiza tu suscripción en la sección «Mi Plan».";
            }

            return $responderError($mensajeLimite, 422, 'plan_limit');
        }
        // ──────────────────────────────────────────────────────────────────────

        // Validación 1: Espacio (metraje dentro del condominio)
        $totalMetrosNuevos    = $metrosApto * $cantidad;
        $apartamentosActuales = $apartamentoModel->where('condominio_id', $condominioId)->findAll();
        $metrosRegistrados    = array_reduce($apartamentosActuales, fn($c, $a) => $c + (float)$a['metros_cuadrados'], 0.0);

        if (($metrosRegistrados + $totalMetrosNuevos) > $totalMetrosCondominio) {
            return $responderError(
                'El metraje total excede la capacidad del condominio. '
                    . '(Disponibles: ' . number_format($totalMetrosCondominio - $metrosRegistrados, 2) . ' m\u00b2)'
            );
        }

        // Generar identificadores
        $nuevosApartamentos = [];
        $identificadores    = [];
        $alicuotaInicial    = round(($metrosApto / $totalMetrosCondominio) * 100, 4);

        for ($i = 0; $i < $cantidad; $i++) {
            $identificador     = $prefijo . sprintf('%03d', $numeroInicial + $i);
            $identificadores[] = $identificador;
            $nuevosApartamentos[] = [
                'condominio_id'         => $condominioId,
                'residente_id'          => null,
                'nombre_edificio_torre' => $condominio['nombre_condominio'],
                'nro_apartamento'       => $identificador,
                'metros_cuadrados'      => $metrosApto,
                'alicuota'              => $alicuotaInicial,
            ];
        }

        // Validación 2: Duplicados
        $duplicados = $apartamentoModel->where('condominio_id', $condominioId)
            ->whereIn('nro_apartamento', $identificadores)
            ->findAll();

        if (!empty($duplicados)) {
            $lista = implode(', ', array_column($duplicados, 'nro_apartamento'));
            return $responderError("Los siguientes apartamentos ya est\u00e1n registrados en este condominio: {$lista}");
        }

        // Inserción masiva dentro de una transacción
        // $db ya fue instanciado arriba.
        try {
            $db->transStart();
            $apartamentoModel->insertBatch($nuevosApartamentos);
            (new \App\Services\CondominioService())->recalcularAlicuotas($condominioId);
            $db->transComplete();

            if ($db->transStatus() === false) {
                return $responderError('Error interno al guardar los apartamentos.', 500);
            }
        } catch (\Throwable $e) {
            log_message('error', '[SuperController::registrarApartamento] ' . $e->getMessage());
            return $responderError('Error en base de datos al guardar los apartamentos: ' . $e->getMessage(), 500);
        }

        session()->setFlashdata('success', "Se han registrado exitosamente {$cantidad} apartamentos.");

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => "Se han registrado exitosamente {$cantidad} apartamentos.",
                'csrf'    => csrf_hash()
            ]);
        }

        return redirect()->to(base_url('super/apartamentos'));
    }

    // ---------------------------------------------------------------
    // Vistas y Handlers de Configuración Root / Super
    // ---------------------------------------------------------------

    public function marcaBlanca()
    {
        $marcaModel = new \App\Models\MarcaModel();
        $marca = $marcaModel->find(1);
        return view('root/marca_blanca', ['marca' => $marca]);
    }

    public function guardarMarcaBlanca()
    {
        try {
            $marcaModel = new \App\Models\MarcaModel();

            // 1. Detección y creación defensiva de columnas faltantes en MySQL (InfinityFree / entornos compartidos)
            try {
                $db = \Config\Database::connect();
                $columnasExistentes = $db->getFieldNames('configuracion_marca');

                if (! in_array('login_bg_url', $columnasExistentes, true)) {
                    $db->query("ALTER TABLE configuracion_marca ADD COLUMN login_bg_url VARCHAR(255) NULL AFTER logo_url");
                }
                if (! in_array('telefono_contacto', $columnasExistentes, true)) {
                    $db->query("ALTER TABLE configuracion_marca ADD COLUMN telefono_contacto VARCHAR(50) NULL AFTER correo_contacto");
                }
                if (! in_array('cedula_pago', $columnasExistentes, true)) {
                    $db->query("ALTER TABLE configuracion_marca ADD COLUMN cedula_pago VARCHAR(20) NULL");
                }
                if (! in_array('telefono_pago', $columnasExistentes, true)) {
                    $db->query("ALTER TABLE configuracion_marca ADD COLUMN telefono_pago VARCHAR(30) NULL");
                }
                if (! in_array('banco_pago', $columnasExistentes, true)) {
                    $db->query("ALTER TABLE configuracion_marca ADD COLUMN banco_pago VARCHAR(100) NULL");
                }
            } catch (\Throwable $t) {
                log_message('warning', '[MarcaBlanca] No se pudieron verificar/crear columnas: ' . $t->getMessage());
            }

            $currentMarca = $marcaModel->find(1) ?? [];

            $data = [
                'nombre_empresa'    => trim((string) $this->request->getPost('nombre_empresa')),
                'correo_contacto'   => trim((string) $this->request->getPost('correo_contacto')),
                'telefono_contacto' => trim((string) $this->request->getPost('telefono_contacto')),
                'cedula_pago'       => trim((string) $this->request->getPost('cedula_pago')),
                'telefono_pago'     => trim((string) $this->request->getPost('telefono_pago')),
                'banco_pago'        => trim((string) $this->request->getPost('banco_pago')),
            ];

            // Mantener color primario solo si fue enviado
            $colorPrimario = $this->request->getPost('color_primario');
            if ($colorPrimario !== null && trim($colorPrimario) !== '') {
                $data['color_primario'] = trim($colorPrimario);
            }

            // 2. Manejo de Logotipo (Eliminar o Subir con FileUploadTrait)
            if ($this->request->getPost('eliminar_logo') == '1') {
                $this->eliminarArchivoSeguro($currentMarca['logo_url'] ?? null);
                $data['logo_url'] = null;
            } else {
                $subidaLogo = $this->subirImagenSegura(
                    $this->request->getFile('logo_condominio'),
                    'branding',
                    $currentMarca['logo_url'] ?? null,
                    2,
                    ['jpg', 'jpeg', 'png', 'webp', 'svg', 'jfif']
                );

                if (is_array($subidaLogo) && isset($subidaLogo['error'])) {
                    return redirect()->to(base_url('super/marca-blanca'))
                        ->with('error', $subidaLogo['error']);
                }

                if ($subidaLogo !== null) {
                    $data['logo_url'] = $subidaLogo;
                }
            }

            // 3. Manejo de Fondo del Login (Eliminar o Subir con FileUploadTrait)
            if ($this->request->getPost('eliminar_login_bg') == '1') {
                $this->eliminarArchivoSeguro($currentMarca['login_bg_url'] ?? null);
                $data['login_bg_url'] = null;
            } else {
                $subidaBg = $this->subirImagenSegura(
                    $this->request->getFile('login_bg'),
                    'branding',
                    $currentMarca['login_bg_url'] ?? null,
                    5,
                    ['jpg', 'jpeg', 'png', 'webp', 'jfif'],
                    'login_bg_'
                );

                if (is_array($subidaBg) && isset($subidaBg['error'])) {
                    return redirect()->to(base_url('super/marca-blanca'))
                        ->with('error', $subidaBg['error']);
                }

                if ($subidaBg !== null) {
                    $data['login_bg_url'] = $subidaBg;
                }
            }

            // 4. Filtrar campos contra columnas reales de la tabla para prevenir excepciones SQL
            try {
                $columnasValidas = $marcaModel->db->getFieldNames('configuracion_marca');
                $data = array_intersect_key($data, array_flip($columnasValidas));
            } catch (\Throwable $t) {
                // Si la introspección falla, continuar con $data
            }

            // 5. Guardar o actualizar registro maestro (ID = 1)
            $existe = $marcaModel->find(1);
            if ($existe) {
                $marcaModel->skipValidation(true)->update(1, $data);
            } else {
                $data['id'] = 1;
                $marcaModel->skipValidation(true)->insert($data);
            }

            log_message('info', '[Auditoría] Usuario ID ' . (session()->get('usuario_id') ?? 'root') . ' actualizó la configuración de marca blanca.');
            return redirect()->to(base_url('super/marca-blanca'))
                ->with('success', 'Identidad de marca actualizada correctamente.');

        } catch (\Throwable $e) {
            log_message('error', '[MarcaBlanca] Error al guardar: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->to(base_url('super/marca-blanca'))
                ->with('error', 'Ocurrió un error al guardar la configuración: ' . $e->getMessage());
        }
    }



    public function planes()
    {
        $marcaModel = new \App\Models\MarcaModel();
        $marca = $marcaModel->first();
        $planActual = $marca['nivel_licencia'] ?? 'Bronce';
        return view('root/planes', ['plan_actual' => $planActual]);
    }

    public function cambiarPlan()
    {
        // TODO: Solicitar cambio de plan
        $nuevoPlan = $this->request->getPost('nuevo_plan');
        return redirect()->to(base_url('super/planes'))->with('success', "Solicitud para el plan $nuevoPlan enviada exitosamente.");
    }



    /**
     * Limpia un número con formato decimal (europeo/latino) a formato de base de datos.
     * Ejemplo: '2.200,50' -> 2200.50
     * Ejemplo: '2.200' -> 2200
     */
    private function sanitizarDecimal($valor): float
    {
        $valor = trim((string)$valor);
        if ($valor === '') {
            return 0.0;
        }

        // Si tiene comas, es formato europeo '2.200,50' o '200,50'
        if (strpos($valor, ',') !== false) {
            $valor = str_replace('.', '', $valor); // Elimina separadores de miles
            $valor = str_replace(',', '.', $valor); // Cambia coma decimal a punto
            return (float) $valor;
        }

        // Si no tiene comas pero tiene puntos
        if (strpos($valor, '.') !== false) {
            // Verificamos si es puramente un formato de miles (ej: '2.200' o '1.000.000')
            if (preg_match('/^-?\d{1,3}(\.\d{3})+$/', $valor)) {
                $valor = str_replace('.', '', $valor);
                return (float) $valor;
            }
            // Si no coincide, asumimos que es un formato inglés válido (ej: '2200.50')
        }

        return (float) $valor;
    }
}
