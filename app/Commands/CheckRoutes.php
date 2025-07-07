<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckRoutes extends BaseCommand
{
    protected $group       = 'Routes';
    protected $name        = 'routes:check';
    protected $description = 'Verifica las rutas registradas en el sistema';

    public function run(array $params)
    {
        CLI::write('Verificando rutas del sistema...', 'yellow');
        
        try {
            // Obtener las rutas registradas
            $routes = service('routes');
            $collection = $routes->getRoutes();
            
            CLI::write('Rutas registradas:', 'cyan');
            CLI::write('================', 'cyan');
            
            foreach ($collection as $route) {
                $methods = implode('|', $route->getMethods());
                $path = $route->getPath();
                $handler = $route->getHandler();
                
                CLI::write($methods . ' ' . $path . ' -> ' . $handler, 'white');
            }
            
            // Verificar rutas específicas de usuarios
            CLI::write('\nVerificando rutas de usuarios:', 'cyan');
            CLI::write('============================', 'cyan');
            
            $userRoutes = [
                'GET /usuarios' => 'Usuarios::index',
                'GET /usuarios/listar' => 'Usuarios::listar',
                'GET /usuarios/crear' => 'Usuarios::crear',
                'POST /usuarios/guardar' => 'Usuarios::guardar',
                'GET /usuarios/editar/2' => 'Usuarios::editar/2',
                'POST /usuarios/actualizar/2' => 'Usuarios::actualizar/2',
                'POST /usuarios/eliminar/2' => 'Usuarios::eliminar/2'
            ];
            
            foreach ($userRoutes as $route => $expected) {
                $found = false;
                foreach ($collection as $registeredRoute) {
                    $methods = implode('|', $registeredRoute->getMethods());
                    $path = $registeredRoute->getPath();
                    $handler = $registeredRoute->getHandler();
                    
                    if ($methods . ' ' . $path === $route) {
                        CLI::write('✓ ' . $route . ' -> ' . $handler, 'green');
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    CLI::write('✗ ' . $route . ' NO ENCONTRADA', 'red');
                }
            }
            
            // Verificar filtros
            CLI::write('\nVerificando filtros:', 'cyan');
            CLI::write('==================', 'cyan');
            
            $filters = service('filters');
            $aliases = $filters->getAliases();
            
            if (isset($aliases['auth'])) {
                CLI::write('✓ Filtro auth registrado: ' . $aliases['auth'], 'green');
            } else {
                CLI::write('✗ Filtro auth NO registrado', 'red');
            }
            
            if (isset($aliases['admin'])) {
                CLI::write('✓ Filtro admin registrado: ' . $aliases['admin'], 'green');
            } else {
                CLI::write('✗ Filtro admin NO registrado', 'red');
            }
            
            // Verificar controlador
            CLI::write('\nVerificando controlador Usuarios:', 'cyan');
            CLI::write('==============================', 'cyan');
            
            $controllerPath = APPPATH . 'Controllers/Usuarios.php';
            if (file_exists($controllerPath)) {
                CLI::write('✓ Controlador Usuarios existe', 'green');
                
                // Verificar métodos
                $methods = [
                    'index',
                    'listar', 
                    'crear',
                    'guardar',
                    'editar',
                    'actualizar',
                    'eliminar'
                ];
                
                foreach ($methods as $method) {
                    if (method_exists('\App\Controllers\Usuarios', $method)) {
                        CLI::write('  ✓ Método ' . $method . ' existe', 'green');
                    } else {
                        CLI::write('  ✗ Método ' . $method . ' NO existe', 'red');
                    }
                }
            } else {
                CLI::write('✗ Controlador Usuarios NO existe', 'red');
            }
            
            CLI::write('\nVerificación de rutas completada.', 'green');
            
        } catch (\Exception $e) {
            CLI::error('Error al verificar rutas: ' . $e->getMessage());
        }
    }
} 