<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckFramework extends BaseCommand
{
    protected $group       = 'Framework';
    protected $name        = 'framework:check';
    protected $description = 'Verifica el estado del framework CodeIgniter';

    public function run(array $params)
    {
        CLI::write('Verificando estado del framework...', 'yellow');
        
        try {
            // Verificar constantes del framework
            CLI::write('Verificando constantes del framework...', 'cyan');
            
            $constants = [
                'FCPATH' => defined('FCPATH') ? FCPATH : 'NO DEFINIDA',
                'APPPATH' => defined('APPPATH') ? APPPATH : 'NO DEFINIDA',
                'SYSTEMPATH' => defined('SYSTEMPATH') ? SYSTEMPATH : 'NO DEFINIDA',
                'WRITEPATH' => defined('WRITEPATH') ? WRITEPATH : 'NO DEFINIDA',
                'ROOTPATH' => defined('ROOTPATH') ? ROOTPATH : 'NO DEFINIDA'
            ];
            
            foreach ($constants as $name => $value) {
                if ($value !== 'NO DEFINIDA') {
                    CLI::write('✓ ' . $name . ': ' . $value, 'green');
                } else {
                    CLI::write('✗ ' . $name . ': ' . $value, 'red');
                }
            }
            
            // Verificar servicios del framework
            CLI::write('\nVerificando servicios del framework...', 'cyan');
            
            $services = [
                'routes' => 'CodeIgniter\Router\RouteCollection',
                'request' => 'CodeIgniter\HTTP\IncomingRequest',
                'response' => 'CodeIgniter\HTTP\Response',
                'logger' => 'CodeIgniter\Log\Logger'
            ];
            
            foreach ($services as $service => $expectedClass) {
                try {
                    $instance = service($service);
                    if ($instance instanceof $expectedClass) {
                        CLI::write('✓ Servicio ' . $service . ' funciona correctamente', 'green');
                    } else {
                        CLI::write('⚠ Servicio ' . $service . ' existe pero es de tipo ' . get_class($instance), 'yellow');
                    }
                } catch (\Exception $e) {
                    CLI::write('✗ Servicio ' . $service . ' NO funciona: ' . $e->getMessage(), 'red');
                }
            }
            
            // Verificar servicio config por separado
            try {
                $config = service('config');
                if ($config !== null) {
                    CLI::write('✓ Servicio config funciona correctamente', 'green');
                } else {
                    CLI::write('⚠ Servicio config es null', 'yellow');
                }
            } catch (\Exception $e) {
                CLI::write('✗ Servicio config NO funciona: ' . $e->getMessage(), 'red');
            }
            
            // Verificar archivos críticos del framework
            CLI::write('\nVerificando archivos críticos...', 'cyan');
            
            $criticalFiles = [
                'system/Boot.php' => SYSTEMPATH . 'Boot.php',
                'system/Config/Services.php' => SYSTEMPATH . 'Config/Services.php',
                'app/Config/Paths.php' => APPPATH . 'Config/Paths.php',
                'app/Config/Routes.php' => APPPATH . 'Config/Routes.php'
            ];
            
            foreach ($criticalFiles as $name => $path) {
                if (file_exists($path)) {
                    CLI::write('✓ ' . $name . ' existe', 'green');
                } else {
                    CLI::write('✗ ' . $name . ' NO existe', 'red');
                }
            }
            
            // Verificar configuración de autoload
            CLI::write('\nVerificando autoload...', 'cyan');
            
            $autoloadFile = APPPATH . 'Config/Autoload.php';
            if (file_exists($autoloadFile)) {
                CLI::write('✓ Archivo Autoload.php existe', 'green');
                
                // Verificar si las clases se pueden cargar
                $testClasses = [
                    'App\Controllers\Auth',
                    'App\Controllers\Usuarios',
                    'App\Models\UsuarioModel'
                ];
                
                foreach ($testClasses as $class) {
                    if (class_exists($class)) {
                        CLI::write('  ✓ Clase ' . $class . ' se puede cargar', 'green');
                    } else {
                        CLI::write('  ✗ Clase ' . $class . ' NO se puede cargar', 'red');
                    }
                }
            } else {
                CLI::write('✗ Archivo Autoload.php NO existe', 'red');
            }
            
            // Verificar entorno
            CLI::write('\nVerificando entorno...', 'cyan');
            
            $environment = getenv('CI_ENVIRONMENT') ?: 'development';
            CLI::write('Environment: ' . $environment, 'white');
            
            // Verificar si estamos en modo CLI
            CLI::write('CLI Mode: true', 'white');
            
            // Intentar crear una ruta simple para probar
            CLI::write('\nProbando creación de ruta simple...', 'cyan');
            
            try {
                $routes = service('routes');
                $routes->get('test', 'Auth::index');
                
                $collection = $routes->getRoutes();
                if (count($collection) > 0) {
                    CLI::write('✓ Las rutas se pueden crear correctamente', 'green');
                    
                    foreach ($collection as $route) {
                        $methods = implode('|', $route->getMethods());
                        $path = $route->getPath();
                        $handler = $route->getHandler();
                        
                        CLI::write('  Ruta: ' . $methods . ' ' . $path . ' -> ' . $handler, 'white');
                    }
                } else {
                    CLI::write('✗ Las rutas NO se pueden crear', 'red');
                }
            } catch (\Exception $e) {
                CLI::write('✗ Error al crear rutas: ' . $e->getMessage(), 'red');
            }
            
            CLI::write('\nVerificación del framework completada.', 'green');
            
        } catch (\Exception $e) {
            CLI::error('Error al verificar framework: ' . $e->getMessage());
        }
    }
} 