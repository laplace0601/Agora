<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Agrega columnas de datos de pago destino a `configuracion_marca`:
 * cedula_pago, telefono_pago, banco_pago.
 */
class AddDatosPagoToConfiguracionMarca extends Migration
{
    public function up()
    {
        $this->forge->addColumn('configuracion_marca', [
            'cedula_pago' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'default'    => null,
                'after'      => 'telefono_contacto',
            ],
            'telefono_pago' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'default'    => null,
                'after'      => 'cedula_pago',
            ],
            'banco_pago' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'telefono_pago',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('configuracion_marca', ['cedula_pago', 'telefono_pago', 'banco_pago']);
    }
}

