<?php

namespace App\Controllers;

use App\Models\PagoModel;
use App\Models\CondominioModel;
use App\Models\ApartamentoModel;
use App\Models\ComunicadoModel;
use App\Models\ResidenteModel;
use App\Models\ReciboModel;
use App\Models\HabitanteModel;

/**
 * AdminController
 *
 * Gestiona todas las vistas del panel de administración (rol: admin).
 * Cada método GET carga una vista; los métodos POST procesan formularios
 * y redirigen con mensajes flash.
 */
class AdminController extends BaseController
{
    // ---------------------------------------------------------------
    // Vistas principales del panel admin
    // ---------------------------------------------------------------

    /**
     * GET /admin/residentes
     *
     * Vista del Directorio de Departamentos (reemplaza el antiguo directorio de residentes).
     * Muestra todos los apartamentos con su residente principal y estado de deuda.
     */
    public function residentes()
    {
        $apartamentoModel = new ApartamentoModel();
        $reciboModel      = new ReciboModel();

        // Obtener todos los apartamentos con datos del condominio y residente
        $apartamentos = $apartamentoModel
            ->select('apartamentos.*, condominios.nombre_condominio, residentes.nombre_completo, residentes.cedula_identidad, residentes.telefono')
            ->join('condominios', 'condominios.id = apartamentos.condominio_id')
            ->join('residentes', 'residentes.id = apartamentos.residente_id', 'left')
            ->orderBy('condominios.nombre_condominio', 'ASC')
            ->orderBy('apartamentos.nro_apartamento', 'ASC')
            ->findAll();

        // Calcular deuda por apartamento
        foreach ($apartamentos as &$apto) {
            $pendientes = $reciboModel
                ->where('apartamento_id', $apto['id'])
                ->where('estado_pago', 'Pendiente')
                ->findAll();

            $deuda = 0;
            foreach ($pendientes as $r) {
                $deuda += (float) $r['monto_total'];
            }
            $apto['deuda_total'] = $deuda;
            $apto['recibos_pendientes'] = count($pendientes);
        }

        return view('admin/directorio_departamentos', ['apartamentos' => $apartamentos]);
    }

    /**
     * GET /admin/apartamento/(:num)/detalle
     *
     * API JSON: Retorna detalle de un apartamento con habitantes y estado de cuenta.
     */
    public function detalleApartamento(int $apartamentoId)
    {
        $apartamentoModel = new ApartamentoModel();
        $habitanteModel   = new HabitanteModel();
        $reciboModel      = new ReciboModel();

        $apartamento = $apartamentoModel
            ->select('apartamentos.*, condominios.nombre_condominio, residentes.nombre_completo, residentes.cedula_identidad, residentes.telefono')
            ->join('condominios', 'condominios.id = apartamentos.condominio_id')
            ->join('residentes', 'residentes.id = apartamentos.residente_id', 'left')
            ->where('apartamentos.id', $apartamentoId)
            ->first();

        if (!$apartamento) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Apartamento no encontrado.'])->setStatusCode(404);
        }

        // Habitantes (censo) — Admin ve TODOS los estados (aprobados, pendientes, rechazados)
        $habitantes = $habitanteModel->listarTodosPorApartamento($apartamentoId);

        // Recibos pendientes (deudas)
        $recibosPendientes = $reciboModel
            ->where('apartamento_id', $apartamentoId)
            ->where('estado_pago', 'Pendiente')
            ->orderBy('anio', 'DESC')
            ->orderBy('mes', 'DESC')
            ->findAll();

