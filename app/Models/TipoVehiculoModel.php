<?php
namespace App\Models;

use CodeIgniter\Model;

class TipoVehiculoModel extends Model
{
    protected $table = 'wp_md_tipo_vehiculo';
    protected $primaryKey = 'id_tipo';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false; // No hay deleted_at en la tabla
    protected $protectFields = true;
    protected $allowedFields = [
        'id_tipo',
        'nombre_tipo'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    // No hay deletedField porque no existe deleted_at

    // Validation
    protected $validationRules = [
        'id_tipo' => 'required|max_length[5]|min_length[4]',
        'nombre_tipo' => 'required|max_length[50]|min_length[2]'
    ];

    protected $validationMessages = [
        'id_tipo' => [
            'required' => 'El ID del tipo es obligatorio',
            'min_length' => 'El ID del tipo debe tener al menos 4 caracteres',
            'max_length' => 'El ID del tipo no puede exceder 5 caracteres'
        ],
        'nombre_tipo' => [
            'required' => 'El nombre del tipo es obligatorio',
            'min_length' => 'El nombre del tipo debe tener al menos 2 caracteres',
            'max_length' => 'El nombre del tipo no puede exceder 50 caracteres'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Métodos personalizados
    public function getTiposActivos()
    {
        return $this->select('id_tipo, nombre_tipo')
                   ->orderBy('nombre_tipo', 'ASC')
                   ->findAll();
    }

    public function obtenerTipo($id)
    {
        return $this->where('id_tipo', $id)->first();
    }

    public function getTiposParaSelect()
    {
        $tipos = $this->getTiposActivos();
        $options = [];
        
        foreach ($tipos as $tipo) {
            $options[$tipo['id_tipo']] = $tipo['nombre_tipo'];
        }
        
        return $options;
    }
} 