<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Agrega el campo 'total_metros_cuadrados' a la tabla condominios.
 * Este campo es obligatorio para el cálculo de alícuotas proporcionales.
 */
class AgregarMetrosCuadradosCondominio extends Migration
{
    public function up()
    {
        $this->forge->addColumn('condominios', [
            'total_metros_cuadrados' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => true,
                'default'    => null,
                'after'      => 'direccion',
                'comment'    => 'Superficie total del condominio en m². Requerida para cálculo de alícuotas.',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('condominios', 'total_metros_cuadrados');
    }
}
