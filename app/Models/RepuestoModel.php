<?php
namespace App\Models;

use CodeIgniter\Model;

class RepuestoModel extends Model
{
    protected $table = 'repuestos';
    protected $primaryKey = 'id_repuesto';
    protected $allowedFields = ['nombre', 'descripcion', 'stock', 'precio'];
} 