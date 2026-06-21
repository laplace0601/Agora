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

        $datos = [
            'condominios' => $condominioModel->findAll(),
        ];

        return view('root/apartamentos', $datos);
    }

    /**
     * POST /super/apartamentos/registrar-condominio
     */
    public function registrarCondominio()
    {
        $condominioModel = new \App\Models\CondominioModel();

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

        return redirect()->to(site_url('super/apartamentos'))
                         ->with('success', 'Condominio registrado exitosamente.');
    }

    /**
     * POST /super/apartamentos/registrar-apartamento
     */
    public function registrarApartamento()
    {
        $apartamentoModel = new \App\Models\ApartamentoModel();
        $residenteModel   = new \App\Models\ResidenteModel();

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

        return redirect()->to(site_url('super/apartamentos'))
                         ->with('success', 'Apartamento registrado exitosamente.');
    }
}
