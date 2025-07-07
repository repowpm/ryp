<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Deshabilitar auto-routing para evitar conflictos
$routes->setAutoRoute(false);

// Ruta principal redirige al login
$routes->get('/', 'Auth::index');

// Rutas de autenticación (sin filtros)
$routes->group('auth', function($routes) {
    $routes->get('/', 'Auth::index');
    $routes->post('login', 'Auth::login');
    $routes->get('logout', 'Auth::logout');
});

// Rutas protegidas que requieren autenticación
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);

// Rutas que requieren rol de administrador
$routes->group('usuarios', ['filter' => 'admin'], function($routes) {
    $routes->get('/', 'Usuarios::index');
    $routes->get('listar', 'Usuarios::listar');
    $routes->get('crear', 'Usuarios::crear');
    $routes->post('guardar', 'Usuarios::guardar');
    $routes->get('editar/(:num)', 'Usuarios::editar/$1');
    $routes->post('actualizar/(:num)', 'Usuarios::actualizar/$1');
    $routes->post('eliminar/(:num)', 'Usuarios::eliminar/$1');
});

$routes->get('reportes', 'Reportes::index', ['filter' => 'admin']);
