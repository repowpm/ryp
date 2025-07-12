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

        // Obtener alertas de stock
        $alertasStock = [];
        try {
            $db = \Config\Database::connect();
            $result = $db->query("CALL WP_SP_OBTENER_ALERTAS_STOCK()");
            $alertasStock = $result->getResultArray();
        } catch (\Exception $e) {
            // Silenciar errores de alertas para no afectar el dashboard
            log_message('error', 'Error al obtener alertas de stock: ' . $e->getMessage());
        }

        return view('dashboard/home', [
            'title' => 'Dashboard - Taller Rápido y Furioso',
            'alertasStock' => $alertasStock
        ]);
    }
} 