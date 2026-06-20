<?php

namespace App\Models;

use CodeIgniter\Model;

class ResidenteModel extends Model
{
    protected $table            = 'residentes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'usuario_id',
        'nombre_completo',
        'cedula_identidad',
        'telefono',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'usuario_id'       => 'permit_empty|integer',
        'nombre_completo'  => 'required|max_length[100]',
        'cedula_identidad' => 'required|max_length[20]|is_unique[residentes.cedula_identidad,id,{id}]',
        'telefono'         => 'permit_empty|max_length[50]',
    ];

    protected $validationMessages = [
        'nombre_completo' => [
            'required' => 'El nombre completo es obligatorio.',
        ],
        'cedula_identidad' => [
            'required'  => 'La cédula de identidad es obligatoria.',
            'is_unique' => 'Esta cédula ya está registrada en el sistema.',
        ],
    ];

    /**
     * Obtiene un residente por su usuario_id.
     */
    public function obtenerPorUsuario(int $usuarioId): ?array
    {
        return $this->where('usuario_id', $usuarioId)->first();
    }
}
