<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CreateOrdenesProcedures extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'create:ordenes-procedures';
    protected $description = 'Crear procedimientos almacenados para el módulo de órdenes integrado';

    public function run(array $params)
    {
        try {
            $db = \Config\Database::connect();
            
            CLI::write('=== CREACIÓN DE PROCEDIMIENTOS PARA ÓRDENES INTEGRADO ===', 'yellow');
            
            // 1. Procedimiento para buscar o crear cliente
            CLI::write('📋 Creando procedimiento WP_SP_BUSCAR_O_CREAR_CLIENTE...', 'blue');
            $sqlCliente = "
            DROP PROCEDURE IF EXISTS WP_SP_BUSCAR_O_CREAR_CLIENTE;
            CREATE PROCEDURE WP_SP_BUSCAR_O_CREAR_CLIENTE(
                IN p_run VARCHAR(12),
                IN p_nombres VARCHAR(100),
                IN p_apellido_paterno VARCHAR(100),
                IN p_apellido_materno VARCHAR(100),
                IN p_telefono VARCHAR(20),
                IN p_correo VARCHAR(100),
                IN p_direccion VARCHAR(200),
                IN p_usuario VARCHAR(50),
                OUT p_id_cliente CHAR(10)
            )
            BEGIN
                DECLARE v_id_cliente CHAR(10);
                DECLARE v_correo_generado VARCHAR(100);
                
                -- Buscar cliente existente por RUN
                SELECT id_cliente INTO v_id_cliente
                FROM wp_md_clientes
                WHERE run = p_run
                LIMIT 1;
                
                -- Si no existe, crear nuevo cliente
                IF v_id_cliente IS NULL THEN
                    -- Generar correo si no se proporciona
                    IF p_correo = '' OR p_correo IS NULL THEN
                        SET v_correo_generado = WP_FN_GENERAR_CORREO(p_nombres, p_apellido_paterno);
                    ELSE
                        SET v_correo_generado = p_correo;
                    END IF;
                    
                    -- Crear cliente usando procedimiento existente
                    CALL WP_SP_INSERTAR_CLIENTE(
                        p_run, p_nombres, p_apellido_paterno, p_apellido_materno,
                        p_telefono, v_correo_generado, p_direccion, p_usuario
                    );
                    
                    -- Obtener el ID del cliente creado
                    SELECT id_cliente INTO v_id_cliente
                    FROM wp_md_clientes
                    WHERE run = p_run
                    LIMIT 1;
                END IF;
                
                SET p_id_cliente = v_id_cliente;
            END;
            ";
            
            $db->query($sqlCliente);
            CLI::write('✅ Procedimiento WP_SP_BUSCAR_O_CREAR_CLIENTE creado', 'green');
            
            // 2. Procedimiento para buscar o crear vehículo
            CLI::write('📋 Creando procedimiento WP_SP_BUSCAR_O_CREAR_VEHICULO...', 'blue');
            $sqlVehiculo = "
            DROP PROCEDURE IF EXISTS WP_SP_BUSCAR_O_CREAR_VEHICULO;
            CREATE PROCEDURE WP_SP_BUSCAR_O_CREAR_VEHICULO(
                IN p_id_cliente CHAR(10),
                IN p_patente VARCHAR(10),
                IN p_marca VARCHAR(50),
                IN p_modelo VARCHAR(50),
                IN p_anio INT,
                IN p_id_tipo CHAR(5),
                IN p_usuario VARCHAR(50),
                OUT p_id_vehiculo CHAR(10)
            )
            BEGIN
                DECLARE v_id_vehiculo CHAR(10);
                
                -- Buscar vehículo existente por patente
                SELECT id_vehiculo INTO v_id_vehiculo
                FROM wp_md_vehiculos
                WHERE patente = p_patente
                LIMIT 1;
                
                -- Si no existe, crear nuevo vehículo
                IF v_id_vehiculo IS NULL THEN
                    -- Crear vehículo usando procedimiento existente
                    CALL WP_SP_INSERTAR_VEHICULO(
                        p_patente, p_marca, p_modelo, p_anio, p_id_tipo, p_id_cliente, p_usuario
                    );
                    
                    -- Obtener el ID del vehículo creado
                    SELECT id_vehiculo INTO v_id_vehiculo
                    FROM wp_md_vehiculos
                    WHERE patente = p_patente
                    LIMIT 1;
                END IF;
                
                SET p_id_vehiculo = v_id_vehiculo;
            END;
            ";
            
            $db->query($sqlVehiculo);
            CLI::write('✅ Procedimiento WP_SP_BUSCAR_O_CREAR_VEHICULO creado', 'green');
            
            // 3. Procedimiento para procesar repuestos de orden
            CLI::write('📋 Creando procedimiento WP_SP_PROCESAR_REPUESTOS_ORDEN...', 'blue');
            $sqlRepuestos = "
            DROP PROCEDURE IF EXISTS WP_SP_PROCESAR_REPUESTOS_ORDEN;
            CREATE PROCEDURE WP_SP_PROCESAR_REPUESTOS_ORDEN(
                IN p_id_orden CHAR(10),
                IN p_repuestos_json JSON,
                IN p_usuario VARCHAR(50),
                OUT p_total DECIMAL(10,2)
            )
            BEGIN
                DECLARE v_total DECIMAL(10,2) DEFAULT 0.00;
                DECLARE v_repuesto_count INT DEFAULT 0;
                DECLARE v_i INT DEFAULT 0;
                DECLARE v_id_repuesto CHAR(10);
                DECLARE v_cantidad INT;
                DECLARE v_precio_unitario DECIMAL(10,2);
                DECLARE v_subtotal DECIMAL(10,2);
                
                -- Contar repuestos en el JSON
                SET v_repuesto_count = JSON_LENGTH(p_repuestos_json);
                
                -- Procesar cada repuesto
                WHILE v_i < v_repuesto_count DO
                    -- Extraer datos del repuesto
                    SET v_id_repuesto = JSON_UNQUOTE(JSON_EXTRACT(p_repuestos_json, CONCAT('$[', v_i, '].id_repuesto')));
                    SET v_cantidad = JSON_EXTRACT(p_repuestos_json, CONCAT('$[', v_i, '].cantidad'));
                    SET v_precio_unitario = JSON_EXTRACT(p_repuestos_json, CONCAT('$[', v_i, '].precio_unitario'));
                    
                    -- Si no se especifica precio, obtenerlo de la tabla de repuestos
                    IF v_precio_unitario IS NULL OR v_precio_unitario = 0 THEN
                        SELECT precio INTO v_precio_unitario
                        FROM wp_md_repuestos
                        WHERE id_repuesto = v_id_repuesto;
                    END IF;
                    
                    -- Calcular subtotal
                    SET v_subtotal = v_cantidad * v_precio_unitario;
                    SET v_total = v_total + v_subtotal;
                    
                    -- Insertar repuesto en la orden
                    INSERT INTO wp_md_ordenes_repuestos (
                        id_orden, id_repuesto, cantidad, precio_unitario, subtotal
                    ) VALUES (
                        p_id_orden, v_id_repuesto, v_cantidad, v_precio_unitario, v_subtotal
                    );
                    
                    SET v_i = v_i + 1;
                END WHILE;
                
                SET p_total = v_total;
            END;
            ";
            
            $db->query($sqlRepuestos);
            CLI::write('✅ Procedimiento WP_SP_PROCESAR_REPUESTOS_ORDEN creado', 'green');
            
            // 3.1. Nuevo procedimiento para validar stock antes de procesar repuestos
            CLI::write('📋 Creando procedimiento WP_SP_VALIDAR_STOCK_REPUESTOS...', 'blue');
            $sqlValidarStock = "
            DROP PROCEDURE IF EXISTS WP_SP_VALIDAR_STOCK_REPUESTOS;
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
                
                -- Validar cada repuesto
                WHILE v_i < v_repuesto_count DO
                    -- Extraer datos del repuesto
                    SET v_id_repuesto = JSON_UNQUOTE(JSON_EXTRACT(p_repuestos_json, CONCAT('$[', v_i, '].id_repuesto')));
                    SET v_cantidad = JSON_EXTRACT(p_repuestos_json, CONCAT('$[', v_i, '].cantidad'));
                    
                    -- Verificar stock disponible
                    SELECT stock, nombre INTO v_stock_disponible, v_nombre_repuesto
                    FROM wp_md_repuestos
                    WHERE id_repuesto = v_id_repuesto;
                    
                    IF v_stock_disponible IS NULL THEN
                        SET p_error_msg = CONCAT('El repuesto con ID ', v_id_repuesto, ' no existe');
                        LEAVE;
                    END IF;
                    
                    IF v_stock_disponible < v_cantidad THEN
                        SET p_error_msg = CONCAT('Stock insuficiente para el repuesto \"', v_nombre_repuesto, '\". Stock disponible: ', v_stock_disponible, ', Cantidad solicitada: ', v_cantidad);
                        LEAVE;
                    END IF;
                    
                    SET v_i = v_i + 1;
                END WHILE;
            END;
            ";
            
            $db->query($sqlValidarStock);
            CLI::write('✅ Procedimiento WP_SP_VALIDAR_STOCK_REPUESTOS creado', 'green');
            
            // 4. Procedimiento principal para crear orden completa
            CLI::write('📋 Creando procedimiento WP_SP_CREAR_ORDEN_COMPLETA...', 'blue');
            $sqlOrdenCompleta = "
            DROP PROCEDURE IF EXISTS WP_SP_CREAR_ORDEN_COMPLETA;
            CREATE PROCEDURE WP_SP_CREAR_ORDEN_COMPLETA(
                IN p_run_cliente VARCHAR(12),
                IN p_nombres_cliente VARCHAR(100),
                IN p_apellido_paterno VARCHAR(100),
                IN p_apellido_materno VARCHAR(100),
                IN p_telefono_cliente VARCHAR(20),
                IN p_correo_cliente VARCHAR(100),
                IN p_direccion_cliente VARCHAR(200),
                IN p_patente_vehiculo VARCHAR(10),
                IN p_marca_vehiculo VARCHAR(50),
                IN p_modelo_vehiculo VARCHAR(50),
                IN p_anio_vehiculo INT,
                IN p_id_tipo_vehiculo CHAR(5),
                IN p_diagnostico TEXT,
                IN p_observaciones TEXT,
                IN p_repuestos_json JSON,
                IN p_usuario VARCHAR(50),
                OUT p_id_orden CHAR(10),
                OUT p_id_cliente CHAR(10),
                OUT p_id_vehiculo CHAR(10)
            )
            BEGIN
                DECLARE v_id_cliente CHAR(10);
                DECLARE v_id_vehiculo CHAR(10);
                DECLARE v_id_orden CHAR(10);
                DECLARE v_total DECIMAL(10,2) DEFAULT 0.00;
                
                -- 1. Crear o buscar cliente
                CALL WP_SP_BUSCAR_O_CREAR_CLIENTE(
                    p_run_cliente, p_nombres_cliente, p_apellido_paterno, 
                    p_apellido_materno, p_telefono_cliente, p_correo_cliente, 
                    p_direccion_cliente, p_usuario, v_id_cliente
                );
                
                -- 2. Crear o buscar vehículo
                CALL WP_SP_BUSCAR_O_CREAR_VEHICULO(
                    v_id_cliente, p_patente_vehiculo, p_marca_vehiculo, 
                    p_modelo_vehiculo, p_anio_vehiculo, p_id_tipo_vehiculo, 
                    p_usuario, v_id_vehiculo
                );
                
                -- 3. Crear orden de trabajo
                INSERT INTO wp_md_orden_trabajo (
                    id_cliente, id_vehiculo, id_estado, fecha_registro,
                    diagnostico, observaciones, usuario_creacion
                ) VALUES (
                    v_id_cliente, v_id_vehiculo, 'E002', NOW(),
                    p_diagnostico, p_observaciones, p_usuario
                );
                
                -- Obtener el ID de la orden creada
                SELECT id_orden INTO v_id_orden
                FROM wp_md_orden_trabajo
                WHERE id_cliente = v_id_cliente 
                AND id_vehiculo = v_id_vehiculo 
                AND usuario_creacion = p_usuario
                ORDER BY created_at DESC
                LIMIT 1;
                
                -- 4. Validar stock antes de procesar repuestos
                IF p_repuestos_json IS NOT NULL AND JSON_LENGTH(p_repuestos_json) > 0 THEN
                    DECLARE v_error_msg VARCHAR(500);
                    
                    -- Validar stock disponible
                    CALL WP_SP_VALIDAR_STOCK_REPUESTOS(p_repuestos_json, v_error_msg);
                    
                    IF v_error_msg IS NOT NULL THEN
                        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_error_msg;
                    END IF;
                    
                    -- Si la validación pasa, procesar repuestos
                    CALL WP_SP_PROCESAR_REPUESTOS_ORDEN(
                        v_id_orden, p_repuestos_json, p_usuario, v_total
                    );
                END IF;
                
                -- 5. Actualizar total de la orden
                UPDATE wp_md_orden_trabajo 
                SET subtotal = v_total,
                    iva = v_total * 0.19,
                    total = v_total + (v_total * 0.19)
                WHERE id_orden = v_id_orden;
                
                -- Retornar IDs
                SET p_id_orden = v_id_orden;
                SET p_id_cliente = v_id_cliente;
                SET p_id_vehiculo = v_id_vehiculo;
                
            END;
            ";
            
            $db->query($sqlOrdenCompleta);
            CLI::write('✅ Procedimiento WP_SP_CREAR_ORDEN_COMPLETA creado', 'green');
            
            // 5. Procedimiento para actualizar orden
            CLI::write('📋 Creando procedimiento WP_SP_ACTUALIZAR_ORDEN_TRABAJO...', 'blue');
            $sqlActualizar = "
            DROP PROCEDURE IF EXISTS WP_SP_ACTUALIZAR_ORDEN_TRABAJO;
            CREATE PROCEDURE WP_SP_ACTUALIZAR_ORDEN_TRABAJO(
                IN p_id_orden CHAR(10),
                IN p_id_estado CHAR(5),
                IN p_diagnostico TEXT,
                IN p_observaciones TEXT,
                IN p_nuevos_repuestos_json JSON,
                IN p_usuario VARCHAR(50)
            )
            BEGIN
                DECLARE v_total_nuevos DECIMAL(10,2) DEFAULT 0.00;
                DECLARE v_total_existente DECIMAL(10,2) DEFAULT 0.00;
                DECLARE v_total_final DECIMAL(10,2) DEFAULT 0.00;
                
                -- Actualizar información básica de la orden
                UPDATE wp_md_orden_trabajo 
                SET id_estado = p_id_estado,
                    diagnostico = p_diagnostico,
                    observaciones = p_observaciones,
                    usuario_actualizacion = p_usuario,
                    updated_at = NOW()
                WHERE id_orden = p_id_orden;
                
                -- Procesar nuevos repuestos si existen
                IF p_nuevos_repuestos_json IS NOT NULL AND JSON_LENGTH(p_nuevos_repuestos_json) > 0 THEN
                    DECLARE v_error_msg VARCHAR(500);
                    
                    -- Validar stock disponible
                    CALL WP_SP_VALIDAR_STOCK_REPUESTOS(p_nuevos_repuestos_json, v_error_msg);
                    
                    IF v_error_msg IS NOT NULL THEN
                        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_error_msg;
                    END IF;
                    
                    -- Si la validación pasa, procesar repuestos
                    CALL WP_SP_PROCESAR_REPUESTOS_ORDEN(
                        p_id_orden, p_nuevos_repuestos_json, p_usuario, v_total_nuevos
                    );
                END IF;
                
                -- Calcular total final
                SELECT COALESCE(SUM(subtotal), 0) INTO v_total_existente
                FROM wp_md_ordenes_repuestos
                WHERE id_orden = p_id_orden;
                
                SET v_total_final = v_total_existente + v_total_nuevos;
                
                -- Actualizar total de la orden
                UPDATE wp_md_orden_trabajo 
                SET total = v_total_final,
                    subtotal = v_total_final * 0.81,
                    iva = v_total_final * 0.19
                WHERE id_orden = p_id_orden;
                
            END;
            ";
            
            $db->query($sqlActualizar);
            CLI::write('✅ Procedimiento WP_SP_ACTUALIZAR_ORDEN_TRABAJO creado', 'green');
            
            // 6. Procedimiento para eliminar orden
            CLI::write('📋 Creando procedimiento WP_SP_ELIMINAR_ORDEN_TRABAJO...', 'blue');
            $sqlEliminar = "
            DROP PROCEDURE IF EXISTS WP_SP_ELIMINAR_ORDEN_TRABAJO;
            CREATE PROCEDURE WP_SP_ELIMINAR_ORDEN_TRABAJO(
                IN p_id_orden CHAR(10),
                IN p_usuario VARCHAR(50)
            )
            BEGIN
                -- Eliminación lógica de la orden
                UPDATE wp_md_orden_trabajo 
                SET deleted_at = NOW(),
                    usuario_eliminacion = p_usuario
                WHERE id_orden = p_id_orden;
                
                -- Eliminación lógica de repuestos de la orden
                UPDATE wp_md_ordenes_repuestos 
                SET deleted_at = NOW()
                WHERE id_orden = p_id_orden;
                
            END;
            ";
            
            $db->query($sqlEliminar);
            CLI::write('✅ Procedimiento WP_SP_ELIMINAR_ORDEN_TRABAJO creado', 'green');
            
            $db->close();
            CLI::write('\n✅ Todos los procedimientos creados exitosamente', 'green');
            
        } catch (\Exception $e) {
            CLI::error('❌ Error: ' . $e->getMessage());
        }
    }
} 