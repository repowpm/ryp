<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestAlertasStock extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'test:alertas-stock';
    protected $description = 'Probar el procedimiento de alertas de stock';

    public function run(array $params)
    {
        try {
            $db = \Config\Database::connect();
            
            CLI::write('=== PRUEBA DE ALERTAS DE STOCK ===', 'yellow');
            
            // Verificar si el procedimiento existe
            CLI::write('🔍 Verificando si el procedimiento existe...', 'blue');
            
            $result = $db->query("SHOW PROCEDURE STATUS WHERE Name = 'WP_SP_OBTENER_ALERTAS_STOCK'");
            $procedures = $result->getResultArray();
            
            if (empty($procedures)) {
                CLI::error('❌ El procedimiento WP_SP_OBTENER_ALERTAS_STOCK no existe');
                return;
            }
            
            CLI::write('✅ Procedimiento encontrado', 'green');
            
            // Ejecutar el procedimiento
            CLI::write('🔍 Ejecutando procedimiento...', 'blue');
            
            $result = $db->query("CALL WP_SP_OBTENER_ALERTAS_STOCK()");
            $alertas = $result->getResultArray();
            
            CLI::write('📊 Resultados:', 'blue');
            CLI::write('Total de alertas: ' . count($alertas), 'green');
            
            if (empty($alertas)) {
                CLI::write('ℹ️ No hay alertas de stock (esto es normal si no hay repuestos con stock bajo)', 'yellow');
            } else {
                foreach ($alertas as $alerta) {
                    $icon = $alerta['nivel_alerta'] === 'CRÍTICO' ? '🔴' : '🟡';
                    CLI::write($icon . ' ' . $alerta['nombre'] . ' - Stock: ' . $alerta['stock'] . ' (' . $alerta['nivel_alerta'] . ')', 'white');
                }
            }
            
            $db->close();
            CLI::write('\n✅ Prueba completada exitosamente', 'green');
            
        } catch (\Exception $e) {
            CLI::error('❌ Error: ' . $e->getMessage());
        }
    }
} 