<?php
namespace App\Controllers;

use App\Models\VehiculoModel;
use CodeIgniter\Controller;

class Vehiculos extends BaseController
{
    public function index()
    {
        // Aquí irá la lógica para mostrar la lista de vehículos
        return view('vehiculos/index');
    }
} 