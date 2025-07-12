<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Reportes extends BaseController
{
    public function __construct()
    {
        // Verificar autenticación
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }
    }

    public function index()
    {
        $data = [
            'title' => 'Reportes - Sistema de Gestión'
        ];
        return view('reportes/index', $data);
    }

    // ===== REPORTE DE MOVIMIENTOS DE STOCK =====

    public function movimientosStock()
    {
        $data = [
            'title' => 'Movimientos de Stock - Reportes'
        ];
        return view('reportes/movimientos_stock', $data);
    }

    // ===== API ENDPOINTS =====

    public function getMovimientosStock()
    {
        try {
            $fechaInicio = $this->request->getGet('fecha_inicio') ?? date('Y-m-01');
            $fechaFin = $this->request->getGet('fecha_fin') ?? date('Y-m-t');
            $idRepuesto = $this->request->getGet('id_repuesto') ?? '';

            $db = \Config\Database::connect();
            $result = $db->query("CALL WP_SP_REPORTE_MOVIMIENTOS(?, ?, ?)", [
                $fechaInicio, $fechaFin, $idRepuesto
            ]);
            $data = $result->getResultArray();
            $result->freeResult(); // Limpiar el cursor correctamente
            $db->close();      // Cerrar la conexión

            // Asegurar que siempre devolvemos un array, incluso si está vacío
            if (!is_array($data)) {
                $data = [];
            }

            return $this->response->setJSON([
                'data' => $data,
                'success' => true
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en getMovimientosStock: ' . $e->getMessage());
            return $this->response->setJSON([
                'data' => [],
                'success' => false,
                'message' => 'Error al obtener movimientos: ' . $e->getMessage()
            ]);
        }
    }

    // ===== NUEVOS REPORTES DE ÓRDENES =====

    public function ordenesPorCliente()
    {
        $data = [
            'title' => 'Órdenes por Cliente - Reportes'
        ];
        return view('reportes/ordenes_cliente', $data);
    }

    public function ordenesPorEstado()
    {
        $data = [
            'title' => 'Órdenes por Estado - Reportes'
        ];
        return view('reportes/ordenes_estado', $data);
    }

    // ===== NUEVOS REPORTES FINANCIEROS =====

    public function totalRecaudado()
    {
        $data = [
            'title' => 'Total Recaudado - Reportes'
        ];
        return view('reportes/total_recaudado', $data);
    }

    public function repuestosUtilizados()
    {
        $data = [
            'title' => 'Repuestos Más Utilizados - Reportes'
        ];
        return view('reportes/repuestos_utilizados', $data);
    }

    // ===== API ENDPOINTS PARA NUEVOS REPORTES =====

    public function getOrdenesPorCliente()
    {
        try {
            // Manejar tanto peticiones web como CLI
            if ($this->request) {
                $fechaInicio = $this->request->getGet('fecha_inicio') ?? date('Y-m-01');
                $fechaFin = $this->request->getGet('fecha_fin') ?? date('Y-m-t');
                $nombreCliente = $this->request->getGet('nombre_cliente') ?? '';
            } else {
                $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
                $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-t');
                $nombreCliente = $_GET['nombre_cliente'] ?? '';
            }

            $db = \Config\Database::connect();
            $result = $db->query("CALL WP_FN_REPORTE_ORDENES_POR_CLIENTE(?, ?, ?)", [
                $fechaInicio, $fechaFin, $nombreCliente
            ]);
            $data = $result->getResultArray();
            $result->freeResult();
            $db->close();

            return $this->response->setJSON([
                'data' => $data,
                'success' => true
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en getOrdenesPorCliente: ' . $e->getMessage());
            return $this->response->setJSON([
                'data' => [],
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ]);
        }
    }

    public function getOrdenesPorEstado()
    {
        try {
            $fechaInicio = $this->request->getGet('fecha_inicio') ?? date('Y-m-01');
            $fechaFin = $this->request->getGet('fecha_fin') ?? date('Y-m-t');

            $db = \Config\Database::connect();
            $result = $db->query("CALL WP_FN_REPORTE_ORDENES_POR_ESTADO(?, ?)", [
                $fechaInicio, $fechaFin
            ]);
            $data = $result->getResultArray();
            $result->freeResult();
            $db->close();

            return $this->response->setJSON([
                'data' => $data,
                'success' => true
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en getOrdenesPorEstado: ' . $e->getMessage());
            return $this->response->setJSON([
                'data' => [],
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ]);
        }
    }

    public function getTotalRecaudado()
    {
        try {
            $fechaInicio = $this->request->getGet('fecha_inicio') ?? date('Y-m-01');
            $fechaFin = $this->request->getGet('fecha_fin') ?? date('Y-m-t');

            $db = \Config\Database::connect();
            $result = $db->query("CALL WP_FN_REPORTE_TOTAL_RECAUDADO(?, ?)", [
                $fechaInicio, $fechaFin
            ]);
            $data = $result->getResultArray();
            $result->freeResult();
            $db->close();

            return $this->response->setJSON([
                'data' => $data,
                'success' => true
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en getTotalRecaudado: ' . $e->getMessage());
            return $this->response->setJSON([
                'data' => [],
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ]);
        }
    }

    public function getRepuestosUtilizados()
    {
        try {
            $fechaInicio = $this->request->getGet('fecha_inicio') ?? date('Y-m-01');
            $fechaFin = $this->request->getGet('fecha_fin') ?? date('Y-m-t');
            $limite = $this->request->getGet('limite') ?? 10;

            $db = \Config\Database::connect();
            $result = $db->query("CALL WP_FN_REPORTE_REPUESTOS_UTILIZADOS(?, ?, ?)", [
                $fechaInicio, $fechaFin, $limite
            ]);
            $data = $result->getResultArray();
            $result->freeResult();
            $db->close();

            return $this->response->setJSON([
                'data' => $data,
                'success' => true
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en getRepuestosUtilizados: ' . $e->getMessage());
            return $this->response->setJSON([
                'data' => [],
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ]);
        }
    }
} 