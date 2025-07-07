<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckConfig extends BaseCommand
{
    protected $group       = 'Config';
    protected $name        = 'config:check';
    protected $description = 'Verifica la configuración del sistema';

    public function run(array $params)
    {
        CLI::write('Verificando configuración del sistema...', 'yellow');
        
        try {
            // Verificar archivo de rutas
            CLI::write('Verificando archivo de rutas...', 'cyan');
            $routesFile = APPPATH . 'Config/Routes.php';
            
            if (file_exists($routesFile)) {
                CLI::write('✓ Archivo Routes.php existe', 'green');
                
                // Verificar contenido del archivo
                $content = file_get_contents($routesFile);
                if (strpos($content, 'usuarios') !== false) {
                    CLI::write('✓ Rutas de usuarios encontradas en el archivo', 'green');
                } else {
                    CLI::write('✗ Rutas de usuarios NO encontradas en el archivo', 'red');
                }
            } else {
                CLI::error('✗ Archivo Routes.php NO existe');
                return;
            }
            
            // Verificar configuración de la aplicación
            CLI::write('\nVerificando configuración de la aplicación...', 'cyan');
            
            $appConfig = config('App');
            CLI::write('App Name: ' . ($appConfig->appName ?? 'No configurado'), 'white');
            CLI::write('Environment: ' . ($appConfig->environment ?? 'No configurado'), 'white');
            CLI::write('Base URL: ' . ($appConfig->baseURL ?? 'No configurado'), 'white');
            
            // Verificar configuración de base de datos
            CLI::write('\nVerificando configuración de base de datos...', 'cyan');
            
            $dbConfig = config('Database');
            $default = $dbConfig->default ?? [];
            CLI::write('Database Host: ' . ($default['hostname'] ?? 'No configurado'), 'white');
            CLI::write('Database Name: ' . ($default['database'] ?? 'No configurado'), 'white');
            CLI::write('Database User: ' . ($default['username'] ?? 'No configurado'), 'white');
            
            // Verificar archivos de configuración
            CLI::write('\nVerificando archivos de configuración...', 'cyan');
            
            $configFiles = [
                'App.php' => APPPATH . 'Config/App.php',
                'Database.php' => APPPATH . 'Config/Database.php',
                'Filters.php' => APPPATH . 'Config/Filters.php',
                'Routes.php' => APPPATH . 'Config/Routes.php'
            ];
            
            foreach ($configFiles as $name => $path) {
                if (file_exists($path)) {
                    CLI::write('✓ ' . $name . ' existe', 'green');
                } else {
                    CLI::write('✗ ' . $name . ' NO existe', 'red');
                }
            }
            
            // Verificar controladores
            CLI::write('\nVerificando controladores...', 'cyan');
            
            $controllers = [
                'Auth.php' => APPPATH . 'Controllers/Auth.php',
                'Dashboard.php' => APPPATH . 'Controllers/Dashboard.php',
                'Usuarios.php' => APPPATH . 'Controllers/Usuarios.php'
            ];
            
            foreach ($controllers as $name => $path) {
                if (file_exists($path)) {
                    CLI::write('✓ ' . $name . ' existe', 'green');
                } else {
                    CLI::write('✗ ' . $name . ' NO existe', 'red');
                }
            }
            
            // Verificar filtros
            CLI::write('\nVerificando filtros...', 'cyan');
            
            $filters = [
                'AuthFilter.php' => APPPATH . 'Filters/AuthFilter.php',
                'AdminFilter.php' => APPPATH . 'Filters/AdminFilter.php'
            ];
            
            foreach ($filters as $name => $path) {
                if (file_exists($path)) {
                    CLI::write('✓ ' . $name . ' existe', 'green');
                } else {
                    CLI::write('✗ ' . $name . ' NO existe', 'red');
                }
            }
            
            CLI::write('\nVerificación de configuración completada.', 'green');
            
        } catch (\Exception $e) {
            CLI::error('Error al verificar configuración: ' . $e->getMessage());
        }
    }
} 