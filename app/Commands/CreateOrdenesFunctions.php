<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CreateOrdenesFunctions extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'create:ordenes-functions';
    protected $description = 'Crear funciones y triggers para el módulo de órdenes';

    public function run(array $params)
    {
        try {
            $db = \Config\Database::connect();
            
            CLI::write('=== CREACIÓN DE FUNCIONES Y TRIGGERS PARA ÓRDENES ===', 'yellow');
            
            // 1. Función para generar número de orden
            CLI::write('📋 Creando función WP_FN_GENERAR_NUMERO_ORDEN...', 'blue');
            $sqlGenerarOrden = "
            DROP FUNCTION IF EXISTS WP_FN_GENERAR_NUMERO_ORDEN;
            DELIMITER //
            CREATE FUNCTION WP_FN_GENERAR_NUMERO_ORDEN()
            RETURNS CHAR(10)
            READS SQL DATA
            DETERMINISTIC
            BEGIN
                DECLARE v_ultimo_numero INT;
                DECLARE v_nuevo_id CHAR(10);
                
                SELECT COALESCE(MAX(CAST(SUBSTRING(id_orden, 3) AS UNSIGNED)), 0)
                INTO v_ultimo_numero
                FROM wp_md_orden_trabajo
                WHERE id_orden LIKE 'OR%';
                
                SET v_nuevo_id = CONCAT('OR', LPAD(v_ultimo_numero + 1, 8, '0'));
                
                RETURN v_nuevo_id;
            END //
            DELIMITER ;
            ";
            
            $db->query($sqlGenerarOrden);
            CLI::write('✅ Función WP_FN_GENERAR_NUMERO_ORDEN creada', 'green');
            
            // 2. Función para calcular total de orden
            CLI::write('📋 Creando función WP_FN_CALCULAR_TOTAL_ORDEN...', 'blue');
            $sqlCalcularTotal = "
            DROP FUNCTION IF EXISTS WP_FN_CALCULAR_TOTAL_ORDEN;
            DELIMITER //
            CREATE FUNCTION WP_FN_CALCULAR_TOTAL_ORDEN(p_id_orden CHAR(10))
            RETURNS DECIMAL(10,2)
            READS SQL DATA
            DETERMINISTIC
            BEGIN
                DECLARE v_total DECIMAL(10,2) DEFAULT 0.00;
                
                SELECT COALESCE(SUM(subtotal), 0.00)
                INTO v_total
                FROM wp_md_ordenes_repuestos
                WHERE id_orden = p_id_orden;
                
                RETURN v_total;
            END //
            DELIMITER ;
            ";
            
            $db->query($sqlCalcularTotal);
            CLI::write('✅ Función WP_FN_CALCULAR_TOTAL_ORDEN creada', 'green');
            
            // 3. Trigger para generar ID de orden automáticamente
            CLI::write('📋 Creando trigger WP_TR_BEFORE_INSERT_ORDEN_TRABAJO...', 'blue');
            $sqlTriggerOrden = "
            DROP TRIGGER IF EXISTS WP_TR_BEFORE_INSERT_ORDEN_TRABAJO;
            DELIMITER //
            CREATE TRIGGER WP_TR_BEFORE_INSERT_ORDEN_TRABAJO
            BEFORE INSERT ON wp_md_orden_trabajo
            FOR EACH ROW
            BEGIN
                IF NEW.id_orden IS NULL OR NEW.id_orden = '' THEN
                    SET NEW.id_orden = WP_FN_GENERAR_NUMERO_ORDEN();
                END IF;
                
                IF NEW.fecha_registro IS NULL THEN
                    SET NEW.fecha_registro = NOW();
                END IF;
                
                IF NEW.id_estado IS NULL OR NEW.id_estado = '' THEN
                    SET NEW.id_estado = 'PEND';
                END IF;
            END //
            DELIMITER ;
            ";
            
            $db->query($sqlTriggerOrden);
            CLI::write('✅ Trigger WP_TR_BEFORE_INSERT_ORDEN_TRABAJO creado', 'green');
            
            // 4. Trigger para actualizar stock al agregar repuestos
            CLI::write('📋 Creando trigger WP_TR_AFTER_INSERT_ORDENES_REPUESTOS...', 'blue');
            $sqlTriggerRepuestos = "
            DROP TRIGGER IF EXISTS WP_TR_AFTER_INSERT_ORDENES_REPUESTOS;
            DELIMITER //
            CREATE TRIGGER WP_TR_AFTER_INSERT_ORDENES_REPUESTOS
            AFTER INSERT ON wp_md_ordenes_repuestos
            FOR EACH ROW
            BEGIN
                DECLARE v_stock_anterior INT;
                DECLARE v_stock_nuevo INT;
                
                -- Obtener stock anterior
                SELECT stock INTO v_stock_anterior
                FROM wp_md_repuestos
                WHERE id_repuesto = NEW.id_repuesto;
                
                -- Calcular stock nuevo
                SET v_stock_nuevo = v_stock_anterior - NEW.cantidad;
                
                -- Actualizar stock del repuesto
                UPDATE wp_md_repuestos 
                SET stock = v_stock_nuevo
                WHERE id_repuesto = NEW.id_repuesto;
                
                -- Registrar movimiento de stock
                INSERT INTO wp_md_movimientos_stock (
                    id_repuesto, tipo_movimiento, cantidad, 
                    stock_anterior, stock_nuevo, motivo, 
                    id_orden, usuario_movimiento
                ) VALUES (
                    NEW.id_repuesto,
                    'orden',
                    NEW.cantidad,
                    v_stock_anterior,
                    v_stock_nuevo,
                    CONCAT('Orden de trabajo: ', NEW.id_orden),
                    NEW.id_orden,
                    'SISTEMA'
                );
            END //
            DELIMITER ;
            ";
            
            $db->query($sqlTriggerRepuestos);
            CLI::write('✅ Trigger WP_TR_AFTER_INSERT_ORDENES_REPUESTOS creado', 'green');
            
            // 5. Trigger para actualizar total de orden cuando se modifican repuestos
            CLI::write('📋 Creando trigger WP_TR_AFTER_UPDATE_ORDENES_REPUESTOS...', 'blue');
            $sqlTriggerUpdateRepuestos = "
            DROP TRIGGER IF EXISTS WP_TR_AFTER_UPDATE_ORDENES_REPUESTOS;
            DELIMITER //
            CREATE TRIGGER WP_TR_AFTER_UPDATE_ORDENES_REPUESTOS
            AFTER UPDATE ON wp_md_ordenes_repuestos
            FOR EACH ROW
            BEGIN
                DECLARE v_total_orden DECIMAL(10,2);
                
                -- Calcular nuevo total de la orden
                SELECT COALESCE(SUM(subtotal), 0.00) INTO v_total_orden
                FROM wp_md_ordenes_repuestos
                WHERE id_orden = NEW.id_orden;
                
                -- Actualizar total de la orden
                UPDATE wp_md_orden_trabajo 
                SET subtotal = v_total_orden,
                    iva = v_total_orden * 0.19,
                    total = v_total_orden + (v_total_orden * 0.19)
                WHERE id_orden = NEW.id_orden;
            END //
            DELIMITER ;
            ";
            
            $db->query($sqlTriggerUpdateRepuestos);
            CLI::write('✅ Trigger WP_TR_AFTER_UPDATE_ORDENES_REPUESTOS creado', 'green');
            
            // 6. Trigger para actualizar total cuando se eliminan repuestos
            CLI::write('📋 Creando trigger WP_TR_AFTER_DELETE_ORDENES_REPUESTOS...', 'blue');
            $sqlTriggerDeleteRepuestos = "
            DROP TRIGGER IF EXISTS WP_TR_AFTER_DELETE_ORDENES_REPUESTOS;
            DELIMITER //
            CREATE TRIGGER WP_TR_AFTER_DELETE_ORDENES_REPUESTOS
            AFTER DELETE ON wp_md_ordenes_repuestos
            FOR EACH ROW
            BEGIN
                DECLARE v_total_orden DECIMAL(10,2);
                
                -- Calcular nuevo total de la orden
                SELECT COALESCE(SUM(subtotal), 0.00) INTO v_total_orden
                FROM wp_md_ordenes_repuestos
                WHERE id_orden = OLD.id_orden;
                
                -- Actualizar total de la orden
                UPDATE wp_md_orden_trabajo 
                SET subtotal = v_total_orden,
                    iva = v_total_orden * 0.19,
                    total = v_total_orden + (v_total_orden * 0.19)
                WHERE id_orden = OLD.id_orden;
            END //
            DELIMITER ;
            ";
            
            $db->query($sqlTriggerDeleteRepuestos);
            CLI::write('✅ Trigger WP_TR_AFTER_DELETE_ORDENES_REPUESTOS creado', 'green');
            
            $db->close();
            CLI::write('\n✅ Todas las funciones y triggers creados exitosamente', 'green');
            
        } catch (\Exception $e) {
            CLI::error('❌ Error: ' . $e->getMessage());
        }
    }
} 