<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Verificar si el usuario está logueado
        if (!session()->get('id_usuario')) {
            // Si es una petición AJAX, devolver error JSON
            if ($request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
                return service('response')
                    ->setStatusCode(401)
                    ->setJSON(['success' => false, 'message' => 'Debes iniciar sesión para acceder a esta sección']);
            }
            
            session()->setFlashdata('swal', [
                'title' => 'Acceso Denegado',
                'text' => 'Debes iniciar sesión para acceder a esta sección',
                'icon' => 'warning'
            ]);
            return redirect()->to('auth');
        }

        // Verificar si el usuario está activo
        if (session()->get('id_estado_usuario') != 1) {
            session()->destroy();
            
            // Si es una petición AJAX, devolver error JSON
            if ($request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
                return service('response')
                    ->setStatusCode(401)
                    ->setJSON(['success' => false, 'message' => 'Tu cuenta ha sido desactivada. Contacta al administrador.']);
            }
            
            session()->setFlashdata('swal', [
                'title' => 'Cuenta Inactiva',
                'text' => 'Tu cuenta ha sido desactivada. Contacta al administrador.',
                'icon' => 'error'
            ]);
            return redirect()->to('auth');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No hacer nada después de la petición
    }
} 