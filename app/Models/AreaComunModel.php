<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * AreaComunModel
 *
 * Tabla: areas_comunes
 * Gestiona las áreas comunes / verdes del condominio disponibles para reserva.
 */
class AreaComunModel extends Model
{
    protected $table            = 'areas_comunes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'nombre',
        'descripcion',
        'capacidad_maxima',
        'estado',           // Activo, Mantenimiento, Inactivo
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';  // La migración no tiene updated_at

    protected $validationRules = [
        'nombre' => 'required|max_length[100]',
        'estado' => 'required|in_list[Activo,Mantenimiento,Inactivo]',
    ];

    protected $validationMessages = [
        'nombre' => ['required' => 'El nombre del área común es obligatorio.'],
    ];

    // ---------------------------------------------------------------
    // Métodos de negocio
    // ---------------------------------------------------------------

    /**
     * Lista solo las áreas activas (disponibles para reserva).
     */
    public function listarActivas(): array
    {
        return $this->where('estado', 'Activo')
                    ->orderBy('nombre', 'ASC')
                    ->findAll();
    }

    /**
     * Lista todas las áreas independientemente de su estado.
     */
    public function listarTodas(): array
    {
        return $this->orderBy('nombre', 'ASC')->findAll();
    }
}

