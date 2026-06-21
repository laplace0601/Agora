<?php

namespace App\Controllers;

use App\Models\CondominioModel;
use App\Models\ApartamentoModel;
use App\Models\ComunicadoModel;
use App\Models\PagoModel;
use App\Models\ReciboModel;
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
     * GET /admin/apartamentos
     *
     * Vista de gestión inmobiliaria: condominios y apartamentos.
     * Pasa los condominios registrados al select del formulario.
     */
    public function apartamentos()
    {
        $condominioModel = new CondominioModel();

        $datos = [
            'condominios' => $condominioModel->findAll(),
        ];

        return view('admin/apartamentos', $datos);
    }

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

        return view('admin/finazas_cobro', $datos);
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
    // Handlers POST — Formularios de las vistas admin
    // ---------------------------------------------------------------

    /**
     * POST /admin/soporte/validar
     *
     * Actualiza el estado de un ticket.
     */
    public function validarTicket()
    {
        $ticketId = (int) $this->request->getPost('ticket_id');
        $estado = trim($this->request->getPost('estado') ?? '');

        if ($ticketId <= 0 || !in_array($estado, ['Abierto', 'En Proceso', 'Resuelto'])) {
            return redirect()->back()->with('error', 'Datos inválidos para actualizar el ticket.');
        }

        $ticketModel = new \App\Models\TicketModel();
        $ticketModel->actualizarEstado($ticketId, $estado);

        return redirect()->to(site_url('admin/soporte'))->with('success', 'El estado del ticket ha sido actualizado a: ' . $estado);
    }

    /**
     * POST /admin/apartamentos/registrar-condominio
     *
     * Registra un nuevo condominio desde el modal de la vista apartamentos.
     */
    public function registrarCondominio()
    {
        $condominioModel = new CondominioModel();

        $datos = [
            'nombre_condominio'  => trim($this->request->getPost('nombre_condo') ?? ''),
            'direccion'          => trim($this->request->getPost('direccion_condo') ?? ''),
            'propietario'        => trim($this->request->getPost('propietario_condo') ?? ''),
            'alicuota_base'      => (float) $this->request->getPost('alicuota_condo'),
        ];

        if (! $condominioModel->insert($datos)) {
            return redirect()->back()
                             ->with('error', 'Error al registrar el condominio: ' . implode(', ', $condominioModel->errors()));
        }

        return redirect()->to(site_url('admin/apartamentos'))
                         ->with('success', 'Condominio registrado exitosamente.');
    }

    /**
     * POST /admin/apartamentos/registrar-apartamento
     *
     * Registra un apartamento desde el modal de la vista apartamentos.
     */
    public function registrarApartamento()
    {
        $apartamentoModel = new ApartamentoModel();
        $residenteModel   = new ResidenteModel();

        // 1. Crear el residente (usamos uniqid para la cédula ya que el form solo pide nombre)
        $nombrePropietario = trim($this->request->getPost('propietario_apto') ?? 'Sin Asignar');
        $residenteId = null;
        
        if ($nombrePropietario !== '') {
            $residenteId = $residenteModel->insert([
                'nombre_completo'  => $nombrePropietario,
                'cedula_identidad' => uniqid('CI-'),
                'telefono'         => '',
            ]);
        }

        // 2. Registrar el apartamento vinculado al residente
        $datos = [
            'condominio_id'         => (int) $this->request->getPost('condominio_id'),
            'residente_id'          => $residenteId ? (int) $residenteId : null,
            'nombre_edificio_torre' => trim($this->request->getPost('direccion_apto') ?? ''),
            'nro_apartamento'       => trim($this->request->getPost('numero_apto') ?? ''),
            'alicuota'              => (float) $this->request->getPost('alicuota_apto'),
        ];

        if (! $apartamentoModel->insert($datos)) {
            return redirect()->back()
                             ->with('error', 'Error al registrar el apartamento: ' . implode(', ', $apartamentoModel->errors()));
        }

        return redirect()->to(site_url('admin/apartamentos'))
                         ->with('success', 'Apartamento registrado exitosamente.');
    }

    /**
     * POST /admin/cartelera/publicar
     *
     * Publica un nuevo comunicado en la cartelera desde la BD.
     */
    public function publicarAnuncio()
    {
        $comunicadoModel = new ComunicadoModel();

        $datos = [
            'autor_id'          => session()->get('usuario_id'),
            'titulo'            => trim($this->request->getPost('titulo') ?? ''),
            'contenido'         => trim($this->request->getPost('descripcion') ?? ''),
            'fecha_publicacion' => date('Y-m-d H:i:s'),
            'estado'            => 'publicado',
        ];

        if (empty($datos['titulo']) || empty($datos['contenido'])) {
            return redirect()->back()->with('error', 'El título y la descripción son obligatorios.');
        }

        $comunicadoModel->insert($datos);

        return redirect()->to(site_url('admin/cartelera'))
                         ->with('success', 'Anuncio publicado exitosamente.');
    }

    /**
     * GET /admin/cartelera/eliminar/(:num)
     *
     * Cambia el estado de un comunicado a 'borrado' (soft delete).
     */
    public function eliminarAnuncio(int $id)
    {
        $comunicadoModel = new ComunicadoModel();
        $comunicadoModel->update($id, ['estado' => 'borrado']);

        return redirect()->to(site_url('admin/cartelera'))
                         ->with('success', 'Anuncio eliminado.');
    }

    /**
     * POST /admin/finanzas/facturar
     *
     * Emite recibos masivos para un condominio (facturación mensual).
     */
    public function emitirRecibos()
    {
        $condominioId = (int) $this->request->getPost('condominio_id');
        $montoBase    = (float) $this->request->getPost('monto_base');
        $mes          = (int) $this->request->getPost('mes');
        $anio         = (int) $this->request->getPost('año');

        if ($condominioId <= 0 || $montoBase <= 0 || $mes < 1 || $mes > 12 || $anio < 2000) {
            return redirect()->back()->with('error', 'Complete todos los campos correctamente.');
        }

        $apartamentoModel = new ApartamentoModel();
        $apartamentos     = $apartamentoModel->where('condominio_id', $condominioId)->findAll();

        if (empty($apartamentos)) {
            return redirect()->back()->with('error', 'No hay apartamentos registrados para este condominio.');
        }

        $reciboModel  = new ReciboModel();
        $insertados   = 0;
        $yaExistentes = 0;

        foreach ($apartamentos as $apto) {
            if ($reciboModel->existeRecibo((int) $apto['id'], $mes, $anio)) {
                $yaExistentes++;
                continue;
            }

            $reciboModel->insert([
                'apartamento_id'  => $apto['id'],
                'mes'             => $mes,
                'anio'            => $anio,
                'monto_base'      => $montoBase,
                'monto_intereses' => 0,
                'monto_total'     => round($montoBase * (float) $apto['alicuota'], 2),
                'estado_pago'     => 'Pendiente',
            ]);

            $insertados++;
        }

        $msg = "Facturación completada: {$insertados} recibos emitidos.";
        if ($yaExistentes > 0) {
            $msg .= " ({$yaExistentes} ya existían y fueron omitidos).";
        }

        return redirect()->to(site_url('admin/finanzas/cobro'))->with('success', $msg);
    }

    /**
     * POST /admin/finanzas/validar-pago
     *
     * Aprueba o rechaza un comprobante de pago enviado por un residente.
     */
    public function validarPago()
    {
        $pagoId = (int) $this->request->getPost('pago_id');
        $accion = trim($this->request->getPost('accion') ?? '');

        if ($pagoId <= 0 || ! in_array($accion, ['aprobar', 'rechazar'], true)) {
            return redirect()->back()->with('error', 'Acción inválida.');
        }

        $pagoModel = new PagoModel();
        $pago      = $pagoModel->obtenerConRecibo($pagoId);

        if (! $pago) {
            return redirect()->back()->with('error', 'El pago no existe.');
        }

        if ($accion === 'aprobar') {
            $pagoModel->actualizarValidacion($pagoId, 'Aprobado');
            $reciboModel = new ReciboModel();
            $reciboModel->actualizarEstadoPago((int) $pago['recibo_mensual_id'], 'Pagado');
            return redirect()->to(site_url('admin/finanzas/pagos'))
                             ->with('success', "Pago #{$pagoId} aprobado. Recibo marcado como Pagado.");
        }

        $pagoModel->actualizarValidacion($pagoId, 'Rechazado');
        return redirect()->to(site_url('admin/finanzas/pagos'))
                         ->with('success', "Pago #{$pagoId} rechazado.");
    }
}
