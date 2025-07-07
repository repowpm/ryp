<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        // Verificar si el usuario está logueado
        if (!session()->get('id_usuario')) {
            session()->setFlashdata('swal', [
                'title' => 'Acceso Denegado',
                'text' => 'Debes iniciar sesión para acceder al sistema',
                'icon' => 'warning'
            ]);
            return redirect()->to('auth');
        }

        return view('dashboard/home', [
            'title' => 'Dashboard - Taller Rápido y Furioso'
        ]);
    }
} 