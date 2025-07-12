<?php

namespace App\Models;

use CodeIgniter\Model;

class OrdenRepuestoModel extends Model
{
    protected $table = 'wp_md_ordenes_repuestos';
    protected $primaryKey = 'id_orden_repuesto';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'id_orden_repuesto', 'id_orden', 'id_repuesto', 'cantidad', 
        'precio_unitario', 'subtotal', 'descuento',
        'fecha_creacion', 'fecha_modificacion', 
        'usuario_creacion', 'usuario_modificacion'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fecha_creacion';
    protected $updatedField = 'fecha_modificacion';

    // Validation
    protected $validationRules = [
        'id_orden' => 'required',
        'id_repuesto' => 'required',
        'cantidad' => 'required|integer|greater_than[0]',
        'precio_unitario' => 'required|integer|greater_than_equal_to[0]'
    ];

    protected $validationMessages = [
        'id_orden' => [
            'required' => 'La orden es obligatoria'
        ],
        'id_repuesto' => [
            'required' => 'El repuesto es obligatorio'
        ],
        'cantidad' => [
            'required' => 'La cantidad es obligatoria',
            'integer' => 'La cantidad debe ser un número entero',
            'greater_than' => 'La cantidad debe ser mayor a 0'
        ],
        'precio_unitario' => [
            'required' => 'El precio unitario es obligatorio',
            'integer' => 'El precio debe ser un número entero',
            'greater_than_equal_to' => 'El precio no puede ser negativo'
        ]
    ];

    // =========================
    // MÉTODOS DE CONSULTA
    // =========================

    /**
     * Obtener repuestos de una orden específica
     */
    public function getRepuestosOrden($id_orden)
    {
        $db = \Config\Database::connect();
        
        $sql = "
            SELECT 
                orr.id_orden_repuesto,
                orr.id_orden,
                orr.id_repuesto,
                orr.cantidad,
                orr.precio_unitario as repuesto_precio,
                orr.subtotal,
                orr.descuento,
                orr.fecha_creacion,
                orr.usuario_creacion,
                r.nombre as repuesto_nombre,
                r.categoria as repuesto_categoria,
                r.stock as repuesto_stock
            FROM wp_md_ordenes_repuestos orr
            INNER JOIN wp_md_repuestos r ON r.id_repuesto = orr.id_repuesto
            WHERE orr.id_orden = ?
            ORDER BY orr.fecha_creacion ASC
        ";
        
        return $db->query($sql, [$id_orden])->getResultArray();
    }

    /**
     * Obtener total de repuestos de una orden
     */
    public function getTotalRepuestosOrden($id_orden)
    {
        $db = \Config\Database::connect();
        
        $sql = "
            SELECT 
                COUNT(*) as total_repuestos,
                SUM(orr.subtotal) as subtotal_repuestos,
                SUM(orr.descuento) as total_descuentos
            FROM wp_md_ordenes_repuestos orr
            WHERE orr.id_orden = ?
        ";
        
        return $db->query($sql, [$id_orden])->getRow();
    }

    /**
     * Verificar stock disponible para un repuesto
     */
    public function verificarStock($id_repuesto, $cantidad_solicitada)
    {
        $db = \Config\Database::connect();
        
        $sql = "
            SELECT stock 
            FROM wp_md_repuestos 
            WHERE id_repuesto = ?
        ";
        
        $result = $db->query($sql, [$id_repuesto])->getRow();
        
        if (!$result) {
            return false;
        }
        
        return $result->stock >= $cantidad_solicitada;
    }

    /**
     * Obtener repuestos con stock bajo
     */
    public function getRepuestosStockBajo($limite = 10)
    {
        $db = \Config\Database::connect();
        
        $sql = "
            SELECT 
                id_repuesto,
                nombre,
                categoria,
                stock
            FROM wp_md_repuestos 
            WHERE stock <= 10
            ORDER BY stock ASC
            LIMIT ?
        ";
        
        return $db->query($sql, [$limite])->getResultArray();
    }

    // =========================
    // MÉTODOS DE INSERCIÓN Y ACTUALIZACIÓN
    // =========================

