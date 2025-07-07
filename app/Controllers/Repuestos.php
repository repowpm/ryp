<?php
namespace App\Controllers;

use App\Models\RepuestoModel;
use CodeIgniter\Controller;

class Repuestos extends BaseController
{
    public function index()
    {
        // Aquí irá la lógica para mostrar la lista de repuestos
        return view('repuestos/index');
    }
} 