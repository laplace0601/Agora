<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddImagenToNoticias extends Migration
{
    public function up()
    {
        $fields = [
            'imagen_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'contenido',
            ],
        ];
        $this->forge->addColumn('noticias_comunicados', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('noticias_comunicados', 'imagen_url');
    }
}
