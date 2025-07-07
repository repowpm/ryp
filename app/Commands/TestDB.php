<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestDB extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:test';
    protected $description = 'Prueba la conexión a la base de datos';

    public function run(array $params)
    {
        CLI::write('Probando conexión a la base de datos...', 'yellow');
        
        try {
            // Obtener la instancia de la base de datos
            $db = \Config\Database::connect();
            
            // Probar conexión básica
            $result = $db->query('SELECT 1 as test')->getRow();
            
            if ($result && $result->test == 1) {
                CLI::write('✓ Conexión a la base de datos exitosa', 'green');
            } else {
                CLI::error('✗ Error en la conexión a la base de datos');
                return;
            }

            // Verificar si existe la tabla de usuarios
            $tables = $db->listTables();
            
            if (in_array('wp_md_usuarios', $tables)) {
                CLI::write('✓ Tabla WP_MD_Usuarios encontrada', 'green');
            } else {
                CLI::error('✗ Tabla WP_MD_Usuarios no encontrada');
                CLI::write('Tablas disponibles:', 'yellow');
                foreach ($tables as $table) {
                    CLI::write('  - ' . $table, 'white');
                }
                return;
            }

            // Verificar si existe la función de generar correo
            try {
                $result = $db->query("SELECT WP_FN_GenerarCorreoUnico('Test', 'User') as correo")->getRow();
                CLI::write('✓ Función WP_FN_GenerarCorreoUnico disponible', 'green');
                CLI::write('Correo de prueba: ' . $result->correo, 'cyan');
            } catch (\Exception $e) {
                CLI::error('✗ Función WP_FN_GenerarCorreoUnico no disponible: ' . $e->getMessage());
            }

            // Verificar si existe el procedimiento de insertar usuario
            try {
                $result = $db->query("SHOW PROCEDURE STATUS WHERE Name = 'WP_MD_InsertarUsuario'")->getResult();
                if (!empty($result)) {
                    CLI::write('✓ Procedimiento WP_MD_InsertarUsuario disponible', 'green');
                } else {
                    CLI::write('⚠ Procedimiento WP_MD_InsertarUsuario no encontrado', 'yellow');
                }
            } catch (\Exception $e) {
                CLI::error('✗ Error al verificar procedimiento: ' . $e->getMessage());
            }

            CLI::write('Prueba de base de datos completada.', 'green');
            
        } catch (\Exception $e) {
            CLI::error('Error al probar la base de datos: ' . $e->getMessage());
        }
    }
} 