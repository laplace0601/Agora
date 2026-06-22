<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\UsuarioModel;

class UsuariosSeeder extends Seeder
{
    public function run()
    {
        $modelo = new UsuarioModel();

        // Desactivar validaciones del modelo para la inserción directa del seeder.
        // Esto evita que reglas como 'is_unique' fallen en un entorno recién migrado.
        $modelo->skipValidation(true);

        // 1. EL USUARIO ROOT OBLIGATORIO
        // La clave se pasa en TEXTO PLANO — el callback beforeInsert del modelo
        // la hashea exactamente UNA sola vez con password_hash (PASSWORD_BCRYPT).
        // Si se pasa ya hasheada, el callback la hashearía DOS veces y el login fallaría.
        $modelo->insert([
            'id'             => 1,
            'correo'         => 'admin@agora.com',
            'nombre_usuario' => 'admin',
            'clave'          => '123456',
            'rol'            => 'root',
            'estado'         => 'activo',
        ]);

        // 2. USUARIOS DE PRUEBA (solo en entorno de desarrollo)
        if (ENVIRONMENT === 'development') {
            $modelo->insertBatch([
                [
                    'id'             => 2,
                    'correo'         => 'comite_demo@agora.com',
                    'nombre_usuario' => 'comite_demo',
                    'clave'          => '123456',
                    'rol'            => 'admin',
                    'estado'         => 'activo',
                ],
                [
                    'id'             => 3,
                    'correo'         => 'residente_demo@agora.com',
                    'nombre_usuario' => 'residente_demo',
                    'clave'          => '123456',
                    'rol'            => 'residente',
                    'estado'         => 'activo',
                ],
            ]);
        }
    }
}
