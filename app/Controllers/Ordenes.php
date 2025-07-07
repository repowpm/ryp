<?php
namespace App\Controllers;

use App\Models\OrdenModel;
use CodeIgniter\Controller;

class Ordenes extends BaseController
{
    public function index()
    {
        // Aquí irá la lógica para mostrar la lista de órdenes
        return view('ordenes/index');
    }
} 