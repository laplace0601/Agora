<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ConfiguracionMarcaSeeder extends Seeder
{
    public function run()
    {
        // ---------------------------------------------------------------
        // NOTA DE ARQUITECTURA:
        // Al igual que en UsuariosSeeder, usamos $this->db->table() (RAW)
        // para garantizar que la configuración inicial se restablezca
        // correctamente sin conflictos de IDs o restricciones.
        // ---------------------------------------------------------------

        // Desactivamos llaves foráneas para permitir el truncate
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        // Limpiar la tabla antes de sembrar para garantizar idempotencia.
        $this->db->table('configuracion_marca')->truncate();

        // Inserción de la configuración inicial única
        $this->db->table('configuracion_marca')->insert([
            'id'                       => 1,
            'nombre_empresa'           => 'Mi Administradora Base',
            'logo_url'                 => null,
            'color_primario'           => '#007bff',
            'correo_contacto'          => 'contacto@agora.com',
            'nivel_licencia'           => 'Bronce',
            'limite_apartamentos'      => 40,
            'codigo_activacion'        => null,
            'fecha_actualizacion_plan' => null,
        ]);

        // Reactivamos las llaves foráneas para mantener la integridad
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }
}
