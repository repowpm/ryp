<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAdminUser extends Migration
{
    public function up()
    {
        try {
            // Generar correo único usando la función de la BD
            $correo = $this->db->query("
                SELECT WP_FN_GenerarCorreoUnico('Administrador', 'Sistema') as correo
            ")->getRow()->correo;

            // Intentar usar el procedimiento almacenado primero
            $this->db->query("
                CALL WP_MD_InsertarUsuario(
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
        } catch (\Exception $e) {
            // Si falla el procedimiento, usar inserción directa
            $this->db->table('WP_MD_Usuarios')->insert([
                'username' => 'admin',
                'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
                'nombres' => 'Administrador',
                'apellido_paterno' => 'Sistema',
                'apellido_materno' => '',
                'correo' => $correo ?? 'administrador.sistema@rapidoyfurioso.cl',
                'telefono' => '+56912345678',
                'id_rol' => 1,
                'id_estado_usuario' => 1,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'usuario_creacion' => 'SISTEMA'
            ]);
        }
    }

    public function down()
    {
        // Eliminar usuario administrador
        $this->db->query("
            CALL WP_MD_EliminarUsuario('US000001', 'SISTEMA')
        ");
    }
} 