<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsuariosSeeder extends Seeder
{
    public function run()
    {
        // ---------------------------------------------------------------
        // NOTA DE ARQUITECTURA:
        // Desactivamos la verificación de llaves foráneas para permitir 
        // el truncate() de una tabla que tiene dependencias.
        // ---------------------------------------------------------------
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        // Limpiar la tabla
        $this->db->table('usuarios')->truncate();

        // 1. USUARIO ROOT OBLIGATORIO
        $this->db->table('usuarios')->insert([
            'correo'         => 'admin@agora.com',
            'nombre_usuario' => 'admin',
            'clave'          => password_hash('123456', PASSWORD_DEFAULT),
            'rol'            => 'root',
            'estado'         => 'activo',
        ]);

        // 2. USUARIOS DE PRUEBA (solo en entorno de desarrollo)
        if (ENVIRONMENT === 'development') {
            $claveHash = password_hash('123456', PASSWORD_DEFAULT);

            $this->db->table('usuarios')->insertBatch([
                [
                    'correo'         => 'comite_demo@agora.com',
                    'nombre_usuario' => 'comite_demo',
                    'clave'          => $claveHash,
                    'rol'            => 'admin',
                    'estado'         => 'activo',
                ],
                [
                    'correo'         => 'residente_demo@agora.com',
                    'nombre_usuario' => 'residente_demo',
                    'clave'          => $claveHash,
                    'rol'            => 'residente',
                    'estado'         => 'activo',
                ],
            ]);
        }

        // ---------------------------------------------------------------
        // REACTIVAR LAS LLAVES FORÁNEAS
        // Muy importante para mantener la integridad de la base de datos.
        // ---------------------------------------------------------------
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }
}
