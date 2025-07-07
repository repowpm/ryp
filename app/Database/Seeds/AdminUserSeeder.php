<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run($params = [])
    {
        $db = \Config\Database::connect();
        $db->query('DELETE FROM WP_MD_Usuarios');
        $db->query('ALTER TABLE WP_MD_Usuarios AUTO_INCREMENT = 1');
        $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $db->table('WP_MD_Usuarios')->insert([
            'run' => '11111111-1',
            'username' => 'admin',
            'password_hash' => $password_hash,
            'nombres' => 'Walter',
            'apellido_paterno' => 'Paredes',
            'apellido_materno' => '',
            'correo' => 'walter.paredes@rapidoyfurioso.cl',
            'telefono' => '+56955555555',
            'id_rol' => 1,
            'id_estado_usuario' => 1,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'usuario_creacion' => 'SISTEMA'
        ]);

        echo "Username: admin\n";
        echo "Password: admin123\n";
        echo "Correo: walter.paredes@rapidoyfurioso.cl\n";
    }
} 