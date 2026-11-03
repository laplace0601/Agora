<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * HabitanteModel
 *
 * Tabla: habitantes
 * Gestiona el censo de personas que habitan cada apartamento.
 * Soporta flujo de aprobación: admin crea con estado 'aprobado',
 * residente solicita con estado 'pendiente' → admin aprueba/rechaza.
 */
class HabitanteModel extends Model
{
    protected $table            = 'habitantes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'apartamento_id',
        'nombre',
        'apellido',
        'cedula_identidad',
        'telefono',
        'tipo_habitante',   // Propietario, Familiar, Inquilino, Otro
        'es_principal',
        'estado',           // aprobado, pendiente, rechazado
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'apartamento_id'   => 'required|integer',
        'nombre'           => 'required|max_length[100]',
        'apellido'         => 'required|max_length[100]',
        'cedula_identidad' => 'permit_empty|max_length[30]',
        'telefono'         => 'permit_empty|max_length[30]',
        'tipo_habitante'   => 'required|in_list[Propietario,Familiar,Inquilino,Otro]',
        'es_principal'     => 'permit_empty|in_list[0,1]',
        'estado'           => 'permit_empty|in_list[aprobado,pendiente,rechazado]',
    ];

    protected $validationMessages = [
        'nombre'  => ['required' => 'El nombre del habitante es obligatorio.'],
        'apellido' => ['required' => 'El apellido del habitante es obligatorio.'],
        'tipo_habitante' => [
            'required' => 'Debe seleccionar un tipo de habitante.',
            'in_list'  => 'El tipo de habitante no es válido.',
        ],
    ];

    // ---------------------------------------------------------------
    // Métodos de negocio
    // ---------------------------------------------------------------

    /**
     * Lista los habitantes APROBADOS de un apartamento, ordenados por es_principal DESC.
     */
    public function listarPorApartamento(int $apartamentoId): array
    {
        return $this->where('apartamento_id', $apartamentoId)
                    ->where('estado', 'aprobado')
                    ->orderBy('es_principal', 'DESC')
                    ->orderBy('nombre', 'ASC')
                    ->findAll();
    }

    /**
     * Lista TODOS los habitantes de un apartamento (incluyendo pendientes y rechazados).
     */
    public function listarTodosPorApartamento(int $apartamentoId): array
    {
        return $this->where('apartamento_id', $apartamentoId)
                    ->orderBy('estado', 'ASC')  // aprobado primero, pendiente después
                    ->orderBy('es_principal', 'DESC')
                    ->orderBy('nombre', 'ASC')
                    ->findAll();
    }

    /**
     * Cuenta los habitantes aprobados de un apartamento.
     */
    public function contarPorApartamento(int $apartamentoId): int
    {
        return $this->where('apartamento_id', $apartamentoId)
                    ->where('estado', 'aprobado')
                    ->countAllResults();
    }

    /**
     * Lista todas las solicitudes pendientes de aprobación (para el panel admin).
     */
    public function listarPendientes(): array
    {
        return $this->select('habitantes.*, apartamentos.nro_apartamento, condominios.nombre_condominio')
                    ->join('apartamentos', 'apartamentos.id = habitantes.apartamento_id')
                    ->join('condominios', 'condominios.id = apartamentos.condominio_id')
                    ->where('habitantes.estado', 'pendiente')
                    ->orderBy('habitantes.created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Cuenta las solicitudes pendientes (para badge en la navbar).
     */
    public function contarPendientes(): int
    {
        return $this->where('estado', 'pendiente')->countAllResults();
    }
}
