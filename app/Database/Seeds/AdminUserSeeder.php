<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run($params = [])
    {
        $db = \Config\Database::connect();
        // Limpiar la tabla de usuarios
        $db->query('DELETE FROM wp_md_usuarios');
        // Generar hash para la contraseña 'admin123'
        $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
        // Insertar usuario admin con los datos exactos proporcionados
        $db->table('wp_md_usuarios')->insert([
            'id_usuario' => 'US000001',
            'run' => '15675728-4',
            'username' => 'wparedes',
            'password_hash' => $password_hash,
            'nombres' => 'Walter',
            'apellido_paterno' => 'Paredes',
            'apellido_materno' => 'Moraga',
            'correo' => 'walter.paredes@rapidoyfurioso.cl',
            'telefono' => '+56931897115',
            'direccion' => 'Yungay #1898',
            'id_rol' => 1,
            'id_estado_usuario' => 1,
            'created_at' => '2025-07-11 06:34:45',
            'updated_at' => '2025-07-11 02:54:10',
            'deleted_at' => '2025-07-11 02:54:10',
            'usuario_creacion' => 'SISTEMA',
            'usuario_actualizacion' => 'admin',
            'usuario_eliminacion' => null
        ]);

        echo "Usuario administrador creado con éxito.\n";
    }
} 