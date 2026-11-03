<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Agrega la columna 'estado' a la tabla habitantes para el flujo de aprobación.
 * - 'aprobado'  → habitante confirmado por el admin (o creado directamente por él)
 * - 'pendiente' → solicitud del residente que espera aprobación del admin
 * - 'rechazado' → solicitud rechazada por el admin
 */
class AddEstadoToHabitantes extends Migration
{
    public function up()
    {
        $this->forge->addColumn('habitantes', [
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['aprobado', 'pendiente', 'rechazado'],
                'default'    => 'aprobado',
                'after'      => 'es_principal',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('habitantes', 'estado');
    }
}
