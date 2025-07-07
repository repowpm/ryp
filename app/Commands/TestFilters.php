<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestFilters extends BaseCommand
{
    protected $group       = 'Filters';
    protected $name        = 'filters:test';
    protected $description = 'Verifica que los filtros se carguen correctamente';

    public function run(array $params)
    {
        CLI::write('Verificando filtros...', 'yellow');
        
        try {
            // Cargar la configuración de filtros
            $filters = new \Config\Filters();
            
            CLI::write('Filtros definidos en la configuración:', 'cyan');
            foreach ($filters->aliases as $alias => $class) {
                CLI::write('  ' . $alias . ' => ' . $class, 'white');
            }
            
            // Verificar que las clases de filtros existan
            CLI::write('Verificando existencia de clases de filtros:', 'cyan');
            
            $authFilterExists = class_exists('\App\Filters\AuthFilter');
            $adminFilterExists = class_exists('\App\Filters\AdminFilter');
            
            if ($authFilterExists) {
                CLI::write('  ✓ AuthFilter existe', 'green');
            } else {
                CLI::error('  ✗ AuthFilter no existe');
            }
            
            if ($adminFilterExists) {
                CLI::write('  ✓ AdminFilter existe', 'green');
            } else {
                CLI::error('  ✗ AdminFilter no existe');
            }
            
            // Verificar que implementen la interfaz correcta
            if ($authFilterExists) {
                $authFilter = new \App\Filters\AuthFilter();
                if ($authFilter instanceof \CodeIgniter\Filters\FilterInterface) {
                    CLI::write('  ✓ AuthFilter implementa FilterInterface', 'green');
                } else {
                    CLI::error('  ✗ AuthFilter no implementa FilterInterface');
                }
            }
            
            if ($adminFilterExists) {
                $adminFilter = new \App\Filters\AdminFilter();
                if ($adminFilter instanceof \CodeIgniter\Filters\FilterInterface) {
                    CLI::write('  ✓ AdminFilter implementa FilterInterface', 'green');
                } else {
                    CLI::error('  ✗ AdminFilter no implementa FilterInterface');
                }
            }
            
            CLI::write('Verificación de filtros completada.', 'green');
            
        } catch (\Exception $e) {
            CLI::error('Error al verificar filtros: ' . $e->getMessage());
        }
    }
} 