    /**
     * Agregar repuesto a una orden
     * El trigger automáticamente generará el ID y calculará el subtotal
     */
    public function agregarRepuestoOrden($datos)
    {
        try {
            // Verificar stock disponible
            if (!$this->verificarStock($datos['id_repuesto'], $datos['cantidad'])) {
                return [
                    'success' => false,
                    'error' => 'Stock insuficiente para el repuesto seleccionado'
                ];
            }

            // Preparar datos para inserción
            $datosInsert = [
                'id_orden' => $datos['id_orden'],
                'id_repuesto' => $datos['id_repuesto'],
                'cantidad' => (int)$datos['cantidad'],
                'precio_unitario' => (int)$datos['precio_unitario'],
                'descuento' => (int)($datos['descuento'] ?? 0),
                'usuario_creacion' => $datos['usuario'] ?? 'SISTEMA'
            ];

            // El trigger automáticamente:
            // - Generará id_orden_repuesto
            // - Calculará subtotal = cantidad * precio_unitario
            // - Establecerá fecha_creacion y usuario_creacion

            $this->insert($datosInsert);

            return [
                'success' => true,
                'message' => 'Repuesto agregado correctamente'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Error al agregar repuesto: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar repuesto de una orden
     */
    public function actualizarRepuestoOrden($id_orden_repuesto, $datos)
    {
        try {
            // Verificar stock disponible si se cambia la cantidad
            if (isset($datos['cantidad'])) {
                $repuestoActual = $this->find($id_orden_repuesto);
                if (!$repuestoActual) {
                    return [
                        'success' => false,
                        'error' => 'Repuesto de orden no encontrado'
                    ];
                }

                $diferenciaCantidad = $datos['cantidad'] - $repuestoActual['cantidad'];
                if ($diferenciaCantidad > 0) {
                    if (!$this->verificarStock($repuestoActual['id_repuesto'], $diferenciaCantidad)) {
                        return [
                            'success' => false,
                            'error' => 'Stock insuficiente para la cantidad solicitada'
                        ];
                    }
                }
            }

            // Preparar datos para actualización
            $datosUpdate = [
                'cantidad' => (int)$datos['cantidad'],
                'precio_unitario' => (int)$datos['precio_unitario'],
                'descuento' => (int)($datos['descuento'] ?? 0),
                'usuario_modificacion' => $datos['usuario'] ?? 'SISTEMA'
            ];

            // El trigger automáticamente:
            // - Calculará subtotal = cantidad * precio_unitario
            // - Establecerá fecha_modificacion y usuario_modificacion

            $this->update($id_orden_repuesto, $datosUpdate);

            return [
                'success' => true,
                'message' => 'Repuesto actualizado correctamente'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Error al actualizar repuesto: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Eliminar repuesto de una orden
     */
    public function eliminarRepuestoOrden($id_orden_repuesto, $usuario = 'SISTEMA')
    {
        try {
            $repuesto = $this->find($id_orden_repuesto);
            if (!$repuesto) {
                return [
                    'success' => false,
                    'error' => 'Repuesto de orden no encontrado'
                ];
            }

            $this->delete($id_orden_repuesto);

            return [
                'success' => true,
                'message' => 'Repuesto eliminado correctamente'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Error al eliminar repuesto: ' . $e->getMessage()
            ];
        }
    }

    // =========================
    // MÉTODOS DE REPORTES
    // =========================

    /**
     * Obtener estadísticas de repuestos por período
     */
    public function getEstadisticasRepuestos($fecha_inicio, $fecha_fin)
    {
        $db = \Config\Database::connect();
        
        $sql = "
            SELECT 
                r.id_repuesto,
                r.nombre as repuesto_nombre,
                r.categoria as repuesto_categoria,
                COUNT(orr.id_orden_repuesto) as veces_utilizado,
                SUM(orr.cantidad) as cantidad_total,
                SUM(orr.subtotal) as valor_total,
                AVG(orr.precio_unitario) as precio_promedio
            FROM wp_md_repuestos r
            LEFT JOIN wp_md_ordenes_repuestos orr ON r.id_repuesto = orr.id_repuesto
            LEFT JOIN wp_md_orden_trabajo ot ON ot.id_orden = orr.id_orden
            WHERE (ot.fecha_registro BETWEEN ? AND ?) OR ot.fecha_registro IS NULL
            GROUP BY r.id_repuesto, r.nombre, r.categoria
            ORDER BY veces_utilizado DESC, valor_total DESC
        ";
        
        return $db->query($sql, [$fecha_inicio, $fecha_fin])->getResultArray();
    }

    /**
     * Obtener repuestos más utilizados
     */
    public function getRepuestosMasUtilizados($limite = 10)
    {
        $db = \Config\Database::connect();
        
        $sql = "
            SELECT 
                r.id_repuesto,
                r.nombre as repuesto_nombre,
                r.categoria as repuesto_categoria,
                COUNT(orr.id_orden_repuesto) as veces_utilizado,
                SUM(orr.cantidad) as cantidad_total,
                SUM(orr.subtotal) as valor_total
            FROM wp_md_repuestos r
            LEFT JOIN wp_md_ordenes_repuestos orr ON r.id_repuesto = orr.id_repuesto
            GROUP BY r.id_repuesto, r.nombre, r.categoria
            ORDER BY veces_utilizado DESC, valor_total DESC
            LIMIT ?
        ";
        
        return $db->query($sql, [$limite])->getResultArray();
    }
} 