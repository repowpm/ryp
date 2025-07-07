<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Verificar si el usuario está logueado
        if (!session()->get('id_usuario')) {
            session()->setFlashdata('swal', [
                'title' => 'Acceso Denegado',
                'text' => 'Debes iniciar sesión para acceder a esta sección',
                'icon' => 'warning'
            ]);
            return redirect()->to('auth');
        }

        // Verificar si el usuario es administrador (id_rol = 1)
        if (session()->get('id_rol') != 1) {
            session()->setFlashdata('swal', [
                'title' => 'Acceso Restringido',
                'text' => 'No tienes permisos para acceder a esta sección',
                'icon' => 'error'
            ]);
            return redirect()->to('dashboard');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No hacer nada después de la petición
    }
} 