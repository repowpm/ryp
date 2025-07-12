<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestAlertasCompletas extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'test:alertas-completas';
    protected $description = 'Probar la funcionalidad completa de alertas de stock';

    public function run(array $params)
    {
        try {
            $db = \Config\Database::connect();
            
            CLI::write('=== PRUEBA COMPLETA DE ALERTAS DE STOCK ===', 'yellow');
            
            // 1. Verificar procedimiento almacenado
            CLI::write('🔍 1. Verificando procedimiento almacenado...', 'blue');
            
            $result = $db->query("SHOW PROCEDURE STATUS WHERE Name = 'WP_SP_OBTENER_ALERTAS_STOCK'");
            $procedures = $result->getResultArray();
            
            if (empty($procedures)) {
                CLI::error('❌ El procedimiento WP_SP_OBTENER_ALERTAS_STOCK no existe');
                return;
            }
            
            CLI::write('✅ Procedimiento encontrado', 'green');
            
            // 2. Ejecutar procedimiento directamente
            CLI::write('🔍 2. Ejecutando procedimiento directamente...', 'blue');
            
            $result = $db->query("CALL WP_SP_OBTENER_ALERTAS_STOCK()");
            $alertas = $result->getResultArray();
            
            CLI::write('📊 Resultados directos:', 'blue');
            CLI::write('Total de alertas: ' . count($alertas), 'green');
            
            if (empty($alertas)) {
                CLI::write('ℹ️ No hay alertas de stock (esto es normal si no hay repuestos con stock bajo)', 'yellow');
            } else {
                foreach ($alertas as $alerta) {
                    $icon = $alerta['nivel_alerta'] === 'CRÍTICO' ? '🔴' : '🟡';
                    CLI::write($icon . ' ' . $alerta['nombre'] . ' - Stock: ' . $alerta['stock'] . ' (' . $alerta['nivel_alerta'] . ')', 'white');
                }
            }
            
            // 3. Simular llamada al controlador
            CLI::write('🔍 3. Simulando llamada al controlador...', 'blue');
            
            // Crear una instancia del controlador
            $controller = new \App\Controllers\Repuestos();
            
            // Simular una petición AJAX
            $request = \Config\Services::request();
            $response = \Config\Services::response();
            
            // Llamar al método alertasStock
            $result = $controller->alertasStock();
            
            CLI::write('✅ Respuesta del controlador obtenida', 'green');
            
            $db->close();
            CLI::write('\n✅ Prueba completa finalizada exitosamente', 'green');
            CLI::write('💡 Si las alertas no aparecen en el frontend, verifica:', 'yellow');
            CLI::write('   - La consola del navegador para errores JavaScript', 'white');
            CLI::write('   - Que el archivo repuestos.js se esté cargando correctamente', 'white');
            CLI::write('   - Que la ruta /repuestos/alertas-stock sea accesible', 'white');
            
        } catch (\Exception $e) {
            CLI::error('❌ Error: ' . $e->getMessage());
        }
    }
} 