<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Agrega la columna `telefono_contacto` a la tabla `configuracion_marca`
 * para almacenar el número telefónico de soporte o contacto del condominio.
 */
class AddTelefonoToConfiguracionMarca extends Migration
{
    public function up()
    {
        $this->forge->addColumn('configuracion_marca', [
            'telefono_contacto' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
                'after'      => 'correo_contacto',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('configuracion_marca', 'telefono_contacto');
    }
}

