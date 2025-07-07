<?php
namespace App\Controllers;

use App\Models\ReporteModel;
use CodeIgniter\Controller;

class Reportes extends BaseController
{
    public function index()
    {
        // Aquí irá la lógica para mostrar los reportes
        return view('reportes/index');
    }
} 