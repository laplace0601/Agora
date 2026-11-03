<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearTablasReservasAreas extends Migration
{
    public function up()
    {
        // Tabla de Áreas Comunes
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'descripcion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'capacidad_maxima' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['Activo', 'Mantenimiento', 'Inactivo'],
                'default'    => 'Activo',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('areas_comunes');

        // Tabla de Reservas
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'area_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'apartamento_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'usuario_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'fecha_reserva' => [
                'type' => 'DATE',
            ],
            'hora_inicio' => [
                'type' => 'TIME',
            ],
            'hora_fin' => [
                'type' => 'TIME',
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['Pendiente', 'Confirmada', 'Cancelada'],
                'default'    => 'Confirmada',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('area_id', 'areas_comunes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('apartamento_id', 'apartamentos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('usuario_id', 'usuarios', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('reservas_areas');
    }

    public function down()
    {
        $this->forge->dropTable('reservas_areas');
        $this->forge->dropTable('areas_comunes');
    }
}
