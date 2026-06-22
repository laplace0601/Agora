<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ConfiguracionMarcaSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'id'                       => 1,
            'nombre_empresa'           => 'Mi Administradora Base',
            'logo_url'                 => null,
            'color_primario'           => '#007bff',
            'correo_contacto'          => 'contacto@agora.com',
            'nivel_licencia'           => 'Bronce',
            'limite_apartamentos'      => 40,
            'codigo_activacion'        => null,
            'fecha_actualizacion_plan' => null
        ];

        $this->db->table('configuracion_marca')->insert($data);
    }
}
