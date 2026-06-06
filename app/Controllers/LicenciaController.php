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
}
