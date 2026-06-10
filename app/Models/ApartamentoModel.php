<?php

namespace App\Models;

use CodeIgniter\Model;

class ApartamentoModel extends Model
{
    protected $table            = 'apartamentos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'condominio_id',
        'residente_id',
        'nombre_edificio_torre',
        'nro_apartamento',
        'alicuota',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'condominio_id'         => 'required|integer',
        'nombre_edificio_torre' => 'required|max_length[255]',
        'nro_apartamento'       => 'required|max_length[20]',
        'alicuota'              => 'required|decimal',
    ];

    protected $validationMessages = [
        'condominio_id' => [
            'required' => 'Debe asociar el apartamento a un condominio.',
        ],
        'nombre_edificio_torre' => [
            'required' => 'El nombre del edificio o torre es obligatorio.',
        ],
        'nro_apartamento' => [
            'required' => 'El número de apartamento es obligatorio.',
        ],
        'alicuota' => [
            'required' => 'La alícuota es obligatoria.',
            'decimal'  => 'La alícuota debe ser un valor decimal (ej: 0.0325).',
        ],
    ];

    // ---------------------------------------------------------------
    // Métodos de negocio
    // ---------------------------------------------------------------

    /**
     * Lista apartamentos de un condominio específico con datos del residente.
     */
    public function listarPorCondominio(int $condominioId): array
    {
        return $this->select('apartamentos.*, residentes.nombre_completo, residentes.cedula_identidad, residentes.telefono')
            ->join('residentes', 'residentes.id = apartamentos.residente_id', 'left')
            ->where('apartamentos.condominio_id', $condominioId)
            ->orderBy('apartamentos.nombre_edificio_torre', 'ASC')
            ->orderBy('apartamentos.nro_apartamento', 'ASC')
            ->findAll();
    }

    /**
     * Obtiene un apartamento por ID incluyendo datos del condominio y residente.
     */
    public function obtenerDetalle(int $apartamentoId): ?array
    {
        return $this->select('apartamentos.*, condominios.nombre_condominio, condominios.marca_id, residentes.nombre_completo, residentes.cedula_identidad')
            ->join('condominios', 'condominios.id = apartamentos.condominio_id')
            ->join('residentes', 'residentes.id = apartamentos.residente_id', 'left')
            ->where('apartamentos.id', $apartamentoId)
            ->first();
    }

    /**
     * Obtiene los apartamentos asignados a un residente (vía usuario_id).
     * El residente solo debe ver sus propios apartamentos.
     */
    public function obtenerPorUsuario(int $usuarioId): array
    {
        return $this->select('apartamentos.*, condominios.nombre_condominio')
            ->join('residentes', 'residentes.id = apartamentos.residente_id')
            ->join('condominios', 'condominios.id = apartamentos.condominio_id')
            ->where('residentes.usuario_id', $usuarioId)
            ->findAll();
    }

    /**
     * Calcula el monto total de un recibo para este apartamento.
     * Fórmula: monto_total = monto_base * alicuota
     */
    public function calcularMontoTotal(int $apartamentoId, float $montoBase): ?float
    {
        $apartamento = $this->find($apartamentoId);

        if (! $apartamento) {
            return null;
        }

        return round($montoBase * (float) $apartamento['alicuota'], 2);
    }

    /**
     * Cuenta apartamentos registrados en un condominio.
     */
    public function contarPorCondominio(int $condominioId): int
    {
        return $this->where('condominio_id', $condominioId)->countAllResults();
    }

    /**
     * Asigna un residente a un apartamento.
     */
    public function asignarResidente(int $apartamentoId, int $residenteId): bool
    {
        return $this->update($apartamentoId, ['residente_id' => $residenteId]);
    }
}