        $deudaTotal = 0;
        foreach ($recibosPendientes as $r) {
            $deudaTotal += (float) $r['monto_total'];
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => [
                'apartamento'       => $apartamento,
                'habitantes'        => $habitantes,
                'recibos_pendientes' => $recibosPendientes,
                'deuda_total'       => $deudaTotal,
            ],
        ]);
    }

    /**
     * GET /admin/finanzas
     *
     * Redirige al submódulo de cobro (facturación masiva) como entrada principal.
     */
    public function finanzas()
    {
        return redirect()->to(site_url('admin/finanzas/cobro'));
    }

    /**
     * GET /admin/finanzas/cobro
     *
     * Vista para emitir facturación masiva mensual.
     */
    public function finanzasCobro()
    {
        $condominioModel = new CondominioModel();

        $datos = [
            'condominios'  => $condominioModel->findAll(),
            'anio_actual'  => (int) date('Y'),
            'mes_actual'   => (int) date('n'),
        ];

        return view('admin/finanzas_cobro', $datos);
    }

    /**
     * GET /admin/finanzas/pagos
     *
     * Vista para validar comprobantes de pago de residentes.
     */
    public function finanzasPagos()
    {
        $pagoModel = new PagoModel();

        // Pagos pendientes de validación con datos del recibo, apartamento y residente
        $pagos = $pagoModel
            ->select('pagos_recibidos.*, recibos_mensuales.mes, recibos_mensuales.anio, recibos_mensuales.monto_total, apartamentos.nro_apartamento, residentes.nombre_completo')
            ->join('recibos_mensuales', 'recibos_mensuales.id = pagos_recibidos.recibo_mensual_id')
            ->join('apartamentos', 'apartamentos.id = recibos_mensuales.apartamento_id')
            ->join('residentes', 'residentes.id = apartamentos.residente_id', 'left')
            ->where('pagos_recibidos.estado_validacion', 'Por Validar')
            ->orderBy('pagos_recibidos.fecha_registro', 'DESC')
            ->findAll();

        return view('admin/finanzas_pago', ['pagos' => $pagos]);
    }

    /**
     * GET /admin/finanzas/morosidad
     *
     * Panel de control de morosidad: recibos pendientes organizados por mes/año.
     */
    public function morosidad()
    {
        $reciboModel      = new ReciboModel();
        $apartamentoModel = new ApartamentoModel();

        // Filtros opcionales por mes y año
        $filtroMes  = $this->request->getGet('mes')  ? (int) $this->request->getGet('mes')  : null;
        $filtroAnio = $this->request->getGet('anio') ? (int) $this->request->getGet('anio') : (int) date('Y');

        // Consulta base: recibos pendientes con datos de apartamento y residente
        $builder = $reciboModel
            ->select('recibos_mensuales.*, apartamentos.nro_apartamento, apartamentos.nombre_edificio_torre, residentes.nombre_completo, residentes.telefono')
            ->join('apartamentos', 'apartamentos.id = recibos_mensuales.apartamento_id')
            ->join('residentes', 'residentes.id = apartamentos.residente_id', 'left')
            ->where('recibos_mensuales.estado_pago', 'Pendiente');

        if ($filtroAnio) {
            $builder->where('recibos_mensuales.anio', $filtroAnio);
        }
        if ($filtroMes) {
            $builder->where('recibos_mensuales.mes', $filtroMes);
        }

        $morosos = $builder
            ->orderBy('recibos_mensuales.anio', 'DESC')
            ->orderBy('recibos_mensuales.mes', 'DESC')
            ->orderBy('apartamentos.nro_apartamento', 'ASC')
            ->findAll();

        // Totales
        $totalDeuda = 0;
        foreach ($morosos as $m) {
            $totalDeuda += (float) $m['monto_total'];
        }

        return view('admin/morosidad', [
            'morosos'     => $morosos,
            'total_deuda' => $totalDeuda,
            'filtro_mes'  => $filtroMes,
            'filtro_anio' => $filtroAnio,
            'anio_actual' => (int) date('Y'),
            'mes_actual'  => (int) date('n'),
        ]);
    }

    /**
     * GET /admin/cartelera
     *
     * Vista del tablón de anuncios (comunicados desde BD).
     */
    public function cartelera()
    {
        $comunicadoModel = new ComunicadoModel();

        $datos = [
            'anuncios_activos'    => $comunicadoModel->listarActivos(),
            'historial_anuncios'  => $comunicadoModel->findAll(),
        ];

        return view('admin/cartelera', $datos);
    }

    /**
     * GET /admin/comunidad
     *
     * Alias de cartelera — mantiene compatibilidad con la ruta anterior.
     */
    public function comunidad()
    {
        return $this->cartelera();
    }

    /**
     * GET /admin/soporte
     *
     * Vista para ver los tickets de soporte y validarlos.
     */
    public function soporte()
    {
        $ticketModel = new \App\Models\TicketModel();

        $tickets = $ticketModel
            ->select('tickets_soporte.*, residentes.nombre_completo, residentes.telefono, usuarios.correo')
            ->join('usuarios', 'usuarios.id = tickets_soporte.usuario_id')
            ->join('residentes', 'residentes.usuario_id = usuarios.id', 'left')
            ->orderBy('tickets_soporte.fecha_creacion', 'DESC')
            ->findAll();

        return view('admin/soporte', ['tickets' => $tickets]);
    }

    // ---------------------------------------------------------------
    // Handlers POST — DELEGADOS a controladores especializados
    // • POST admin/finanzas/facturar    → FinanzasController::emitirRecibos
    // • POST admin/finanzas/validar-pago → FinanzasController::validarPago
    // • POST admin/cartelera/publicar    → ComunidadController::crearComunicado
    // • POST admin/soporte/validar       → ComunidadController::responderTicket
    // Ver: app/Config/Routes.php — grupo 'admin'
    // ---------------------------------------------------------------

    /**
     * GET /admin/cartelera/eliminar/(:num)
     *
     * Cambia el estado de un comunicado a 'borrado' (soft delete).
     * Permanece aquí por ser una acción GET del panel, sin equivalente en CRM.
     */
    public function eliminarAnuncio(int $id)
    {
        $comunicadoModel = new ComunicadoModel();
        $comunicadoModel->update($id, ['estado' => 'borrado']);

        return redirect()->to(site_url('admin/cartelera'))
            ->with('success', 'Anuncio eliminado.');
    }
}
