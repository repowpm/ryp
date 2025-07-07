<?php
namespace App\Models;

use CodeIgniter\Model;

class VehiculoModel extends Model
{
    protected $table = 'vehiculos';
    protected $primaryKey = 'id_vehiculo';
    protected $allowedFields = ['patente', 'marca', 'modelo', 'anio', 'color', 'id_cliente'];
} 