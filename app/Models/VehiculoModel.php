<?php
namespace App\Models;

use CodeIgniter\Model;

class VehiculoModel extends Model
{
    protected $table = 'wp_md_vehiculos';
    protected $primaryKey = 'id_vehiculo';
    protected $useAutoIncrement = false; // Se genera automáticamente por trigger
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'patente',
        'marca',
        'modelo', 
        'anio',
        'id_tipo',
        'id_cliente',
        'usuario_creacion',
        'usuario_actualizacion'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'patente' => 'required|min_length[4]|max_length[10]|regex_match[/^[A-Za-z0-9]{4,10}$/]',
        'marca' => 'required|min_length[2]|max_length[50]',
        'modelo' => 'required|min_length[2]|max_length[50]',
        'anio' => 'required|integer|greater_than[1900]|less_than_equal_to[2030]',
        'id_tipo' => 'required|min_length[4]|max_length[5]',
        'id_cliente' => 'required|min_length[8]|max_length[10]'
    ];

    protected $validationMessages = [
        'patente' => [
            'required' => 'La patente es obligatoria',
            'min_length' => 'La patente debe tener al menos 4 caracteres',
            'max_length' => 'La patente no puede exceder 10 caracteres',
            'regex_match' => 'La patente solo puede contener letras y números'
        ],
        'marca' => [
            'required' => 'La marca es obligatoria',
            'min_length' => 'La marca debe tener al menos 2 caracteres',
            'max_length' => 'La marca no puede exceder 50 caracteres'
        ],
        'modelo' => [
            'required' => 'El modelo es obligatorio',
            'min_length' => 'El modelo debe tener al menos 2 caracteres',
            'max_length' => 'El modelo no puede exceder 50 caracteres'
        ],
        'anio' => [
            'required' => 'El año es obligatorio',
            'integer' => 'El año debe ser un número entero',
            'greater_than' => 'El año debe ser mayor a 1900',
            'less_than_equal_to' => 'El año no puede ser mayor a 2030'
        ],
        'id_tipo' => [
            'required' => 'El tipo de vehículo es obligatorio',
            'min_length' => 'El ID del tipo debe tener 5 caracteres',
            'max_length' => 'El ID del tipo debe tener 5 caracteres'
        ],
        'id_cliente' => [
            'required' => 'El cliente es obligatorio',
            'min_length' => 'El ID del cliente debe tener al menos 8 caracteres',
            'max_length' => 'El ID del cliente no puede exceder 10 caracteres'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['beforeInsert'];
    protected $afterInsert = ['afterInsert'];
    protected $beforeUpdate = ['beforeUpdate'];
    protected $afterUpdate = ['afterUpdate'];
    protected $beforeFind = ['beforeFind'];
    protected $afterFind = ['afterFind'];
    protected $beforeDelete = ['beforeDelete'];
    protected $afterDelete = ['afterDelete'];

    protected function beforeInsert(array $data)
    {
        // El ID se genera automáticamente por el trigger
        return $data;
    }

    protected function afterInsert(array $data)
    {
        // Log de auditoría
        log_message('info', 'Vehículo creado: ID ' . $data['id']);
        return $data;
    }

    protected function beforeUpdate(array $data)
    {
        return $data;
    }

    protected function afterUpdate(array $data)
    {
        // Log de auditoría
        log_message('info', 'Vehículo actualizado: ID ' . $data['id']);
        return $data;
    }

    protected function beforeFind(array $data)
    {
        return $data;
    }

    protected function afterFind(array $data)
    {
        return $data;
    }

    protected function beforeDelete(array $data)
    {
        return $data;
    }

    protected function afterDelete(array $data)
    {
        // Log de auditoría
        log_message('info', 'Vehículo eliminado: ID ' . $data['id']);
        return $data;
    }

    // Métodos personalizados
    public function insertarVehiculo($data)
    {
        $db = \Config\Database::connect();
        try {
            $db->query("CALL wp_sp_insertar_vehiculo(?, ?, ?, ?, ?, ?, ?)", [
                $data['patente'],
                $data['marca'],
                $data['modelo'],
                $data['anio'],
                $data['id_tipo'],
                $data['id_cliente'],
                $data['usuario_creacion']
            ]);
            // Obtener el vehículo insertado
            $query = $db->query("SELECT * FROM wp_md_vehiculos WHERE patente = ? ORDER BY created_at DESC LIMIT 1", [$data['patente']]);
            return $query->getRowArray();
        } catch (\Throwable $e) {
            throw new \Exception('Error al crear vehículo: ' . $e->getMessage());
        }
    }

    public function actualizarVehiculo($id, $data)
    {
        $db = \Config\Database::connect();
        try {
            $db->query("CALL wp_sp_actualizar_vehiculo(?, ?, ?, ?, ?, ?, ?, ?)", [
                $id,
                $data['patente'],
                $data['marca'],
                $data['modelo'],
                $data['anio'],
                $data['id_tipo'],
                $data['id_cliente'],
                $data['usuario_actualizacion']
            ]);
            return true;
        } catch (\Throwable $e) {
            throw new \Exception('Error al actualizar vehículo: ' . $e->getMessage());
        }
    }

    public function eliminarVehiculo($id, $usuario)
    {
        $db = \Config\Database::connect();
        try {
            $db->query("CALL wp_sp_eliminar_vehiculo(?, ?)", [
                $id,
                $usuario
            ]);
            return true;
        } catch (\Throwable $e) {
            throw new \Exception('Error al eliminar vehículo: ' . $e->getMessage());
        }
    }

    public function obtenerVehiculo($id)
    {
        return $this->where('id_vehiculo', $id)->where('deleted_at', null)->first();
    }

    public function verificarPatenteUnica($patente, $excludeId = null)
    {
        $builder = $this->where('patente', $patente)->where('deleted_at IS NULL');
        if ($excludeId) {
            $builder->where('id_vehiculo !=', $excludeId);
        }
        return $builder->first() === null;
    }

    public function getVehiculosWithClientes()
    {
        log_message('info', '=== INICIO getVehiculosWithClientes ===');
        
        $result = $this->select('
                v.id_vehiculo,
                v.patente,
                v.marca,
                v.modelo,
                v.anio,
                tv.nombre_tipo as tipo_vehiculo,
                c.nombres as cliente_nombres,
                c.apellido_paterno as cliente_apellido_paterno,
                c.apellido_materno as cliente_apellido_materno
            ')
            ->from('wp_md_vehiculos v')
            ->join('wp_md_clientes c', 'c.id_cliente = v.id_cliente', 'left')
            ->join('wp_md_tipo_vehiculo tv', 'tv.id_tipo = v.id_tipo', 'left')
            ->where('v.deleted_at IS NULL')
            ->orderBy('v.created_at', 'DESC')
            ->groupBy('v.id_vehiculo')
            ->findAll();
            
        log_message('info', 'Registros obtenidos del modelo: ' . count($result));
        if (!empty($result)) {
            log_message('info', 'Primer registro del modelo: ' . json_encode($result[0]));
        }
        log_message('info', '=== FIN getVehiculosWithClientes ===');
        
        return $result;
    }

    public function getVehiculoWithCliente($id)
    {
        return $this->select('
                v.*,
                c.nombres as cliente_nombres,
                c.apellido_paterno as cliente_apellido_paterno,
                c.apellido_materno as cliente_apellido_materno,
                c.telefono as cliente_telefono,
                c.correo as cliente_correo,
                tv.nombre_tipo as tipo_vehiculo
            ')
            ->from('wp_md_vehiculos v')
            ->join('wp_md_clientes c', 'c.id_cliente = v.id_cliente', 'left')
            ->join('wp_md_tipo_vehiculo tv', 'tv.id_tipo = v.id_tipo', 'left')
            ->where('v.id_vehiculo', $id)
            ->where('v.deleted_at IS NULL')
            ->where('c.deleted_at IS NULL')
            ->first();
    }

    public function getVehiculosByCliente($clienteId)
    {
        return $this->where('id_cliente', $clienteId)
                   ->where('deleted_at IS NULL')
                   ->findAll();
    }

    public function getVehiculosPorCliente($clienteId)
    {
        return $this->select('
                id_vehiculo,
                patente,
                marca,
                modelo,
                anio,
                id_tipo
            ')
            ->where('id_cliente', $clienteId)
            ->where('deleted_at IS NULL')
            ->findAll();
    }

    public function searchVehiculos($search)
    {
        return $this->select('
                v.*,
                c.nombres as cliente_nombres,
                c.apellido_paterno as cliente_apellido_paterno,
                c.apellido_materno as cliente_apellido_materno
            ')
            ->from('wp_md_vehiculos v')
            ->join('wp_md_clientes c', 'c.id_cliente = v.id_cliente', 'left')
            ->where('v.deleted_at IS NULL')
            ->where('c.deleted_at IS NULL')
            ->groupStart()
                ->like('v.marca', $search)
                ->orLike('v.modelo', $search)
                ->orLike('v.patente', $search)
                ->orLike('c.nombres', $search)
                ->orLike('c.apellido_paterno', $search)
                ->orLike('c.apellido_materno', $search)
            ->groupEnd()
            ->findAll();
    }

    public function getVehiculosActivos()
    {
        return $this->where('deleted_at IS NULL')->findAll();
    }

    public function getVehiculosInactivos()
    {
        return $this->where('deleted_at IS NOT NULL')->findAll();
    }

    public function cambiarEstado($id, $estado)
    {
        if ($estado === 'inactivo') {
            return $this->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);
        } else {
            return $this->update($id, ['deleted_at' => null]);
        }
    }

    public function getEstadisticas()
    {
        $total = $this->countAll();
        $activos = $this->where('deleted_at IS NULL')->countAllResults();
        $inactivos = $this->where('deleted_at IS NOT NULL')->countAllResults();

        return [
            'total' => $total,
            'activos' => $activos,
            'inactivos' => $inactivos
        ];
    }
} 