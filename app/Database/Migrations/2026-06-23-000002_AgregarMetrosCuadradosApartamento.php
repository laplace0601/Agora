<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Agrega el campo 'metros_cuadrados' a la tabla apartamentos.
 * Este campo almacena la superficie del apartamento para calcular su alícuota proporcional.
 */
class AgregarMetrosCuadradosApartamento extends Migration
{
    public function up()
    {
        $this->forge->addColumn('apartamentos', [
            'metros_cuadrados' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
                'after'      => 'nro_apartamento',
                'comment'    => 'Superficie del apartamento en m². Se usa para calcular la alícuota proporcional.',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('apartamentos', 'metros_cuadrados');
    }
}
