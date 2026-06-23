<?php

namespace App\Controllers;

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
            return redirect()->back()->with('error', 'El nombre del condominio es obligatorio.');
        }

        $metrosFloat = (float) $metros;
        if ($metrosFloat <= 0) {
            return redirect()->back()->with('error', 'El total de metros cuadrados debe ser mayor a 0.');
        }

        $datos = [
            'nombre_condominio'      => $nombre,
            'rif_jurisdiccion'       => $rif,
            'total_metros_cuadrados' => $metrosFloat,
            'marca_id'               => 1,
        ];

        if (! $condominioModel->insert($datos)) {
            $errores = implode(', ', $condominioModel->errors());
            return redirect()->back()->with('error', 'Error al registrar: ' . $errores);
        }

        return redirect()->to(site_url('super/apartamentos'))
                         ->with('success', 'Condominio registrado exitosamente.');
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
        $nroApartamento = trim($this->request->getPost('numero_apto') ?? '');
        $metrosApto    = (float) $this->request->getPost('metros_cuadrados_apto');

        // Validaciones básicas
        if ($condominioId <= 0) {
            return redirect()->back()->with('error', 'Debe seleccionar un condominio válido.');
        }
        if (empty($nroApartamento)) {
            return redirect()->back()->with('error', 'El número de apartamento es obligatorio.');
        }
        if ($metrosApto <= 0) {
            return redirect()->back()->with('error', 'Los metros cuadrados del apartamento deben ser mayor a 0.');
        }

        // Obtener el condominio para calcular la alícuota proporcional
        $condominio = $condominioModel->find($condominioId);
        if (! $condominio) {
            return redirect()->back()->with('error', 'El condominio seleccionado no existe.');
        }

        $totalMetros = (float) ($condominio['total_metros_cuadrados'] ?? 0);
        if ($totalMetros <= 0) {
            return redirect()->back()->with('error', 'El condominio no tiene metros cuadrados registrados. Edita el condominio primero.');
        }

        // Alícuota = (m² del apartamento / m² totales del condominio) × 100
        $alicuota = round(($metrosApto / $totalMetros) * 100, 4);

        $datos = [
            'condominio_id'         => $condominioId,
            'residente_id'          => null, // Se asigna cuando se vincula un usuario residente
            'nombre_edificio_torre' => $condominio['nombre_condominio'], // Nombre del condominio como referencia
            'nro_apartamento'       => $nroApartamento,
            'metros_cuadrados'      => $metrosApto,
            'alicuota'              => $alicuota,
        ];

        if (! $apartamentoModel->insert($datos)) {
            $errores = implode(', ', $apartamentoModel->errors());
            return redirect()->back()->with('error', 'Error al registrar el apartamento: ' . $errores);
        }

        return redirect()->to(site_url('super/apartamentos'))
                         ->with('success', "Apartamento {$nroApartamento} registrado. Alícuota calculada: {$alicuota}%");
    }

    // ---------------------------------------------------------------
    // Vistas y Handlers de Configuración Root / Super
    // ---------------------------------------------------------------

    public function crearUsuario()
    {
        $condominioModel = new \App\Models\CondominioModel();
        $apartamentoModel = new \App\Models\ApartamentoModel();

        $datos = [
            'condominios'  => $condominioModel->findAll(),
            'apartamentos' => $apartamentoModel->findAll(),
        ];

        return view('root/crear_usuario', $datos);
    }

    public function guardarResidente()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $usuarioModel = new \App\Models\UsuarioModel();
        $residenteModel = new \App\Models\ResidenteModel();
        $apartamentoModel = new \App\Models\ApartamentoModel();

        $nombre = trim($this->request->getPost('nombre') ?? '');
        $cedula = trim($this->request->getPost('cedula') ?? '');
        $telefono = trim($this->request->getPost('telefono') ?? '');
        $correo = trim($this->request->getPost('correo') ?? '');
        $apartamentoId = $this->request->getPost('apartamento_id');

        if (empty($nombre) || empty($cedula) || empty($correo) || empty($apartamentoId)) {
            return redirect()->back()->with('error', 'Faltan campos obligatorios para registrar el residente.');
        }

        // 1. Crear usuario
        $datosUsuario = [
            'correo' => $correo,
            'nombre_usuario' => strtolower(explode(' ', $nombre)[0] . rand(10, 99)),
            'clave' => '123456',
            'rol' => 'residente',
            'estado' => 'activo',
        ];

        if (!$usuarioModel->insert($datosUsuario)) {
            $errores = implode(', ', $usuarioModel->errors());
            return redirect()->back()->with('error', 'Error al crear usuario: ' . $errores);
        }
        $usuarioId = $usuarioModel->getInsertID();

        // 2. Crear residente
        $datosResidente = [
            'usuario_id' => $usuarioId,
            'nombre_completo' => $nombre,
            'cedula_identidad' => $cedula,
            'telefono' => $telefono,
        ];

        if (!$residenteModel->insert($datosResidente)) {
            $db->transRollback();
            $errores = implode(', ', $residenteModel->errors());
            return redirect()->back()->with('error', 'Error al crear residente: ' . $errores);
        }
        $residenteId = $residenteModel->getInsertID();

        // 3. Asignar apartamento
        if (!$apartamentoModel->asignarResidente($apartamentoId, $residenteId)) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Error al asignar el apartamento.');
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Ocurrió un error inesperado al procesar la solicitud.');
        }

        return redirect()->to(site_url('super/crear-usuario'))->with('success', 'Residente dado de alta y asignado al apartamento exitosamente.');
    }

    public function guardarAdmin()
    {
        // TODO: Lógica real de inserción de usuario y administrador/condominio
        return redirect()->to(site_url('super/crear-usuario'))->with('success', 'Administrador dado de alta exitosamente.');
    }

    public function guardarSuper()
    {
        // TODO: Lógica real de inserción de usuario root
        return redirect()->to(site_url('super/crear-usuario'))->with('success', 'Nuevo Súper Usuario dado de alta exitosamente.');
    }

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

    public function usuarios()
    {
        return view('root/gestion_usuarios');
    }
}
