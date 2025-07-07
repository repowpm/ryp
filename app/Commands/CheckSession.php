<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Session\Session;

class CheckSession extends BaseCommand
{
    protected $group       = 'Debug';
    protected $name        = 'check:session';
    protected $description = 'Verifica el estado de la sesión y configuración';

    public function run(array $params)
    {
        CLI::write('=== Verificación de Sesión ===', 'green');
        
        // Verificar configuración de sesión
        $sessionConfig = config('Session');
        CLI::write("Driver de sesión: " . $sessionConfig->driver, 'yellow');
        CLI::write("Save Path: " . $sessionConfig->savePath, 'yellow');
        CLI::write("Cookie Name: " . $sessionConfig->cookieName, 'yellow');
        CLI::write("Expiration: " . $sessionConfig->expiration . " segundos", 'yellow');
        
        // Verificar constantes de paths
        CLI::write("\n=== Constantes de Paths ===", 'green');
        CLI::write("WRITEPATH: " . (defined('WRITEPATH') ? WRITEPATH : 'NO DEFINIDA'), 'yellow');
        CLI::write("FCPATH: " . (defined('FCPATH') ? FCPATH : 'NO DEFINIDA'), 'yellow');
        CLI::write("APPPATH: " . (defined('APPPATH') ? APPPATH : 'NO DEFINIDA'), 'yellow');
        
        // Verificar directorio de sesiones
        CLI::write("\n=== Directorio de Sesiones ===", 'green');
        $sessionDir = WRITEPATH . 'session';
        CLI::write("Directorio de sesiones: " . $sessionDir, 'yellow');
        CLI::write("¿Existe el directorio?: " . (is_dir($sessionDir) ? 'SÍ' : 'NO'), 'yellow');
        CLI::write("¿Es escribible?: " . (is_writable($sessionDir) ? 'SÍ' : 'NO'), 'yellow');
        
        if (is_dir($sessionDir)) {
            $files = scandir($sessionDir);
            CLI::write("Archivos en el directorio: " . count($files), 'yellow');
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    CLI::write("  - " . $file, 'cyan');
                }
            }
        }
        
        // Verificar si hay sesión activa
        CLI::write("\n=== Estado de Sesión ===", 'green');
        $session = \Config\Services::session();
        CLI::write("¿Sesión iniciada?: " . ($session->get('id_usuario') ? 'SÍ' : 'NO'), 'yellow');
        
        if ($session->get('id_usuario')) {
            CLI::write("ID de sesión: " . session_id(), 'yellow');
            CLI::write("Datos de sesión:", 'yellow');
            foreach ($session->get() as $key => $value) {
                CLI::write("  - $key: " . (is_array($value) ? json_encode($value) : $value), 'cyan');
            }
        }
        
        CLI::write("\n=== Fin de Verificación ===", 'green');
    }
} 