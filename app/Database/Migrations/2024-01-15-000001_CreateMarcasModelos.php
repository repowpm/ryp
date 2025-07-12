<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMarcasModelos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_marca_modelo' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
                'null' => false,
            ],
            'marca' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'modelo' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'anio_inicio' => [
                'type' => 'INT',
                'constraint' => 4,
                'null' => true,
            ],
            'anio_fin' => [
                'type' => 'INT',
                'constraint' => 4,
                'null' => true,
            ],
            'id_tipo' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
                'null' => true,
            ],
            'usuario_creacion' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'usuario_actualizacion' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id_marca_modelo', true);
        $this->forge->addUniqueKey(['marca', 'modelo']);
        $this->forge->createTable('WP_MD_MarcasModelos');
    }

    public function down()
    {
        $this->forge->dropTable('WP_MD_MarcasModelos');
    }
} 