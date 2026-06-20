<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EsquemaInicialAgora extends Migration
{
    public function up()
    {
        // 1. TABLAS INDEPENDIENTES (Sin llaves foráneas)

        // Tabla: configuracion_marca
        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nombre_empresa'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'logo_url'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'color_primario'      => ['type' => 'VARCHAR', 'constraint' => 7, 'default' => '#007bff'],
            'correo_contacto'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'nivel_licencia'      => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Bronce'],
            'limite_apartamentos' => ['type' => 'INT', 'constraint' => 11, 'default' => 100],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('configuracion_marca');

        // Tabla: usuarios
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'correo'         => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'clave'          => ['type' => 'VARCHAR', 'constraint' => 255],
            'rol'            => ['type' => 'VARCHAR', 'constraint' => 20],
            'estado'         => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'activa', 'null' => true],
            'fecha_registro' => ['type' => 'TIMESTAMP', 'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP')],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('usuarios');

        // 2. TABLAS DE PRIMER NIVEL DE DEPENDENCIA

        // Tabla: condominios
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nombre_condominio' => ['type' => 'VARCHAR', 'constraint' => 100],
            'rif_jurisdiccion' => ['type' => 'VARCHAR', 'constraint' => 20, 'unique' => true],
            'direccion'        => ['type' => 'TEXT', 'null' => true],
            'marca_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('marca_id', 'configuracion_marca', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('condominios');

        // Tabla: residentes
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'usuario_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'unique' => true],
            'nombre_completo'  => ['type' => 'VARCHAR', 'constraint' => 100],
            'cedula_identidad' => ['type' => 'VARCHAR', 'constraint' => 20, 'unique' => true],
            'telefono'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('usuario_id', 'usuarios', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('residentes');

        // Tabla: noticias_comunicados
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'autor_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'titulo'            => ['type' => 'VARCHAR', 'constraint' => 100],
            'contenido'         => ['type' => 'TEXT'],
            'fecha_publicacion' => ['type' => 'TIMESTAMP', 'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP')],
            'estado'            => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'publicado'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('autor_id', 'usuarios', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('noticias_comunicados');

        // Tabla: tickets_soporte
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'usuario_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'categoria'        => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Mantenimiento', 'null' => true],
            'asunto'           => ['type' => 'VARCHAR', 'constraint' => 100],
            'detalle'          => ['type' => 'TEXT'],
            'estado'           => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Abierto', 'null' => true],
            'fecha_creacion'   => ['type' => 'TIMESTAMP', 'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP')],
            'fecha_resolucion' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('usuario_id', 'usuarios', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tickets_soporte');

        // 3. TABLAS DE SEGUNDO NIVEL DE DEPENDENCIA

        // Tabla: apartamentos
        $this->forge->addField([
            'id'                    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'condominio_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'residente_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'nombre_edificio_torre' => ['type' => 'VARCHAR', 'constraint' => 100],
            'nro_apartamento'       => ['type' => 'VARCHAR', 'constraint' => 10],
            'alicuota'              => ['type' => 'DECIMAL', 'constraint' => '5,4'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('condominio_id', 'condominios', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('residente_id', 'residentes', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('apartamentos');

        // 4. TABLAS DE TERCER Y CUARTO NIVEL DE DEPENDENCIA

        // Tabla: recibos_mensuales
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'apartamento_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'mes'             => ['type' => 'INT', 'constraint' => 11],
            'anio'            => ['type' => 'INT', 'constraint' => 11],
            'monto_base'      => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'monto_intereses' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00, 'null' => true],
            'monto_total'     => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'estado_pago'     => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Pendiente', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('apartamento_id', 'apartamentos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('recibos_mensuales');

        // Tabla: pagos_recibidos
        $this->forge->addField([
            'id'                     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'recibo_mensual_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'monto_pagado'           => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'metodo_pago'            => ['type' => 'VARCHAR', 'constraint' => 50],
            'referencia_transaccion' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'comprobante_url'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'fecha_registro'         => ['type' => 'TIMESTAMP', 'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP')],
            'estado_validacion'      => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Por Validar', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('recibo_mensual_id', 'recibos_mensuales', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pagos_recibidos');

        // Tabla: recibos_solvencia
        $this->forge->addField([
            'id'                     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'apartamento_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'emitido_por_usuario_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'fecha_emision'          => ['type' => 'TIMESTAMP', 'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP')],
            'hasta_mes'              => ['type' => 'INT', 'constraint' => 11],
            'hasta_anio'             => ['type' => 'INT', 'constraint' => 11],
            'codigo_verificacion'    => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('apartamento_id', 'apartamentos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('emitido_por_usuario_id', 'usuarios', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->createTable('recibos_solvencia');
    }

    public function down()
    {
        // El orden de borrado debe ser EXACTAMENTE el inverso al de creación para evitar errores de llaves foráneas
        $this->forge->dropTable('recibos_solvencia', true);
        $this->forge->dropTable('pagos_recibidos', true);
        $this->forge->dropTable('recibos_mensuales', true);
        $this->forge->dropTable('apartamentos', true);
        $this->forge->dropTable('tickets_soporte', true);
        $this->forge->dropTable('noticias_comunicados', true);
        $this->forge->dropTable('residentes', true);
        $this->forge->dropTable('condominios', true);
        $this->forge->dropTable('usuarios', true);
        $this->forge->dropTable('configuracion_marca', true);
    }
}
