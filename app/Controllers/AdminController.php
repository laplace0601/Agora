<?php

namespace App\Controllers;

use App\Models\PagoModel;
use App\Models\CondominioModel;
use App\Models\ApartamentoModel;
use App\Models\ComunicadoModel;
use App\Models\ResidenteModel;

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
     * Vista del directorio de residentes para consultar y buscar.
     */
    public function residentes()
    {
        $residenteModel = new ResidenteModel();

        $residentes = $residenteModel
            ->select('residentes.*, usuarios.correo, usuarios.estado')
            ->join('usuarios', 'usuarios.id = residentes.usuario_id', 'left')
            ->findAll();

        $apartamentoModel = new ApartamentoModel();
        $apartamentos = $apartamentoModel->findAll();

        $aptosPorResidente = [];
        foreach ($apartamentos as $apto) {
            if ($apto['residente_id']) {
                $aptosPorResidente[$apto['residente_id']][] = $apto['nombre_edificio_torre'] . ' - ' . $apto['nro_apartamento'];
            }
        }

        foreach ($residentes as &$res) {
            $res['apartamentos'] = $aptosPorResidente[$res['id']] ?? [];
        }

        return view('admin/residentes', ['residentes' => $residentes]);
    }

    /**
     * GET /admin/finanzas
     *
     * Redirige al submódulo de cobro (facturación masiva) como entrada principal.
     * La vista admin/finanzas.php no existe como página independiente;
     * los submódulos son: /admin/finanzas/cobro y /admin/finanzas/pagos
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
