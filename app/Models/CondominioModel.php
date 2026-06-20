<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * CondominioModel
 *
 * Tabla: condominios
 * Representa una torre, edificio o conjunto residencial dentro del sistema.
 */
class CondominioModel extends Model
{
    protected $table            = 'condominios';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'nombre_condominio',
        'rif_jurisdiccion',
        'direccion',
        'propietario',             // Nombre del propietario general
        'alicuota_base',           // Alícuota base del condominio
        'marca_id',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'nombre_condominio' => 'required|max_length[255]',
        'rif_jurisdiccion'  => 'permit_empty|max_length[50]',
        'direccion'         => 'permit_empty|max_length[500]',
        'propietario'       => 'permit_empty|max_length[255]',
        'alicuota_base'     => 'permit_empty|decimal',
        'marca_id'          => 'permit_empty|integer',
    ];

    protected $validationMessages = [
        'nombre_condominio' => [
            'required' => 'El nombre del condominio es obligatorio.',
        ],
    ];

    // ---------------------------------------------------------------
    // Métodos de negocio
    // ---------------------------------------------------------------

    /**
     * Lista todos los condominios que pertenecen a una marca específica.
     * Uso: panel del admin para ver solo sus condominios.
     */
    public function listarPorMarca(int $marcaId): array
    {
        return $this->where('marca_id', $marcaId)
                    ->orderBy('nombre_condominio', 'ASC')
                    ->findAll();
    }

    /**
     * Obtiene un condominio verificando que pertenezca a la marca indicada.
     * Previene que un admin acceda a condominios de otra marca.
     */
    public function obtenerDetalleSeguro(int $condominioId, int $marcaId): ?array
    {
        return $this->where('id', $condominioId)
                    ->where('marca_id', $marcaId)
                    ->first();
    }

    /**
     * Cuenta el total de condominios registrados bajo una marca.
     */
    public function contarPorMarca(int $marcaId): int
    {
        return $this->where('marca_id', $marcaId)->countAllResults();
    }
}
