<?php

namespace App\Models;

use CodeIgniter\Model;

class MarcaModeloModel extends Model
{
    protected $table = 'wp_md_marcas_modelos';
    protected $primaryKey = 'id_marca_modelo';
    protected $useAutoIncrement = false; // Se genera automáticamente por trigger
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'marca',
        'modelo',
        'anio_inicio',
        'anio_fin',
        'id_tipo',
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
        'marca' => 'required|max_length[50]',
        'modelo' => 'required|max_length[50]',
        'anio_inicio' => 'permit_empty|integer|greater_than[1900]|less_than[2031]',
        'anio_fin' => 'permit_empty|integer|greater_than[1900]|less_than[2031]',
        'id_tipo' => 'permit_empty|max_length[5]'
    ];

    protected $validationMessages = [
        'marca' => [
            'required' => 'La marca es obligatoria',
            'max_length' => 'La marca no puede exceder 50 caracteres'
        ],
        'modelo' => [
            'required' => 'El modelo es obligatorio',
            'max_length' => 'El modelo no puede exceder 50 caracteres'
        ],
        'anio_inicio' => [
            'integer' => 'El año de inicio debe ser un número entero',
            'greater_than' => 'El año de inicio debe ser mayor a 1900',
            'less_than' => 'El año de inicio no puede ser mayor a 2030'
        ],
        'anio_fin' => [
            'integer' => 'El año de fin debe ser un número entero',
            'greater_than' => 'El año de fin debe ser mayor a 1900',
            'less_than' => 'El año de fin no puede ser mayor a 2030'
        ],
        'id_tipo' => [
            'max_length' => 'El ID del tipo debe tener 5 caracteres'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Métodos personalizados
        public function getMarcas()
    {
        return $this->select('marca')
                    ->where('deleted_at IS NULL')
                    ->groupBy('marca')
                    ->orderBy('marca', 'ASC')
                    ->findAll();
    }

        public function getModelosByMarca($marca)
    {
        return $this->where('marca', $marca)
                    ->where('deleted_at IS NULL')
                    ->orderBy('modelo', 'ASC')
                    ->findAll();
    }

    public function getModelosByMarcaAndTipo($marca, $tipo = null)
    {
        $builder = $this->where('marca', $marca)
                        ->where('deleted_at IS NULL');
        
        if ($tipo) {
            $builder->where('id_tipo', $tipo);
        }
        
        return $builder->orderBy('modelo', 'ASC')->findAll();
    }

        public function getMarcasByTipo($tipo)
    {
        return $this->select('marca')
                    ->where('id_tipo', $tipo)
                    ->where('deleted_at IS NULL')
                    ->groupBy('marca')
                    ->orderBy('marca', 'ASC')
                    ->findAll();
    }

        public function searchMarcasModelos($search)
    {
        return $this->groupStart()
                    ->like('marca', $search)
                    ->orLike('modelo', $search)
                    ->groupEnd()
                    ->where('deleted_at IS NULL')
                    ->orderBy('marca', 'ASC')
                    ->orderBy('modelo', 'ASC')
                    ->findAll();
    }

        public function getMarcasModelosCompletos()
    {
        return $this->where('deleted_at IS NULL')
                    ->orderBy('marca', 'ASC')
                    ->orderBy('modelo', 'ASC')
                    ->findAll();
    }
} 