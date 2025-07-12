<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CreateStockValidationProcedure extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'create:stock-validation-procedure';
    protected $description = 'Crear procedimiento de validación de stock para órdenes';

    public function run(array $params)
    {
        try {
            $db = \Config\Database::connect();
            
            CLI::write('=== CREACIÓN DE PROCEDIMIENTO DE VALIDACIÓN DE STOCK ===', 'yellow');
            
            // Procedimiento para validar stock antes de procesar repuestos
            CLI::write('📋 Creando procedimiento WP_SP_VALIDAR_STOCK_REPUESTOS...', 'blue');
            
            // Primero eliminar si existe
            $db->query("DROP PROCEDURE IF EXISTS WP_SP_VALIDAR_STOCK_REPUESTOS");
            
            $sqlValidarStock = "
            CREATE PROCEDURE WP_SP_VALIDAR_STOCK_REPUESTOS(
                IN p_repuestos_json JSON,
                OUT p_error_msg VARCHAR(500)
            )
            BEGIN
                DECLARE v_repuesto_count INT DEFAULT 0;
                DECLARE v_i INT DEFAULT 0;
                DECLARE v_id_repuesto CHAR(10);
                DECLARE v_cantidad INT;
                DECLARE v_stock_disponible INT;
                DECLARE v_nombre_repuesto VARCHAR(100);
                
                SET p_error_msg = NULL;
                
                -- Contar repuestos en el JSON
                SET v_repuesto_count = JSON_LENGTH(p_repuestos_json);
                
                validar_loop: WHILE v_i < v_repuesto_count DO
                    -- Extraer datos del repuesto
                    SET v_id_repuesto = JSON_UNQUOTE(JSON_EXTRACT(p_repuestos_json, CONCAT('$[', v_i, '].id_repuesto')));
                    SET v_cantidad = JSON_EXTRACT(p_repuestos_json, CONCAT('$[', v_i, '].cantidad'));
                    
                    -- Verificar stock disponible
                    SELECT stock, nombre INTO v_stock_disponible, v_nombre_repuesto
                    FROM wp_md_repuestos
                    WHERE id_repuesto = v_id_repuesto;
                    
                    IF v_stock_disponible IS NULL THEN
                        SET p_error_msg = CONCAT('El repuesto con ID ', v_id_repuesto, ' no existe');
                        LEAVE validar_loop;
                    END IF;
                    
                    IF v_stock_disponible < v_cantidad THEN
                        SET p_error_msg = CONCAT('Stock insuficiente para el repuesto ', v_nombre_repuesto, '. Stock disponible: ', v_stock_disponible, ', Cantidad solicitada: ', v_cantidad);
                        LEAVE validar_loop;
                    END IF;
                    
                    SET v_i = v_i + 1;
                END WHILE validar_loop;
            END
            ";
            
            $db->query($sqlValidarStock);
            CLI::write('✅ Procedimiento WP_SP_VALIDAR_STOCK_REPUESTOS creado exitosamente', 'green');
            
            $db->close();
            CLI::write('\n✅ Procedimiento de validación de stock creado exitosamente', 'green');
            
        } catch (\Exception $e) {
            CLI::error('❌ Error: ' . $e->getMessage());
        }
    }
} 