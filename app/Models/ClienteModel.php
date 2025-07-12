<?php
namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table = 'wp_md_clientes';
    protected $primaryKey = 'id_cliente';
    protected $useAutoIncrement = false; // Se genera automáticamente por trigger
    protected $allowedFields = [
        'run', 'nombres', 'apellido_paterno', 'apellido_materno', 'telefono', 'correo', 'direccion',
        'created_at', 'updated_at', 'deleted_at', 'usuario_creacion', 'usuario_actualizacion'
    ];

    public function obtenerClientes()
    {
        return $this->where('deleted_at', null)->findAll();
    }

    public function insertarCliente($data)
    {
        $db = \Config\Database::connect();
        try {
            // Usar el correo proporcionado por el usuario o generar uno si no se proporciona
            $correo = $data['correo'] ?? '';
            
            // Si no se proporcionó correo, generar uno automáticamente
            if (empty($correo)) {
                $correo = $db->query("SELECT WP_FN_GENERAR_CORREO(?, ?) as correo", [
                    $data['nombres'],
                    $data['apellido_paterno']
                ])->getRow()->correo;
                if (!$correo) {
                    throw new \Exception('No se pudo generar un correo único.');
                }
            }
            
            // Validar que el correo no exista ya en la base de datos (solo si se proporcionó)
            if (!empty($correo)) {
                $existeCorreo = $db->query("SELECT COUNT(*) as total FROM wp_md_clientes WHERE correo = ? AND deleted_at IS NULL", [$correo])->getRow()->total;
                if ($existeCorreo > 0) {
                    throw new \Exception('El correo ya existe para otro cliente.');
                }
            }
            
            // Insertar cliente usando el procedimiento almacenado
            $db->query("CALL wp_sp_insertar_cliente(?, ?, ?, ?, ?, ?, ?, ?)", [
                $data['run'],
                $data['nombres'],
                $data['apellido_paterno'],
                $data['apellido_materno'],
                $data['telefono'],
                $correo,
                $data['direccion'],
                $data['usuario_creacion']
            ]);
            
            // Obtener el cliente insertado
            $query = $db->query("SELECT * FROM wp_md_clientes WHERE run = ? ORDER BY created_at DESC LIMIT 1", [$data['run']]);
            return $query->getRowArray();
        } catch (\Throwable $e) {
            throw new \Exception('Error al crear cliente: ' . $e->getMessage());
        }
    }

    public function actualizarCliente($id, $data)
    {
        $db = \Config\Database::connect();
        $db->query("CALL wp_sp_actualizar_cliente(?, ?, ?, ?, ?, ?, ?, ?, ?)", [
            $id,
            $data['run'],
            $data['nombres'],
            $data['apellido_paterno'],
            $data['apellido_materno'],
            $data['telefono'],
            $data['correo'],
            $data['direccion'],
            $data['usuario_actualizacion']
        ]);
        return true;
    }

    public function eliminarCliente($id, $usuario)
    {
        $db = \Config\Database::connect();
        $db->query("CALL wp_sp_eliminar_cliente(?, ?)", [
            $id,
            $usuario
        ]);
        return true;
    }

    public function obtenerCliente($id)
    {
        return $this->where('id_cliente', $id)->where('deleted_at', null)->first();
    }

        public function getClientesActivos()
    {
        return $this->select('id_cliente, run, nombres, apellido_paterno, apellido_materno')
                    ->where('deleted_at IS NULL')
                    ->orderBy('nombres', 'ASC')
                    ->orderBy('apellido_paterno', 'ASC')
                    ->findAll();
    }

    public function buscarClientes($query)
    {
        $query = trim($query);
        
        if (strlen($query) < 2) {
            return [];
        }
        
        return $this->select('id_cliente, run, nombres, apellido_paterno, apellido_materno')
                   ->where('deleted_at IS NULL')
                   ->groupStart()
                       ->like('nombres', $query)
                       ->orLike('apellido_paterno', $query)
                       ->orLike('apellido_materno', $query)
                       ->orLike('run', $query)
                   ->groupEnd()
                   ->orderBy('nombres', 'ASC')
                   ->orderBy('apellido_paterno', 'ASC')
                   ->limit(10)
                   ->findAll();
    }
} 