<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestRoutes extends BaseCommand
{
    protected $group       = 'Routes';
    protected $name        = 'routes:test';
    protected $description = 'Prueba las rutas específicamente';

    public function run(array $params)
    {
        CLI::write('Probando rutas específicamente...', 'yellow');
        
        try {
            // Cargar las rutas manualmente
            CLI::write('Cargando rutas manualmente...', 'cyan');
            
            $routes = service('routes');
            
            // Verificar si las rutas están vacías
            $collection = $routes->getRoutes();
            CLI::write('Total de rutas registradas: ' . count($collection), 'white');
            
            if (empty($collection)) {
                CLI::error('No hay rutas registradas. El problema está en la carga de rutas.');
                
                // Intentar cargar las rutas manualmente
                CLI::write('Intentando cargar rutas manualmente...', 'cyan');
                
                $routesFile = APPPATH . 'Config/Routes.php';
                if (file_exists($routesFile)) {
                    CLI::write('Archivo de rutas encontrado, intentando cargar...', 'yellow');
                    
                    // Simular la carga de rutas
                    $routes->setAutoRoute(false);
                    
                    // Definir rutas manualmente
                    $routes->get('/', 'Auth::index');
                    
                    $routes->group('auth', function($routes) {
                        $routes->get('/', 'Auth::index');
                        $routes->post('login', 'Auth::login');
                        $routes->get('logout', 'Auth::logout');
                    });
                    
                    $routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);
                    
                    $routes->group('usuarios', ['filter' => 'auth'], function($routes) {
                        $routes->get('/', 'Usuarios::index');
                        $routes->get('listar', 'Usuarios::listar');
                        $routes->get('crear', 'Usuarios::crear');
                        $routes->post('guardar', 'Usuarios::guardar');
                        $routes->get('editar/(:num)', 'Usuarios::editar/$1');
                        $routes->post('actualizar/(:num)', 'Usuarios::actualizar/$1');
                        $routes->post('eliminar/(:num)', 'Usuarios::eliminar/$1');
                    });
                    
                    CLI::write('Rutas definidas manualmente. Verificando...', 'yellow');
                    
                    $collection = $routes->getRoutes();
                    CLI::write('Rutas después de carga manual: ' . count($collection), 'white');
                    
                    foreach ($collection as $route) {
                        $methods = implode('|', $route->getMethods());
                        $path = $route->getPath();
                        $handler = $route->getHandler();
                        
                        CLI::write($methods . ' ' . $path . ' -> ' . $handler, 'green');
                    }
                }
            } else {
                CLI::write('Rutas encontradas:', 'green');
                foreach ($collection as $route) {
                    $methods = implode('|', $route->getMethods());
                    $path = $route->getPath();
                    $handler = $route->getHandler();
                    
                    CLI::write($methods . ' ' . $path . ' -> ' . $handler, 'white');
                }
            }
            
            // Verificar configuración de routing
            CLI::write('\nVerificando configuración de routing...', 'cyan');
            
            $routingConfig = config('Routing');
            CLI::write('Auto Route: ' . (($routingConfig->autoRoute ?? false) ? 'true' : 'false'), 'white');
            CLI::write('Default Namespace: ' . ($routingConfig->defaultNamespace ?? 'No configurado'), 'white');
            CLI::write('Route Files: ' . implode(', ', ($routingConfig->routeFiles ?? [])), 'white');
            
            CLI::write('\nPrueba de rutas completada.', 'green');
            
        } catch (\Exception $e) {
            CLI::error('Error al probar rutas: ' . $e->getMessage());
        }
    }
} 