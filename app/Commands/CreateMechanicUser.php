<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class CreateMechanicUser extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'create:mechanic-user';
    protected $description = 'Crea un usuario mecánico de prueba';

    public function run(array $params)
    {
        CLI::write('🔧 Creando usuario mecánico de prueba...', 'blue');
        
        $db = Database::connect();
        
        try {
            // Verificar si el usuario ya existe
            $result = $db->query("SELECT id_usuario FROM wp_md_usuarios WHERE username = 'mecanico'");
            $existingUser = $result->getRow();
            
            if ($existingUser) {
                CLI::write('⚠️ El usuario mecánico ya existe', 'yellow');
                return;
            }
            
            // Obtener el siguiente ID disponible
            $result = $db->query("SELECT MAX(CAST(SUBSTRING(id_usuario, 3) AS UNSIGNED)) as max_id FROM wp_md_usuarios");
            $maxId = $result->getRow();
            $nextId = ($maxId->max_id ?? 0) + 1;
            $newId = 'US' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            
            // Crear usuario mecánico
            $db->query("INSERT INTO wp_md_usuarios (
                id_usuario, username, nombres, apellido_paterno, apellido_materno,
                correo, telefono, password_hash, id_rol, id_estado_usuario,
                usuario_creacion, created_at, updated_at
            ) VALUES (
                ?,
                'mecanico',
                'Juan',
                'Mecánico',
                'Test',
                'mecanico@rapidoyfurioso.cl',
                '+56987654321',
                '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
                2, -- id_rol (Mecánico)
                1, -- id_estado_usuario (Activo)
                'admin',
                NOW(),
                NOW()
            )", [$newId]);
            
            CLI::write('✅ Usuario mecánico creado exitosamente', 'green');
            CLI::write('📋 Credenciales de acceso:', 'cyan');
            CLI::write('   Usuario: mecanico', 'white');
            CLI::write('   Contraseña: password', 'white');
            CLI::write('   Rol: Mecánico', 'white');
            CLI::write("   ID: $newId", 'white');
            
        } catch (\Exception $e) {
            CLI::error('Error al crear usuario mecánico: ' . $e->getMessage());
            return;
        }
        
        $db->close();
    }
} 