<?php

namespace App\Models;

use CodeIgniter\Model;

class PagoModel extends Model
{
    protected $table            = 'pagos_recibidos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'recibo_mensual_id',
        'monto_pagado',
        'metodo_pago',
        'referencia_transaccion',
        'comprobante_url',
        'fecha_registro',
        'estado_validacion',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'recibo_mensual_id'      => 'required|integer',
        'monto_pagado'           => 'required|decimal',
        'metodo_pago'            => 'required|max_length[100]',
        'referencia_transaccion' => 'required|max_length[255]',
        'estado_validacion'      => 'required|in_list[Por Validar,Aprobado,Rechazado]',
    ];

    /**
     * Lista los pagos asociados a un recibo mensual.
     */
    public function listarPorRecibo(int $reciboMensualId): array
    {
        return $this->where('recibo_mensual_id', $reciboMensualId)
                    ->orderBy('fecha_registro', 'DESC')
                    ->findAll();
    }

    /**
     * Cambia el estado de validación de un pago.
     */
    public function actualizarValidacion(int $pagoId, string $estado): bool
    {
        return $this->update($pagoId, ['estado_validacion' => $estado]);
    }

    /**
     * Obtiene un pago con los datos del recibo asociado.
     */
    public function obtenerConRecibo(int $pagoId): ?array
    {
        return $this->select('pagos_recibidos.*, recibos_mensuales.apartamento_id, recibos_mensuales.estado_pago')
                    ->join('recibos_mensuales', 'recibos_mensuales.id = pagos_recibidos.recibo_mensual_id')
                    ->where('pagos_recibidos.id', $pagoId)
                    ->first();
    }
}
