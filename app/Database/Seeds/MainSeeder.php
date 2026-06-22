<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run()
    {
        // Desactivar llaves foráneas temporalmente por seguridad al poblar datos
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0;');

        // Llamamos a los seeders en orden de jerarquía
        $this->call('ConfiguracionMarcaSeeder');
        $this->call('UsuariosSeeder');

        // Volver a activar las llaves foráneas
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
