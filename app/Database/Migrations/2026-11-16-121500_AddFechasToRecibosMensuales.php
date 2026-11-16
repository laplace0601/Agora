<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Agrega columnas fecha_emision y fecha_pago a recibos_mensuales
 * para evitar el error de timestamp nulo (1970-01-01).
 */
class AddFechasToRecibosMensuales extends Migration
{
    public function up()
    {
        $fields = [];

        if (! $this->db->fieldExists('fecha_emision', 'recibos_mensuales')) {
            $fields['fecha_emision'] = [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'after'   => 'estado_pago',
            ];
        }

        if (! $this->db->fieldExists('fecha_pago', 'recibos_mensuales')) {
            $fields['fecha_pago'] = [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'after'   => 'fecha_emision',
            ];
        }

        if (! empty($fields)) {
            $this->forge->addColumn('recibos_mensuales', $fields);
        }
    }

    public function down()
    {
        $columns = [];
        if ($this->db->fieldExists('fecha_pago', 'recibos_mensuales')) {
            $columns[] = 'fecha_pago';
        }
        if ($this->db->fieldExists('fecha_emision', 'recibos_mensuales')) {
            $columns[] = 'fecha_emision';
        }
        if (! empty($columns)) {
            $this->forge->dropColumn('recibos_mensuales', $columns);
        }
    }
}
