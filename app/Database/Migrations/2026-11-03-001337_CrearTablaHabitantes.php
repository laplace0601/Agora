<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearTablaHabitantes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'apartamento_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'apellido' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'cedula_identidad' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'telefono' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'tipo_habitante' => [
                'type'       => 'ENUM',
                'constraint' => ['Propietario', 'Familiar', 'Inquilino', 'Otro'],
                'default'    => 'Inquilino',
            ],
            'es_principal' => [
                'type'       => 'BOOLEAN',
                'default'    => false,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('apartamento_id', 'apartamentos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('habitantes');
    }

    public function down()
    {
        $this->forge->dropTable('habitantes');
    }
}
