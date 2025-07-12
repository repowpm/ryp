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
$routes->get('login', 'Auth::index');
$routes->group('auth', function($routes) {
    $routes->get('/', 'Auth::index');
    $routes->post('login', 'Auth::login');
    $routes->get('logout', 'Auth::logout');
});

// Rutas protegidas que requieren autenticación y control de roles
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'role']);

// Rutas que requieren rol de administrador
$routes->group('usuarios', ['filter' => 'role'], function($routes) {
    $routes->get('/', 'Usuarios::index');
    $routes->get('listar', 'Usuarios::listar');
    $routes->get('crear', 'Usuarios::crear');
    $routes->post('guardar', 'Usuarios::guardar');
    $routes->get('editar/(:any)', 'Usuarios::editar/$1');
    $routes->post('actualizar/(:any)', 'Usuarios::actualizar/$1');
    $routes->post('eliminar/(:any)', 'Usuarios::eliminar/$1');
});

$routes->group('clientes', ['filter' => 'role'], function($routes) {
    $routes->get('/', 'Clientes::index');
    $routes->get('listar', 'Clientes::listar');
    $routes->get('crear', 'Clientes::crear');
    $routes->post('guardar', 'Clientes::guardar');
    $routes->get('editar/(:segment)', 'Clientes::editar/$1');
    $routes->post('actualizar/(:segment)', 'Clientes::actualizar/$1');
    $routes->post('eliminar/(:segment)', 'Clientes::eliminar/$1');
});

// Rutas AJAX para clientes
$routes->post('clientes/getClientes', 'Clientes::getClientes', ['filter' => 'role']);
$routes->post('clientes/createCliente', 'Clientes::createCliente', ['filter' => 'role']);
$routes->post('clientes/updateCliente', 'Clientes::updateCliente', ['filter' => 'role']);
$routes->post('clientes/deleteCliente', 'Clientes::deleteCliente', ['filter' => 'role']);
$routes->post('clientes/getClienteById', 'Clientes::getClienteById', ['filter' => 'role']);

$routes->group('vehiculos', ['filter' => 'role'], function($routes) {
    $routes->get('/', 'Vehiculos::index');
    $routes->get('crear', 'Vehiculos::crear');
    $routes->post('guardar', 'Vehiculos::guardar');
    $routes->get('editar/(:segment)', 'Vehiculos::editar/$1');
    $routes->post('actualizar/(:segment)', 'Vehiculos::actualizar/$1');
    $routes->post('eliminar/(:segment)', 'Vehiculos::eliminar/$1');
});

// Rutas AJAX para vehículos
$routes->post('vehiculos/getVehiculos', 'Vehiculos::getVehiculos', ['filter' => 'role']);
$routes->post('vehiculos/getModelosByMarca', 'Vehiculos::getModelosByMarca', ['filter' => 'role']);
$routes->post('vehiculos/buscarClientes', 'Vehiculos::buscarClientes', ['filter' => 'role']);
$routes->post('vehiculos/deleteVehiculo', 'Vehiculos::deleteVehiculo', ['filter' => 'role']);

$routes->group('repuestos', ['filter' => 'role'], function($routes) {
    $routes->get('/', 'Repuestos::index');
    $routes->get('listar', 'Repuestos::listar');
    $routes->get('crear', 'Repuestos::crear');
    $routes->post('guardar', 'Repuestos::guardar');
    $routes->get('editar/(:segment)', 'Repuestos::editar/$1');
    $routes->post('actualizar/(:segment)', 'Repuestos::actualizar/$1');
    $routes->post('eliminar/(:segment)', 'Repuestos::eliminar/$1');
    
    // Rutas para gestión de stock
    $routes->get('alertas-stock', 'Repuestos::alertasStock');
    $routes->post('entrada-stock', 'Repuestos::entradaStock');
    $routes->post('ajuste-stock', 'Repuestos::ajusteStock');
    $routes->get('movimientosStock', 'Repuestos::movimientosStock');
    $routes->get('estadisticas-stock', 'Repuestos::estadisticasStock');
    $routes->get('movimientos', 'Repuestos::vistaMovimientos');
});

// Rutas AJAX para repuestos
$routes->post('repuestos/getRepuestos', 'Repuestos::getRepuestos', ['filter' => 'role']);
$routes->post('repuestos/deleteRepuesto', 'Repuestos::deleteRepuesto', ['filter' => 'role']);

// Rutas para Órdenes de Trabajo (accesibles para todos los roles autenticados)
$routes->group('ordenes', ['filter' => 'role'], function($routes) {
    $routes->get('/', 'Ordenes::index');
    $routes->get('listar', 'Ordenes::listar');
    $routes->get('crear', 'Ordenes::crear');
    $routes->post('guardar', 'Ordenes::guardar');
    $routes->get('editar/(:segment)', 'Ordenes::editar/$1');
    $routes->post('actualizar/(:segment)', 'Ordenes::actualizar/$1');
    $routes->post('eliminar/(:segment)', 'Ordenes::eliminar/$1');
    $routes->get('ver/(:segment)', 'Ordenes::ver/$1');
    $routes->get('imprimir/(:segment)', 'Ordenes::imprimir/$1');
});

// Rutas AJAX para órdenes
$routes->get('ordenes/getVehiculosCliente/(:segment)', 'Ordenes::getVehiculosCliente/$1', ['filter' => 'role']);
$routes->get('ordenes/getMarcas', 'Ordenes::getMarcas', ['filter' => 'role']);
$routes->get('ordenes/getModelos/(:segment)', 'Ordenes::getModelos/$1', ['filter' => 'role']);
$routes->get('ordenes/getRepuestos', 'Ordenes::getRepuestos', ['filter' => 'role']);
$routes->get('ordenes/verificarCliente/(:segment)', 'Ordenes::verificarCliente/$1', ['filter' => 'role']);
$routes->get('ordenes/verificarVehiculo/(:segment)', 'Ordenes::verificarVehiculo/$1', ['filter' => 'role']);

// Rutas de Reportes (solo para administradores)
$routes->group('reportes', ['filter' => 'role'], function($routes) {
    $routes->get('/', 'Reportes::index');
    
    // Reporte de Movimientos de Stock
    $routes->get('movimientos-stock', 'Reportes::movimientosStock');
    $routes->get('get-movimientos-stock', 'Reportes::getMovimientosStock');
    
    // Nuevos Reportes de Órdenes
    $routes->get('ordenes-cliente', 'Reportes::ordenesPorCliente');
    $routes->get('ordenes-estado', 'Reportes::ordenesPorEstado');
    $routes->get('get-ordenes-cliente', 'Reportes::getOrdenesPorCliente');
    $routes->get('get-ordenes-estado', 'Reportes::getOrdenesPorEstado');
    
    // Nuevos Reportes Financieros
    $routes->get('total-recaudado', 'Reportes::totalRecaudado');
    $routes->get('repuestos-utilizados', 'Reportes::repuestosUtilizados');
    $routes->get('get-total-recaudado', 'Reportes::getTotalRecaudado');
    $routes->get('get-repuestos-utilizados', 'Reportes::getRepuestosUtilizados');
});
