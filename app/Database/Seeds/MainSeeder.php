<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run($params = [])
    {
        echo "Iniciando proceso de seeding...\n\n";
        
        // Ejecutar seeder de usuario administrador
        $this->call('AdminUserSeeder');
        
        echo "\nProceso de seeding completado.\n";
    }
} 