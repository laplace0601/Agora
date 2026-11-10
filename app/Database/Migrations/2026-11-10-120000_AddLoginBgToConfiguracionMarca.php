<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Agrega la columna `login_bg_url` a la tabla `configuracion_marca`
 * para almacenar la ruta de la imagen de fondo del login.
 */
class AddLoginBgToConfiguracionMarca extends Migration
{
    public function up()
    {
        $this->forge->addColumn('configuracion_marca', [
            'login_bg_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'after'      => 'logo_url',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('configuracion_marca', 'login_bg_url');
    }
}

