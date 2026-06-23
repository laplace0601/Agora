<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDescripcionToRecibos extends Migration
{
    public function up()
    {
        // 1. Campos a agregar en la tabla 'recibos_mensuales'
        $fields_mensuales = [
            'descripcion' => [
                'type'       => 'TEXT',
                'null'       => true, // Permite nulos para no romper registros anteriores
                'after'      => 'estado_pago' // Lo posiciona visualmente después de este campo
            ]
        ];
        $this->forge->addColumn('recibos_mensuales', $fields_mensuales);

        // 2. Campos a agregar en la tabla 'recibos_solvencia'
        $fields_solvencia = [
            'descripcion' => [
                'type'       => 'TEXT',
                'null'       => true, // Permite nulos
                'after'      => 'codigo_verificacion' // Lo posiciona visualmente después de este campo
            ]
        ];
        $this->forge->addColumn('recibos_solvencia', $fields_solvencia);
    }

    public function down()
    {
        // Método para revertir los cambios de forma segura en orden inverso
        $this->forge->dropColumn('recibos_solvencia', 'descripcion');
        $this->forge->dropColumn('recibos_mensuales', 'descripcion');
    }
}
