<?php
namespace App\Models;

use CodeIgniter\Model;

class RepuestoModel extends Model
{
    protected $table = 'wp_md_repuestos';
    protected $primaryKey = 'id_repuesto';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';

    protected $allowedFields = [
        'id_repuesto',
        'nombre',
        'categoria',
        'precio',
        'stock',
        'usuario_creacion',
        'usuario_actualizacion'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'nombre' => 'required|min_length[2]|max_length[100]',
        'categoria' => 'max_length[50]',
        'precio' => 'required|decimal|greater_than[0]',
        'stock' => 'required|integer|greater_than_equal_to[0]'
    ];

    protected $validationMessages = [
        'nombre' => [
            'required' => 'El nombre del repuesto es obligatorio',
            'min_length' => 'El nombre debe tener al menos 2 caracteres',
            'max_length' => 'El nombre no puede exceder 100 caracteres'
        ],
        'categoria' => [
            'max_length' => 'La categoría no puede exceder 50 caracteres'
        ],
        'precio' => [
            'required' => 'El precio es obligatorio',
            'decimal' => 'El precio debe ser un número decimal',
            'greater_than' => 'El precio debe ser mayor a 0'
        ],
        'stock' => [
            'required' => 'El stock es obligatorio',
            'integer' => 'El stock debe ser un número entero',
            'greater_than_equal_to' => 'El stock no puede ser negativo'
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
        log_message('info', 'Repuesto creado: ID ' . $data['id']);
        return $data;
    }

    protected function beforeUpdate(array $data)
    {
        return $data;
    }

    protected function afterUpdate(array $data)
    {
        // Log de auditoría
        log_message('info', 'Repuesto actualizado: ID ' . $data['id']);
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
        log_message('info', 'Repuesto eliminado: ID ' . $data['id']);
        return $data;
    }

    // Métodos personalizados usando procedimientos almacenados
    public function insertarRepuesto($data)
    {
        $db = \Config\Database::connect();
        try {
            // Insertar directamente en la tabla, el trigger generará el ID
            $db->query("INSERT INTO wp_md_repuestos (
                nombre, categoria, precio, stock, usuario_creacion
            ) VALUES (?, ?, ?, ?, ?)", [
                $data['nombre'],
                $data['categoria'],
                $data['precio'],
                $data['stock'],
                $data['usuario_creacion']
            ]);
            
            // Obtener el repuesto insertado
            $query = $db->query("SELECT * FROM wp_md_repuestos WHERE nombre = ? ORDER BY created_at DESC LIMIT 1", [$data['nombre']]);
            return $query->getRowArray();
        } catch (\Throwable $e) {
            throw new \Exception('Error al crear repuesto: ' . $e->getMessage());
        }
    }

    public function actualizarRepuesto($id, $data)
    {
        $db = \Config\Database::connect();
        try {
            $db->query("CALL wp_sp_actualizar_repuesto(?, ?, ?, ?, ?, ?)", [
                $id,
                $data['nombre'],
                $data['categoria'],
                $data['precio'],
                $data['stock'],
                $data['usuario_actualizacion']
            ]);
            return true;
        } catch (\Throwable $e) {
            throw new \Exception('Error al actualizar repuesto: ' . $e->getMessage());
        }
    }

    public function eliminarRepuesto($id, $usuario)
    {
        $db = \Config\Database::connect();
        try {
            $db->query("CALL wp_sp_eliminar_repuesto(?, ?)", [
                $id,
                $usuario
            ]);
            return true;
        } catch (\Throwable $e) {
            throw new \Exception('Error al eliminar repuesto: ' . $e->getMessage());
        }
    }

    // Métodos de consulta
    public function getRepuestos()
    {
        return $this->select('
                id_repuesto,
                nombre,
                categoria,
                precio,
                stock,
                deleted_at,
                created_at,
                updated_at
            ')
            ->where('deleted_at IS NULL')
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getRepuestoById($id)
    {
        return $this->select('
                id_repuesto,
                nombre,
                categoria,
                precio,
                stock,
                deleted_at,
                created_at,
                updated_at
            ')
            ->where('id_repuesto', $id)
            ->where('deleted_at IS NULL')
            ->first();
    }

        public function getRepuestosByCategoria($categoria)
    {
        return $this->where('categoria', $categoria)
                    ->where('deleted_at IS NULL')
                    ->findAll();
    }

        public function searchRepuestos($search)
    {
        return $this->groupStart()
                    ->like('nombre', $search)
                    ->orLike('categoria', $search)
                    ->groupEnd()
                    ->where('deleted_at IS NULL')
                    ->findAll();
    }

    public function getRepuestosActivos()
    {
        return $this->where('deleted_at IS NULL')->findAll();
    }

    public function getRepuestosInactivos()
    {
        return $this->where('deleted_at IS NOT NULL')->findAll();
    }

    public function getRepuestosBajoStock($limite = 10)
    {
        return $this->where('stock <=', $limite)
                   ->where('deleted_at IS NULL')
                   ->findAll();
    }

    public function getEstadisticas()
    {
        $total = $this->countAll();
        $activos = $this->where('deleted_at IS NULL')->countAllResults();
        $inactivos = $this->where('deleted_at IS NOT NULL')->countAllResults();
        $bajoStock = $this->where('stock <=', 10)->where('deleted_at IS NULL')->countAllResults();

        return [
            'total' => $total,
            'activos' => $activos,
            'inactivos' => $inactivos,
            'bajo_stock' => $bajoStock
        ];
    }

    // Validación de unicidad de nombre
    public function verificarNombreUnico($nombre, $idExcluir = null)
    {
        $query = $this->where('nombre', $nombre)
                      ->where('deleted_at IS NULL');
        
        if ($idExcluir) {
            $query->where('id_repuesto !=', $idExcluir);
        }
        
        return $query->countAllResults() === 0;
    }
} 