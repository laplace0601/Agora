<?php

namespace App\Controllers;

use App\Models\PagoModel;

class SuperController extends BaseController
{
    public function panel()
    {
        return view('super/welcome_message');
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
            return $this->response->setJSON([
                'status' => 'error',
                'error' => 'El nombre del condominio es obligatorio.',
                'csrf' => csrf_hash()
            ]);
        }

        $metrosFloat = $this->sanitizarDecimal($metros);
        if ($metrosFloat <= 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'error' => 'El total de metros cuadrados debe ser mayor a 0.',
                'csrf' => csrf_hash()
            ]);
        }

        $datos = [
            'nombre_condominio'      => $nombre,
            'rif_jurisdiccion'       => $rif,
            'total_metros_cuadrados' => $metrosFloat,
            'marca_id'               => 1,
        ];

        if (! $condominioModel->insert($datos)) {
            $errores = implode(', ', $condominioModel->errors());
            return $this->response->setJSON([
                'status' => 'error',
                'error' => 'Error al registrar: ' . $errores,
                'csrf' => csrf_hash()
            ]);
        }

        session()->setFlashdata('success', 'Condominio registrado exitosamente.');

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Condominio registrado exitosamente.',
            'csrf' => csrf_hash()
        ]);
    }

    /**
     * POST /super/apartamentos/condominios/delete/(:num)
     */
    public function deleteCondominio($id = null)
    {
        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'error' => 'ID requerido.']);
        }

        $condominioModel = new \App\Models\CondominioModel();

        if ($condominioModel->delete($id)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Condominio eliminado correctamente.']);
        }

        return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'error' => 'Error al eliminar el condominio.']);
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

        // Helper: respuesta JSON de error unificada
        $jsonError = function (string $mensaje, int $code = 400, string $tipo = 'error') {
            return $this->response->setStatusCode($code)->setJSON([
                'status'     => 'error',
                'error_type' => $tipo,
                'error'      => $mensaje,
                'csrf'       => csrf_hash()
            ]);
        };

        // Validaciones básicas
        if ($condominioId <= 0) {
            return $jsonError('Debe seleccionar un condominio válido.');
        }
        if ($numeroInicial <= 0 || $cantidad <= 0) {
            return $jsonError('El número inicial y la cantidad deben ser mayores a 0.');
        }
        if ($metrosApto <= 0) {
            return $jsonError('Los metros cuadrados por apartamento deben ser mayor a 0.');
        }

        // Obtener el condominio
        $condominio = $condominioModel->find($condominioId);
        if (! $condominio) {
            return $jsonError('El condominio seleccionado no existe.');
        }

        $totalMetrosCondominio = (float) ($condominio['total_metros_cuadrados'] ?? 0);
        if ($totalMetrosCondominio <= 0) {
            return $jsonError('El condominio no tiene metros cuadrados registrados.');
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
            return $jsonError(
                "Tu plan \u00abNivel: {$nivelPlan}\u00bb permite un m\u00e1ximo de {$limitePlan} apartamentos en total. "
                    . "Ya tienes {$totalGlobal} registrados e intentas crear {$cantidad} m\u00e1s. "
                    . "Puedes agregar como m\u00e1ximo {$disponibles} apartamento(s).",
                422,
                'plan_limit'
            );
        }
        // ──────────────────────────────────────────────────────────────────────

        // Validación 1: Espacio (metraje dentro del condominio)
        $totalMetrosNuevos    = $metrosApto * $cantidad;
        $apartamentosActuales = $apartamentoModel->where('condominio_id', $condominioId)->findAll();
        $metrosRegistrados    = array_reduce($apartamentosActuales, fn($c, $a) => $c + (float)$a['metros_cuadrados'], 0.0);

        if (($metrosRegistrados + $totalMetrosNuevos) > $totalMetrosCondominio) {
            return $jsonError(
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
            return $jsonError("Los siguientes apartamentos ya est\u00e1n registrados en este condominio: {$lista}");
        }

        // Inserción masiva dentro de una transacción
        // $db ya fue instanciado arriba.
        $db->transStart();
        $apartamentoModel->insertBatch($nuevosApartamentos);
        (new \App\Services\CondominioService())->recalcularAlicuotas($condominioId);
        $db->transComplete();

        if ($db->transStatus() === false) {
            return $jsonError('Error interno al guardar los apartamentos.', 500);
        }

        session()->setFlashdata('success', "Se han registrado exitosamente {$cantidad} apartamentos.");

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => "Se han registrado exitosamente {$cantidad} apartamentos.",
            'csrf'    => csrf_hash()
        ]);
    }

    // ---------------------------------------------------------------
    // Vistas y Handlers de Configuración Root / Super
    // ---------------------------------------------------------------

    public function marcaBlanca()
    {
        return view('root/marca_blanca');
    }

    public function guardarMarcaBlanca()
    {
        // TODO: Guardar logo y colores
        return redirect()->to(site_url('super/marca-blanca'))->with('success', 'Identidad de marca actualizada.');
    }



    public function planes()
    {
        return view('root/planes');
    }

    public function cambiarPlan()
    {
        // TODO: Solicitar cambio de plan
        $nuevoPlan = $this->request->getPost('nuevo_plan');
        return redirect()->to(site_url('super/planes'))->with('success', "Solicitud para el plan $nuevoPlan enviada exitosamente.");
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
