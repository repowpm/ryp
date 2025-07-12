<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Verificar si el usuario está autenticado
        if (!session()->get('id_usuario')) {
            return redirect()->to('/login');
        }

        // Obtener el rol del usuario
        $userRole = session()->get('id_rol');
        $currentPath = $request->getUri()->getPath();

        // Definir las rutas permitidas para cada rol
        $adminRoutes = [
            'dashboard',
            'clientes',
            'vehiculos', 
            'repuestos',
            'ordenes',
            'usuarios',
            'reportes'
        ];

        $mechanicRoutes = [
            'dashboard',
            'ordenes',
            'repuestos'
        ];

        // Permitir acceso a rutas específicas sin restricción
        $publicRoutes = [
            'auth/logout',
            'auth/login',
            'login',
            'auth'
        ];

        // Verificar si la ruta actual está en las rutas públicas
        foreach ($publicRoutes as $route) {
            if (strpos($currentPath, $route) !== false) {
                return;
            }
        }

        // Verificar permisos según el rol
        if ($userRole == 1) { // Administrador
            // Los administradores pueden acceder a todo
            return;
        } elseif ($userRole == 2) { // Mecánico
            // Los mecánicos solo pueden acceder a dashboard y órdenes
            $allowed = false;
            foreach ($mechanicRoutes as $route) {
                if (strpos($currentPath, $route) !== false) {
                    $allowed = true;
                    break;
                }
            }
            
            if (!$allowed) {
                // Redirigir a dashboard con mensaje de error
                session()->setFlashdata('error', 'No tienes permisos para acceder a esta sección.');
                return redirect()->to('/dashboard');
            }
        } else {
            // Rol no reconocido
            session()->setFlashdata('error', 'Rol de usuario no válido.');
            return redirect()->to('/login');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No hacer nada después de la respuesta
    }
} 