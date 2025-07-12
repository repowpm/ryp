<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CreateAdmin extends BaseCommand
{
    protected $group       = 'Auth';
    protected $name        = 'auth:create-admin';
    protected $description = 'Crea un usuario administrador para el sistema';

    public function run(array $params)
    {
        CLI::write('Creando usuario administrador...', 'yellow');
        
        try {
            // Obtener la instancia de la base de datos
            $db = \Config\Database::connect();
            
            // Verificar si el usuario admin ya existe
            $existingUser = $db->table('WP_MD_Usuarios')
                ->where('username', 'admin')
                ->get()
                ->getRow();

            if ($existingUser) {
                CLI::error('El usuario administrador ya existe.');
                return;
            }

            // Generar correo único usando la función de la BD
            $correo = $db->query("
                SELECT WP_FN_GenerarCorreo('Administrador', 'Sistema') as correo
            ")->getRow()->correo;

            // Intentar usar el procedimiento almacenado primero
            try {
                $db->query("
                    CALL WP_SP_INSERTAR_USUARIO(
                        'admin', 
                        '" . password_hash('admin123', PASSWORD_DEFAULT) . "',
                        'Administrador',
                        'Sistema',
                        '',
                        '" . $correo . "',
                        '+56912345678',
                        1, -- id_rol (Administrador)
                        1, -- id_estado_usuario (activo)
                        'SISTEMA'
                    )
                ");
                
                CLI::write('Usuario administrador creado exitosamente usando procedimiento almacenado.', 'green');
            } catch (\Exception $e) {
                // Si falla el procedimiento, usar inserción directa
                CLI::write('Procedimiento almacenado no disponible, usando inserción directa...', 'yellow');
                
                $db->table('WP_MD_Usuarios')->insert([
                    'username' => 'admin',
                    'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
                    'nombres' => 'Administrador',
                    'apellido_paterno' => 'Sistema',
                    'apellido_materno' => '',
                    'correo' => $correo,
                    'telefono' => '+56912345678',
                    'id_rol' => 1,
                    'id_estado_usuario' => 1,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'usuario_creacion' => 'SISTEMA'
                ]);

                CLI::write('Usuario administrador creado exitosamente usando inserción directa.', 'green');
            }

            CLI::write('Credenciales de acceso:', 'cyan');
            CLI::write('Username: admin', 'white');
            CLI::write('Password: admin123', 'white');
            CLI::write('Correo: ' . $correo, 'white');
            
        } catch (\Exception $e) {
            CLI::error('Error al crear usuario administrador: ' . $e->getMessage());
        }
    }
} 