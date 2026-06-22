<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CrearBaseDeDatosInicial extends Migration
{
    public function up()
    {
        // Deshabilitar revisión de claves foráneas con SQL nativo seguro
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0;');

        // 1. Tabla: configuracion_marca
        $this->forge->addField([
            'id'                       => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'nombre_empresa'           => ['type' => 'VARCHAR', 'constraint' => 100],
            'logo_url'                 => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'color_primario'           => ['type' => 'VARCHAR', 'constraint' => 7, 'default' => '#007bff'],
            'correo_contacto'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'nivel_licencia'           => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Bronce'],
            'limite_apartamentos'      => ['type' => 'INT', 'constraint' => 11, 'default' => 40],
            'codigo_activacion'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'fecha_actualizacion_plan' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('configuracion_marca');

        // INSERCIÓN AUTOMÁTICA DEL REGISTRO MAESTRO PARA MARCA BLANCA
        // Esto previene el error #1452 al registrar condominios
        $this->db->table('configuracion_marca')->insert([
            'id'                       => 1,
            'nombre_empresa'           => 'Mi Administradora Base',
            'logo_url'                 => null,
            'color_primario'           => '#007bff',
            'correo_contacto'          => 'contacto@agora.com',
            'nivel_licencia'           => 'Bronce',
            'limite_apartamentos'      => 40,
            'codigo_activacion'        => null,
            'fecha_actualizacion_plan' => null
        ]);

        // 2. Tabla: usuarios
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'correo'         => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'nombre_usuario' => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'clave'          => ['type' => 'VARCHAR', 'constraint' => 255],
            'rol'            => ['type' => 'VARCHAR', 'constraint' => 20],
            'estado'         => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'activa'],
            'fecha_registro' => ['type' => 'TIMESTAMP', 'default' => new RawSql('CURRENT_TIMESTAMP')],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('usuarios');

        // 3. Tabla: condominios
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'nombre_condominio' => ['type' => 'VARCHAR', 'constraint' => 100],
            'rif_jurisdiccion'  => ['type' => 'VARCHAR', 'constraint' => 20, 'unique' => true],
            'direccion'         => ['type' => 'TEXT', 'null' => true],
            'marca_id'          => ['type' => 'INT', 'constraint' => 11],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('marca_id', 'configuracion_marca', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('condominios');

        // 4. Tabla: residentes
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'usuario_id'       => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'unique' => true],
            'nombre_completo'  => ['type' => 'VARCHAR', 'constraint' => 100],
            'cedula_identidad' => ['type' => 'VARCHAR', 'constraint' => 20, 'unique' => true],
            'telefono'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('usuario_id', 'usuarios', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('residentes');

        // 5. Tabla: apartamentos
        $this->forge->addField([
            'id'                    => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'condominio_id'         => ['type' => 'INT', 'constraint' => 11],
            'residente_id'          => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'nombre_edificio_torre' => ['type' => 'VARCHAR', 'constraint' => 100],
            'nro_apartamento'       => ['type' => 'VARCHAR', 'constraint' => 10],
            'alicuota'              => ['type' => 'DECIMAL', 'constraint' => '5,4'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('condominio_id', 'condominios', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('residente_id', 'residentes', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('apartamentos');

        // 6. Tabla: noticias_comunicados
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'autor_id'          => ['type' => 'INT', 'constraint' => 11],
            'titulo'            => ['type' => 'VARCHAR', 'constraint' => 100],
            'contenido'         => ['type' => 'TEXT'],
            'fecha_publicacion' => ['type' => 'TIMESTAMP', 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'estado'            => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'publicado'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('autor_id', 'usuarios', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('noticias_comunicados');

        // 7. Tabla: recibos_mensuales
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'apartamento_id'  => ['type' => 'INT', 'constraint' => 11],
            'mes'             => ['type' => 'INT', 'constraint' => 11],
            'anio'            => ['type' => 'INT', 'constraint' => 11],
            'monto_base'      => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'monto_intereses' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'monto_total'     => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'estado_pago'     => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Pendiente'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('apartamento_id', 'apartamentos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('recibos_mensuales');

        // 8. Tabla: pagos_recibidos
        $this->forge->addField([
            'id'                     => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'recibo_mensual_id'      => ['type' => 'INT', 'constraint' => 11],
            'monto_pagado'           => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'metodo_pago'            => ['type' => 'VARCHAR', 'constraint' => 50],
            'referencia_transaccion' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'comprobante_url'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'fecha_registro'         => ['type' => 'TIMESTAMP', 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'estado_validacion'      => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Por Validar'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('recibo_mensual_id', 'recibos_mensuales', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pagos_recibidos');

        // 9. Tabla: recibos_solvencia
        $this->forge->addField([
            'id'                     => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'apartamento_id'         => ['type' => 'INT', 'constraint' => 11],
            'emitido_por_usuario_id' => ['type' => 'INT', 'constraint' => 11],
            'fecha_emision'          => ['type' => 'TIMESTAMP', 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'hasta_mes'              => ['type' => 'INT', 'constraint' => 11],
            'hasta_anio'             => ['type' => 'INT', 'constraint' => 11],
            'codigo_verificacion'    => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'unique' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('apartamento_id', 'apartamentos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('emitido_por_usuario_id', 'usuarios', 'id');
        $this->forge->createTable('recibos_solvencia');

        // 10. Tabla: tickets_soporte
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'usuario_id'       => ['type' => 'INT', 'constraint' => 11],
            'categoria'        => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Mantenimiento'],
            'asunto'           => ['type' => 'VARCHAR', 'constraint' => 100],
            'detalle'          => ['type' => 'TEXT'],
            'estado'           => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Abierto'],
            'fecha_creacion'   => ['type' => 'TIMESTAMP', 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'fecha_resolucion' => ['type' => 'TIMESTAMP', 'null' => true], // Modificado para evitar errores de Strict Mode (0000-00-00)
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('usuario_id', 'usuarios', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tickets_soporte');

        // Volver a habilitar claves foráneas
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1;');
    }

    public function down()
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0;');

        $this->forge->dropTable('tickets_soporte', true);
        $this->forge->dropTable('recibos_solvencia', true);
        $this->forge->dropTable('pagos_recibidos', true);
        $this->forge->dropTable('recibos_mensuales', true);
        $this->forge->dropTable('noticias_comunicados', true);
        $this->forge->dropTable('apartamentos', true);
        $this->forge->dropTable('residentes', true);
        $this->forge->dropTable('condominios', true);
        $this->forge->dropTable('usuarios', true);
        $this->forge->dropTable('configuracion_marca', true);

        $this->db->query('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
