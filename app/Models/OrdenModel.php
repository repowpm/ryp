<?php
namespace App\Models;

use CodeIgniter\Model;

class OrdenModel extends Model
{
    protected $table = 'ordenes';
    protected $primaryKey = 'id_orden';
    protected $allowedFields = ['id_cliente', 'id_vehiculo', 'fecha', 'estado', 'total'];
} 