<?php
namespace App\Models;

use CodeIgniter\Model;

class ReporteModel extends Model
{
    protected $table = 'wp_md_reportes';
    protected $primaryKey = 'id_reporte';
    protected $useAutoIncrement = false; // Se genera automáticamente por trigger
    protected $allowedFields = ['titulo', 'descripcion', 'fecha', 'tipo'];
} 