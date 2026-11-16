<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Agrega columna motivo_rechazo a pagos_recibidos para registrar
 * la justificación obligatoria cuando un pago es rechazado por el administrador.
 */
class AddMotivoRechazoToPagosRecibidos extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('motivo_rechazo', 'pagos_recibidos')) {
            $this->forge->addColumn('pagos_recibidos', [
                'motivo_rechazo' => [
                    'type'       => 'TEXT',
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'estado_validacion',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('motivo_rechazo', 'pagos_recibidos')) {
            $this->forge->dropColumn('pagos_recibidos', 'motivo_rechazo');
        }
    }
}
