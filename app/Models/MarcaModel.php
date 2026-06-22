<?php

namespace App\Models;

use CodeIgniter\Model;

class MarcaModel extends Model
{
    protected $table            = 'configuracion_marca';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'nombre_empresa',
        'logo_url',
        'color_primario',
        'correo_contacto',
        'nivel_licencia',
        'limite_apartamentos',
        'codigo_activacion',
        'fecha_actualizacion_plan',
    ];

    protected $useTimestamps = false;

    // Reglas de validación para inserción y actualización
    protected $validationRules = [
        'nombre_empresa'      => 'required|max_length[255]',
        'correo_contacto'     => 'required|valid_email|max_length[255]',
        'nivel_licencia'      => 'permit_empty|max_length[50]',
        'limite_apartamentos' => 'permit_empty|integer',
    ];

    protected $validationMessages = [
        'nombre_empresa' => [
            'required'   => 'El nombre de la empresa es obligatorio.',
        ],
        'correo_contacto' => [
            'required'    => 'El correo de contacto es obligatorio.',
            'valid_email' => 'Debe ingresar un correo electrónico válido.',
        ],
    ];

    /**
     * Obtiene una marca por su ID con validación de existencia.
     */
    public function obtenerMarcaPorId(int $id): ?array
    {
        return $this->find($id);
    }

    /**
     * Lista todas las marcas registradas (uso exclusivo del rol root).
     */
    public function listarMarcas(): array
    {
        return $this->orderBy('nombre_empresa', 'ASC')->findAll();
    }

    /**
     * Verifica si la marca ha alcanzado su límite de apartamentos contratados.
     * Retorna true si aún hay cupo disponible.
     */
    public function tieneCupoDisponible(int $marcaId): bool
    {
        $marca = $this->find($marcaId);

        if (! $marca || (int) $marca['limite_apartamentos'] <= 0) {
            return false;
        }

        $db = \Config\Database::connect();
        $totalApartamentos = $db->table('apartamentos')
            ->join('condominios', 'condominios.id = apartamentos.condominio_id')
            ->where('condominios.marca_id', $marcaId)
            ->countAllResults();

        return $totalApartamentos < (int) $marca['limite_apartamentos'];
    }
}
