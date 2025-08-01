<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table = 'wp_md_usuarios';
    protected $primaryKey = 'id_usuario';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'run', 'username', 'password_hash', 'nombres', 'apellido_paterno', 
        'apellido_materno', 'correo', 'telefono', 'direccion', 'id_rol', 'id_estado_usuario',
        'created_at', 'usuario_creacion', 'updated_at', 
        'usuario_actualizacion', 'deleted_at', 'usuario_eliminacion'
    ];

    public function autenticar($username, $password)
    {
        $usuario = $this->select('
            u.id_usuario, u.username, u.password_hash, u.nombres, 
            u.apellido_paterno, u.apellido_materno, u.correo, u.telefono,
            u.id_rol, u.id_estado_usuario,
            r.nombre_rol
        ')
        ->from('wp_md_usuarios u')
        ->join('wp_md_roles r', 'u.id_rol = r.id_rol')
        ->where('u.username', $username)
        ->first();

        if (!$usuario) {
            return ['status' => 'not_found'];
        }
        if ($usuario['id_estado_usuario'] != 1) {
            return ['status' => 'inactive'];
        }
        if (!password_verify($password, $usuario['password_hash'])) {
            return ['status' => 'wrong_password'];
        }
        return ['status' => 'success', 'usuario' => $usuario];
    }

    public function crearUsuario($datos)
    {
        try {
            // Generar correo único usando la función de la BD
            try {
                $correo = $this->db->query("
                    SELECT WP_FN_GENERAR_CORREO(?, ?) as correo
                ", [$datos['nombres'], $datos['apellido_paterno']])->getRow()->correo;
                if (!$correo) {
                    throw new \Exception('No se pudo generar un correo único.');
                }
            } catch (\Exception $e) {
                // Si falla la función, lanzar excepción y no crear el usuario
                throw new \Exception('Error al generar correo único: ' . $e->getMessage());
            }

            // Encriptar contraseña
            $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);

            // Preparar datos para inserción
            $datosInsert = [
                'run' => $datos['run'],
                'username' => $datos['username'],
                'password_hash' => $password_hash,
                'nombres' => $datos['nombres'],
                'apellido_paterno' => $datos['apellido_paterno'],
                'apellido_materno' => $datos['apellido_materno'] ?? null,
                'correo' => $correo,
                'telefono' => $datos['telefono'] ?? null,
                'direccion' => $datos['direccion'] ?? null,
                'id_rol' => $datos['id_rol'],
                'id_estado_usuario' => $datos['id_estado_usuario'] ?? 1,
                'created_at' => date('Y-m-d H:i:s'),
                'usuario_creacion' => $datos['usuario_creacion'] ?? 'SISTEMA'
            ];

            // Intentar usar procedimiento almacenado
            try {
                $result = $this->db->query("
                    CALL WP_SP_INSERTAR_USUARIO(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ", [
                    $datosInsert['run'],
                    $datosInsert['username'],
                    $datosInsert['password_hash'],
                    $datosInsert['nombres'],
                    $datosInsert['apellido_paterno'],
                    $datosInsert['apellido_materno'],
                    $datosInsert['correo'],
                    $datosInsert['telefono'],
                    $datosInsert['direccion'],
                    $datosInsert['id_rol'],
                    $datosInsert['id_estado_usuario'],
                    $datosInsert['usuario_creacion']
                ]);
                
                // Obtener el ID del usuario creado
                $id = $this->db->query("SELECT LAST_INSERT_ID() as id")->getRow()->id;
                return $id;
            } catch (\Exception $e) {
                if (strpos($e->getMessage(), 'Duplicate') !== false || strpos($e->getMessage(), 'duplicada') !== false) {
                    throw new \Exception('El usuario o correo ya existe.');
                }
                throw $e;
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al crear usuario: ' . $e->getMessage());
            throw $e;
        }
    }

    public function obtenerUsuarios()
    {
        return $this->select('
            u.id_usuario, u.username, u.nombres, u.apellido_paterno, 
            u.apellido_materno, u.correo, u.telefono, u.id_rol,
            u.id_estado_usuario, u.created_at,
            r.nombre_rol,
            eu.nombre_estado
        ')
        ->from('wp_md_usuarios u')
        ->join('wp_md_roles r', 'u.id_rol = r.id_rol')
        ->join('wp_md_estados_usuario eu', 'u.id_estado_usuario = eu.id_estado_usuario')
        ->where('u.deleted_at IS NULL')
        ->findAll();
    }

    public function obtenerUsuariosDataTable($start, $length, $search, $orderColumn, $orderDir)
    {
        try {
            // Verificar qué tablas existen
            $tables = $this->db->listTables();
            
            // Verificar si las tablas relacionadas existen
            try {
                $roles = $this->db->table('wp_md_roles')->get()->getResultArray();
            } catch (\Exception $e) {
                log_message('error', 'Error al acceder a wp_md_roles: ' . $e->getMessage());
                $roles = [];
            }
            
            try {
                $estados = $this->db->table('wp_md_estados_usuario')->get()->getResultArray();
            } catch (\Exception $e) {
                log_message('error', 'Error al acceder a wp_md_estados_usuario: ' . $e->getMessage());
                $estados = [];
            }
            
            // Construir la consulta según las tablas disponibles
            if (!empty($roles) && !empty($estados)) {
                // Ambas tablas existen, usar joins completos
                $builder = $this->db->table('wp_md_usuarios u')
                    ->select('u.id_usuario, u.username, u.nombres, u.apellido_paterno, 
                             u.apellido_materno, u.correo, u.telefono, u.id_rol,
                             u.id_estado_usuario, u.created_at,
                             r.nombre_rol, eu.nombre_estado')
                    ->join('wp_md_roles r', 'u.id_rol = r.id_rol')
                    ->join('wp_md_estados_usuario eu', 'u.id_estado_usuario = eu.id_estado_usuario');
            } elseif (!empty($roles)) {
                // Solo existe la tabla de roles
                $builder = $this->db->table('wp_md_usuarios u')
                    ->select('u.id_usuario, u.username, u.nombres, u.apellido_paterno, 
                             u.apellido_materno, u.correo, u.telefono, u.id_rol,
                             u.id_estado_usuario, u.created_at,
                             r.nombre_rol, 
                             CASE WHEN u.id_estado_usuario = 1 THEN "Activo" ELSE "Inactivo" END as nombre_estado')
                    ->join('wp_md_roles r', 'u.id_rol = r.id_rol');
            } elseif (!empty($estados)) {
                // Solo existe la tabla de estados
                $builder = $this->db->table('wp_md_usuarios u')
                    ->select('u.id_usuario, u.username, u.nombres, u.apellido_paterno, 
                             u.apellido_materno, u.correo, u.telefono, u.id_rol,
                             u.id_estado_usuario, u.created_at,
                             CASE WHEN u.id_rol = 1 THEN "Administrador" ELSE "Mecánico" END as nombre_rol,
                             eu.nombre_estado')
                    ->join('wp_md_estados_usuario eu', 'u.id_estado_usuario = eu.id_estado_usuario');
            } else {
                // Ninguna tabla existe, usar valores por defecto
                $builder = $this->db->table('wp_md_usuarios u')
                    ->select('u.id_usuario, u.username, u.nombres, u.apellido_paterno, 
                             u.apellido_materno, u.correo, u.telefono, u.id_rol,
                             u.id_estado_usuario, u.created_at,
                             CASE WHEN u.id_rol = 1 THEN "Administrador" ELSE "Mecánico" END as nombre_rol,
                             CASE WHEN u.id_estado_usuario = 1 THEN "Activo" ELSE "Inactivo" END as nombre_estado');
            }

        // Aplicar búsqueda
        if (!empty($search)) {
            $builder->groupStart()
                ->like('u.username', $search)
                ->orLike('u.nombres', $search)
                ->orLike('u.apellido_paterno', $search)
                ->orLike('u.apellido_materno', $search)
                ->orLike('u.correo', $search)
                ->orLike('r.nombre_rol', $search)
                ->orLike('eu.nombre_estado', $search)
                ->groupEnd();
        }

        // Aplicar ordenamiento
        $columns = ['u.id_usuario', 'u.username', 'u.nombres', 'u.correo', 'u.telefono', 'r.nombre_rol', 'eu.nombre_estado'];
        if (isset($columns[$orderColumn])) {
            $builder->orderBy($columns[$orderColumn], $orderDir);
        }

                    $result = $builder->limit($length, $start)->get()->getResultArray();
            
            // Si no hay resultados, usar fallback
            if (empty($result)) {
                log_message('warning', 'No se encontraron resultados con joins, usando fallback');
                return $this->obtenerUsuariosSimple();
            }
            
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Error en obtenerUsuariosDataTable: ' . $e->getMessage());
            log_message('warning', 'Usando fallback sin joins');
            return $this->obtenerUsuariosSimple();
        }
    }

    public function contarUsuarios()
    {
        try {
            $count = $this->db->table('wp_md_usuarios')
                ->where('deleted_at IS NULL')
                ->countAllResults();
            
            log_message('debug', 'Total de usuarios: ' . $count);
            return $count;
        } catch (\Exception $e) {
            log_message('error', 'Error al contar usuarios: ' . $e->getMessage());
            return 0;
        }
    }

    public function obtenerUsuariosSimple()
    {
        try {
            $usuarios = $this->db->table('wp_md_usuarios')
                ->select('id_usuario, username, nombres, apellido_paterno, correo, telefono, id_rol, id_estado_usuario')
                ->where('deleted_at IS NULL')
                ->get()
                ->getResultArray();
            
            // Agregar nombres descriptivos para roles y estados
            foreach ($usuarios as &$usuario) {
                $usuario['nombre_rol'] = $usuario['id_rol'] == 1 ? 'Administrador' : 'Mecánico';
                $usuario['nombre_estado'] = $usuario['id_estado_usuario'] == 1 ? 'Activo' : 'Inactivo';
            }
            
            log_message('debug', 'Usuarios simples obtenidos: ' . json_encode($usuarios));
            return $usuarios;
        } catch (\Exception $e) {
            log_message('error', 'Error al obtener usuarios simples: ' . $e->getMessage());
            return [];
        }
    }

    public function contarUsuariosFiltrados($search)
    {
        $builder = $this->db->table('wp_md_usuarios u')
            ->join('wp_md_roles r', 'u.id_rol = r.id_rol')
            ->join('wp_md_estados_usuario eu', 'u.id_estado_usuario = eu.id_estado_usuario')
            ->where('u.deleted_at IS NULL');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('u.username', $search)
                ->orLike('u.nombres', $search)
                ->orLike('u.apellido_paterno', $search)
                ->orLike('u.apellido_materno', $search)
                ->orLike('u.correo', $search)
                ->orLike('r.nombre_rol', $search)
                ->orLike('eu.nombre_estado', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    public function obtenerUsuario($id)
    {
        return $this->select('
            u.id_usuario, u.username, u.nombres, u.apellido_paterno, 
            u.apellido_materno, u.correo, u.telefono, u.run, u.direccion, u.id_rol,
            u.id_estado_usuario, u.created_at,
            r.nombre_rol,
            eu.nombre_estado
        ')
        ->from('wp_md_usuarios u')
        ->join('wp_md_roles r', 'u.id_rol = r.id_rol')
        ->join('wp_md_estados_usuario eu', 'u.id_estado_usuario = eu.id_estado_usuario')
        ->where('u.id_usuario', $id)
        ->where('u.deleted_at IS NULL')
        ->first();
    }

    public function actualizarUsuario($id, $datos)
    {
        try {
            // Si hay nueva contraseña, encriptarla
            if (isset($datos['password'])) {
                $datos['password_hash'] = password_hash($datos['password'], PASSWORD_DEFAULT);
                unset($datos['password']);
            }

            // Proteger el correo: si no viene en los datos, obtener el actual
            if (!isset($datos['correo'])) {
                $usuarioActual = $this->find($id);
                if ($usuarioActual && isset($usuarioActual['correo'])) {
                    $datos['correo'] = $usuarioActual['correo'];
                }
            }

            // Intentar usar procedimiento almacenado
            $this->db->query(
                "CALL WP_SP_ACTUALIZAR_USUARIO(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
                    $id,
                    $datos['username'],
                    $datos['password_hash'] ?? null,
                    $datos['nombres'],
                    $datos['apellido_paterno'],
                    $datos['apellido_materno'],
                    $datos['correo'] ?? null,
                    $datos['telefono'] ?? null,
                    $datos['id_rol'],
                    $datos['id_estado_usuario'],
                    $datos['usuario_actualizacion']
                ]
            );

            return true;
        } catch (\Exception $e) {
            // Si falla el procedimiento, usar actualización directa
            $datos['updated_at'] = date('Y-m-d H:i:s');
            $this->where('id_usuario', $id)->set($datos)->update();
            return true;
        }
    }

    public function eliminarUsuario($id, $usuarioEliminacion)
    {
        try {
            // Intentar usar procedimiento almacenado
            $this->db->query("
                CALL WP_SP_ELIMINAR_USUARIO(?, ?)
            ", [$id, $usuarioEliminacion]);

            return true;
        } catch (\Exception $e) {
            // Si falla el procedimiento, usar eliminación lógica directa
            $this->where('id_usuario', $id)->set([
                'deleted_at' => date('Y-m-d H:i:s'),
                'usuario_eliminacion' => $usuarioEliminacion,
                'id_estado_usuario' => 2
            ])->update();
            return true;
        }
    }
} 