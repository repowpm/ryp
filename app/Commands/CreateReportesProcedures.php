<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class CreateReportesProcedures extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'create:reportes-procedures';
    protected $description = 'Crea los procedimientos almacenados para los nuevos reportes';

    public function run(array $params)
    {
        CLI::write('🔧 Creando procedimientos almacenados para reportes...', 'blue');
        
        $db = Database::connect();
        
        try {
            // 1. Procedimiento para órdenes por cliente
            CLI::write('📊 Creando procedimiento para órdenes por cliente...', 'yellow');
            
            // Eliminar procedimiento si existe
            $db->query("DROP PROCEDURE IF EXISTS WP_FN_REPORTE_ORDENES_POR_CLIENTE");
            
            $sqlOrdenesCliente = "
            CREATE PROCEDURE WP_FN_REPORTE_ORDENES_POR_CLIENTE(
                IN p_fecha_inicio DATE,
                IN p_fecha_fin DATE,
                IN p_nombre_cliente VARCHAR(200)
            )
            BEGIN
                SELECT 
                    CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS cliente,
                    COUNT(o.id_orden) AS total_ordenes,
                    CAST(COALESCE(SUM(o.total), 0) AS UNSIGNED) AS total_facturado,
                    CAST(COALESCE(AVG(o.total), 0) AS UNSIGNED) AS promedio_orden,
                    MAX(o.fecha_registro) AS ultima_orden,
                    ROUND((COUNT(o.id_orden) * 100.0 / (SELECT COUNT(*) FROM wp_md_orden_trabajo WHERE fecha_registro BETWEEN p_fecha_inicio AND p_fecha_fin)), 2) AS porcentaje_total
                FROM wp_md_clientes c
                LEFT JOIN wp_md_orden_trabajo o ON c.id_cliente = o.id_cliente 
                    AND o.fecha_registro BETWEEN p_fecha_inicio AND p_fecha_fin
                WHERE c.deleted_at IS NULL
                    AND (p_nombre_cliente = '' OR CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) LIKE CONCAT('%', p_nombre_cliente, '%'))
                GROUP BY c.id_cliente, c.nombres, c.apellido_paterno, c.apellido_materno
                HAVING total_ordenes > 0
                ORDER BY total_ordenes DESC, total_facturado DESC;
            END
            ";
            
            $db->query($sqlOrdenesCliente);
            CLI::write('✓ Procedimiento WP_FN_REPORTE_ORDENES_POR_CLIENTE creado', 'green');
            
            // 2. Procedimiento para órdenes por estado
            CLI::write('📊 Creando procedimiento para órdenes por estado...', 'yellow');
            
            // Eliminar procedimiento si existe
            $db->query("DROP PROCEDURE IF EXISTS WP_FN_REPORTE_ORDENES_POR_ESTADO");
            
            $sqlOrdenesEstado = "
            CREATE PROCEDURE WP_FN_REPORTE_ORDENES_POR_ESTADO(
                IN p_fecha_inicio DATE,
                IN p_fecha_fin DATE
            )
            BEGIN
                SELECT 
                    eo.nombre_estado AS estado,
                    COUNT(o.id_orden) AS cantidad,
                    COALESCE(SUM(o.total), 0) AS total_facturado,
                    ROUND((COUNT(o.id_orden) * 100.0 / (SELECT COUNT(*) FROM wp_md_orden_trabajo WHERE fecha_registro BETWEEN p_fecha_inicio AND p_fecha_fin)), 2) AS porcentaje,
                    COALESCE(AVG(o.total), 0) AS promedio_orden
                FROM wp_md_orden_trabajo o
                INNER JOIN wp_md_estado_orden eo ON eo.id_estado = o.id_estado
                WHERE o.fecha_registro BETWEEN p_fecha_inicio AND p_fecha_fin
                GROUP BY eo.nombre_estado
                ORDER BY cantidad DESC;
            END
            ";
            
            $db->query($sqlOrdenesEstado);
            CLI::write('✓ Procedimiento WP_FN_REPORTE_ORDENES_POR_ESTADO creado', 'green');
            
            // 3. Procedimiento para total recaudado
            CLI::write('📊 Creando procedimiento para total recaudado...', 'yellow');
            
            // Eliminar procedimiento si existe
            $db->query("DROP PROCEDURE IF EXISTS WP_FN_REPORTE_TOTAL_RECAUDADO");
            
            $sqlTotalRecaudado = "
            CREATE PROCEDURE WP_FN_REPORTE_TOTAL_RECAUDADO(
                IN p_fecha_inicio DATE,
                IN p_fecha_fin DATE
            )
            BEGIN
                SELECT 
                    DATE_FORMAT(o.fecha_registro, '%Y-%m') AS periodo,
                    COUNT(o.id_orden) AS ordenes,
                    COALESCE(SUM(o.total), 0) AS ingresos,
                    COALESCE(SUM(o.total) / DAY(LAST_DAY(o.fecha_registro)), 0) AS promedio_diario,
                    COALESCE(AVG(o.total), 0) AS promedio_orden,
                    ROUND((SUM(o.total) * 100.0 / (SELECT COALESCE(SUM(total), 0) FROM wp_md_orden_trabajo WHERE fecha_registro BETWEEN p_fecha_inicio AND p_fecha_fin)), 2) AS porcentaje
                FROM wp_md_orden_trabajo o
                WHERE o.fecha_registro BETWEEN p_fecha_inicio AND p_fecha_fin
                GROUP BY DATE_FORMAT(o.fecha_registro, '%Y-%m')
                ORDER BY periodo DESC;
            END
            ";
            
            $db->query($sqlTotalRecaudado);
            CLI::write('✓ Procedimiento WP_FN_REPORTE_TOTAL_RECAUDADO creado', 'green');
            
            // 4. Procedimiento para repuestos más utilizados
            CLI::write('📊 Creando procedimiento para repuestos más utilizados...', 'yellow');
            
            // Eliminar procedimiento si existe
            $db->query("DROP PROCEDURE IF EXISTS WP_FN_REPORTE_REPUESTOS_UTILIZADOS");
            
            $sqlRepuestosUtilizados = "
            CREATE PROCEDURE WP_FN_REPORTE_REPUESTOS_UTILIZADOS(
                IN p_fecha_inicio DATE,
                IN p_fecha_fin DATE,
                IN p_limite INT
            )
            BEGIN
                SELECT 
                    r.nombre AS repuesto,
                    COALESCE(r.categoria, 'Sin Categoría') AS categoria,
                    COALESCE(SUM(or_repuestos.cantidad), 0) AS cantidad_utilizada,
                    COALESCE(SUM(or_repuestos.cantidad * or_repuestos.precio_unitario), 0) AS valor_total,
                    COALESCE(AVG(or_repuestos.cantidad), 0) AS promedio_orden,
                    ROUND((SUM(or_repuestos.cantidad) * 100.0 / (SELECT COALESCE(SUM(or2.cantidad), 0) FROM wp_md_ordenes_repuestos or2 
                        INNER JOIN wp_md_orden_trabajo o2 ON or2.id_orden = o2.id_orden 
                        WHERE o2.fecha_registro BETWEEN p_fecha_inicio AND p_fecha_fin)), 2) AS porcentaje
                FROM wp_md_repuestos r
                LEFT JOIN wp_md_ordenes_repuestos or_repuestos ON r.id_repuesto = or_repuestos.id_repuesto
                LEFT JOIN wp_md_orden_trabajo o ON or_repuestos.id_orden = o.id_orden 
                    AND o.fecha_registro BETWEEN p_fecha_inicio AND p_fecha_fin
                WHERE r.deleted_at IS NULL
                    AND o.id_orden IS NOT NULL -- Solo repuestos realmente utilizados en órdenes
                GROUP BY r.id_repuesto, r.nombre, r.categoria
                HAVING cantidad_utilizada > 0
                ORDER BY cantidad_utilizada DESC, valor_total DESC
                LIMIT p_limite;
            END
            ";
            
            $db->query($sqlRepuestosUtilizados);
            CLI::write('✓ Procedimiento WP_FN_REPORTE_REPUESTOS_UTILIZADOS creado', 'green');
            
            CLI::write('🎉 Todos los procedimientos almacenados para reportes han sido creados exitosamente!', 'green');
            
        } catch (\Exception $e) {
            CLI::error('Error al crear procedimientos: ' . $e->getMessage());
            return;
        }
        
        $db->close();
    }
} 