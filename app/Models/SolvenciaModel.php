<?php

namespace App\Models;

use CodeIgniter\Model;

class SolvenciaModel extends Model
{
    protected $table            = 'recibos_solvencia';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'apartamento_id',
        'emitido_por_usuario_id',
        'fecha_emision',
        'hasta_mes',
        'hasta_anio',
        'codigo_verificacion',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'apartamento_id'         => 'required|integer',
        'emitido_por_usuario_id' => 'required|integer',
        'fecha_emision'          => 'required|valid_date',
        'hasta_mes'              => 'required|integer|greater_than[0]|less_than[13]',
        'hasta_anio'             => 'required|integer|greater_than[2000]',
        'codigo_verificacion'    => 'required|max_length[64]',
    ];

    /**
     * Genera un código de verificación único (hash aleatorio SHA-256 truncado).
     */
    public function generarCodigoVerificacion(): string
    {
        return strtoupper(substr(hash('sha256', random_bytes(32)), 0, 16));
    }

    /**
     * Busca una solvencia por su código de verificación.
     * Uso público: cualquier tercero puede validar autenticidad.
     */
    public function buscarPorCodigo(string $codigo): ?array
    {
        return $this->where('codigo_verificacion', $codigo)->first();
    }

    /**
     * Lista las solvencias emitidas para un apartamento.
     */
    public function listarPorApartamento(int $apartamentoId): array
    {
        return $this->where('apartamento_id', $apartamentoId)
                    ->orderBy('fecha_emision', 'DESC')
                    ->findAll();
    }
}
