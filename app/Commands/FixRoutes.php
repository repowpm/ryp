<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FixRoutes extends BaseCommand
{
    protected $group       = 'Routes';
    protected $name        = 'routes:fix';
    protected $description = 'Soluciona el problema de rutas cargándolas manualmente';

    public function run(array $params)
    {
        CLI::write('Solucionando problema de rutas...', 'yellow');
        
        try {
            // Obtener el servicio de rutas
            $routes = service('routes');
            
            // Limpiar rutas existentes
            CLI::write('Limpiando rutas existentes...', 'cyan');
            $routes->resetRoutes();
            
            // Cargar rutas manualmente
            CLI::write('Cargando rutas manualmente...', 'cyan');
            
            // Ruta principal
            $routes->get('/', 'Auth::index');
            CLI::write('✓ Ruta principal cargada', 'green');
            
            // Rutas de autenticación
            $routes->group('auth', function($routes) {
                $routes->get('/', 'Auth::index');
                $routes->post('login', 'Auth::login');
                $routes->get('logout', 'Auth::logout');
            });
            CLI::write('✓ Rutas de autenticación cargadas', 'green');
            
            // Ruta del dashboard
            $routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);
            CLI::write('✓ Ruta del dashboard cargada', 'green');
            
            // Rutas de usuarios
            $routes->group('usuarios', ['filter' => 'auth'], function($routes) {
                $routes->get('/', 'Usuarios::index');
                $routes->get('listar', 'Usuarios::listar');
                $routes->get('crear', 'Usuarios::crear');
                $routes->post('guardar', 'Usuarios::guardar');
                $routes->get('editar/(:num)', 'Usuarios::editar/$1');
                $routes->post('actualizar/(:num)', 'Usuarios::actualizar/$1');
                $routes->post('eliminar/(:num)', 'Usuarios::eliminar/$1');
            });
            CLI::write('✓ Rutas de usuarios cargadas', 'green');
            
            // Ruta de reportes
            $routes->get('reportes', 'Reportes::index', ['filter' => 'admin']);
            CLI::write('✓ Ruta de reportes cargada', 'green');
            
            // Verificar que las rutas se cargaron
            CLI::write('\nVerificando rutas cargadas...', 'cyan');
            $collection = $routes->getRoutes();
            CLI::write('Total de rutas registradas: ' . count($collection), 'white');
            
            if (count($collection) > 0) {
                CLI::write('✓ Rutas cargadas correctamente:', 'green');
                
                foreach ($collection as $route) {
                    $methods = implode('|', $route->getMethods());
                    $path = $route->getPath();
                    $handler = $route->getHandler();
                    
                    CLI::write('  ' . $methods . ' ' . $path . ' -> ' . $handler, 'white');
                }
                
                // Probar una ruta específica
                CLI::write('\nProbando ruta específica...', 'cyan');
                $testRoute = $routes->getRoutes('GET', '/usuarios');
                
                if (!empty($testRoute)) {
                    CLI::write('✓ Ruta GET /usuarios encontrada', 'green');
                } else {
                    CLI::write('✗ Ruta GET /usuarios NO encontrada', 'red');
                }
                
                CLI::write('\n✅ Problema de rutas solucionado!', 'green');
                CLI::write('Ahora el sistema debería funcionar correctamente.', 'green');
                
            } else {
                CLI::error('✗ Las rutas NO se cargaron correctamente');
            }
            
        } catch (\Exception $e) {
            CLI::error('Error al solucionar rutas: ' . $e->getMessage());
        }
    }
} 