<?php

namespace App\Models;

use CodeIgniter\Model;

class ReciboModel extends Model
{
    protected $table            = 'recibos_mensuales';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'apartamento_id',
        'mes',
        'anio',
        'monto_base',
        'monto_intereses',
        'monto_total',
        'estado_pago',
        'descripcion',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'apartamento_id' => 'required|integer',
        'mes'            => 'required|integer|greater_than[0]|less_than[13]',
        'anio'           => 'required|integer|greater_than[2000]',
        'monto_base'     => 'required|decimal',
        'monto_total'    => 'required|decimal',
        'estado_pago'    => 'required|in_list[Pendiente,Pagado]',
    ];

    /**
     * Obtiene los recibos de un apartamento específico.
     */
    public function listarPorApartamento(int $apartamentoId): array
    {
        return $this->where('apartamento_id', $apartamentoId)
            ->orderBy('anio', 'DESC')
            ->orderBy('mes', 'DESC')
            ->findAll();
    }

    /**
     * Cuenta recibos pendientes de un apartamento.
     * Usado para determinar morosidad antes de emitir solvencia.
     */
    public function contarPendientes(int $apartamentoId): int
    {
        return $this->where('apartamento_id', $apartamentoId)
            ->where('estado_pago', 'Pendiente')
            ->countAllResults();
    }

    /**
     * Cambia el estado de pago de un recibo.
     */
    public function actualizarEstadoPago(int $reciboId, string $estado): bool
    {
        return $this->update($reciboId, ['estado_pago' => $estado]);
    }

    /**
     * Verifica si ya existe un recibo para un apartamento en un mes/año dado.
     */
    public function existeRecibo(int $apartamentoId, int $mes, int $anio): bool
    {
        return $this->where('apartamento_id', $apartamentoId)
            ->where('mes', $mes)
            ->where('anio', $anio)
            ->countAllResults() > 0;
    }
}
