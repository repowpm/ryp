<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CreateAlertasStockProcedure extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'create:alertas-stock-procedure';
    protected $description = 'Crear procedimiento almacenado para alertas de stock';

    public function run(array $params)
    {
        try {
            $db = \Config\Database::connect();
            
            CLI::write('=== CREACIÓN DE PROCEDIMIENTO PARA ALERTAS DE STOCK ===', 'yellow');
            
            // Procedimiento para obtener alertas de stock
            CLI::write('📋 Creando procedimiento WP_SP_OBTENER_ALERTAS_STOCK...', 'blue');
            
            // Primero eliminar si existe
            $db->query("DROP PROCEDURE IF EXISTS WP_SP_OBTENER_ALERTAS_STOCK");
            
            $sqlAlertas = "
            CREATE PROCEDURE WP_SP_OBTENER_ALERTAS_STOCK()
            BEGIN
                SELECT 
                    r.id_repuesto,
                    r.nombre,
                    r.categoria,
                    r.stock,
                    CASE 
                        WHEN r.stock <= 5 THEN 'CRÍTICO'
                        WHEN r.stock <= 10 THEN 'BAJO'
                        ELSE 'NORMAL'
                    END AS nivel_alerta
                FROM wp_md_repuestos r
                WHERE r.deleted_at IS NULL
                    AND r.stock <= 10
                ORDER BY r.stock ASC, r.nombre ASC;
            END
            ";
            
            $db->query($sqlAlertas);
            CLI::write('✅ Procedimiento WP_SP_OBTENER_ALERTAS_STOCK creado exitosamente', 'green');
            
            $db->close();
            CLI::write('\n✅ Procedimiento de alertas de stock creado exitosamente', 'green');
            
        } catch (\Exception $e) {
            CLI::error('❌ Error: ' . $e->getMessage());
        }
    }
} 